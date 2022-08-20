<?php

declare (strict_types=1);
namespace Reveal\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;
use Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use Reveal\TwigPHPStanCompiler\ObjectTypeMethodAnalyzer;
use Reveal\TwigPHPStanCompiler\Reflection\PublicPropertyAnalyzer;
use Symplify\Astral\Naming\SimpleNameResolver;
use RevealPrefix20220820\Symplify\PHPStanRules\Exception\ShouldNotHappenException;
final class TwigGetAttributeExpanderNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var \Symplify\Astral\Naming\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Reveal\TwigPHPStanCompiler\ObjectTypeMethodAnalyzer
     */
    private $objectTypeMethodAnalyzer;
    /**
     * @var \Reveal\TwigPHPStanCompiler\Reflection\PublicPropertyAnalyzer
     */
    private $publicPropertyAnalyzer;
    /**
     * @var VariableAndType[]
     */
    private $variablesAndTypes;
    /**
     * @var array<string, string>
     */
    private $foreachedVariablesBySingleName;
    /**
     * @param VariableAndType[] $variablesAndTypes
     * @param array<string, string> $foreachedVariablesBySingleName
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer, PublicPropertyAnalyzer $publicPropertyAnalyzer, array $variablesAndTypes, array $foreachedVariablesBySingleName)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->objectTypeMethodAnalyzer = $objectTypeMethodAnalyzer;
        $this->publicPropertyAnalyzer = $publicPropertyAnalyzer;
        $this->variablesAndTypes = $variablesAndTypes;
        $this->foreachedVariablesBySingleName = $foreachedVariablesBySingleName;
    }
    /**
     * @return \PhpParser\Node\Expr|null
     */
    public function enterNode(Node $node)
    {
        $funcCall = $this->matchTwigAttributeFuncCall($node);
        if (!$funcCall instanceof FuncCall) {
            return null;
        }
        $variableName = $this->resolveVariableName($funcCall);
        if ($variableName === null) {
            return null;
        }
        $accessorName = $this->resolveAccessor($funcCall);
        // @todo correct improve get method, getter property
        $variableType = $this->matchVariableType($variableName);
        if (!$variableType instanceof Type) {
            // dummy fallback
            return new MethodCall(new Variable($variableName), new Identifier($accessorName));
        }
        if ($variableType->isOffsetAccessible()->yes()) {
            // array access safe fallback?
            return new ArrayDimFetch(new Variable($variableName), new String_($accessorName));
        }
        if ($this->publicPropertyAnalyzer->hasPublicProperty($variableType, $accessorName)) {
            return new PropertyFetch(new Variable($variableName), new Identifier($accessorName));
        }
        return $this->resolveMethodCall($accessorName, $variableType, $variableName);
    }
    private function resolveAccessor(FuncCall $funcCall) : string
    {
        $string = $funcCall->getArgs()[3]->value;
        if (!$string instanceof String_) {
            throw new ShouldNotHappenException();
        }
        return $string->value;
    }
    private function matchVariableType(string $variableName) : ?Type
    {
        foreach ($this->variablesAndTypes as $variableAndType) {
            if ($variableAndType->getVariable() !== $variableName) {
                continue;
            }
            return $variableAndType->getType();
        }
        return $this->matchForeachVariableType($variableName);
    }
    private function matchForeachVariableType(string $variableName) : ?Type
    {
        // foreached variable
        foreach ($this->variablesAndTypes as $variableAndType) {
            foreach ($this->foreachedVariablesBySingleName as $foreachedVariables) {
                if ($foreachedVariables !== $variableName) {
                    continue;
                }
                $possibleArrayType = $variableAndType->getType();
                if (!$possibleArrayType instanceof ArrayType) {
                    continue;
                }
                return $possibleArrayType->getItemType();
            }
        }
        return null;
    }
    /**
     * @return string|null
     */
    private function resolveVariableName(FuncCall $funcCall)
    {
        // @todo match with provided type
        $variable = $funcCall->getArgs()[2]->value;
        if (!$variable instanceof Variable) {
            throw new ShouldNotHappenException();
        }
        if ($variable->name instanceof Expr) {
            return null;
        }
        return $variable->name;
    }
    private function resolveMethodCall(string $accessorName, Type $variableType, string $variableName) : MethodCall
    {
        $matchedMethodName = $accessorName;
        // unwrap nullable method calls
        if ($variableType instanceof UnionType) {
            $variableType = TypeCombinator::removeNull($variableType);
        }
        // twig can work with 3 magic types: ".name" in twig => "getName()" method, "$name" property and "name()" method in PHP
        if ($variableType instanceof TypeWithClassName) {
            $resolvedGetterMethodName = $this->objectTypeMethodAnalyzer->matchObjectTypeGetterName($variableType, $accessorName);
            if ($resolvedGetterMethodName) {
                $matchedMethodName = $resolvedGetterMethodName;
            }
        }
        return new MethodCall(new Variable($variableName), new Identifier($matchedMethodName));
    }
    /**
     * @return null|\PhpParser\Node\Expr\FuncCall
     */
    private function matchTwigAttributeFuncCall(Node $node)
    {
        if (!$node instanceof FuncCall) {
            return null;
        }
        if ($node->name instanceof Expr) {
            return null;
        }
        // @see https://github.com/twigphp/Twig/blob/ed29f0010f93df22a96409f5ea442e91728213da/src/Extension/CoreExtension.php#L1378
        if (!$this->simpleNameResolver->isName($node, 'twig_get_attribute')) {
            return null;
        }
        return $node;
    }
}
