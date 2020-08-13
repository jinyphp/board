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
 * 테이블 동작 상태도
 */
class State
{
    protected $conf;
    protected $http;

    public function setConf($conf)
    {
        $this->conf = $conf;
    }

    protected function javascript()
    {
        // 계시판 자바스크립트 코드 삽입.
        $vars['csrf'] = \jiny\board\csrf()->get(); // 로직에서 생성된값 적용
        $javascript = \jiny\html_get_contents("../resource/board/board.js", $vars);
        // $body = str_replace("</body>",\jiny\javascript($javascript)."</body>",$body);
        return  \jiny\javascript($javascript);
    }

    protected function search()
    {
        // 리스트목록 출력
        $this->searchField()->searchValue(); // search 쿠키 설정
        $limit = $this->limit();
        
        // 일반 리스트 출력
        $method = "stateLIST";
        return $this->$method($limit);
    }

    protected function cookieInit()
    {
        // 페이지 경로 설정
        if (!isset($_COOKIE['current'])) {
            //echo "경로설정";
            \jiny\cookie("current", $this->conf['uri']);
            \jiny\cookieClears("limit","search","type");
        } else 
        // 페이지 이동시, 쿠기 초기화
        {
            if ($_COOKIE['current'] != $this->conf['uri']) {
                //echo "경로 초기화";
                //echo $_COOKIE['current']."!=".$this->conf['uri'] ;
                \jiny\cookie("current", $this->conf['uri']);
                \jiny\cookieClears("limit","search","type");
            }
        }
        
        return $this;
    }

    /**
     * 페이지네이션 limit 기억
     */
    protected function limit($limit=null)
    {
        // echo "limit=".$limit;

        // post 요청시
        if(isset($_POST['limit'])) {
            //echo "동작1";
            $limit = $_POST['limit'];
            \jiny\cookie("limit", $limit);
        } else
        // 직접 지정한 값이 있는 경우 
        if( $limit >= 0  && $limit !== null) {
            //echo "동작2";
            \jiny\cookie("limit", $limit);
        } else
        // 쿠키값이 존재하는 경우
        if( isset($_COOKIE['limit']) ) {
            //echo "동작3";
            $limit = $_COOKIE['limit'];
        } else 
        // 아무값도 없는 경우 기본값 0
        {
            //echo "동작4";
            $limit = 0;
        }
        //echo "limit===".$limit;
        return $limit;
    }

    protected function searchField()
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

    protected function searchValue()
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
     * 상태객체를 생성하고 호출 합니다.
     */
    public function factory($name)
    {
        $name = "\Jiny\Board\\State\\".$name;
        return new $name ($this->conf);
    }

    public function list($method, $limit=null)
    {
        $obj = $this->factory("TableList");
        if(\method_exists($obj, $method)) {
            return $obj->$method($limit);
        }

        // return $this->factory("TableList")->main($limit);
    }

    /*
    protected function stateLIST($limit=null)
    {
        return $this->factory("TableList")->main($limit);
    }
    */

    public function view($method, $id)
    {
        // $id = intval($id);
        $obj = $this->factory("TableView");
        if(\method_exists($obj, $method)) {
            return $obj->$method($id);
        }
    }

    /*
    protected function stateView($id)
    {
        return $this->factory("TableView")->main($id);
    }
    */

    public function edit($method, $id)
    {
        // if(!$id) $id = intval($_POST['id']);
        if(!$id) $id = intval($_POST['id']);
        $obj = $this->factory("TableEdit");
        if(\method_exists($obj, $method)) {
            return $obj->$method($id);
        }
    }

/*
    protected function stateEDIT($id=null)
    {
        if(!$id) $id = intval($_POST['id']);
        if ($this->request_method == "GET") {
            return $this->factory("TableEdit")->GET($id);
        } else if ($this->request_method == "POST") {
            return $this->factory("TableEdit")->main($id);
        }        
    }
    */

    public function editup($method, $id=null)
    {
        $obj = $this->factory("TableUpdate");
        if(\method_exists($obj, $method)) {
            return $obj->$method($id);
        }
        //return $this->factory("TableUpdate")->api();
    }

    /*
    protected function stateEDITUP()
    {
        return $this->factory("TableUpdate")->main();
    }
    */

    public function new($method, $id=null)
    {
        $obj = $this->factory("TableNew");
        if(\method_exists($obj, $method)) {
            return $obj->$method($id);
        }
    }

    /*
    public function stateNEW()
    {
        return $this->factory("TableNew")->main();
    }
    */

    public function newup($method, $id=null)
    {
        $obj = $this->factory("TableInsert");
        if(\method_exists($obj, $method)) {
            return $obj->$method($id);
        }
    }

    /*
    protected function stateNEWUP()
    {
        return $this->factory("TableInsert")->main();        
    }
    */

    public function delete($method, $id)
    {
        echo "삭제";
        exit;

        $obj = $this->factory("TableDelete");
        if(\method_exists($obj, $method)) {
            return $obj->$method($id);
        }
    }

    /*
    protected function stateDELETE()
    {
        return $this->factory("TableDelete")->main(); 
    }
    */

}