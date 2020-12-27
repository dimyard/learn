<?
$lessonsDirs = scandir($_SERVER["DOCUMENT_ROOT"]);
echo "Here we go:</br></br>";

foreach ($lessonsDirs as $item)
{
    if(is_dir($item))
    {
        $indexPage =  $item . "/index.php";

        if (file_exists($indexPage) && $item != ".")
            echo "<a href='{$indexPage}'> Lesson: {$item} </a></br>";
    }
}
