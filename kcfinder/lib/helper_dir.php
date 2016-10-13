<?php
namespace kcfinder;
class dir {
static function isWritable($dir) {
$dir = path::normalize($dir);
if (!is_dir($dir))
return false;
$i = 0;
do {
$file = "$dir/is_writable_" . md5($i++);
} while (file_exists($file));
if (!@touch($file))
return false;
unlink($file);
return true;
}
static function prune($dir, $firstFailExit=true, array $failed=null) {
if ($failed === null) $failed = array();
$files = self::content($dir);
if ($files === false) {
if ($firstFailExit)
return $dir;
$failed[] = $dir;
return $failed;
}
foreach ($files as $file) {
if (is_dir($file)) {
$failed_in = self::prune($file, $firstFailExit, $failed);
if ($failed_in !== true) {
if ($firstFailExit)
return $failed_in;
if (is_array($failed_in))
$failed = array_merge($failed, $failed_in);
else
$failed[] = $failed_in;
}
} elseif (!@unlink($file)) {
if ($firstFailExit)
return $file;
$failed[] = $file;
}
}
if (!@rmdir($dir)) {
if ($firstFailExit)
return $dir;
$failed[] = $dir;
}
return count($failed) ? $failed : true;
}
static function content($dir, array $options=null) {
$defaultOptions = array(
'types' => "all",   // Allowed: "all" or possible return values
// of filetype(), or an array with them
'addPath' => true,  // Whether to add directory path to filenames
'pattern' => '/./', // Regular expression pattern for filename
'followLinks' => true
);
if (!is_dir($dir) || !is_readable($dir))
return false;
if (strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
$dir = str_replace("\\", "/", $dir);
$dir = rtrim($dir, "/");
$dh = @opendir($dir);
if ($dh === false)
return false;
if ($options === null)
$options = $defaultOptions;
foreach ($defaultOptions as $key => $val)
if (!isset($options[$key]))
$options[$key] = $val;
$files = array();
while (($file = @readdir($dh)) !== false) {
if (($file == '.') || ($file == '..') ||
!preg_match($options['pattern'], $file)
)
continue;
$fullpath = "$dir/$file";
$type = filetype($fullpath);
// If file is a symlink, get the true type of its destination
if ($options['followLinks'] && ($type == "link"))
$type = filetype(realpath($fullpath));
if (($options['types'] === "all") || ($type === $options['types']) ||
(is_array($options['types']) && in_array($type, $options['types']))
)
$files[] = $options['addPath'] ? $fullpath : $file;
}
closedir($dh);
usort($files, array(__NAMESPACE__ . "\\dir", "fileSort"));
return $files;
}
static function fileSort($a, $b) {
if (function_exists("mb_strtolower")) {
$a = mb_strtolower($a);
$b = mb_strtolower($b);
} else {
$a = strtolower($a);
$b = strtolower($b);
}
if ($a == $b) return 0;
return ($a < $b) ? -1 : 1;
}
}
?>