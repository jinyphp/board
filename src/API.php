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

class API extends State
{
    protected $conf;
    protected $http;

    public function setConf($conf)
    {
        $this->conf = $conf;
    }

    public function main($param=[])
    {
        $this->http = \jiny\http();
        $this->cookieInit();
 
        //echo "회원관리";
        $method = $this->http->Request->method();
        $body = $this->$method();
        
        return $body;
    }

    public function GET()
    {
        $id = $this->http->Endpoint->last();
        if(is_numeric($id)) {
            $method = "stateVIEW";
            return $this->$method($id);
        } else if($id == "new") {
            // echo "신규입력";
            $method = "stateNEW";
            return $this->$method();
        } else {
            // 1. GET요청, 목록출력
            $limit = $this->limit();
            $method = "stateLIST";
            return $this->$method($limit);
        }
    }

    public function POST()
    {
        if(isset($_POST['mode'])) {
            $method = "state".strtoupper($_POST['mode']);
            if(\method_exists($this, $method)) {
                return $this->$method();
            }
        } else {
            // 리스트목록 출력
            $this->searchField()->searchValue(); // search 쿠키 설정
            $limit = $this->limit();
            $method = "stateLIST";          
            return $this->$method($limit);
        }
    }


    private function cookieInit()
    {
        if (!isset($_COOKIE['current'])) {
            // setcookie("current", $this->conf->uri, time()+60*60, "/");
            \jiny\cookie("current", $this->conf['uri']);
            // \jiny\cookie("limit", 0, -3600);
            // \jiny\cookie("search", "", -3600);
            // \jiny\cookie("type", "", -3600);
            \jiny\cookieClears("limit","search","type");
        } else {
            if ($_COOKIE['current'] != $this->conf['uri']) {
                // setcookie("current", $this->conf->uri, time()+60*60, "/");
                \jiny\cookie("current", $this->conf['uri']);
                //\jiny\cookie("limit", 0, -3600);
                //\jiny\cookie("search", "", -3600);
                //\jiny\cookie("type", "", -3600);
                \jiny\cookieClears("limit","search","type");
            }
        }
    }

    public function PUT()
    {

    }

    public function DELETE()
    {

    }
    
    private function limit()
    {
        if(isset($_POST['limit'])) {
            $limit = $_POST['limit'];
            \jiny\cookie("limit", $limit);
        } else if( isset($_COOKIE['limit']) ) {
            $limit = $_COOKIE['limit'];
        } else {
            $limit = 0;
        }
        return $limit;
    }

    private function searchField()
    {
        if(isset($_POST['field'])) {
            $field = $_POST['field'];
            \jiny\cookie("field", $field);
        } else if( isset($_COOKIE['field']) ) {
            $field = $_COOKIE['field'];
        } else {
            $field = "";
        }
        return $this;
    }

    private function searchValue()
    {
        if(isset($_POST['search'])) {
            if($_POST['search'] == "") {
                \jiny\cookie("search", "", -3000); // 공백검색, 초기화
            } else {
                $search = $_POST['search'];
                \jiny\cookie("search", $search);
            }
        } else if( isset($_COOKIE['search']) ) {
            $search = $_COOKIE['search'];
        } else {
            $search = "";
        }
        return $this;
    }
    /**
     * 
     */
}