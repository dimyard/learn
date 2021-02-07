<?

class Module
{
    //Name of file with dependencies
    const VERSION_CONTROL_FILE = "version_control.txt";

    //Module name.
    public string $moduleName;

    //Module folder.
    public string $moduleFolder;

    //Module version.
    public string $moduleVersion;

    //Current module version folder.
    public string $versionFolder;

    //Current module version control file (if exists).
    public string $versionControlFile;

    public function __construct(string $moduleName)
    {
        $this->moduleName = $moduleName;
    }

    function GetPathToVersionControlFile ()
    {
        $versionControlFile = $this->versionFolder . DIRECTORY_SEPARATOR . self::VERSION_CONTROL_FILE;
        $this->versionControlFile =  file_exists($versionControlFile) ? $versionControlFile : "";
    }

    function ParseVersionFile () : array
    {
        $result = [];
        if(file_exists($this->versionControlFile) && !is_readable($this->versionControlFile)) {
            throw new Exception("Error: There was a try to read version_control file that does`t 
                exist or for witch access is deny. Module" . $this->moduleName . " " . $this->moduleVersion);
        }
        if(empty($this->versionControlFile)) {
            return $result;
        }

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
            $result += [$module => $version];
        }
        return $result;
    }
}