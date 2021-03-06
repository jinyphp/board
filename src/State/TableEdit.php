<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\Board\State;

/**
 * 데이터를 수정하는 입력폼을 생성합니다.
 */
class TableEdit extends \Jiny\Board\State\Table
{
    private $db;
    private $parser;
    private $table;
    private $conf;

    public function __construct($conf)
    {
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        if ($conf) $this->conf = $conf;
        $this->table = $conf['table']; // 테이블명 설정"members";
    }

    /**
     * 처리로직
     */
    public function main($id)
    {
        if ($this->isID($id)) {         //id 유효성
            $row = $this->read($id);    // db데이터 읽기
            $this->builder($row);       // form 입력기 생성
            return $this->resource(['data'=>$row]);   // 리소스 결합하여 화면 반환
        }
    }

    /**
     * GET 방식으로 edit 요청시 처리
     * ~~/edit/id
     */
    public function GET($id)
    {
        if ($this->isID($id)) {         //id 유효성
            $row = $this->read($id);    // db데이터 읽기
            $this->builder($row);       // form 입력기 생성
            return $this->resource(['data'=>$row]);   // 리소스 결합하여 화면 반환
        }
    }

    public function POST($id)
    {
        if ($this->isID($id) && $this->isCSRF()) {
            $row = $this->read($id);
            $this->builder($row);
            return $this->resource(['data'=>$row]); 
        }
    }

    public function api($id)
    {
        if ($this->isID($id)) {
            $row = $this->read($id);
            // print_r($row);
            $this->builder($row);
            return $this->resource(['data'=>$row]); 
        }
    }

    /**
     * html 코드빌더
     */
    private function builder($row)
    {
        $form = \jiny\html\form();
        $form->setAction($this->conf['uri']);
        // 공용요소
        $form->fields [] = $form->hidden(['name'=>"mode", 'value'=>"editup"]);
        $form->fields [] = $form->hidden(['name'=>"csrf", 'value'=>\jiny\board\csrf()->new() ]);
        $form->fields [] = $form->hidden(['name'=>"id", 'value'=>$row['id'] ]);

        // 폼 요소를 생성
        $form->setFields( $this->conf['edit']['fields'], $row);


    }

    /**
     * 화면처리 리소스
     */
    private function resource($vars=[])
    {
        if(isset($this->conf['edit']['title'])) {
            $vars['title'] = $this->conf['edit']['title'];
        }

        $file = $this->resourcePath();
        $body = \jiny\html_get_contents($file, $vars);
        return $body;
    }

    private function resourcePath()
    {
        if(isset($this->conf['edit']['resource'])) {
            return "..".$this->conf['edit']['resource'];
        }
        return "../vendor/jiny/board/resource/edit.html";
    }

    /**
     * 데이터베이스 read
     */
    private function read($id)
    {
        if ($row = $this->db->select($this->table)->id($id)) {
            return $row;
        }
    
        $msg = $id." 데이터를 읽어 처리할 수 없습니다.";
        return $this->error($msg);
    }

    private function isID($id)
    {
        if (is_numeric($id)) {
            return true;
        }
        
        $msg = $id." 는 숫자로 입력되어야 합니다.";
        return $this->error($msg);
    }

    private function isCSRF()
    {
        if ($bool = \jiny\board\csrf()->is()) {
            return true;
        }
        $msg = "CSRF 불일치"; 
        return $this->error($msg);
    }

    /**
     * 
     */
}