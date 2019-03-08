<?php


namespace Ling\Uni2\Util;


use Ling\BabyYaml\BabyYamlUtil;
use Ling\UniverseTools\DependencyTool;
use Ling\UniverseTools\MetaInfoTool;
use Ling\UniverseTools\PlanetTool;

/**
 * The DependencyMasterBuilderUtil class.
 * This class helps creating the dependency master file.
 *
 * See the @page(uni-tool dependency master file) section for more info.
 *
 */
class DependencyMasterBuilderUtil
{


    /**
     *
     * Creates the dependency master file for the given $universeDir.
     *
     * The dependency master file will be created at the given $file path.
     *
     * If the galaxy name of a planet cannot be found, an error message will be appended
     * to the given $errors array.
     *
     * Similarly, if the planet does not have a version number, an error message will be appended.
     *
     *
     *
     * Note: the galaxy name and the version number should be found in the meta info of the planet.
     * See the @page(meta-info of a planet) for more details.
     *
     *
     *
     * How to use:
     * ------------
     * ```php
     * $universeDir = "/Users/pierrelafitte/Documents/it/php/projects/universe/planets";
     * $file = "/komin/jin_site_demo/tmp/dependency-master.byml";
     * $errors = [];
     * $util = new DependencyMasterBuilderUtil();
     * $util->createDependencyMasterByUniverseDir($universeDir, $file, $errors);
     * az($errors);
     * ```
     *
     *
     *
     *
     *
     * @param string $universeDir
     * @param string $file
     * @param array $errors
     * @throws \Ling\UniverseTools\Exception\UniverseToolsException
     */
    public function createDependencyMasterByUniverseDir(string $universeDir, string $file, array &$errors = [])
    {
        $galaxies = [];
        $planets = PlanetTool::getPlanetDirs($universeDir);
        foreach ($planets as $planetDir) {

            $planetName = basename($planetDir);
            $meta = MetaInfoTool::parseInfo($planetDir);
            $galaxy = $meta['galaxy'] ?? null;
            $version = $meta['version'] ?? null;

            if (null !== $galaxy) {

                if (null !== $version) {

                    if (false === array_key_exists($galaxy, $galaxies)) {
                        $galaxies[$galaxy] = [];
                    }

                    $dependencyItem = DependencyTool::getDependencyItem($planetDir);
                    if (false === array_key_exists("post_install", $dependencyItem)) {
                        $dependencyItem['post_install'] = [];
                    }


                    $galaxies[$galaxy][$planetName] = [
                        "version" => $version,
                        "dependencies" => $dependencyItem['dependencies'],
                        "post_install" => $dependencyItem['post_install'],
                    ];
                } else {
                    $errors[] = "The planet $planetName does not have a version number.";
                }

            } else {
                $errors[] = "The planet $planetName does not have a galaxy.";
            }
        }


        BabyYamlUtil::writeFile([
            "galaxies" => $galaxies,
        ], $file);
    }
}