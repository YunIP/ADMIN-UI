<?php
namespace kcfinder;
abstract class image {
const DEFAULT_JPEG_QUALITY = 75;
/** Image resource or object
* @var mixed */
protected $image;
/** Image width in pixels
* @var integer */
protected $width;
/** Image height in pixels
* @var integer */
protected $height;
/** Init error
* @var bool */
protected $initError = false;
/** Driver specific options
* @var array */
protected $options = array();
/** Magic method which allows read-only access to all protected or private
* class properties
* @param string $property
* @return mixed */
final public function __get($property) {
return property_exists($this, $property) ? $this->$property : null;
}
public function __construct($image, array $options=array()) {
$this->image = $this->width = $this->height = null;
$imageDetails = $this->buildImage($image);
if ($imageDetails !== false)
list($this->image, $this->width, $this->height) = $imageDetails;
else
$this->initError = true;
$this->options = $options;
}
final static function factory($driver, $image, array $options=array()) {
$class = __NAMESPACE__ . "\\image_$driver";
return new $class($image, $options);
}
final static function getDriver(array $drivers=array('gd')) {
foreach ($drivers as $driver) {
if (!preg_match('/^[a-z0-9\_]+$/i', $driver))
continue;
$class = __NAMESPACE__ . "\\image_$driver";
if (class_exists($class) && method_exists($class, "available")) {
eval("\$avail = $class::available();");
if ($avail) return $driver;
}
}
return false;
}
final protected function buildImage($image) {
$class = get_class($this);
if ($image instanceof $class) {
$width = $image->width;
$height = $image->height;
$img = $image->image;
} elseif (is_array($image)) {
list($key, $width) = each($image);
list($key, $height) = each($image);
$img = $this->getBlankImage($width, $height);
} else
$img = $this->getImage($image, $width, $height);
return ($img !== false)
? array($img, $width, $height)
: false;
}
final public function getPropWidth($resizedHeight) {
$width = round(($this->width * $resizedHeight) / $this->height);
if (!$width) $width = 1;
return $width;
}
final public function getPropHeight($resizedWidth) {
$height = round(($this->height * $resizedWidth) / $this->width);
if (!$height) $height = 1;
return $height;
}
static function available() { return false; }
static function checkImage($file) { return false; }
abstract public function resize($width, $height);
abstract public function resizeFit($width, $height, $background=false);
abstract public function resizeCrop($width, $height, $offset=false);
abstract public function rotate($angle, $background="#000000");
abstract public function flipHorizontal();
abstract public function flipVertical();
abstract public function watermark($file, $left=false, $top=false);
abstract public function output($type='jpeg', array $options=array());
abstract protected function getBlankImage($width, $height);
abstract protected function getImage($image, &$width, &$height);
}
?>