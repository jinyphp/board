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

class Validate
{
    private $_filter;
    private $_pass = true;
    private $_errmsg=[];
    public function __construct($filter)
    {
        $this->_filter = $filter;
    }

    public function isPass()
    {
        return $this->_pass;
    }

    public function filter($key, $value)
    {
        if (isset($this->_filter[$key])) {
            foreach ($this->_filter[$key] as $rule => $check) {
                if (method_exists($this,$rule)) {
                    if (!$this->$rule($check, $value) ) {
                        //echo "체크실패";
                        $this->_pass = false;
                    } else {
                        //echo "체크패스";
                    }
                }
            }      
        }
        //
    }

    private function type($check, $value)
    {
        // echo "데이터타입 확인 = ".$check;
        $method = "type".ucfirst($check);
        // echo $method;
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
    }

    private function typeEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    private function typeText($value)
    {
        return true;
    }

    private function typePassword($value)
    {
        return true;
    }

    private function minlen($check, $value)
    {
        //echo "최저글자 확인";
        if (strlen($value) >= $check) {
            return true; 
        } else {
            return false;
        }
    }

    private function require($check, $value)
    {
        if (empty($value)) {
            $this->_errmsg []= "require 필수 요구사항 입니다.";
            return false;
        } else {
            return true;
        }
    }

}