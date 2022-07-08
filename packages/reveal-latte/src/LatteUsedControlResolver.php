<?php

declare (strict_types=1);
namespace Reveal\RevealLatte;

use RevealPrefix20220708\Nette\Utils\Strings;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use RevealPrefix20220708\Symfony\Component\Finder\Finder;
use RevealPrefix20220708\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use RevealPrefix20220708\Symplify\SmartFileSystem\SmartFileInfo;
final class LatteUsedControlResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/iTz04c/1/
     */
    private const CONTROL_MARCO_REGEX = '#{(control|form) (?<' . self::NAME_PART . '>\\w+)(.*?)}#';
    /**
     * @var string
     */
    private const NAME_PART = 'name';
    /**
     * @var array<string, string[]>
     */
    private $latteUsedComponentNames = [];
    /**
     * @var string[]
     */
    private $layoutUsedComponentNames = [];
    /**
     * @var \Symplify\SmartFileSystem\Finder\FinderSanitizer
     */
    private $finderSanitizer;
    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }
    /**
     * @todo should be scoped per template that is related to control/presenter
     *
     * @return string[]
     */
    public function resolveControlNames(Scope $scope) : array
    {
        $suffixlessPresenterShortName = $this->resolveSuffixlessPresenterShortName($scope);
        if ($suffixlessPresenterShortName === null) {
            return [];
        }
        if (isset($this->latteUsedComponentNames[$suffixlessPresenterShortName])) {
            return $this->latteUsedComponentNames[$suffixlessPresenterShortName];
        }
        $latteFileInfos = $this->findLatteFileInfos($suffixlessPresenterShortName);
        $latteUsedComponentNames = $this->resolveControlNamesFromFileInfos($latteFileInfos);
        $this->latteUsedComponentNames[$suffixlessPresenterShortName] = $latteUsedComponentNames;
        return $latteUsedComponentNames;
    }
    /**
     * @return string[]
     */
    public function resolveLayoutControlNames() : array
    {
        if ($this->layoutUsedComponentNames !== []) {
            return $this->layoutUsedComponentNames;
        }
        $layoutLatteFileInfos = $this->findLatteLayoutFileInfos();
        $latteUsedComponentNames = $this->resolveControlNamesFromFileInfos($layoutLatteFileInfos);
        $this->layoutUsedComponentNames = $latteUsedComponentNames;
        return $latteUsedComponentNames;
    }
    /**
     * @return SmartFileInfo[]
     */
    private function findLatteFileInfos(string $presenterPathName) : array
    {
        $finder = new Finder();
        $finder->files()->in(\getcwd())->exclude('vendor')->path($presenterPathName)->name('*latte');
        return $this->finderSanitizer->sanitize($finder);
    }
    private function resolveSuffixlessPresenterShortName(Scope $scope) : ?string
    {
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return null;
        }
        $shortClassName = Strings::after($classReflection->getName(), '\\', -1);
        if ($shortClassName === null) {
            return null;
        }
        if (\substr_compare($shortClassName, 'Presenter', -\strlen('Presenter')) === 0) {
            return Strings::substring($shortClassName, 0, -Strings::length('Presenter'));
        }
        return $shortClassName;
    }
    /**
     * @return SmartFileInfo[]
     */
    private function findLatteLayoutFileInfos() : array
    {
        $finder = new Finder();
        $finder->files()->in(\getcwd())->exclude('vendor')->name('#@(.*?)\\.latte$#');
        return $this->finderSanitizer->sanitize($finder);
    }
    /**
     * @param SmartFileInfo[] $latteFileInfos
     * @return string[]
     */
    private function resolveControlNamesFromFileInfos(array $latteFileInfos) : array
    {
        $latteUsedComponentNames = [];
        foreach ($latteFileInfos as $latteFileInfo) {
            $matches = Strings::matchAll($latteFileInfo->getContents(), self::CONTROL_MARCO_REGEX);
            foreach ($matches as $match) {
                $latteUsedComponentNames[] = (string) $match[self::NAME_PART];
            }
        }
        return $latteUsedComponentNames;
    }
}
