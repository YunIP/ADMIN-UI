<?php
namespace kcfinder;
class type_img {
public function checkFile($file, array $config) {
$driver = isset($config['imageDriversPriority'])
? image::getDriver(explode(" ", $config['imageDriversPriority'])) : "gd";
$img = image::factory($driver, $file);
if ($img->initError)
return "Unknown image format/encoding.";
return true;
}
}
?>