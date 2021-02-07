<?

include_once ("repository.php");

$repository = new Repository($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "part2" . DIRECTORY_SEPARATOR . "modules");
//var_dump($repository->modulesList);
//$myTestModule = new Module("general", "20.0.100");
//$myTestModule->PrintVersionInfo();
//var_dump($myTestModule->Dependencies);