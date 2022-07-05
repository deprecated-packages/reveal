<?php

declare (strict_types=1);
namespace Reveal\TemplatePHPStanCompiler\NodeAnalyzer;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use RevealPrefix20220705\Symfony\Component\Finder\Finder;
use RevealPrefix20220705\Symplify\Astral\NodeValue\NodeValueResolver;
/**
 * @api
 */
final class TemplateFilePathResolver
{
    /**
     * @var \Symplify\Astral\NodeValue\NodeValueResolver
     */
    private $nodeValueResolver;
    public function __construct(NodeValueResolver $nodeValueResolver)
    {
        $this->nodeValueResolver = $nodeValueResolver;
    }
    /**
     * @return string[]
     */
    public function resolveExistingFilePaths(Expr $expr, Scope $scope, string $templateSuffix) : array
    {
        $resolvedValue = $this->nodeValueResolver->resolveWithScope($expr, $scope);
        $possibleTemplateFilePaths = $this->arrayizeStrings($resolvedValue);
        if ($possibleTemplateFilePaths === []) {
            return [];
        }
        $resolvedTemplateFilePaths = [];
        foreach ($possibleTemplateFilePaths as $possibleTemplateFilePath) {
            // file could not be found, nothing we can do
            if (!\is_string($possibleTemplateFilePath)) {
                continue;
            }
            // 1. file exists
            if (\file_exists($possibleTemplateFilePath)) {
                $resolvedTemplateFilePaths[] = $possibleTemplateFilePath;
                continue;
            }
            // 2. look for possible template candidate in /templates directory
            $filePath = $this->findCandidateInTemplatesDirectory($possibleTemplateFilePath, $templateSuffix);
            if ($filePath === null) {
                continue;
            }
            $fileRealPath = \realpath($filePath);
            if ($fileRealPath === \false) {
                continue;
            }
            $resolvedTemplateFilePaths[] = $fileRealPath;
        }
        return $resolvedTemplateFilePaths;
    }
    /**
     * Helps with mapping of short name to FQN template name; Make configurable via rule constructor?
     * @return string|null
     */
    private function findCandidateInTemplatesDirectory(string $resolvedTemplateFilePath, string $templateSuffix)
    {
        $symfonyTemplatesDirectory = \getcwd() . '/templates';
        if (!\file_exists($symfonyTemplatesDirectory)) {
            return null;
        }
        $finder = new Finder();
        $finder->in($symfonyTemplatesDirectory)->files()->name('*.' . $templateSuffix);
        foreach ($finder->getIterator() as $fileInfo) {
            if (\substr_compare($fileInfo->getRealPath(), $resolvedTemplateFilePath, -\strlen($resolvedTemplateFilePath)) === 0) {
                return $fileInfo->getRealPath();
            }
        }
        return null;
    }
    /**
     * @return string[]|mixed[]
     * @param mixed $resolvedValue
     */
    private function arrayizeStrings($resolvedValue) : array
    {
        if (\is_string($resolvedValue)) {
            return [$resolvedValue];
        }
        if (\is_array($resolvedValue)) {
            return $resolvedValue;
        }
        // impossible to resolve
        return [];
    }
}
