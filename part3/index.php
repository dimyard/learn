<?

include_once ("module.php");
$myTestModule = new Module("general", "20.0.100");
$myTestModule->PrintInfo();
//var_dump($myTestModule->Dependencies);