<?php

declare (strict_types=1);
namespace Reveal\TemplatePHPStanCompiler\ValueObject;

use PhpParser\Node\Expr\Array_;
/**
 * @api
 */
final class RenderTemplateWithParameters
{
    /**
     * @var string
     */
    private $templateFilePath;
    /**
     * @var \PhpParser\Node\Expr\Array_
     */
    private $parametersArray;
    public function __construct(string $templateFilePath, Array_ $parametersArray)
    {
        $this->templateFilePath = $templateFilePath;
        $this->parametersArray = $parametersArray;
    }
    public function getTemplateFilePath() : string
    {
        return $this->templateFilePath;
    }
    public function getParametersArray() : Array_
    {
        return $this->parametersArray;
    }
}
