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
 * 테이블의 목록을 출력합니다.
 */
class TableList
{
    private $db;
    private $parser;
    private $table;
    private $conf;

    private $pagenation;
    public function __construct($conf=null)
    {
        // echo __CLASS__;
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        if ($conf) $this->conf = $conf;

        $this->table = $conf['table']; // 테이블명 설정"members";
        $this->pagenation = \jiny\board\pagenation();
    }

    /**
     * 처리로직
     */
    private $total;
    public function main($limit=0)
    {
        // 검색
        $rows = $this->select($limit);
        $this->builder($rows);

        $vars = [
            'total'=>$this->total
        ];
        return $this->resource($vars);

            
        // $msg = "데이터 목록을 읽어 올 수 없습니다.";
        // $error = new \Jiny\Members\Error($msg);
        // return $error->main();
    }

    /**
     * html 코드빌더
     */
    private function builder($rows)
    {
        $html = \jiny\html\table($rows);

        $fields = $this->conf['list']['fields'];
        
        $html->displayfield($fields)->theadTitle($fields);

        // 연결링크
        foreach ($this->conf['list']['href'] as $key => $value) {
            $html->setHref($key, $value);
        }
    }

    /**
     * 화면처리 리소스
     */
    private function resource($vars=[])
    {
        $vars['csrf'] = \jiny\board\csrf()->new(); //\jiny\html\csrf($this->csrf);
        $vars['mode'] = "list";

        // $vars['field'] = $_COOKIE['field'];
        $vars['field'] = "email";
        $vars['search'] = $this->searchValue();

        if(isset($this->conf['list']['title'])) {
            $vars['title'] = $this->conf['list']['title'];
        }
        

        $file = "..".$this->conf['list']['resource'];
        $body = \jiny\html_get_contents($file, $vars);

        return $body;
    }

    /**
     * 데이터베이스 select
     */
    private function select($start)
    {
        // 데이터베이스 select 객체 생성
        $db = $this->db->select($this->table)->autoCreate()->autoField();
        // exit;

        // 검색 조건
        $field = $this->searchField();
        $search = $this->searchValue();
        if( $field && $search) {
            $where = $field." like '%".$search."%' ";
            $db->where($where);
        }

        $this->total = $db->count();
        $this->pagenation->setTotal($this->total);
        $this->pagenation->setLimit($start);

        // 페이지네이션
        $display = $this->pagenation->num;
        $db->limit($display, $start); // pagenation limit 설정: 출력갯수, 시작위치
        $rows = $db->runAssocAll();
        return $rows;
    }

    private function searchField()
    {
        if(isset($_POST['field']) && $_POST['field']) {
            
            // echo "post-field";
            return $_POST['field'];
        } else if(isset($_COOKIE['field']) && $_COOKIE['field']) {
            //echo "cookie-field=".$_COOKIE['field'];;
            return $_COOKIE['field'];
        }
        return "";
    }

    private function searchValue()
    {
        if(isset($_POST['search'])) {
            //  && $_POST['search']
            return $_POST['search'];
        } else if(isset($_COOKIE['search']) && $_COOKIE['search']) {
            return $_COOKIE['search'];
        }
        return "";
    }
}