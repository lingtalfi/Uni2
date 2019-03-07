<?php


namespace Ling\Uni2\Application;


use Ling\BabyYaml\BabyYamlUtil;
use Ling\BabyYaml\Reader\Exception\ParseErrorException;
use Ling\Bat\BDotTool;
use Ling\CliTools\Command\CommandInterface;
use Ling\CliTools\Input\InputInterface;
use Ling\CliTools\Output\OutputInterface;
use Ling\CliTools\Program\Application;
use Ling\Uni2\Command\UniToolGenericCommand;
use Ling\Uni2\DependencySystemImporter\DependencySystemImporterInterface;
use Ling\Uni2\DependencySystemImporter\GitGalaxyDependencySystemImporter;
use Ling\Uni2\DependencySystemImporter\GitRepoDependencySystemImporter;
use Ling\Uni2\Exception\Uni2Exception;
use Ling\Uni2\LocalServer\LocalServer;
use Ling\UniverseTools\MetaInfoTool;


/**
 * The UniToolApplication class.
 * This is the console @object(Application) of the uni tool.
 *
 *
 * It has the following commands:
 *
 * - listplanet: lists the planets in the current application, optionally with their version number.
 * - version: shows the current version of this Uni2 planet.
 * - conf: displays the uni tool configuration, or updates the configuration values.
 * - confpath: displays the uni tool's configuration path
 *
 *
 *
 * The following options apply at this application level and can be passed via the @concept(command-line):
 *
 * Options:
 * - --application-dir: the path to the application dir to use. The default value is the current directory.
 *
 *
 *
 *
 * The uni-tool info file
 * -----------------------
 * The uni-tool info file contains internal information about the uni-tool state.
 * It contains the following data:
 *
 * - last_update: string|null. The date (mysql format) when the uni tool was last updated (with the upgrade command).
 *
 *
 * This information is used internally and shouldn't be edited manually (unless you do exactly what you are doing).
 *
 *
 *
 *
 */
class UniToolApplication extends Application
{


    /**
     * This property holds the currentDirectory for this instance.
     * This is the path of the script calling this class.
     *
     * By default, it's also the application directory,
     * unless the application directory is passed explicitly as a @kw(command line option).
     *
     *
     * @var string
     */
    protected $currentDirectory;

    /**
     * This property holds the application directory.
     * It must be set via the --application-dir option.
     * If not set, this will default to the current directory (see $currentDirectory property).
     *
     * @var string
     */
    private $applicationDir;

    /**
     * This property holds the path to the configuration file.
     * See the @object(configuration command) for more info.
     *
     * Note: there should be only one configuration file per machine, since there should be only uni tool per machine.
     *
     * @var string
     */
    private $confFile;

    /**
     * This property holds the path to the uni-tool info file.
     *
     * @var string
     */
    private $infoFile;

    /**
     * This property holds the dependencyMasterConf for this instance.
     * This is a cache for the dependency master array.
     *
     * @var array = null
     */
    private $dependencyMasterConf;


    /**
     * This property holds an array of the available importers for this instance.
     * It's an array of dependencySystemName => DependencySystemImporterInterface.
     *
     * @var DependencySystemImporterInterface[]
     */
    private $importers;

    /**
     * This property holds the localServer for this instance.
     * It's used as a cache value.
     *
     * @var LocalServer
     */
    private $localServer;


    public function __construct()
    {
        parent::__construct();
        $this->currentDirectory = null;
        $this->applicationDir = null;
        $this->dependencyMasterConf = null;
        $this->localServer = null;
        $this->confFile = __DIR__ . "/../private/configuration/conf.byml";
        $this->infoFile = __DIR__ . "/../private/info/uni-tool-info.byml";

        $this->registerCommand("Ling\Uni2\Command\CheckCommand", "check");
        $this->registerCommand("Ling\Uni2\Command\CleanCommand", "clean");
        $this->registerCommand("Ling\Uni2\Command\ConfCommand", "conf");
        $this->registerCommand("Ling\Uni2\Command\ConfPathCommand", "confpath");
        $this->registerCommand("Ling\Uni2\Command\HelpCommand", "help");

        $this->registerCommand("Ling\Uni2\Command\ImportCommand", "import");
        $this->registerCommand("Ling\Uni2\Command\ImportAllCommand", "import-all");
        $this->registerCommand("Ling\Uni2\Command\ImportGalaxyCommand", "import-galaxy");
        $this->registerCommand("Ling\Uni2\Command\ImportMapCommand", "import-map");
        $this->registerCommand("Ling\Uni2\Command\ImportUniverseCommand", "import-universe");

        $this->registerCommand("Ling\Uni2\Command\ListPlanetCommand", "listplanet");

        $this->registerCommand("Ling\Uni2\Command\CreateMapCommand", "map");

        $this->registerCommand("Ling\Uni2\Command\ShowDependencyMasterCommand", "master");
        $this->registerCommand("Ling\Uni2\Command\DependencyMasterPathCommand", "masterpath");


        $this->registerCommand("Ling\Uni2\Command\ReimportCommand", "reimport");
        $this->registerCommand("Ling\Uni2\Command\ReimportAllCommand", "reimport-all");
        $this->registerCommand("Ling\Uni2\Command\ReimportGalaxyCommand", "reimport-galaxy");
        $this->registerCommand("Ling\Uni2\Command\ReimportMapCommand", "reimport-map");
        $this->registerCommand("Ling\Uni2\Command\ReimportUniverseCommand", "reimport-universe");


        $this->registerCommand("Ling\Uni2\Command\StoreCommand", "store");
        $this->registerCommand("Ling\Uni2\Command\StoreGalaxyCommand", "store-galaxy");
        $this->registerCommand("Ling\Uni2\Command\StoreMapCommand", "store-map");

        $this->registerCommand("Ling\Uni2\Command\ToDirCommand", "todir");
        $this->registerCommand("Ling\Uni2\Command\ToLinkCommand", "tolink");
        $this->registerCommand("Ling\Uni2\Command\UpgradeCommand", "upgrade");
        $this->registerCommand("Ling\Uni2\Command\VersionCommand", "version");


        $this->registerCommand("Ling\Uni2\Command\Internal\PackUni2Command", "private:pack");

//        $this->registerCommand("Ling\Uni2\Command\DependencyMasterPathCommand", "info"); // info about a planet: meta and recursive dependencies

//        $this->registerCommand("Ling\Uni2\Command\HelpCommand", "import-galaxy");


        $lingImporter = new GitGalaxyDependencySystemImporter("Ling");
        $lingImporter->setBaseRepoName("lingtalfi");
        $lingImporter->setBaseRepoName("karayabin/universe-snapshot/tree/master/universe");

        $gitImporter = new GitRepoDependencySystemImporter("git");

        $this->importers = [
            $lingImporter->getDependencySystemName() => $lingImporter,
            $gitImporter->getDependencySystemName() => $gitImporter,
        ];
    }


    /**
     * Returns a valid application directory.
     *
     * It will check that the directory exists and is a directory.
     *
     * @return string
     * @throws Uni2Exception
     */
    public function getApplicationDir()
    {
        if (null === $this->applicationDir) {
            throw new Uni2Exception("The application directory hasn't been set. You can set it using either the --application-dir option, or execute the program from the application directory.");
        }

        if (false === is_dir($this->applicationDir)) {
            throw new Uni2Exception("The application directory (" . $this->applicationDir . ") is not a directory. You can set the application directory using either the --application-dir option, or executing the program from the application directory directly.");
        }

        return $this->applicationDir;
    }

    public function getUniverseDependenciesDir()
    {
        $appDir = $this->getApplicationDir();
        return $appDir . "/universe-dependencies";
    }

    /**
     * Returns the importers of this instance.
     * It's an array of dependencySystemName => DependencySystemImporterInterface.
     *
     * @return DependencySystemImporterInterface[]
     */
    public function getImporters(): array
    {
        return $this->importers;
    }


    /**
     * Returns the location of a valid universe directory.
     * The universe directory is the universe directory at the root of the application directory.
     *
     *
     * @return string
     * @throws Uni2Exception
     */
    public function getUniverseDirectory()
    {
        $universeDir = $this->getApplicationDir() . "/universe";
        if (false === is_dir($universeDir)) {
            throw new Uni2Exception("The universe directory (" . $universeDir . ") is not a directory. You must create the universe directory at the root of your application directory.");
        }
        return $universeDir;
    }

    /**
     * Returns the confFile of this instance.
     *
     * @return string
     */
    public function getConfFile(): string
    {
        return realpath($this->confFile);
    }

    public function getConf(): array
    {
        return BabyYamlUtil::readFile($this->confFile);
    }


    /**
     * Returns a value from the uni-tool configuration.
     *
     * If the provided $key doesn't exist in the configuration,
     * then the $default value is returned.
     *
     *
     * @param string $key
     * @param mixed $default = null
     * @return mixed
     */
    public function getConfValue(string $key, $default = null)
    {
        $conf = BabyYamlUtil::readFile($this->confFile);
        return BDotTool::getDotValue($key, $conf, $default);
    }


    /**
     * Returns an instance of the local server.
     *
     * @return LocalServer
     */
    public function getLocalServer(): LocalServer
    {
        if (null === $this->localServer) {
            $this->localServer = new LocalServer();
            $this->localServer->setRootDir($this->getConfValue("local_server.root_dir"));
            $this->localServer->setActive($this->getConfValue("local_server.is_active"));
        }

        return $this->localServer;
    }

    /**
     * Returns the version number of the uni-tool on the web.
     * See the @page(uni-tool upgrade-system) for more info.
     *
     *
     * @return string
     * @throws Uni2Exception
     */
    public function getWebVersionNumber(): string
    {
        $url = "https://raw.githubusercontent.com/lingtalfi/universe-naive-importer/master/meta-info.byml";
        $content = file_get_contents($url); // version: x.x.x
        if (false === $content) {
            throw new Uni2Exception("Cannot access the web url: $url");
        }
        $version = trim(explode(':', $content)[1]);
        return $version;
    }


    /**
     * Copies the dependency-master file on the web to the local uni-tool copy's root directory.
     * Returns whether the copy operation was successful.
     *
     * See the @page(dependency master page) for more info.
     *
     *
     * @return bool
     * @throws Uni2Exception
     * @throws ParseErrorException
     */
    public function copyDependencyMasterFileFromWeb(): bool
    {
        $url = "https://raw.githubusercontent.com/lingtalfi/universe-naive-importer/master/dependency-master.byml";
        $file = $this->getLocalDependencyMasterPath();
        return copy($url, $file);
    }


    /**
     * Returns the dependency master array.
     * See @page(the dependency master page) for more info.
     *
     * @return array
     */
    public function getDependencyMasterConf(): array
    {
        if (null === $this->dependencyMasterConf) {
            $ret = [];
            $file = $this->getLocalDependencyMasterPath();
            if (file_exists($file)) {
                $ret = BabyYamlUtil::readFile($file);
            }
            $this->dependencyMasterConf = $ret;
        }
        return $this->dependencyMasterConf;
    }


    /**
     * Returns the galaxies known to the local dependency master array.
     * See @page(the dependency master page) for more info.
     *
     *
     * @return array
     * An array of galaxy names.
     */
    public function getKnownGalaxies(): array
    {
        $conf = $this->getDependencyMasterConf();
        return array_keys($conf['galaxies']);
    }


    /**
     * Returns the version number of the uni-tool on this local machine.
     * See the @page(uni-tool upgrade-system) for more info.
     *
     * Note: if for some reason the meta-info.byml is not accessible (i.e. the user deleted it for instance),
     * we return false.
     *
     *
     *
     *
     * @return string|false
     */
    public function getVersionNumber(): string
    {
        $meta = MetaInfoTool::parseInfo(__DIR__ . "/../");
        return $meta['version'] ?? false;
    }


    /**
     * Returns the path to the local dependency-master file.
     * See @page(the dependency master file page) for more details.
     *
     *
     * @return string
     */
    public function getLocalDependencyMasterPath(): string
    {
        return realpath(__DIR__ . "/../dependency-master.byml");
    }

    /**
     * Parses general options.
     *
     * @overrides
     */
    public function run(InputInterface $input, OutputInterface $output)
    {

        //--------------------------------------------
        // APPLICATION DIR
        //--------------------------------------------
        $appDir = $input->getOption("application-dir");
        if (null === $appDir) {
            $appDir = $this->currentDirectory;
        }
        $this->applicationDir = $appDir;

        //--------------------------------------------
        //
        //--------------------------------------------
        parent::run($input, $output);
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @overrides
     */
    protected function onCommandInstantiated(CommandInterface $command)
    {
        if ($command instanceof UniToolGenericCommand) {
            $command->setApplication($this);
        } else {
            throw new Uni2Exception("All commands must inherit from Uni2\Command\UniToolGenericCommand.");
        }
    }

}





