<?php


namespace Ling\Uni2\Command;


use Ling\Bat\FileSystemTool;
use Ling\CliTools\Input\InputInterface;
use Ling\CliTools\Output\OutputInterface;
use Ling\DirScanner\DirScanner;


/**
 * The CleanCommand class.
 *
 * Parses all planets and items of the application recursively, and removes any entries set in the
 * clean_items configuration.
 *
 * Use the **conf** command to see the value of the clean_items directive.
 *
 *
 *
 */
class CleanCommand extends UniToolGenericCommand
{


    /**
     * @implementation
     */
    public function run(InputInterface $input, OutputInterface $output)
    {


        $filesToRemove = [];
        $sCleanItems = $this->application->getConfValue("clean_items", "");
        $entriesToRemove = array_map(function ($v) {
            return trim($v);
        }, explode(",", $sCleanItems));


        $callable = function ($path, $rPath, $level) use ($entriesToRemove, &$filesToRemove) {
            $file = basename($path);
            if (in_array($file, $entriesToRemove, true)) {
                $filesToRemove[] = $path;
            }
        };


        //--------------------------------------------
        // COLLECTING FROM PLANETS
        //--------------------------------------------
        $universeDir = $this->application->getUniverseDirectory();
        $scanner = DirScanner::create();
        $scanner->scanDir($universeDir, $callable);


        //--------------------------------------------
        // COLLECTING FROM DEPENDENCIES
        //--------------------------------------------
        $universeDepDir = $this->application->getUniverseDependenciesDir();
        $scanner->scanDir($universeDepDir, $callable);


        //--------------------------------------------
        // EXECUTING
        //--------------------------------------------
        foreach ($filesToRemove as $entry) {
            FileSystemTool::remove($entry);
        }


    }
}