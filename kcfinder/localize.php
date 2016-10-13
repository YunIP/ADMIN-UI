<?php
namespace kcfinder;
require "core/autoload.php";
if (!isset($_GET['lng']) || ($_GET['lng'] == 'en') ||
($_GET['lng'] != basename($_GET['lng'])) ||
!is_file("lang/" . $_GET['lng'] . ".php")
) {
header("Content-Type: text/javascript");
die;
}
$file = "lang/" . $_GET['lng'] . ".php";
$mtime = @filemtime($file);
if ($mtime)
httpCache::checkMTime($mtime, "Content-Type: text/javascript");
require $file;
header("Content-Type: text/javascript");
echo "_.labels={";
$i = 0;
foreach ($lang as $english => $native) {
if (substr($english, 0, 1) != "_") {
echo "'" . text::jsValue($english) . "':\"" . text::jsValue($native) . "\"";
if (++$i < count($lang))
echo ",";
}
}
echo "}";
?>