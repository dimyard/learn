<?

include_once("module.php");

class ModulesList
{
    //Repository entity
    public Repository $repository;

    //List of module versions
    public array $moduleList;

    //Folders in repository to ignore it
    private array $blackList = [];

    public function __construct(Repository $repositoryEntity)
    {
        $this->repository = $repositoryEntity;
        self::GetModulesList();
    }

    function GetModulesList ()
    {
        $moduleDirs = array_diff(scandir($this->repository->repositoryPath), array('..', '.'));
        foreach ($moduleDirs as $moduleScanned) {
            if (in_array($moduleScanned, $this->blackList)) {
                continue;
            }
            try {
                self::GetModuleVersions($moduleScanned);
            }
            catch (Exception $exception)
            {
                echo "Cant initialize module {$moduleScanned}:" . $exception;
            }
        }
    }

    function GetModuleVersions (string $moduleName)
    {
        $pathToModule = $this->repository->repositoryPath . DIRECTORY_SEPARATOR . $moduleName;
        $versionDirs = array_diff(scandir($pathToModule), array('..', '.'));
        foreach ($versionDirs as $version) {
            $versionPath = $pathToModule . DIRECTORY_SEPARATOR . $version;

            if(!is_dir($versionPath)) {
                continue;
            }

            $version = htmlspecialchars($version);
            try {
                $module = new Module($moduleName);
                $module->moduleFolder = $pathToModule;
                $module->moduleVersion = self::NormalizeVersionName($version);
                $module->versionFolder = $versionPath;
                $module->GetPathToVersionControlFile();
                $this->moduleList[] = $module;
            }
            catch (Exception $exception) {
                echo "Cant initialize module`s update: " . $exception;
            }
        }
    }

    function NormalizeVersionName (string $folderName): string
    {
        $result = trim(preg_replace('/[^0-9\.]/', '', $folderName), ".");
        return str_replace(" ", "", $result);
    }

    function GetModuleByNameAndVersion (string $moduleName, string $version)
    {
        foreach ($this->moduleList as $moduleVersion) {
            if ($moduleVersion->moduleName !== $moduleName || $moduleVersion->moduleVersion !== $version) {
                continue;
            }
            return $moduleVersion;
        }
        throw new Exception("Cant find this update: {$moduleName} {$version}.");
    }

    private function GetAllDependencies(string $moduleName, string $version): array
    {
        $result = [];
        foreach ($this->moduleList as $moduleVersion) {
            if ($moduleVersion->moduleName !== $moduleName || $moduleVersion->moduleVersion !== $version ) {
                continue;
            }
            $result[] = $moduleVersion;
            if (!$moduleVersion->versionControlFile) {
                continue;
            }
            $dependencies = $moduleVersion->ParseVersionFile();
            if(count($dependencies) == 0) {
                return $result;
            }
            foreach ($dependencies as $moduleInList=>$versionInList) {
                $result = array_merge($result, $this->GetAllDependencies($moduleInList, $versionInList));
            }
            return $result;
        }

        return $result;
    }

    function GetDependencies(string $moduleName, string $version): array
    {
        $result = self::GetAllDependencies($moduleName, $version);
        foreach ($result as $key=>$value) {
            if ($value->moduleName === $moduleName && $value->moduleVersion === $version ) {
                unset($result[$key]);
            }
        }
        return $result;
    }

    function PrintVersionInfo (string $moduleName, string $version)
    {
        try {
            $module = self::GetModuleByNameAndVersion($moduleName, $version);
            echo "</br>___________________________________________";
            echo "</br><b>Module:</b> " . $module->moduleName . "</br>";
            echo "<b>Version:</b> " . $module->moduleVersion . "</br>";
            echo "<b>Folder of current version:</b> " . $module->versionFolder . "</br>";
            $dependencies = $this->GetDependencies($module->moduleName, $module->moduleVersion);
            if (count($dependencies) > 0) {
                echo "<b>This version have following dependencies:</b> </br>";
                foreach ($dependencies as $module) {
                    echo "- " . $module->moduleName . " " . $module->moduleVersion . "</br>";
                }
            }
            echo "___________________________________________</br>";
        }
        catch (Exception $e)
        {
            echo "</br><b>Error:</b> Cant find or access patch of this module: " . $moduleName . " "
                . $version . "</br>";
        }
    }
}