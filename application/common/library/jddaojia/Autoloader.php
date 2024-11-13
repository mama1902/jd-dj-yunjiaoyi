<?php

class Autoloader{
    public static function autoload($class) {
        $name = $class;
        if(false !== strpos($name,'\\')){
          $name = strstr($class, '\\', true);
        }

        $filename = __DIR__."/jd/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = __DIR__."/jd/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }
    }
}

spl_autoload_register('Autoloader::autoload');
?>
