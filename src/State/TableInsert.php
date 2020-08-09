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
class TableInsert
{
    private $db;
    private $parser;
    private $table;
    private $conf;

    public function __construct($conf)
    {
        // echo __CLASS__;
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        if ($conf) $this->conf = $conf;
        $this->table = $conf['table']; // 테이블명 설정"members";
    }


    public function main()
    {
        if ($this->validate()) {
            //echo "유효성 패스";
            //echo $this->table;
            //print_r($_POST['data']);
            $insert = $this->db->insert($this->table, $_POST['data']);
            echo $insert->build()->getQuery();
            $insert->save();

            \jiny\board\redirect($this->conf['uri']);
        
        } 
        
        $msg = "유효성 실패";
        $this->error($msg);
    }

    // 유효성 검사
    private function validate()
    {
        $validate = new \Jiny\Board\Validate($this->conf['new']['validate']);
        foreach($_POST['data'] as $key => $value) {
            $validate->filter($key, $value);
        }

        return $validate->isPass();
    }

    private function error($msg)
    {
        $error = new \Jiny\App\Error($msg);
        return $error->main();
    }

}