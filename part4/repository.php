<?

include_once ("module_list.php");

class Repository
{
    //Path to current repository
    public string $repositoryPath;

    public function __construct(string $path)
    {
        $this->repositoryPath = $path ?? $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR .
            "part2" . DIRECTORY_SEPARATOR . "modules";
        if (!self::CheckPath()) {
            return false;
        }
        return $this;
    }

    function CheckPath(): bool
    {
        return is_dir($this->repositoryPath);
    }
}
