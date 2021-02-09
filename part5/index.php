<?

include_once ('module_list.php');

$modulesList = new ModulesList($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'part2' . DIRECTORY_SEPARATOR . 'modules');
$modulesList->printVersionInfo('general', '20.0.100');
