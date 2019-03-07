<?php


namespace Ling\Uni2\Command;


use Ling\CliTools\Input\InputInterface;
use Ling\CliTools\Output\OutputInterface;
use Ling\Uni2\ErrorSummary\ErrorSummary;
use Ling\Uni2\Helper\OutputHelper as H;
use Ling\Uni2\Util\ImportUtil;
use Ling\UniverseTools\PlanetTool;


/**
 * The ReimportAllCommand class.
 *
 * Same as the @object(ReimportCommand) command,
 * but applies for all planets in the current application.
 *
 *
 * Options, flags, parameters
 * -----------
 * - -f: force reimport.
 *
 *      - If this flag is set, the uni-tool will force the reimport of the planets, even if there is no newer version.
 *          This can be useful for testing purposes for instance.
 *          If the planets have dependencies, the dependencies will also be reimported forcibly.
 *
 *
 */
class ReimportAllCommand extends UniToolGenericCommand
{

    /**
     *
     * This property holds the importMode for this instance.
     * See the @page(importMode definition) for more details.
     *
     * @var string = reimport (import|reimport)
     */
    protected $importMode;


    /**
     * Builds the ReimportAllCommand instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->importMode = "reimport";
    }


    /**
     * @implementation
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $forceMode = $input->hasFlag("f");
        $indentLevel = 0;


        $universeDir = $this->application->getUniverseDirectory();
        $planetDirs = PlanetTool::getPlanetDirs($universeDir);


        $errorSummary = new ErrorSummary();
        $helper = new ImportUtil();
        $helper->setErrorSummary($errorSummary);


        foreach ($planetDirs as $planetDir) {
            $pInfo = PlanetTool::getGalaxyNamePlanetNameByDir($planetDir);
            if (false !== $pInfo) {
                list($galaxy, $planetName) = $pInfo;
                $longPlanetName = $galaxy . "/" . $planetName;

                $helper->importPlanet($longPlanetName, $this->application, $output, [
                    "forceMode" => $forceMode,
                    "importMode" => "reimport",
                ]);
            } else {
                H::warning(H::i($indentLevel) . "Invalid planet dir: <bold>$planetDir</bold>." . PHP_EOL, $output);
            }
        }

        $errorSummary->displayErrorRecap($output);
    }
}