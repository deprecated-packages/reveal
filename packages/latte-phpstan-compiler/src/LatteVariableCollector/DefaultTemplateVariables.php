<?php

declare (strict_types=1);
namespace Reveal\LattePHPStanCompiler\LatteVariableCollector;

use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use Reveal\LattePHPStanCompiler\Contract\LatteVariableCollectorInterface;
use Reveal\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use stdClass;
final class DefaultTemplateVariables implements LatteVariableCollectorInterface
{
    /**
     * @return VariableAndType[]
     */
    public function getVariablesAndTypes() : array
    {
        $variablesAndTypes = [];
        $variablesAndTypes[] = new VariableAndType('baseUrl', new StringType());
        $variablesAndTypes[] = new VariableAndType('basePath', new StringType());
        $variablesAndTypes[] = new VariableAndType('ʟ_fi', new ObjectType('RevealPrefix20220820\\Latte\\Runtime\\FilterInfo'));
        // nette\security bridge
        $variablesAndTypes[] = new VariableAndType('user', new ObjectType('RevealPrefix20220820\\Nette\\Security\\User'));
        // nette\application bridge
        $variablesAndTypes[] = new VariableAndType('presenter', new ObjectType('RevealPrefix20220820\\Nette\\Application\\UI\\Presenter'));
        $variablesAndTypes[] = new VariableAndType('control', new ObjectType('RevealPrefix20220820\\Nette\\Application\\UI\\Control'));
        $flashesArrayType = new ArrayType(new MixedType(), new ObjectType(stdClass::class));
        $variablesAndTypes[] = new VariableAndType('flashes', $flashesArrayType);
        return $variablesAndTypes;
    }
}
