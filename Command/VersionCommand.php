<?php


namespace Ling\Uni2\Command;


use Ling\CliTools\Input\InputInterface;
use Ling\CliTools\Output\OutputInterface;
use Ling\UniverseTools\MetaInfoTool;


/**
 * The VersionCommand class.
 * This command will display the version of this Uni2 planet.
 *
 *
 * Example
 * -------------
 *
 *
 *
 * ```bash
 * $ uni version
 * 1.0.0
 *
 * ```
 *
 *
 *
 *
 *
 */
class VersionCommand extends UniToolGenericCommand
{


    /**
     * @implementation
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $thisPlanetDir = __DIR__ . "/../";
        $info = MetaInfoTool::parseInfo($thisPlanetDir);
        $output->write(($info['version'] ?? "undefined") . PHP_EOL);
    }

}