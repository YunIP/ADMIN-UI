<?php
namespace kcfinder;
class text {
static function clearWhitespaces($string) {
return trim(preg_replace('/\s+/s', " ", $string));
}
static function htmlValue($string) {
return
str_replace('"', "&quot;",
str_replace("'", '&#39;',
str_replace('<', '&lt;',
str_replace('&', "&amp;",
$string))));
}
static function jsValue($string) {
return
preg_replace('/\r?\n/', "\\n",
str_replace('"', "\\\"",
str_replace("'", "\\'",
str_replace("\\", "\\\\",
$string))));
}
}
?>