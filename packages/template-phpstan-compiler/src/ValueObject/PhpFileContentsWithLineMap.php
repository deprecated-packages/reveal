<?php

declare (strict_types=1);
namespace Reveal\TemplatePHPStanCompiler\ValueObject;

use RevealPrefix20220713\Webmozart\Assert\Assert;
final class PhpFileContentsWithLineMap
{
    /**
     * @var string
     */
    private $phpFileContents;
    /**
     * @var array<int, int>
     */
    private $phpToTemplateLines;
    /**
     * @param array<int, int> $phpToTemplateLines
     */
    public function __construct(string $phpFileContents, array $phpToTemplateLines)
    {
        $this->phpFileContents = $phpFileContents;
        $this->phpToTemplateLines = $phpToTemplateLines;
        Assert::allInteger(\array_keys($phpToTemplateLines));
        Assert::allInteger($phpToTemplateLines);
    }
    public function getPhpFileContents() : string
    {
        return $this->phpFileContents;
    }
    /**
     * @return array<int, int>
     */
    public function getPhpToTemplateLines() : array
    {
        return $this->phpToTemplateLines;
    }
}
