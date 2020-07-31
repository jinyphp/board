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
            $this->db->insert($this->table, $_POST['data'])->save();
        }

        //echo "재입력 필요";
        \jiny\board\redirect($this->conf['uri']);
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
        $error = new \App\Controllers\Members\Error($msg);
        return $error->main();
    }

}