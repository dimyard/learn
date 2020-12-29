<?

include_once ("module.php");

class Versions
{
    //Module entity
    public Module $Module;

    //Versions with Destination folder name.
    public array $VersionsAndFoldersList;

    public function __construct(Module $module)
    {
        $this->Module = $module;
        $this->GetModulesVersions();
        return $this;
    }

    function GetModulesVersions (): bool
    {
        $result = [];
        $versionDirs = scandir($this->Module->ModuleFolder);
        if (!count($versionDirs) > 0) {
            return false;
        }
        foreach ($versionDirs as $version) {
            $version = htmlspecialchars($version);
            $currentVersion = $this->NormalizeVersionName($version);
            if ($currentVersion) {
                $result[$currentVersion] = $version;
            }
        }
        $this->VersionsAndFoldersList = $result;
        return true;
    }

    function NormalizeVersionName (string $folderName): string
    {
        $result = trim(preg_replace('/[^0-9\.]/', '', $folderName), ".");
        return str_replace(" ", "", $result);
    }
}
