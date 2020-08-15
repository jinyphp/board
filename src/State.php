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
    // protected $scriptfile = "../resource/board/board.js";
    protected $scriptfile = "../vendor/jiny/board/src/board.js";

    /**
     * 계시판 
     * 자바스크립트 코드 삽입.
     */
    protected function javascript()
    {
        // 로직에서 생성된값 적용
        $vars['csrf'] = \jiny\board\csrf()->get();

        // 스크립트 파일 읽기
        $javascript = \jiny\html_get_contents($this->scriptfile, $vars);
        return  \jiny\javascript($javascript);
    }

    /**
     * 검색처리
     */
    protected function search()
    {
        // 리스트목록 출력
        $this->searchField()->searchValue(); // search 쿠키 설정
        $limit = $this->limit();
        
        // 일반 리스트 출력
        return $this->list();
        /*
        $method = "_list";
        $stateObj = $this->$method();

        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            return $stateObj->GET();
        } else {
            return $stateObj->main();
        }
        */

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
        // post 요청시
        if(isset($_POST['limit'])) {
            $limit = $_POST['limit'];
            \jiny\cookie("limit", $limit);
        } else
        // 직접 지정한 값이 있는 경우 
        if( $limit >= 0  && $limit !== null) {
            \jiny\cookie("limit", $limit);
        } else
        // 쿠키값이 존재하는 경우
        if( isset($_COOKIE['limit']) ) {
            $limit = $_COOKIE['limit'];
        } else 
        // 아무값도 없는 경우 기본값 0
        {
            $limit = 0;
        }

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

    public function list($limit=null)
    {
        $stateObj = $this->factory("TableList");
        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            $method = \jiny\http\request()->method();
            if(\method_exists($stateObj, $method)) {
                $limit = $this->limit($_SERVER['HTTP_LIMIT']);
                return $stateObj->$method($limit);
            }
        }
        
        $limit = $this->limit();
        return $stateObj->main($limit);
    }

    public function view($id=null)
    {
        $stateObj = $this->factory("TableView");
        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            $method = \jiny\http\request()->method();
            if(\method_exists($stateObj, $method)) {
                $id = $_SERVER['HTTP_ID'];
                return $stateObj->$method($id);
            }
        }

        // $limit = $this->limit();
        return $stateObj->main($id); 
    }


    public function edit($id=null)
    {
        $stateObj = $this->factory("TableEdit");
        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            $method = \jiny\http\request()->method();
            if(\method_exists($stateObj, $method)) {
                $id = $_SERVER['HTTP_ID'];
                return $stateObj->$method($id);
            }
        }
        
        if(!$id) $id = intval($_POST['id']);
        return $stateObj->main($id);
    }

    public function editup($id=null)
    {
        $stateObj = $this->factory("TableUpdate");
        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            $method = \jiny\http\request()->method();
            if(\method_exists($stateObj, $method)) {
                return $stateObj->$method($id);
            }
        }

        return $stateObj->main($id);
    }


    public function new($id=null)
    {
        $stateObj = $this->factory("TableNew");
        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            $method = \jiny\http\request()->method();
            if(\method_exists($stateObj, $method)) {
                return $stateObj->$method($id);
            }
        }
        
        return $stateObj->main($id);
    }

    public function newup($id=null)
    {
        $stateObj = $this->factory("TableInsert");
        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            $method = \jiny\http\request()->method();
            if(\method_exists($stateObj, $method)) {
                return $stateObj->$method($id);
            }
        }

        return $stateObj->main($id);
    }

    public function remove($id)
    {
        $stateObj = $this->factory("TableDelete");
        $contenttype = $this->http->Request->contentType();
        if($contenttype == "application/json") {
            $method = \jiny\http\request()->method();
            if(\method_exists($stateObj, $method)) {
                return $stateObj->$method($id);
            }
        }
        
        return $stateObj->main($id);     
    }

    

}