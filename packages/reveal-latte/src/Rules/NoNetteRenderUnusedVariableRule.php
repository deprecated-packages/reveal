<?php

declare (strict_types=1);
namespace Reveal\RevealLatte\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Reveal\LattePHPStanCompiler\NodeAnalyzer\UnusedNetteTemplateRenderVariableResolver;
use Reveal\RevealLatte\NodeAnalyzer\TemplateRenderAnalyzer;
use Reveal\TemplatePHPStanCompiler\NodeAnalyzer\TemplateFilePathResolver;
use RevealPrefix20220820\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use RevealPrefix20220820\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Reveal\RevealLatte\Tests\Rules\NoNetteRenderUnusedVariableRule\NoNetteRenderUnusedVariableRuleTest
 */
final class NoNetteRenderUnusedVariableRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Extra variables "%s" are passed to the template but never used there';
    /**
     * @var \Reveal\RevealLatte\NodeAnalyzer\TemplateRenderAnalyzer
     */
    private $templateRenderAnalyzer;
    /**
     * @var \Reveal\TemplatePHPStanCompiler\NodeAnalyzer\TemplateFilePathResolver
     */
    private $templateFilePathResolver;
    /**
     * @var \Reveal\LattePHPStanCompiler\NodeAnalyzer\UnusedNetteTemplateRenderVariableResolver
     */
    private $unusedNetteTemplateRenderVariableResolver;
    public function __construct(TemplateRenderAnalyzer $templateRenderAnalyzer, TemplateFilePathResolver $templateFilePathResolver, UnusedNetteTemplateRenderVariableResolver $unusedNetteTemplateRenderVariableResolver)
    {
        $this->templateRenderAnalyzer = $templateRenderAnalyzer;
        $this->templateFilePathResolver = $templateFilePathResolver;
        $this->unusedNetteTemplateRenderVariableResolver = $unusedNetteTemplateRenderVariableResolver;
    }
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
        if (!$this->templateRenderAnalyzer->isNetteTemplateRenderMethodCall($node, $scope)) {
            return [];
        }
        if (\count($node->args) < 2) {
            return [];
        }
        $firstArgOrVariadicPlaceholder = $node->args[0];
        if (!$firstArgOrVariadicPlaceholder instanceof Arg) {
            return [];
        }
        $firstArgValue = $firstArgOrVariadicPlaceholder->value;
        $templateFilePaths = $this->templateFilePathResolver->resolveExistingFilePaths($firstArgValue, $scope, 'latte');
        if ($templateFilePaths === []) {
            return [];
        }
        $unusedVariableNamesByTemplateFilePath = [];
        foreach ($templateFilePaths as $templateFilePath) {
            $unusedVariableNamesByTemplateFilePath[] = $this->unusedNetteTemplateRenderVariableResolver->resolveMethodCallAndTemplate($node, $templateFilePath, $scope);
        }
        $everywhereUnusedVariableNames = \array_intersect(...$unusedVariableNamesByTemplateFilePath);
        if ($everywhereUnusedVariableNames === []) {
            return [];
        }
        $unusedPassedVariablesString = \implode('", "', $everywhereUnusedVariableNames);
        $error = \sprintf(self::ERROR_MESSAGE, $unusedPassedVariablesString);
        return [$error];
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte');
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use Nette\Application\UI\Control;

final class SomeControl extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/some_file.latte', [
            'never_used_in_template' => 'value'
        ]);
    }
}
CODE_SAMPLE
)]);
    }
}
