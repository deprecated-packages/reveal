<?php

declare (strict_types=1);
namespace RevealPrefix20220820\Symplify\AutowireArrayParameter\Skipper;

use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RevealPrefix20220820\Symfony\Component\DependencyInjection\Definition;
use RevealPrefix20220820\Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver;
final class ParameterSkipper
{
    /**
     * Classes that create circular dependencies
     *
     * @var string[]
     */
    private const DEFAULT_EXCLUDED_FATAL_CLASSES = ['RevealPrefix20220820\\Symfony\\Component\\Form\\FormExtensionInterface', 'RevealPrefix20220820\\Symfony\\Component\\Asset\\PackageInterface', 'RevealPrefix20220820\\Symfony\\Component\\Config\\Loader\\LoaderInterface', 'RevealPrefix20220820\\Symfony\\Component\\VarDumper\\Dumper\\ContextProvider\\ContextProviderInterface', 'RevealPrefix20220820\\EasyCorp\\Bundle\\EasyAdminBundle\\Form\\Type\\Configurator\\TypeConfiguratorInterface', 'RevealPrefix20220820\\Sonata\\CoreBundle\\Model\\Adapter\\AdapterInterface', 'RevealPrefix20220820\\Sonata\\Doctrine\\Adapter\\AdapterChain', 'RevealPrefix20220820\\Sonata\\Twig\\Extension\\TemplateExtension'];
    /**
     * @var string[]
     */
    private $excludedFatalClasses = [];
    /**
     * @var \Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver
     */
    private $parameterTypeResolver;
    /**
     * @param string[] $excludedFatalClasses
     */
    public function __construct(ParameterTypeResolver $parameterTypeResolver, array $excludedFatalClasses)
    {
        $this->parameterTypeResolver = $parameterTypeResolver;
        $this->excludedFatalClasses = \array_merge(self::DEFAULT_EXCLUDED_FATAL_CLASSES, $excludedFatalClasses);
    }
    public function shouldSkipParameter(ReflectionMethod $reflectionMethod, Definition $definition, ReflectionParameter $reflectionParameter) : bool
    {
        if (!$this->isArrayType($reflectionParameter)) {
            return \true;
        }
        // already set
        $argumentName = '$' . $reflectionParameter->getName();
        if (isset($definition->getArguments()[$argumentName])) {
            return \true;
        }
        $parameterType = $this->parameterTypeResolver->resolveParameterType($reflectionParameter->getName(), $reflectionMethod);
        if ($parameterType === null) {
            return \true;
        }
        if (\in_array($parameterType, $this->excludedFatalClasses, \true)) {
            return \true;
        }
        if (!\class_exists($parameterType) && !\interface_exists($parameterType)) {
            return \true;
        }
        // prevent circular dependency
        if ($definition->getClass() === null) {
            return \false;
        }
        return \is_a($definition->getClass(), $parameterType, \true);
    }
    private function isArrayType(ReflectionParameter $reflectionParameter) : bool
    {
        if ($reflectionParameter->getType() === null) {
            return \false;
        }
        $reflectionParameterType = $reflectionParameter->getType();
        if (!$reflectionParameterType instanceof ReflectionNamedType) {
            return \false;
        }
        return $reflectionParameterType->getName() === 'array';
    }
}
