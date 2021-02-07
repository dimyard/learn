<?

include_once ("module.php");

class Repository
{
    //Path to current repository
    public string $repositoryPath;

    //Module entity list
    public array $modulesList;

    public function __construct(string $path)
    {
        $this->repositoryPath = $path ?? $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR .
            "part2" . DIRECTORY_SEPARATOR . "modules";
        if (!self::CheckPath()) {
            return false;
        }
        return $this;
    }

    function CheckPath()
    {
        return is_dir($this->repositoryPath);
    }


    function InitializeDependencies ()
    {
        foreach ($this->modulesList as $module) {
            print_r($module->moduleVersionsList);
            echo "</br>";
            foreach ($module->moduleVersionsList as $moduleVersion) {
                $moduleVersion->GetVersionDependencies();
            }
        }
    }
}
