<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\Board;

if (! function_exists('id')) {
    function id()
    {
        if(isset($_POST['_id'])) {
            $id = $_POST['_id'];
        } else {
            $arr = array_reverse(conf("req")['uridata']);
            if (isset($arr[0]) && is_numeric($arr[0])) {
                $id = intval($arr[0]);
            } else {
                $id = null;
            }        
        }        

        return $id;
    }
}

if (! function_exists('_method')) {
    function _method()
    {
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            return "GET";
        } else {
            if (isset($_POST['_method']) && $_POST['_method'] == "DELETE") {
                return "DELETE";
            } else if(isset($_POST['_method']) &&  $_POST['_method'] == "PUT") {
                return "PUT";
            } else {
                return "POST";
            }
        }
    }
}