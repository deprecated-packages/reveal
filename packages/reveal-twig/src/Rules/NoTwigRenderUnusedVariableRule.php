<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\RevealTwig\Rules;

use RevealPrefix20220606\PhpParser\Node;
use RevealPrefix20220606\PhpParser\Node\Expr\MethodCall;
use RevealPrefix20220606\PHPStan\Analyser\Scope;
use RevealPrefix20220606\PHPStan\Rules\Rule;
use RevealPrefix20220606\Reveal\RevealTwig\NodeAnalyzer\SymfonyRenderWithParametersMatcher;
use RevealPrefix20220606\Reveal\TwigPHPStanCompiler\NodeAnalyzer\UnusedTwigTemplateVariableAnalyzer;
use RevealPrefix20220606\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use RevealPrefix20220606\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @implements Rule<MethodCall>
 * @see \Reveal\RevealTwig\Tests\Rules\NoTwigRenderUnusedVariableRule\NoTwigRenderUnusedVariableRuleTest
 */
final class NoTwigRenderUnusedVariableRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Passed "%s" variable is not used in the template';
    /**
     * @var \Reveal\TwigPHPStanCompiler\NodeAnalyzer\UnusedTwigTemplateVariableAnalyzer
     */
    private $unusedTwigTemplateVariableAnalyzer;
    /**
     * @var \Reveal\RevealTwig\NodeAnalyzer\SymfonyRenderWithParametersMatcher
     */
    private $symfonyRenderWithParametersMatcher;
    public function __construct(UnusedTwigTemplateVariableAnalyzer $unusedTwigTemplateVariableAnalyzer, SymfonyRenderWithParametersMatcher $symfonyRenderWithParametersMatcher)
    {
        $this->unusedTwigTemplateVariableAnalyzer = $unusedTwigTemplateVariableAnalyzer;
        $this->symfonyRenderWithParametersMatcher = $symfonyRenderWithParametersMatcher;
    }
    /**
     * @return class-string<Node>
     */
    public function getNodeType() : string
    {
        return MethodCall::class;
    }
    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope) : array
    {
        $renderTemplatesWithParameters = $this->symfonyRenderWithParametersMatcher->matchTwigRender($node, $scope);
        $templateFilePaths = [];
        foreach ($renderTemplatesWithParameters as $renderTemplateWithParameter) {
            $templateFilePaths[] = $renderTemplateWithParameter->getTemplateFilePath();
        }
        $unusedVariableNames = $this->unusedTwigTemplateVariableAnalyzer->resolveMethodCallAndTemplate($node, $templateFilePaths, $scope);
        if ($unusedVariableNames === []) {
            return [];
        }
        $errorMessages = [];
        foreach ($unusedVariableNames as $unusedVariableName) {
            $errorMessages[] = \sprintf(self::ERROR_MESSAGE, $unusedVariableName);
        }
        return $errorMessages;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
use Twig\Environment;

$environment = new Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'unused_variable' => 'value'
]);
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use Twig\Environment;

$environment = new Environment();
$environment->render(__DIR__ . '/some_file.twig', [
    'used_variable' => 'value'
]);
CODE_SAMPLE
)]);
    }
}
/**
 * @implements Rule<MethodCall>
 * @see \Reveal\RevealTwig\Tests\Rules\NoTwigRenderUnusedVariableRule\NoTwigRenderUnusedVariableRuleTest
 */
\class_alias('RevealPrefix20220606\\Reveal\\RevealTwig\\Rules\\NoTwigRenderUnusedVariableRule', 'Reveal\\RevealTwig\\Rules\\NoTwigRenderUnusedVariableRule', \false);
