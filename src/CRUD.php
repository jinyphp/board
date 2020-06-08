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
 * 계시판 CRUD 작업을 위한 데이터베이스 brige 입니다.
 */
class CRUD
{
    private $_db;
    private $_table;
    public function __construct($db=null)
    {
        if ($db) {
            $this->_db = $db;
        } else {
            // 데이터베이스
            $dbinfo = \jiny\dbinfo();
            $this->_db = \jiny\factory("\Jiny\Mysql\Connection", $dbinfo);
        }
        
    }

    // 테이블을 설정합니다.
    public function setTable($table)
    {
        $this->_table = $table;
        return $this; // 메서드체인
    }

    /**
     * 목록을 읽어 옵니다.
     * $fields = 첫번째 인자는 읽어올 필드와 순서를 지정합니다.
     */
    public function list($fields)
    {
        $obj = $this->_db->select($this->_table, $fields);
        if ($this->_pagenation) {
            $obj->limit($this->_page_num, $this->_page_start);
        }
        return $obj->runObjAll();
    }

    public function total()
    {
        return $this->_db->select($this->_table)->count();
    }

    private $_pagenation;
    private $_page_start = 0;
    private $_page_num = 5;
    public function paging($num, $start)
    {
        $this->_pagenation = true; // 페이지네이션 활성화
        $this->_page_start = $start;
        $this->_page_num = $num;
    }

    public function read($id, $fields=null)
    {
        // $fields = ["id","title"];
        $select = $this->_db->select($this->_table)->setFields($fields)->setWheres(["id"]);
        $select->build($fields);
        return $select->run(['id'=>$id])->fetchObj();
    }

    public function update($id, $rows)
    {
        $update = $this->_db->update($this->_table)->setFields($rows)->id($id);
    }

    public function create($rows)
    {
        return $this->_db->insert($this->_table, $rows)->save();
    }

    public function delete($id)
    {
        $this->_db->delete($this->_table)->id($id);
    }
    /**
     * 
     */
}