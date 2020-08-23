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
    public $_msg=[];
    public function __construct($filter)
    {
        $this->_filter = $filter;
    }

    public $_rules = [];
    public function rules()
    {
        foreach($this->_filter as $id => $field) {
            foreach ($field as $act => $a) {
                // echo "-".$act."\n";
                if($act[0] == "_") { // 유효성 검사필드
                    $name = $a['name'];
                    $this->_rules[$name]= $a;
                }
            }
        }
        return $this;
    }

    private function _require($value, $rule)
    {
        if(\array_key_exists("required", $rule)) {
            if(empty($value)) {
                $this->_msg []= $rule['title']." 필수 입력 항목입니다.\n";
                $this->_pass = false;
                return false;
            }
        }
        return true;
    }

    public function email($value, $rule)
    {
        //echo "이메일 유효성, ";
        // 필수 입력항목
        if(!$this->_require($value, $rule)) return false;
        
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->_msg []= $rule['title']." 유효한 이메일 주소가 아닙니다.\n";
            $this->_pass = false;
            return false;
        }
    
        return true;
    }

    private function _minlength($value, $rule)
    {
        if(\array_key_exists("data-minlength", $rule)) {
            if (strlen($value) < $rule['data-minlength']) {
                $this->_msg []= $rule['title']." 최소 ".$rule['data-minlength']." 이상 입력해야 합니다.\n";
                $this->_pass = false;
                return false;
            }
        }
        return true;
    }

    public function password($value, $rule)
    {
        //echo "password 유효성 ";
        // 필수 입력항목
        if(!$this->_require($value, $rule)) return false;

        // 최소길이
        if(!$this->_minlength($value, $rule)) return false;
        
        return true;
    }

    public function _maxlength($value, $rule)
    {
        if(\array_key_exists("maxlength", $rule)) {
            if (strlen($value) > $rule['maxlength']) {
                $this->_msg []= $rule['title']." 최대 ".$rule['maxlength']." 이상 입력할 수 없습니다.\n";
                $this->_pass = false;
                return false;
            }
        }
        return true;
    }

    public function text($value, $rule)
    {
        //echo "text 유효성 ";
        // 필수 입력항목
        if(!$this->_require($value, $rule)) return false;

        // 최소길이
        if(!$this->_minlength($value, $rule)) return false;

        // 최대길이
        if(!$this->_maxlength($value, $rule)) return false;
        
        return true;
    }

    /**
     * textarea 유효성 검사
     */
    public function textarea($value, $rule)
    {
        // 필수 입력항목
        if(!$this->_require($value, $rule)) return false;

        return true;
    }

    /**
     * select 
     */
    public function select($value, $rule)
    {

        return true;
    }

    //////

    public function isPass()
    {
        return $this->_pass;
    }

    public function filter($post)
    {
        foreach($post as $key => $value) {
            if(isset($this->_rules[$key])) {
                $check = $this->_rules[$key]['type'];
                if($this->$check($value, $this->_rules[$key])) {
                    // 성공
                } else {
                    return false;
                }
            }
        }
        return $this;
    }

}