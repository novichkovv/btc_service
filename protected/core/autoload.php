<?php
/**
 * Created by PhpStorm.
 * User: novichkov
 * Date: 14.09.17
 * Time: 12:48
 */
function autoload($class_name)
{
    $exp_arr = explode('_', $class_name);
    if (count($exp_arr) === 1) {
        $folder = 'core';
    } else {
        $n = array_pop($exp_arr);
        $folder = $n . ($n[strlen($n) - 1] == 's' ? 'es' : 's');
    }
    switch($folder) {
//        case "controllers":
//            $arr = explode('_', $class_name);
//            $sub_folder = array_shift($arr);
//            $class_file = PROTECTED_DIR . $folder . DS . PROJECT . DS . $sub_folder . DS . $class_name . '.php';
//            break;
//        case "helpers":
//        case "templates":
//            $class_file = PROTECTED_DIR . $folder . DS . PROJECT . DS . $class_name . '.php';
//            break;
        default:
            $class_file = PROTECTED_DIR . $folder . DS . $class_name . '.php';
            if(!file_exists($class_file)) {
                $class_file = PROTECTED_DIR . 'objects' . DS . $class_name . '.php';
            }
            if(!file_exists($class_file)) {
                $arr = explode('_', $class_name);
                $sub_folder = array_shift($arr);
                $class_file = PROTECTED_DIR . 'objects' . DS . $sub_folder . DS . $class_name . '.php';
            }
            break;
    }
    if (file_exists($class_file)) {
        require_once($class_file);
    }
}
spl_autoload_register('autoload');
