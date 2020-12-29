<?
///
/// 1. Смотрим, есть ли файл с зависимостями.
/// 2. Проверяем наличие самой директории с из файла с зависимосстей.
///

/// Расположение папки с модулями
define("MODULES_FOLDER", $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "part2" . DIRECTORY_SEPARATOR . "modules");
define("VERSION_DEPENDENCIES_FILE", "version_control.txt");

function GetDependenciesInVersion (string $moduleName, string $version): array
{
    $result = [];
    $moduleVersions = GetModulesVersions($moduleName);
    $versionFile = GetPathToVersionControlFile($moduleName, $moduleVersions[$version]);
    if (!file_exists($versionFile)) {
        return $result;
    }
    $versionsFromFile = ParseVersionFile($versionFile);
    if (!$versionsFromFile || (is_array($versionsFromFile) && count($versionsFromFile) <= 0)) {
        return $result;
    }
    $result = $versionsFromFile;
    foreach ($result as $dependencyModule => $dependencyVersion) {
        if ($dependencyModule === null || $dependencyVersion === null) {
            continue;
        }
        $newDependencies = GetDependenciesInVersion($dependencyModule, $dependencyVersion);
        if ($newDependencies) {
            $result = GetArrayVsMaximalVersions($result, $newDependencies) ?? [];
        }
    }
    return $result;
}

function GetPathToVersionControlFile (string $moduleName, string $version): string
{
    return MODULES_FOLDER . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR .
        $version . DIRECTORY_SEPARATOR . VERSION_DEPENDENCIES_FILE;
}

function NormalizeVersionName (string $folderName): string
{
    $result = trim(preg_replace('/[^0-9\.]/', '', $folderName), ".");
    return str_replace(" ", "", $result);
}

function GetModulesVersions (string $moduleName)
{
    $result = [];
    $moduleName = CheckModuleExists($moduleName);
    if (!$moduleName) {
        return false;
    }
    $versionDirs = scandir($moduleName);
    if (!count($versionDirs) > 0) {
        return false;
    }
    foreach ($versionDirs as $version) {
        $version = htmlspecialchars($version);
        $currentVersion = NormalizeVersionName($version);
        if ($currentVersion) {
            $result[$currentVersion] = $version;
        }
    }
    return $result;
}

function CheckModuleExists (string $moduleName): string
{
    $moduleFolder = MODULES_FOLDER . DIRECTORY_SEPARATOR . $moduleName;
    if (is_dir($moduleFolder)) {
        return htmlspecialchars($moduleFolder);
    }
    else {
        throw new \http\Exception\InvalidArgumentException("Не удалось найти такой модуль: " .
            htmlspecialchars($moduleName));
    }
}

function ParseVersionFile (string $filePath)
{
    $result = [];
    if(!file_exists($filePath)) {
        return false;
    }
    foreach (file($filePath) as $line)
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
        if(is_string(CheckModuleExists($module)) && key_exists($version, GetModulesVersions($module))) {
            $result += [$module => $version];
        }
    }
    return $result;
}

function GetArrayVsMaximalVersions (array $firstArray, array $secondArray): array
{
    if (!is_array($firstArray) || !is_array($secondArray)) {
        return [];
    }
    $result = array_merge($firstArray, $secondArray);
    foreach ($firstArray as $module => $version) {
        if (key_exists($module, $secondArray)) {
            $result[$module] = version_compare($version, $secondArray[$module], ">=") ? $version : $secondArray[$module];
        }
    }
    return $result;
}

$sample = GetDependenciesInVersion("general", "20.0.100");

 foreach ($sample as $key => $val) {
     echo $key . " " . $val . "</br>";
 }
