<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\LattePHPStanCompiler\LatteVariableCollector;

use RevealPrefix20220606\Nette\Utils\Strings;
use RevealPrefix20220606\PHPStan\Type\ObjectType;
use ReflectionClass;
use ReflectionException;
use RevealPrefix20220606\Reveal\LattePHPStanCompiler\Contract\LatteVariableCollectorInterface;
use RevealPrefix20220606\Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
final class DynamicFilterVariables implements LatteVariableCollectorInterface
{
    /**
     * @var array<string, (string | array{string, string})>
     */
    private $latteFilters;
    /**
     * @param array<string, string|array{string, string}> $latteFilters
     */
    public function __construct(array $latteFilters)
    {
        $this->latteFilters = $latteFilters;
    }
    /**
     * @return VariableAndType[]
     */
    public function getVariablesAndTypes() : array
    {
        $variablesAndTypes = [];
        foreach ($this->latteFilters as $latteFilter) {
            if (\is_string($latteFilter)) {
                continue;
            }
            /** @var class-string $className */
            $className = $latteFilter[0];
            $methodName = $latteFilter[1];
            try {
                $reflectionClass = new ReflectionClass($className);
                $reflectionMethod = $reflectionClass->getMethod($methodName);
                if ($reflectionMethod->isStatic()) {
                    continue;
                }
                $variableName = Strings::firstLower(Strings::replace($className, '#\\\\#', '')) . 'Filter';
                $variablesAndTypes[] = new VariableAndType($variableName, new ObjectType($className));
            } catch (ReflectionException $exception) {
                continue;
            }
        }
        return $variablesAndTypes;
    }
}
