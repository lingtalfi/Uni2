<?php


namespace Ling\Uni2\Command\Internal;


use Ling\Bat\FileSystemTool;
use Ling\CliTools\Input\InputInterface;
use Ling\CliTools\Output\OutputInterface;
use Ling\DirScanner\DirScanner;
use Ling\Uni2\Command\UniToolGenericCommand;
use Ling\Uni2\Helper\OutputHelper as H;
use Ling\Uni2\Util\ImportUtil;
use Ling\UniverseTools\DependencyTool;


/**
 * The PackUni2Command class.
 *
 * This is a private command that I use to prepare the uni-tool for export to github.com.
 *
 * - creates the following structure at the location defined by the path option (usually ending with /uni)
 *
 *
 * ```txt
 * - $path/
 * ----- uni.php
 * ----- universe
 * --------- ...contains all planets necessary to make the uni-tool work properly
 * ```
 *
 *
 * The script **uni.php** is the uni-tool console program. It's ready to execute.
 *
 *
 *
 * Options
 * -----------
 * --path=$path, the path to the uni-tools directory to create. Generally, this directory is named uni.
 *
 *
 * Flags
 * -----------
 * - -f: force mode. By default, if a file exists at the path specified with the $path option,
 *      then the command does nothing (it aborts).
 *      To force the creation of the directory, set this flag: it will overwrite the **$path** directory/entry if
 *      it exists before creating the new directory.
 *
 *
 *
 */
class PackUni2Command extends UniToolGenericCommand
{


    /**
     * @implementation
     */
    public function run(InputInterface $input, OutputInterface $output)
    {

        $indentLevel = 0;
        $path = $input->getOption("path");
        $forceMode = $input->hasFlag("f");


        H::info(H::i($indentLevel) . "Creating the uni-tools directory:" . PHP_EOL, $output);


        if (null !== $path) {


            $proceed = true;
            if (file_exists($path)) {
                if (false === $forceMode) {
                    H::warning(H::i($indentLevel + 1) . "The entry <bold>$path</bold> already exists. Use the -f flag to overwrite." . PHP_EOL, $output);
                    $proceed = false;
                } else {
                    FileSystemTool::remove($path);
                }
            }


            if (true === $proceed) {


                $skeletonDir = __DIR__ . "/../../assets/uni-skeleton";
                if (is_dir($skeletonDir)) {

                    H::info(H::i($indentLevel + 1) . "Copying <bold>uni-skeleton</bold> directory to <bold>$path</bold>" . PHP_EOL, $output);
                    FileSystemTool::copyDir($skeletonDir, $path);

                    az($skeletonDir);


                    $universeDir = $this->application->getUniverseDirectory();
                    $uni2Dir = $universeDir . "/Ling/Uni2";
                    if (is_dir($uni2Dir)) {

                        //--------------------------------------------
                        // DOWNLOAD LATEST VERSIONS OF DEPENDENCIES
                        //--------------------------------------------
                        $deps = DependencyTool::getDependencyList($uni2Dir);

                        H::discover(H::i($indentLevel) . "Found " . count($deps) . " dependencies to import to the internal-universe of Uni2:" . PHP_EOL, $output);


                        $deps[] = [
                            "Ling",
                            "BumbleBee",
                        ];


                        $util = new ImportUtil();
                        foreach ($deps as $dep) {
                            list($galaxy, $planetName) = $dep;
                            $replacedPlanetDir = $internalUniverseDir . "/$galaxy/$planetName";

                            $util->importPlanet($galaxy . '/' . $planetName, $this->application, $output, [
                                "indentLevel" => $indentLevel + 1,
                                "forceMode" => false,
                                "importMode" => "reimport",
                                "_appReplacedItemDir" => $replacedPlanetDir,
                            ]);
                        }


                        //--------------------------------------------
                        // CLEAN THE DOWNLOADED PLANETS
                        //--------------------------------------------
                        $entriesToRemove = [
                            ".git",
                            ".gitignore",
                        ];
                        $internalUniverseDir = realpath($internalUniverseDir);
                        H::info(H::i($indentLevel) . "Cleaning the <bold>internal-universe</bold> directory from <bold>.git</bold> and <bold>.gitignore</bold> files...", $output);
                        $scanner = DirScanner::create();
                        $scanner->scanDir($internalUniverseDir, function ($path, $rPath, $level) use ($entriesToRemove) {
                            $file = basename($path);
                            if (in_array($file, $entriesToRemove, true)) {
                                FileSystemTool::remove($path);
                            }
                        });
                        $output->write("<success>ok</success>." . PHP_EOL);


                    } else {
                        H::error(H::i($indentLevel) . "The <bold>Uni2</bold> directory was not found!!" . PHP_EOL, $output);
                    }
                } else {
                    H::error(H::i($indentLevel) . "The <bold>uni-skeleton</bold> directory was not found!!" . PHP_EOL, $output);
                }
            }


            //--------------------------------------------
            //
            //--------------------------------------------
        } else {
            H::error(H::i($indentLevel + 1) . "You must pass the <bold>path</bold> option to continue." . PHP_EOL, $output);
        }


    }
}
