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
class TableUpdate extends \Jiny\Board\State\Table
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

    public function main()
    {
        return $this->post();
    }

    /**
     * post 처리루틴 "application/x-www-form-urlencoded"
     * 처리후 redirection
     */
    public function post()
    {
        if ($this->put()) {
            // 성공후 페이지 리다이렉션
            \jiny\board\redirect($this->conf['uri']);
        }
        $msg = "update CSRF 불일치";
        return $this->error($msg);
    }

    /**
     * application/json
     * 데이터를 갱신합니다.
     */
    public function put()
    {
        if (\jiny\board\csrf()->is()) {
            \jiny\board\csrf()->clear();

            $id = $this->id(); // id 선택값
            $data = $this->formData();
            $update = $this->db->update($this->table,$data)->id($id);
            
            return true;        
        }
    }

    /**
     * id 유효성 체크
     */
    private function id()
    {
        return isset($_POST['id']) ? intval($_POST['id']) : 0;
    }

    /**
     * 테이터 읽기
     */
    private function formData()
    {
        $data = \jiny\formData();

        // --- 패스워드 처리 ---
        if(isset($data['password']) && empty($data['password'])) {
            // password가 비어있는 경우, update 항목 삭제
            unset($data['password']);
        } else {
            // 패스워드 암호화
            $Encryption = new \Jiny\Members\Encryption();
            $data['password'] = $Encryption->encryption($data['password']);
        }
        return $data;
    }

    /**
     * 에러출력
     */
    private function error($msg)
    {
        $error = new \Jiny\App\Error($msg);
        return $error->main();
    }

}