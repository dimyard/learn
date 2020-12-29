<?

include_once("versions.php");

/// Расположение папки с модулями
define("MODULES_FOLDER", $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "part2" . DIRECTORY_SEPARATOR . "modules");
define("VERSION_DEPENDENCIES_FILE", "version_control.txt");


class Module
{
    //Module name.
    public string $ModuleName;

    //Module version.
    public string $ModuleVersion;

    //Module folder.
    public string $ModuleFolder;

    //Current module version folder.
    public string $VersionFolder;

    //Current module version control file (if exists).
    public string $VersionControlFile;

    //List of module dependencies in current version.
    public array $Dependencies = [];

    public function __construct(string $moduleName, string $version)
    {
        $this->ModuleName = $moduleName;
        $this->ModuleVersion = $version;
        try{
            $this->ModuleFolder = $this->CheckModuleExists();
        }
        catch (Exception $e)
        {
            echo "</br><b>Error:</b>Cant find this module: " . htmlspecialchars($moduleName);
        }

        $moduleVersions = new Versions($this);
        $this->VersionFolder = $moduleVersions->VersionsAndFoldersList[$this->ModuleVersion];
        $this->GetPathToVersionControlFile();
        if (!empty($this->VersionControlFile)) {
            try {
                $this->ParseVersionFile();
            }
            catch (Exception $e)
            {
                echo "</br><b>Error:</b> Error in paring version_control file: " . $this->VersionControlFile . "</br>";
            }

        }
    }

    function GetPathToVersionControlFile ()
    {
        $versionControlFile="";
        try {
            $versionControlFile = $this->GetPathToCurrentVersion() . DIRECTORY_SEPARATOR . VERSION_DEPENDENCIES_FILE;
        }
        catch (Exception $e)
        {
            echo "</br><b>Error:</b> Cant find or access patch of this module: " . $this->ModuleName . " "
                . $this->ModuleVersion . "</br>";
        }
        $this->VersionControlFile =  file_exists($versionControlFile) ? $versionControlFile : "";
    }

    function GetPathToCurrentVersion (): string
    {
        $versionFolder = MODULES_FOLDER . DIRECTORY_SEPARATOR . $this->ModuleName . DIRECTORY_SEPARATOR .
            $this->VersionFolder . DIRECTORY_SEPARATOR;
        if(!file_exists($versionFolder) || !is_readable($versionFolder)) {
            throw new Exception("</br><b>Error:</b> There was a try to access the version folder, but it failed.
             Module" . $this->ModuleName . " " . $this->ModuleVersion);
        }
        return $versionFolder;
    }

    function ParseVersionFile ()
    {
        if(!file_exists($this->VersionControlFile) || !is_readable($this->VersionControlFile)) {
            throw new Exception("</br><b>Error:</b> There was a try to read version_control file that does`t 
                exist or for witch access is deny. Module" . $this->ModuleName . " " . $this->ModuleVersion);
        }
        foreach (file($this->VersionControlFile) as $line)
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
            try
            {
                $dependencyModule = new Module($module, $version);
                $this->Dependencies[] = $dependencyModule;
            }
            catch (Exception $e)
            {
                echo "</br><b>Error:</b> Cant handle dependencies of this module: " .
                    $this->ModuleName . " " . $this->ModuleVersion . "</br>";
            }
        }
    }

    function CheckModuleExists (string $moduleName = null): string
    {
        $moduleName = $moduleName ?? $this->ModuleName;
        $moduleFolder = MODULES_FOLDER . DIRECTORY_SEPARATOR . $moduleName;
        if (is_dir($moduleFolder)) {
            return htmlspecialchars($moduleFolder);
        }
        else {
            throw new Exception("</br><b>Error:</b>Cant find this module: " . htmlspecialchars($moduleName));
        }
    }

    function GetDependencies (Module $module = null): array
    {
        $module = $module ?? $this;
        $result = [];
        if (count($module->Dependencies) === 0) {
            return $result;
        }
        foreach ($module->Dependencies as $moduleInList) {
            $result[] =  $moduleInList;
            $result = array_merge($result, $this->GetDependencies($moduleInList));
        }
        return $result;
    }

    function PrintInfo ()
    {
        echo "___________________________________________";
        echo "</br><b>Module:</b> " . $this->ModuleName . "</br>";
        echo "<b>Version:</b> " . $this->ModuleVersion . "</br>";
        try {
            echo "<b>Folder of current version:</b> " . $this->GetPathToCurrentVersion() . "</br>";
        }
        catch (Exception $e)
        {
            echo "</br><b>Error:</b> Cant find or access patch of this module: " . $this->ModuleName . " "
                . $this->ModuleVersion . "</br>";
        }

        if (count($this->Dependencies) > 0) {
            $dependencies = $this->GetDependencies();
            echo "<b>This version have following dependencies:</b> </br>";
            foreach ($dependencies as $module) {
                //var_export($module);
                echo "- " . $module->ModuleName . " " . $module->ModuleVersion . "</br>";
            }
        }
        echo "___________________________________________";
    }
}