<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\Latte\Filters;

use RevealPrefix20220820\Latte\Runtime\Defaults;
use ReflectionClass;
use ReflectionException;
use Reveal\LattePHPStanCompiler\Contract\ValueObject\CallReferenceInterface;
use Reveal\LattePHPStanCompiler\Exception\InvalidLatteFilterFormatException;
use Reveal\LattePHPStanCompiler\ValueObject\DynamicCallReference;
use Reveal\LattePHPStanCompiler\ValueObject\FunctionCallReference;
use Reveal\LattePHPStanCompiler\ValueObject\StaticCallReference;
/**
 * @see \Reveal\LattePHPStanCompiler\Tests\Filters\FilterMatcherTest
 */
final class FilterMatcher
{
    /**
     * @var array<string, CallReferenceInterface>
     */
    private $latteFilters = [];
    /**
     * @var array<string, callable>
     */
    private $defaultFilters = [];
    /**
     * @param array<string, string|array{string, string}> $latteFilters
     */
    public function __construct(array $latteFilters)
    {
        foreach ($latteFilters as $filterName => $latteFilter) {
            $this->latteFilters[\strtolower($filterName)] = $this->createCallReference($latteFilter);
        }
        $defaults = new Defaults();
        $this->defaultFilters = \array_change_key_case($defaults->getFilters());
    }
    /**
     * @return \Reveal\LattePHPStanCompiler\Contract\ValueObject\CallReferenceInterface|null
     */
    public function match(string $filterName)
    {
        $filterName = \strtolower($filterName);
        $callReference = $this->findInDefaultFilters($filterName);
        if ($callReference !== null) {
            return $callReference;
        }
        return $this->latteFilters[$filterName] ?? null;
    }
    /**
     * @return \Reveal\LattePHPStanCompiler\Contract\ValueObject\CallReferenceInterface|null
     */
    private function findInDefaultFilters(string $filterName)
    {
        // match filter name in
        $filterCallable = $this->defaultFilters[$filterName] ?? null;
        if ($filterCallable === null) {
            return null;
        }
        /** @var array<string, string> $filterCallable */
        return $this->createCallReference($filterCallable);
    }
    /**
     * @param string|string[] $filterCallable
     * @return \Reveal\LattePHPStanCompiler\ValueObject\DynamicCallReference|\Reveal\LattePHPStanCompiler\ValueObject\FunctionCallReference|\Reveal\LattePHPStanCompiler\ValueObject\StaticCallReference
     */
    private function createCallReference($filterCallable)
    {
        if (\is_string($filterCallable)) {
            return new FunctionCallReference($filterCallable);
        }
        if (!\is_array($filterCallable)) {
            throw new InvalidLatteFilterFormatException();
        }
        if (\count($filterCallable) !== 2) {
            throw new InvalidLatteFilterFormatException('Filter should be consist of array ["class", "method"]');
        }
        /** @var class-string $filterClass */
        $filterClass = $filterCallable[0];
        $filterMethod = $filterCallable[1];
        try {
            // @todo method exists
            $reflectionClass = new ReflectionClass($filterClass);
            $reflectionMethod = $reflectionClass->getMethod($filterMethod);
        } catch (ReflectionException $exception) {
            throw new InvalidLatteFilterFormatException();
        }
        if ($reflectionMethod->isStatic()) {
            return new StaticCallReference($filterClass, $filterMethod);
        }
        return new DynamicCallReference($filterClass, $filterMethod);
    }
}
