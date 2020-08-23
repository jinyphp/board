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
 * 데이터를 갱신합니다.
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
        if (\jiny\board\csrf()->is()) {
            \jiny\board\csrf()->clear();

            if ($this->validate()) {
                $id = $this->id(); // id 선택값
                $data = $this->formData();
                $update = $this->db->update($this->table, $data)->autoField()->id($id);
                
                // 성공후 페이지 리다이렉션
                \jiny\board\redirect($this->conf['uri']);
                return true;
            }

            $msg = "유효성 실패";
            return $this->error($msg);
        }

        $msg = "update CSRF 불일치";
        return $this->error($msg);
    }

    /**
     * post 처리루틴 "application/x-www-form-urlencoded"
     * 처리후 redirection
     */
    public function POST($body)
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
    public function PUT()
    {
        
        if (\jiny\board\csrf()->is()) {
            \jiny\board\csrf()->clear();

            if ($this->validate()) {
                $id = $this->id(); // id 선택값
                $data = $this->formData();
                //echo "update 요청";
                //print_r($data);

                $update = $this->db->update($this->table, $data)->autoField()->id($id);
            
                return true;
            }

            $msg = "유효성 실패";
            return $this->error($msg);                    
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
        if(isset($data['password'])){
            if(empty($data['password'])) {
                // password가 비어있는 경우, update 항목 삭제
                unset($data['password']);
            } else {
                // 패스워드 암호화
                $Encryption = new \Jiny\Members\Encryption();
                $data['password'] = $Encryption->encryption($data['password']);
            }
        }

        
        return $data;
    }

    // 유효성 검사
    private function validate()
    {
        $validate = new \Jiny\Board\Validate($this->conf['edit']['fields']);
        $validate->rules()->filter($_POST['data']);
        return $validate->isPass();
    }


}