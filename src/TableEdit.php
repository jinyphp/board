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
class TableEdit
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
     * 처리로직
     */
    public function main($id)
    {
        if(is_numeric($id)) {
            if ($bool = \jiny\board\csrf()->is()) {
                if ($row = $this->read($id)) {
                    $this->builder($row);
                    return $this->resource(['data'=>$row]);
                }
                $msg = $id." 데이터를 읽어 처리할 수 없습니다.";
    
            }
            $msg = "CSRF 불일치";            
            
        } else {
            $msg = $id." 는 숫자로 입력되어야 합니다.";
        }
        
        $error = new \App\Controllers\Members\Error($msg);
        return $error->main();
    }

    /**
     * html 코드빌더
     */
    private function builder($row)
    {
        $form = \jiny\html\form();
        $form->setAction($this->conf['uri']);
        $form->hidden(['name'=>"mode", 'value'=>"editup"]);
        $form->hidden(['name'=>"csrf", 'value'=>\jiny\board\csrf()->new() ]);
        $form->hidden(['name'=>"id", 'value'=>$row['id'] ]);

        foreach ($this->conf['edit']['fields'] as $field) {
            $type = $field['type'];
            if (isset($field['name']) && $name = $field['name']) {
                if(isset($row[$name]) && !empty($row[$name])) {
                    $field['value'] = $row[$name];
                }
            }
            $form->$type($field);
        }
    }

    /**
     * 화면처리 리소스
     */
    private function resource($vars=[])
    {
        $file = "..".$this->conf['edit']['resource'];
        $body = \jiny\html_get_contents($file, $vars);
        return $body;
    }

    /**
     * 데이터베이스 read
     */
    private function read($id)
    {
        return $this->db->select($this->table)->id($id);
    }

    /**
     * 
     */
    /*
    public function update()
    {
        // 수정
        if (\jiny\board\csrf()->is()) {
            \jiny\board\csrf()->clear();

            // id 선택값
            $id = isset($_POST['id'])?intval($_POST['id']) : 0;

            // 데이터삽입
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

            $update = $this->db->update($this->table,$data)->id($id);

            \jiny\board\redirect($this->conf['uri']);
            
        }

        $msg = "update CSRF 불일치";
        $error = new \App\Controllers\Members\Error($msg);
        return $error->main();
    }
    */

}