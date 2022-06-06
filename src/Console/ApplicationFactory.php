<?php

declare (strict_types=1);
namespace RevealPrefix20220606\Reveal\Console;

use RevealPrefix20220606\Symfony\Component\Console\Application;
use RevealPrefix20220606\Symfony\Component\Console\Command\Command;
final class ApplicationFactory
{
    /**
     * @var Command[]
     */
    private $commands;
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }
    public function create() : Application
    {
        $application = new Application();
        $application->addCommands($this->commands);
        return $application;
    }
}
\class_alias('RevealPrefix20220606\\Reveal\\Console\\ApplicationFactory', 'Reveal\\Console\\ApplicationFactory', \false);
