<?php

namespace Jiny\Board;

class Board
{
    private $db;

    public function __construct($db=null)
    {
        if($db) {
            $this->db = $db;
        } else {
            // echo __DIR__;
            // E:\test\jinydb\demo1\vendor\jiny\board\src
            $dbconf = __DIR__."\..\..\..\..\conf\\"."dbconf.php";
            $this->db = \Jiny\Database\db_init($dbconf);
        }

        $this->db->connect();      
        
    }

    public function db()
    {
        return $this->db;
    }

    // 목록을 출력합니다.
    public function list()
    {

    }


    

}