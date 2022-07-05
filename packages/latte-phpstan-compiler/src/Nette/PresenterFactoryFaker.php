<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\Nette;

use RevealPrefix20220705\Nette\Application\PresenterFactory;
/**
 * @todo provide presenter factory from the project itself, so we have the full mapping available.
 * @see --twig-provider in AnalyzeTwigCommand
 */
final class PresenterFactoryFaker
{
    /**
     * @var array<string, string>
     */
    private $mapping;
    /**
     * @param array<string, string> $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }
    public function getPresenterFactory() : PresenterFactory
    {
        $presenterFactory = new PresenterFactory();
        $presenterFactory->setMapping($this->mapping);
        return $presenterFactory;
    }
}
