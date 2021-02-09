<?

include_once('module.php');

class ModulesList
{
    //Repository entity
    public Repository $repository;

    //List of module versions
    public array $moduleList;

    //Folders in repository to ignore it
    private array $blackList = [];

    public function __construct(Repository $repositoryEntity, array $blackList = [])
    {
        $this->repository = $repositoryEntity;
        self::getModulesList();
        $this->blackList = $blackList;
    }

    private function getModulesList ()
    {
        $moduleDirs = array_diff(scandir($this->repository->repositoryPath), array('..', '.'));
        foreach ($moduleDirs as $moduleScanned) {
            if (in_array($moduleScanned, $this->blackList)) {
                continue;
            }
            try {
                self::getModuleVersions($moduleScanned);
            }
            catch (Exception $exception)
            {
                echo "Cant initialize module {$moduleScanned}:" . $exception;
            }
        }
    }

    private function getModuleVersions (string $moduleName)
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
                $module->moduleVersion = self::normalizeVersionName($version);
                $module->versionFolder = $versionPath;
                $module->getPathToVersionControlFile();
                $this->moduleList[] = $module;
            }
            catch (Exception $exception) {
                echo 'Cant initialize module`s update: ' . $exception;
            }
        }
    }

    protected function normalizeVersionName (string $folderName): string
    {
        $result = trim(preg_replace('/[^0-9\.]/', '', $folderName), '.');
        return str_replace(' ', '', $result);
    }

    protected function getModuleByNameAndVersion (string $moduleName, string $version)
    {
        foreach ($this->moduleList as $moduleVersion) {
            if ($moduleVersion->moduleName !== $moduleName || $moduleVersion->moduleVersion !== $version) {
                continue;
            }
            return $moduleVersion;
        }
        throw new Exception("Cant find this update: {$moduleName} {$version}.");
    }

    private function getAllDependencies(string $moduleName, string $version): array
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
                $result = array_merge($result, $this->getAllDependencies($moduleInList, $versionInList));
            }
            return $result;
        }

        return $result;
    }

    public function getDependencies(string $moduleName, string $version): array
    {
        $result = self::getAllDependencies($moduleName, $version);
        foreach ($result as $key=>$value) {
            if ($value->moduleName === $moduleName && $value->moduleVersion === $version ) {
                unset($result[$key]);
            }
        }
        return $result;
    }

    public function printVersionInfo (string $moduleName, string $version)
    {
        try {
            $module = self::getModuleByNameAndVersion($moduleName, $version);
            echo '</br>___________________________________________';
            echo '</br><b>Module:</b> ' . $module->moduleName . '</br>';
            echo '<b>Version:</b> ' . $module->moduleVersion . '</br>';
            echo '<b>Folder of current version:</b> ' . $module->versionFolder . '</br>';
            $dependencies = $this->getDependencies($module->moduleName, $module->moduleVersion);
            if (count($dependencies) > 0) {
                echo '<b>This version have following dependencies:</b> </br>';
                foreach ($dependencies as $module) {
                    echo '- ' . $module->moduleName . ' ' . $module->moduleVersion . '</br>';
                }
            }
            echo '___________________________________________</br>';
        }
        catch (Exception $e)
        {
            echo '</br><b>Error:</b> Cant find or access patch of this module: ' . $moduleName . ' '
                . $version . '</br>';
        }
    }
}
