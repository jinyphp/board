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
class TableDelete
{
    private $db;
    private $parser;
    private $table;
    public function __construct($conf)
    {
        // echo __CLASS__;
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        if ($conf) $this->conf = $conf;

        $this->table = $conf['table']; // 테이블명 설정"members";
        //$this->url = $conf['uri'];
    }

    /**
     * 처리로직
     */
    public function main()
    {
        if(isset($_POST['id'])) {
            $id = $_POST['id'];
            if(is_numeric($id)) {
                if (\jiny\board\csrf()->is()) {
                    \jiny\board\csrf()->clear();
                    $this->delete($id);

                    // exit;
                    \jiny\board\redirect($this->conf['uri']);
                }
                $msg = "CSRF 불일치";
            }
            $msg = $id." 는 숫자로 입력되어야 합니다.";
        }

        $msg = "삭제할 id가 선택되지 안았습니다.";
        $error = new \App\Controllers\Members\Error($msg);
        return $error->main();
    }

    private function delete($id)
    {
        $this->db->delete($this->table)->id($id);
        //echo "데이터 삭제완료";
    }
}