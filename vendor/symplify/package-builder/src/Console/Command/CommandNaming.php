<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Symplify\PackageBuilder\Console\Command;

use RevealPrefix20220606\Nette\Utils\Strings;
use RevealPrefix20220606\Symfony\Component\Console\Command\Command;
/**
 * @api
 * @see \Symplify\PackageBuilder\Tests\Console\Command\CommandNamingTest
 */
final class CommandNaming
{
    /**
     * @var string
     * @see https://regex101.com/r/DfCWPx/1
     */
    private const BIG_LETTER_REGEX = '#[A-Z]#';
    /**
     * Converts:
     * - "SomeClass\SomeSuperCommand" → "some-super"
     * - "SomeClass\SOMESuperCommand" → "some-super"
     */
    public function resolveFromCommand(Command $command) : string
    {
        $commandClass = \get_class($command);
        return self::classToName($commandClass);
    }
    /**
     * Converts:
     * - "SomeClass\SomeSuperCommand" → "some-super"
     * - "SomeClass\SOMESuperCommand" → "some-super"
     */
    public static function classToName(string $class) : string
    {
        /** @var string $shortClassName */
        $shortClassName = self::resolveShortName($class);
        $rawCommandName = Strings::substring($shortClassName, 0, -\strlen('Command'));
        // ECSCommand => ecs
        for ($i = 0; $i < \strlen($rawCommandName); ++$i) {
            if (\ctype_upper($rawCommandName[$i]) && self::isFollowedByUpperCaseLetterOrNothing($rawCommandName, $i)) {
                $rawCommandName[$i] = \strtolower($rawCommandName[$i]);
            } else {
                break;
            }
        }
        $lowercasedRawCommandName = \lcfirst($rawCommandName);
        return Strings::replace($lowercasedRawCommandName, self::BIG_LETTER_REGEX, function (array $matches) : string {
            return '-' . \strtolower($matches[0]);
        });
    }
    private static function resolveShortName(string $class) : string
    {
        $classParts = \explode('\\', $class);
        return \array_pop($classParts);
    }
    private static function isFollowedByUpperCaseLetterOrNothing(string $string, int $position) : bool
    {
        // this is the last letter
        if (!isset($string[$position + 1])) {
            return \true;
        }
        // next letter is uppercase
        return \ctype_upper($string[$position + 1]);
    }
}
