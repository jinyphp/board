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

class CSRF
{
    /**
     * 싱글턴
     */
    public static $_instance;
    public static function instance($args=null)
    {
        if (!isset(self::$_instance)) {        
            //echo "객체생성\n";
            // print_r($args);   
            self::$_instance = new self($args); // 인스턴스 생성
            if (method_exists(self::$_instance,"init")) {
                self::$_instance->init();
            }
            return self::$_instance;
        } else {
            //echo "객체공유\n";
            return self::$_instance; // 인스턴스가 중복
        }
    }

    public $salt = "jiny_hello";
    private $algo = "sha1";
    private $_csrf;
    
    // csrf 해쉬키 생성
    public function get()
    {
        return $this->_csrf;
    }

    // 신규생성
    public function new()
    {
        $this->_csrf = \hash($this->algo, $this->salt.date("Y-m-d H:i:s"));
        $_SESSION['_csrf'] = $this->_csrf;
        return $this->_csrf;
    }


    // csrf 해쉬키 검사
    public function is()
    {
        if(isset($_SESSION)) { // session_start 여부 확인
            if(isset($_POST['csrf']) && $_SESSION['_csrf'] == $_POST['csrf']) {
                return true;
            }
        }   
    }

    // 초기화
    public function clear()
    {
        $_SESSION['_csrf'] = null;
        return false;  
    }

}