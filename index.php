<?
$lessonsDirs = scandir($_SERVER["DOCUMENT_ROOT"]);
echo "Here we go:</br></br>";

foreach ($lessonsDirs as $item)
{
    if(is_dir($item))
    {
        $indexPage =  $item . "/index.php";
        $infoFile =  $item . "/about.txt";

        if (file_exists($indexPage)
            && file_exists($infoFile)
            && $item != ".") {
            echo "<b>Lesson</b>: <a href='{$indexPage}'> {$item} </a></br>";
            echo "<b>Информация:</b> " . htmlspecialchars(file_get_contents($infoFile)) . "</br></br>";
        }
    }
}
