<?php
/**
 * Files includes all necessary files used in all TRIAL projects.
 * 
 * Created on 10/09/2016, 15:46:07
 * @author Matheus Leonardo dos Santos Martins
 * @copyright (c) 2016, TRIAL
 * 
 * @version 1.01
 * @package TRIAL
 */

namespace NoRedo;

include_once 'Utils/Constant.php';
include_once 'Utils/Utils.php';

function load($namespace) {
    $splitpath = explode('\\', $namespace);
    $path = '';
    $name = '';
    $firstword = true;
    for ($i = 0; $i < count($splitpath); $i++) {
        if ($splitpath[$i] && !$firstword) {
            if ($i == count($splitpath) - 1)
                $name = $splitpath[$i];
            else
                $path .= DIRECTORY_SEPARATOR . $splitpath[$i];
        }
        if ($splitpath[$i] && $firstword) {
            if ($splitpath[$i] != __NAMESPACE__)
                break;
            $firstword = false;
        }
    }
    if (!$firstword) {
        $fullpath = __DIR__ . $path . DIRECTORY_SEPARATOR . $name;
        if (file_exists($fullpath . '.php'))
            return include_once($fullpath . '.php');
        else if (file_exists($fullpath . '.class.php'))
            return include_once($fullpath . '.class.php');
        else if (file_exists($fullpath . '.interface.php'))
            return include_once($fullpath . '.interface.php');
    }
    return false;
}

spl_autoload_extensions('.php,.class.php,.interface.php');
spl_autoload_register(__NAMESPACE__ . '\load');
