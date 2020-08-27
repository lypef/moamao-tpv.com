<?php
/**
 * @package php-svg-lib
 * @link    https://github.com/PhenX/php-svg-lib
 * @author  Fabien Ménager <fabien.menager@gmail.com>
 * @license GNU LGPLv3+ https://www.gnu.org/copyleft/lesser.html
 */

spl_autoload_register(function($class) {
  if (0 === strpos($class, "Svg")) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = realpath(__DIR__ . DIRECTORY_SEPARATOR . $file . '.php');
    if (file_exists($file)) {
      include_once $file;
    }
  }
});