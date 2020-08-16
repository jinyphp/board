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
class TableList extends \Jiny\Board\State\Table
{
    private $db;
    private $parser;
    private $table;
    private $conf;
    private $pagenation;

    //private $_config;

    public function __construct($conf=null)
    {
        //echo __CLASS__."<br>";
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        //$Config = new \Jiny\Board\Config($conf);
        //$fields = $this->_config->listFields();

        if ($conf) $this->conf = $conf;

        $this->table = $conf['table']; // 테이블명 설정"members";
        $this->pagenation = \jiny\board\pagenation();
    }

    /**
     * 처리로직
     */
    private $total;
    public function main($limit=null)
    {
        $rows = $this->select($limit); // 데이터 조회
        $this->builder($rows);

        $vars = [
            'total'=>$this->total
        ];
        return $this->resource($vars);
    }

    /**
     * application/json
     * API GET 요청
     */
    public function GET($limit=null)
    {
        $rows = $this->select($limit);
        $this->builder($rows);

        $vars = [
            'total'=>$this->total
        ];
        return $this->resource($vars);
    }

    /**
     * html 코드빌더
     */
    private function builder($rows)
    {
        // HTML 테이블 빌더생성
        $html = \jiny\html\table($rows);
        $fields = $this->setTableFields($html);     
        $html->displayfield($fields)->theadTitle($fields);
    }


    // 필드 정보만 가공추출
    private function setTableFields($htmlTable)
    {
        $fields = [];
        foreach ($this->conf['list']['fields'] as $key => $value)
        {
            if(is_string($value)) {
                $fields[$key] = $value;
            } else if(is_array($value)) {
                $fields[$key] = $value['title'];
                if(isset($value['width'])) $htmlTable->field_width[$key] = $value['width'];

                // href 링크설정
                if(isset($value['href'])) {
                    $htmlTable->setHref($key, $value['href']);
                }

                // td attr 설정
                if(isset($value['attr'])) {
                    $htmlTable->field_attr[$key] = $value['attr'];
                }

            }
        }
        return $fields;
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
        $db = $this->db->select($this->table, array_keys($this->conf['list']['fields']))->autoCreate()->autoField();

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
        $db->build()->getQuery(); // 이전 count 쿼리 삭제하고, 재빌드 합니다.
        $rows = $db->runAssocAll();
        return $rows;
    }

    private function searchField()
    {
        if(isset($_POST['field']) && $_POST['field']) {
            return $_POST['field'];
        } else if(isset($_COOKIE['field']) && $_COOKIE['field']) {
            return $_COOKIE['field'];
        }
        return "";
    }

    private function searchValue()
    {
        if(isset($_POST['search'])) {
            return $_POST['search'];
        } else if(isset($_COOKIE['search']) && $_COOKIE['search']) {
            return $_COOKIE['search'];
        }
        return "";
    }
    
}