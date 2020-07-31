<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace jiny\board;

// csrf 해쉬키 생성
if (!function_exists("redirect")) {
    function redirect($url)
    {
        
        header("Pragma: no-cache");  
        header("Cache-Control: no-cache,must-revalidate"); 
        

        // post redirect get pattern
        header("HTTP/1.1 301 Moved Permanently");
        header("location:".$url);

        /*
        <script>
alert('전송을 완료 했습니다.');
history.pushState(null, null, location.href);
window.onpopstate = function(event) {
    history.go(1);
};
</script>
        */

    }
}

// CSRF 객체생성
if (!function_exists("csrf")) {
    function csrf()
    {
        return \Jiny\Board\CSRF::instance();
    }
}




/*
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
*/

////////////

function pagenation($total = 0)
{
    $obj = \Jiny\Board\Pagenation::instance();
    if ($total) $obj->setTotal($total);
    return $obj;
}

namespace jiny\board\pagenation;

function build($limit = 0)
{
    $obj = \Jiny\Board\Pagenation::instance();
    return $obj->build($limit);
}