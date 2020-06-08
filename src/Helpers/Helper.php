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

// csrf 해쉬키 생성
if (!function_exists("redirect")) {
    function redirect($url)
    {
        // post redirect get pattern
        header("HTTP/1.1 301 Moved Permanently");
        header("location:".$url);
    }
}

// csrf 해쉬키 생성
if (!function_exists("csrf")) {
    function csrf($salt, $algo="sha1")
    {
        $csrf = \hash($algo,$salt.date("Y-m-d H:i:s"));
        $_SESSION['_csrf'] = $csrf;
        return $csrf;
    }
}

// csrf 해쉬키 생성
if (!function_exists("isCsrf")) {
    function isCsrf()
    {
        if($_SESSION['_csrf'] == $_POST['csrf']) {
            $_SESSION['_csrf'] = null;
            return true;
        } else {
            $_SESSION['_csrf'] = null;
            return false;
        }
    }
}

if (!function_exists("postData")) {
    function postData()
    {
        return $_POST['data'];
    }
}

if (!function_exists("id")) {
    function id()
    {
        return $_POST['data']['id'];
    }
}


