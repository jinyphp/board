<?php
/**
 * 계시판용 데이터 처리 모듈
 */

namespace Jiny\Board;

class Data
{
    private $db;

    private $name;
    private $bootstrap;

    private $count;
    private $limit = 0;
    private $num = 10;

    private $fields;

    private $rows;

    public function __construct($db=null)
    {
        if($db) {
            $this->db = $db;
        } else {
            $dbconf = __DIR__."\..\..\..\..\conf\\"."dbconf.php";
            $this->db = \Jiny\Database\db_init($dbconf);
        }

        $this->db->connect();
        
        $this->bootstrap = new \Jiny\Html\Bootstrap($this);

        $this->limit = 0;
        $this->num = 10;
        
    }

    public function db()
    {
        return $this->db;
    }

    /**
     * 계시판을 선택합니다.
     */
    public function setBoard($board)
    {
        $this->name = $board;
        return $this;
    }

    public function setLimit($limit=0)
    {
        if(isset($_GET['limit'])) {
            $this->limit = intval($_GET['limit']);
        } else {
            $this->limit = intval($limit);
        }
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setNum($num)
    {
        $this->num = $num;
        return $this;
    }

    /**
     * 전체 계시물의 수를 읽어 옵니다.
     */
    public function count()
    {
        $this->count = $this->db->table($this->name)->count();
        return $this->count;
    }

    /**
     * 출력 필드를 설정합니다.
     */
    public function setField($field)
    {
        $this->fields = $field;
        return $this;
    }


    // 목록을 출력합니다.
    public function load()
    {
        $query = $this->db->table($this->name)->select($this->fields);
        $query->limit($this->limit, $this->num);
        // echo $query;
        // exit;

        $this->rows = $query->get();
        return $this;
    }

    public function setLinks($key, $href)
    {
        for($i=0; $i<count($this->rows); $i++) {
            $this->rows[$i][$key] = "<a href='$href".$this->rows[$i]['id']."'>".$this->rows[$i][$key]."</a>";
        }

        return $this;
    }

    /**
     * 데이터 반환
     */
    public function get()
    {
        return $this->rows;
    }

    /**
     * 페이지네이션 데이터처리
     */
    public function pagenation()
    {
        $pagenation = new \Jiny\Board\Pagenation($this->count);
        return $pagenation->pageArr($this->limit);      
    }

    /**
     * 삽입
     */

    public function insert($data, $matching=false)
    {
        $this->db->table($this->name)->insert($data, $matching);
        return $this;
    }

    public function read($id)
    {
        if ($row = $this->db->table($this->name)->select($this->fields)->where(['id'=>$id])->get() ) {
            return $row[0];
        }
    }

    public function delete($id)
    {
        $this->db->table($this->name)->delete()->id($id)->exec();
        return $this;
    }

    public function update($id, $data)
    {
        $row = $this->db->table($this->name)->select($this->fields)->where(['id'=>$id])->get();
        if ($row) {
            unset($data['_id']);
            unset($data['_method']);

            foreach($data as $key => $value) {
            }

            $query = $this->db->table($this->name)->update($data)->id($id);
            $query->exec();
            return $this;
        }

        return false;
    }


    /**
     * 
     */

}