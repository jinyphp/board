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
class TableUpdate
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

    
    /**
     * 
     */
    public function main()
    {
        // 수정
        if (\jiny\board\csrf()->is()) {
            \jiny\board\csrf()->clear();

            // id 선택값
            $id = $this->id();
            $data = $this->formData();
            $update = $this->db->update($this->table,$data)->id($id);

            \jiny\board\redirect($this->conf['uri']);
        }

        $msg = "update CSRF 불일치";
        return $this->error($msg);
        
    }

    private function id()
    {
        return isset($_POST['id']) ? intval($_POST['id']) : 0;
    }

    private function formData()
    {
        $data = \jiny\formData();

        // --- 패스워드 처리 ---
        if(isset($data['password']) && empty($data['password'])) {
            // password가 비어있는 경우, update 항목 삭제
            unset($data['password']);
        } else {
            // 패스워드 암호화
            $PassWord = new \Jiny\Members\Password();
            $data['password'] = $PassWord->encryption($data['password']);
        }
        return $data;
    }

    private function error($msg)
    {
        $error = new \App\Controllers\Members\Error($msg);
        return $error->main();
    }

}