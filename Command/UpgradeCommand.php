<?php


namespace Ling\Uni2\Command;


use Ling\Bat\BDotTool;
use Ling\CliTools\Input\InputInterface;
use Ling\CliTools\Output\BufferedOutput;
use Ling\CliTools\Output\OutputInterface;
use Ling\DirScanner\YorgDirScannerTool;
use Ling\Uni2\DependencySystemImporter\DependencySystemImporterInterface;
use Ling\Uni2\Helper\OutputHelper as H;
use Ling\UniverseTools\MetaInfoTool;


/**
 * The UpgradeCommand class.
 *
 * This class implements the upgrade system defined in the @page(uni-tool upgrade system document).
 *
 * Options, flags, parameters
 * -----------
 * - -l: live mode.
 *      - The importers output will be displayed in live mode (see the @object(DependencySystemImporterInterface) interface for more info).
 *              If not set, the importers output will be displayed only in case of an error.
 *
 *
 *
 */
class UpgradeCommand extends UniToolGenericCommand
{


    /**
     * @implementation
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        /**
         * 1. Update the dependency master file if necessary (i.e. if the version number of the uni-tool on the web is greater than the version number on the uni-tool in the local machine)
         * 2. If there was an update, then upgrade all planets from the local server
         */

        $indentLevel = $this->application->getBaseIndent();

        $liveMode = $input->hasFlag("l");

        $webVersion = $this->application->getWebVersionNumber();
        $version = $this->application->getVersionNumber();
        if (false === $version) {
            $version = "0.0.0"; // make sure this looses against the comparison that follows
        }


        $version = "0.9.0"; // TODO: remove


        //--------------------------------------------
        // DO WE REALLY NEED AN UPGRADE?
        //--------------------------------------------
        if ($webVersion > $version) {

            H::discover("A newer version of the uni-tool was found ($version --> $webVersion)." . PHP_EOL, $output);
            H::info(H::i($indentLevel + 1) . "Creating local copy of the dependency-master.byml from the web...", $output);

            /**
             * The web dependency master file is more recent: we will update the dependency master...
             */
            if (true === $this->application->copyDependencyMasterFileFromWeb()) {
                $output->write("<success>ok.</success>" . PHP_EOL);


                //--------------------------------------------
                // UPGRADING LOCAL SERVER IF ANY
                //--------------------------------------------
                H::info(H::i($indentLevel + 1) . 'Searching for local server...', $output);
                $serverDir = $this->application->getConfValue("local_server.root_dir");
                if (null !== $serverDir) {


                    $nbUpgrades = 0;

                    $output->write(' <success>Found ' . $serverDir . '.</success>' . PHP_EOL);
                    H::info(H::i($indentLevel + 2) . 'Upgrading local server:' . PHP_EOL, $output); // from local dependencies-master.byml


                    /**
                     * Updating all planets in the local server
                     */
                    $depMaster = $this->application->getDependencyMasterConf();
                    $knownGalaxies = $this->application->getKnownGalaxies();

                    $localServerDirs = YorgDirScannerTool::getDirs($serverDir);
                    if (count($localServerDirs) > 0) {

                        /**
                         * Counting planets and galaxies
                         */
                        $nbGalaxiesFound = 0;
                        $nbPlanetsFound = 0;
                        $all = [];

                        foreach ($localServerDirs as $localServerDir) {
                            $baseName = basename($localServerDir);
                            if (in_array($baseName, $knownGalaxies)) {

                                $nbGalaxiesFound++;

                                $galaxyName = $baseName;
                                $planetDirs = YorgDirScannerTool::getDirs($localServerDir);
                                $nbPlanetsFound += count($planetDirs);

                                $all[$galaxyName] = $planetDirs;
                            }
                        }

                        $sPlanet = (1 === $nbPlanetsFound) ? "planet" : "planets";
                        $sGalaxy = (1 === $nbGalaxiesFound) ? "galaxy" : "galaxies";

                        H::info(H::i($indentLevel + 3) . "$nbPlanetsFound $sPlanet found in $nbGalaxiesFound $sGalaxy." . PHP_EOL, $output);


                        /**
                         * Parsing planets
                         */
                        if ($all) {
                            $importers = $this->application->getImporters();
                            if (true === $liveMode) {
                                $importersOutput = null;
                            } else {
                                $importersOutput = new BufferedOutput();
                            }


                            foreach ($all as $galaxyName => $planetDirs) {

                                if (array_key_exists($galaxyName, $importers)) {

                                    /**
                                     * @var $importer DependencySystemImporterInterface
                                     */
                                    $importer = $importers[$galaxyName];

                                    foreach ($planetDirs as $planetDir) {
                                        $planetName = basename($planetDir);

                                        $path = "galaxies.$galaxyName.$planetName";
                                        $dependencyItem = BDotTool::getDotValue($path, $depMaster, []);

                                        $masterVersion = $dependencyItem['version'] ?? '0.0.0';
                                        $planetMeta = MetaInfoTool::parseInfo($planetDir);
                                        $planetVersion = $planetMeta['version'] ?? '0.0.0';

                                        if ($masterVersion > $planetVersion) {

                                            $nbUpgrades++;

                                            H::discover(H::i($indentLevel + 3) . "Upgrading <blue>$galaxyName/$planetName</blue> ($planetVersion --> $masterVersion)...", $output);

                                            $res = $importer->importPackage($planetName, $planetDir, $importersOutput);


                                            if (true === $res) {
                                                // debug mode
//                                                if (false && false === $liveMode) {
//                                                    $output->write(PHP_EOL);
//                                                    $output->write(implode(PHP_EOL, $importersOutput->getMessages()));
//                                                    $output->write(PHP_EOL);
//                                                }
                                                $output->write("<success>ok</success>." . PHP_EOL);


                                            } else {
                                                $output->write("<error>oops</error>." . PHP_EOL);
                                                if (null !== $importersOutput) {
                                                    H::error($importersOutput->getMessages(), $output, 4);
                                                }
                                            }

                                            if (null !== $importersOutput) {
                                                $importersOutput->reset();
                                            }
                                        }
                                    }
                                } else {
                                    H::warning(H::i($indentLevel + 3) . "The galaxy <bold>$galaxyName</bold> doesn't have an importer. It will be ignored entirely." . PHP_EOL, $output);
                                }
                            }
                        }
                    }


                    if (0 === $nbUpgrades) {
                        H::info(H::i($indentLevel + 3) . 'Nothing to upgrade.' . PHP_EOL, $output);
                    }
                } else {
                    $output->write(' <warning>No local server was found.</warning>' . PHP_EOL);
                }


            } else {
                H::error("the copy failed. The upgrade will stop." . PHP_EOL, $output);
            }
        } else {
            H::info("This uni-tool copy is already up-to-date (with version: $version). Nothing will be done." . PHP_EOL, $output);
        }


    }
}