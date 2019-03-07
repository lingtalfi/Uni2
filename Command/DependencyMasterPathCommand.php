<?php


namespace Ling\Uni2\Command;


use Ling\CliTools\Input\InputInterface;
use Ling\CliTools\Output\OutputInterface;


/**
 * The DependencyMasterPathCommand class.
 * This command displays the path of the local @page(dependency-master file).
 *
 */
class DependencyMasterPathCommand extends UniToolGenericCommand
{


    /**
     * @implementation
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $output->write($this->application->getLocalDependencyMasterPath() . PHP_EOL);
    }

}