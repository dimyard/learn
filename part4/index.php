<?

include_once ("repository.php");
include_once ("module_list.php");

$repository = new Repository($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "part2" . DIRECTORY_SEPARATOR . "modules");
$modulesList = new ModulesList($repository);
$dependencies = $modulesList->PrintVersionInfo("general", "20.0.100");