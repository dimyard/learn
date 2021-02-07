<?

include_once("module_version.php");

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

            if(!is_dir($version)) {
                continue;
            }

            $version = htmlspecialchars($version);
            try {
                $module = new Module($moduleName);
                $module->moduleFolder = $pathToModule;
                $module->moduleVersion = self::NormalizeVersionName($version);
                $module->versionFolder = $pathToModule . DIRECTORY_SEPARATOR . $version;
                $module::GetPathToVersionControlFile();
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

    function GetDependencies(string $moduleName, string $version)
    {
        $result = [];
        foreach ($this->moduleList as $moduleVersion) {
            if ($moduleVersion->moduleName !== $moduleName || $moduleVersion->moduleVersion !== $version ) {
                continue;
            }
            if (!$moduleVersion->versionControlFile) {
                continue;
            }
            //Really?
            if (count($moduleVersion->dependencies) > 0) {
              return $moduleVersion->dependencies;
            }
            $dependencies = $moduleVersion->ParseVersionFile();
            if(count($dependencies) == 0) {
                return $result;
            }
            foreach ($dependencies as $moduleInList=>$versionInList) {
                //$result[] =  $moduleInList;
                $result = array_merge($result, $this->GetDependencies($moduleInList, $versionInList));
            }
            return $result;
        }
        throw new Exception("Cant find module '{$moduleName}' with version '{$version}'.");
    }
}