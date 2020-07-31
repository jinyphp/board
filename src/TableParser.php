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

/**
 * 테이블을 출력합니다.
 */
class TableParser
{
    protected $conf;
    protected $http;
    /*
    public function __construct($class)
    {
        // echo __CLASS__;
        // $this->init($class);
    }

    protected function init($class)
    {
        $this->conf = \json_decode(\file_get_contents("..".DIRECTORY_SEPARATOR.$class.".json"), true);
    }
    */



    public function main()
    {
        $this->http = \jiny\http();
        $this->cookieInit();
 
        //echo "회원관리";
        $method = $this->http->Request->method();
        $body = $this->$method();

        // 계시판 자바스크립트 코드 삽입.
        $vars['csrf'] = \jiny\board\csrf()->get(); // 로직에서 생성된값 적용
        $javascript = \jiny\html_get_contents("../resource/board/board.js", $vars);
        $body = str_replace("</body>",\jiny\javascript($javascript)."</body>",$body);
        
        return $body;
    }

    protected function GET()
    {
        $id = $this->http->Endpoint->last();
        if(is_numeric($id)) {
            return $this->view($id);
        } else if($id == "new") {
            // echo "신규입력";
            return $this->new();
        } else {
            // 1. GET요청, 목록출력
            $limit = $this->limit();  
            return $this->list($limit);
        }
    }

    protected function POST()
    {
        if(isset($_POST['mode'])) {
            $method = $_POST['mode'];
            if(\method_exists($this, $method)) {
                return $this->$method();
            }
        } else {
            // 리스트목록 출력
            $this->searchField()->searchValue(); // search 쿠키 설정
            $limit = $this->limit();            
            return $this->list($limit);
        }
    }

    private function cookieInit()
    {
        if (!isset($_COOKIE['current'])) {
            // setcookie("current", $this->conf->uri, time()+60*60, "/");
            \jiny\cookie("current", $this->conf['uri']);
            \jiny\cookie("limit", 0, -3600);
            \jiny\cookie("search", "", -3600);
            \jiny\cookie("type", "", -3600);
        } else {
            if ($_COOKIE['current'] != $this->conf['uri']) {
                // setcookie("current", $this->conf->uri, time()+60*60, "/");
                \jiny\cookie("current", $this->conf['uri']);
                \jiny\cookie("limit", 0, -3600);
                \jiny\cookie("search", "", -3600);
                \jiny\cookie("type", "", -3600);
            }
        }
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
     * 목록을 출력합니다.
     */
    private function factory($name)
    {
        $name = "\Jiny\Board\\".$name;
        return new $name ($this->conf);
    }

    protected function list($limit=0)
    {
        return $this->factory("TableList")->main($limit);
    }

    protected function view($id)
    {
        return $this->factory("TableView")->main($id);
    }

    protected function edit()
    {
        $id = intval($_POST['id']);
        return $this->factory("TableEdit")->main($id);
    }

    protected function editup()
    {
        return $this->factory("TableUpdate")->main();
    }

    protected function new()
    {
        return $this->factory("TableNew")->main();
    }

    protected function newup()
    {
        return $this->factory("TableInsert")->main();        
    }

    protected function delete()
    {
        return $this->factory("TableDelete")->main(); 
    }
}