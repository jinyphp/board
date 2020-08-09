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
abstract class Parser extends State
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
        if (!isset($_COOKIE['current'])) {
            \jiny\cookie("current", $this->conf['uri']);
            \jiny\cookieClears("limit","search","type");
        } else {
            if ($_COOKIE['current'] != $this->conf['uri']) {
                \jiny\cookie("current", $this->conf['uri']);
                \jiny\cookieClears("limit","search","type");
            }
        }
        return $this;
    }

    protected function limit($limit=null)
    {
        if(isset($_POST['limit'])) {
            $limit = $_POST['limit'];
            \jiny\cookie("limit", $limit);
        } else 
        // 쿠키값 우선적용
        if( isset($_COOKIE['limit']) ) {
            $limit = $_COOKIE['limit'];
        } 
        // 기본값 0
        else {
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

        /*
    protected $request_method;
    public function main($params=[])
    {
        $this->http = \jiny\http();
        $this->cookieInit();
 
        $method = $this->http->Request->method();
        $this->request_method = $method;

        $body = "<div id='main'>".$this->$method($params)."</div>";
        $body .= $this->javascript();        
        
        return $body;
    }
*/

    /*
    public function POST($params=[])
    {
        if(isset($_POST['mode'])) {
            $method = "state".strtoupper($_POST['mode']);
            if(\method_exists($this, $method)) {
                return $this->$method();
            }
        }

        // 검색 목록 리스트 출력
        return $this->search();
    }
    */

        /*
    public function GET($params=[])
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

    */
    /**
     * 
     */
}