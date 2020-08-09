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
 * 테이블의 목록을 출력합니다.
 */
class TableView
{
    private $db;
    private $parser;
    private $table;
    private $conf;
    public function __construct($conf=null)
    {
        //echo __CLASS__;
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        if ($conf) $this->conf = $conf;

        $this->table = $conf['table']; // 테이블명 설정"members";
        $this->csrf = "hello";
    }

    /**
     * 처리로직
     */
    public function main($id)
    {
        if(is_numeric($id)) {
            if ($row = $this->read($id)) {
                $this->builder($row);
                return $this->resource(['data'=>$row]);
            }
            $msg = $id." 데이터를 읽어 처리할 수 없습니다.";
        } else {
            $msg = $id." 는 숫자로 입력되어야 합니다.";
        }
        
        $error = new \App\Controllers\Members\Error($msg);
        return $error->main();
    }

    /**
     * html 코드빌더
     */
    private function builder($row)
    {

    }

    /**
     * 화면처리 리소스
     */
    private function resource($vars=[])
    {
        $file = "..".$this->conf['view']['resource'];
        $body = \jiny\html_get_contents($file, $vars);
        return $body;
    }

    /**
     * 데이터베이스 read
     */
    private function read($id)
    {
        $fields = isset($this->conf['view']['fields']) ? $this->conf['view']['fields'] : [];
        return $this->db->select($this->table, array_keys($fields))->id($id);
    }

}