<?
/*
include_once("module.php");

class ModuleVersion
{
    //Name of file with dependencies
    const VERSION_CONTROL_FILE = "version_control.txt";

    //Module name.
    public Module $moduleEntity;

    //Module version.
    public string $moduleVersion;

    //Current module version folder.
    public string $versionFolder;

    //Current module version control file (if exists).
    public string $versionControlFile;

    //List of module dependencies in current version.
    public array $dependencies = [];

    public function __construct(Module $module, string $version)
    {
        try{
            $this->moduleEntity = $module;
            $this->moduleVersion = $this->NormalizeVersionName($version);
            $this->versionFolder = $this->moduleEntity->moduleFolder . DIRECTORY_SEPARATOR . $version;

            if(!$this->CheckPathToCurrentVersion()){
                echo "Cant find or get access to folder: " . $this->versionFolder;
            }
            $this->PrintVersionInfo();
        }
        catch (Exception $e)
        {
            echo "Error in initializing module`s update folder: " .
                htmlspecialchars($this->moduleEntity->moduleName . " : " . $version . ". " . $e);
        }
    }


    function GetPathToVersionControlFile ()
    {
        $versionControlFile="";
        try {
            $this->CheckPathToCurrentVersion();
            $versionControlFile = $this->versionFolder . DIRECTORY_SEPARATOR . self::VERSION_CONTROL_FILE;
        }
        catch (Exception $e)
        {
            echo "Error: Cant find or access patch of this module: " . $this->moduleEntity->ModuleName . " "
                . $this->moduleVersion;
        }
        $this->versionControlFile =  file_exists($versionControlFile) ? $versionControlFile : "";
    }

    function CheckPathToCurrentVersion (): bool
    {
        if(!is_readable($this->versionFolder)) {
            throw new Exception("Error: There was a try to access the version folder, but it failed.
             Module" . $this->moduleEntity->moduleName . " " . $this->moduleVersion);
        }
        return is_dir($this->versionFolder);
    }

    function GetVersionDependencies ()
    {
        try {
            $this->GetPathToVersionControlFile();
            $this->ParseVersionFile();
            $this->PrintVersionInfo();
        }
        catch (Exception $exception) {
            echo "Error in handling version dependencies file: " . $exception;
        }
    }


    function GetVersionFolderNameByVersion(string $version) : string
    {
        $versionPath = $this->moduleEntity->moduleFolder . DIRECTORY_SEPARATOR . $version;

        if(!is_dir($versionPath)) {
            return "";
        }
        if(!file_exists($versionPath)) {
            $versionCountProtection = 0;
            try {
                    $repository = $this->moduleEntity->repositoryEntity;
                    $moduleVersionList = $repository->modulesList[$this->moduleEntity->ModuleName]->moduleVersionsList;
                    foreach ($moduleVersionList as $versionFolder) {
                        if (!substr_count($versionFolder, $this->moduleVersion)) {
                            continue;
                        }
                        if ($versionCountProtection > 0) {
                            echo "Problem in version list: we have multiply values of this version in repository: " . $versionFolder;
                            continue;
                        }
                        $versionPath = $this->moduleEntity->moduleFolder . DIRECTORY_SEPARATOR . $versionFolder;
                        $versionCountProtection++;
                    }
            } catch (Exception $exception) {
                echo "Failed to initialize module or version: " . $exception;
            }
        }
        return $versionPath;
    }

    function ParseVersionFile ()
    {
        if(file_exists($this->versionControlFile) && !is_readable($this->versionControlFile)) {
            throw new Exception("Error: There was a try to read version_control file that does`t 
                exist or for witch access is deny. Module" . $this->moduleEntity->ModuleName . " " . $this->moduleVersion);
        }
        if(empty($this->versionControlFile)) {

        }
        else {
            foreach (file($this->versionControlFile) as $line)
            {
                if ($line === false || !is_int(strpos($line, ","))) {
                    continue;
                }
                list($module, $version) = explode(',', $line, 2) + array(NULL, NULL);
                if ($version === NULL || $module === NULL) {
                    continue;
                }
                $module = trim($module);
                $version = trim($version);
                $repositoryModules = $this->moduleEntity->repositoryEntity->modulesList;
                $keyOfNeedleModule = array_search($module, array_column($repositoryModules, 'moduleName'));
                $versionList = $repositoryModules[$keyOfNeedleModule]->moduleVersionsList;
                //var_dump($repositoryModules[$keyOfNeedleModule]);
                if (!is_array($versionList) || count($versionList) < 1) {
                    //var_dump($this->moduleEntity->repositoryEntity->modulesList);
                    echo "Cant find module {$module} in repository";

                    continue;
                }
                //var_dump($versionList);
                foreach ($versionList as $moduleVersion) {
                    if (!substr_count($moduleVersion, $version) > 0) {
                        continue;
                    }
                    $this->dependencies[] = $moduleVersion;
                }
            }
        }
    }

    function GetDependencies (Module $module = null): array
    {
        $module = $module ?? $this;
        $result = [];
        if (count($module->dependencies) === 0) {
            return $result;
        }
        foreach ($module->dependencies as $moduleInList) {
            $result[] =  $moduleInList;
            $result = array_merge($result, $this->GetDependencies($moduleInList));
        }
        return $result;
    }

    function PrintVersionInfo ()
    {
        echo "</br>___________________________________________";
        echo "</br><b>Module:</b> " . $this->moduleEntity->moduleName . "</br>";
        echo "<b>Version:</b> " . $this->moduleVersion . "</br>";
        try {
            echo "<b>Folder of current version:</b> " . $this->versionFolder . "</br>";
        }
        catch (Exception $e)
        {
            echo "</br><b>Error:</b> Cant find or access patch of this module: " . $this->moduleEntity->moduleName . " "
                . $this->moduleVersion . "</br>";
        }

        if (count($this->dependencies) > 0) {
            $dependencies = []; //$this->GetDependencies();
            echo "<b>This version have following dependencies:</b> </br>";
            foreach ($dependencies as $module) {
                //var_export($module);
                echo "- " . $module->ModuleName . " " . $module->ModuleVersion . "</br>";
            }
        }
        echo "___________________________________________</br>";
    }
}*/