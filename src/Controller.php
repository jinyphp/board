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
 * 테이블 상태 파서
 */
class Controller extends State
{
    use Config; // 설정관련 기능들
    use Javascript;
    use Setting;

    protected $http;

    public function __construct($path=null)
    {
        $this->init($path);
    }

    public function init($path=null)
    {
        
        $this->http = \jiny\http();
        $this->cookieInit();
        $this->confLoad($path);
        return $this;
    }

    
    /**
     * 동작 메서드 호출
     */
    public function main($params=[])
    {
        $method = $this->http->Request->method();
        $body = "<div id='jiny-board'>".$this->$method($params)."</div>";

        
        $contenttype = \jiny\http\request()->contentType();
        if($contenttype != "application/json") {
            // trait : 자바스크립트 코드 추가
            $body .= $this->javascript();  
        }
                   
        return $body;
    }

    /**
     * GET 호출동작
     */
    public function GET($params=[], $body=null)
    {
        $contenttype = \jiny\http\request()->contentType();
        if($contenttype == "application/json") {
            //print_r($this->conf);
            //exit;

            if(isset($_SERVER['HTTP_MODE'])) {
                $method = \strtolower($_SERVER['HTTP_MODE']);
                
                return $this->$method();
            } else {
                $msg = "api 동작모드가 설정되어 있지 않습니다.";
                return \json_encode(
                    ['code'=>'400','message'=>$msg], 
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                );
            }
        
        } else {
            // 일반동작, uri 파싱으로 처리
            $id = $this->http->Endpoint->last();

            // 수정 페이지, ~~/edit/id
            if( isset($params[0]) && $params[0] == "edit" &&
                isset($params[1]) && is_numeric($params[1])) {
                return $this->edit($params[1]);
            } else 
            // 상세 페이지, ~~/id
            if(\is_numeric($id)) {
                return $this->view($id);
            } else 
            // 신규입력 페이지, ~~/new
            if($id == "new") {
                return $this->new();
            } else 
            if($id == "setting") {
                return $this->setting();
            } else 
            // 목록 요청 페이지
            {
                $limit = $this->limit();
                return $this->list($limit);
            }
        }
        //       
    }


    /**
     * POST 호출동작
     */
    public function POST($params=[], $body=null)
    {
        if(isset($_POST['mode'])) {
            $method = \strtolower($_POST['mode']);
            return $this->$method($body);
        }
        return $this->search();
    }

    /**
     * application/json
     * PUT 호출동작
     */
    public function PUT($params=[], $body=null)
    {
        //echo "put 요청";
        if(isset($_POST['mode'])) {
            $method = \strtolower($_POST['mode']);
            // print_r($this->conf);
            //exit;

            return $this->$method($param);
        }
    }

    /**
     * application/json
     * DELETE 호출동작
     */
    public function DELETE($params=[], $body=null)
    {
        return $this->remove($body);
    }


}