<?php
namespace kcfinder;
class path {
static function rel2abs_url($path) {
if (substr($path, 0, 1) == "/") return $path;
$dir = @getcwd();
if (!isset($_SERVER['DOCUMENT_ROOT']) || ($dir === false))
return false;
$dir = self::normalize($dir);
$doc_root = self::normalize($_SERVER['DOCUMENT_ROOT']);
if (substr($dir, 0, strlen($doc_root)) != $doc_root)
return false;
$return = self::normalize(substr($dir, strlen($doc_root)) . "/$path");
if (substr($return, 0, 1) !== "/")
$return = "/$return";
return $return;
}
static function url2fullPath($url) {
$url = self::normalize($url);
$uri = isset($_SERVER['SCRIPT_NAME'])
? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['PHP_SELF'])
? $_SERVER['PHP_SELF']
: false);
$uri = self::normalize($uri);
if (substr($url, 0, 1) !== "/") {
if ($uri === false) return false;
$url = dirname($uri) . "/$url";
}
if (isset($_SERVER['DOCUMENT_ROOT'])) {
return self::normalize($_SERVER['DOCUMENT_ROOT'] . "/$url");
} else {
if ($uri === false) return false;
if (isset($_SERVER['SCRIPT_FILENAME'])) {
$scr_filename = self::normalize($_SERVER['SCRIPT_FILENAME']);
return self::normalize(substr($scr_filename, 0, -strlen($uri)) . "/$url");
}
$count = count(explode('/', $uri)) - 1;
for ($i = 0, $chdir = ""; $i < $count; $i++)
$chdir .= "../";
$chdir = self::normalize($chdir);
$dir = getcwd();
if (($dir === false) || !@chdir($chdir))
return false;
$rdir = getcwd();
chdir($dir);
return ($rdir !== false) ? self::normalize($rdir . "/$url") : false;
}
}
static function normalize($path) {
// Backslash to slash convert
if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN") {
$path = preg_replace('/([^\\\])\\\+([^\\\])/s', "$1/$2", $path);
if (substr($path, -1) == "\\") $path = substr($path, 0, -1);
if (substr($path, 0, 1) == "\\") $path = "/" . substr($path, 1);
}
$path = preg_replace('/\/+/s', "/", $path);
$path = "/$path";
if (substr($path, -1) != "/")
$path .= "/";
$expr = '/\/([^\/]{1}|[^\.\/]{2}|[^\/]{3,})\/\.\.\//s';
while (preg_match($expr, $path))
$path = preg_replace($expr, "/", $path);
$path = substr($path, 0, -1);
$path = substr($path, 1);
return $path;
}
static function urlPathEncode($path) {
$path = self::normalize($path);
$encoded = "";
foreach (explode("/", $path) as $dir)
$encoded .= rawurlencode($dir) . "/";
$encoded = substr($encoded, 0, -1);
return $encoded;
}
static function urlPathDecode($path) {
$path = self::normalize($path);
$decoded = "";
foreach (explode("/", $path) as $dir)
$decoded .= rawurldecode($dir) . "/";
$decoded = substr($decoded, 0, -1);
return $decoded;
}
}
?>