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
class TableDelete extends \Jiny\Board\State\Table
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
                    $this->byDelete($id);

                    // exit;
                    \jiny\board\redirect($this->conf['uri']);
                }
                $msg = "CSRF 불일치";
            }
            $msg = $id." 는 숫자로 입력되어야 합니다.";
        }

        $msg = "삭제할 id가 선택되지 안았습니다.";
        $error = new \Jiny\App\Error($msg);
        return $error->main();
    }

    public function POST($body)
    {
        //print_r($body);
        //echo "를 삭제합니다.";
        if ($this->isID($body->id)) {
            // echo $body->csrf;
            // exit;
            if ( $body->csrf == $_SESSION['_csrf'] ) {
                \jiny\board\csrf()->clear();
                $this->byDelete($body->id);

                exit;
                \jiny\board\redirect($this->conf['uri']);
            }
            $msg = "CSRF 불일치";
            echo $msg;
        }        
    }

    public function delete()
    {
        echo __METHOD__."삭제!!";
        exit;
    }


    private function isID($id)
    {
        if($id) {
            if(is_numeric($id)) {
                return true;
            } else {
                $msg = $id." 는 숫자로 입력되어야 합니다.";
            }
        } else {
            $msg = "삭제할 id가 선택되지 안았습니다.";
        }       

        $error = new \Jiny\App\Error($msg);
        return $error->main();
    }

    private function byDelete($id)
    {
        $this->db->delete($this->table)->id($id);
        //echo "데이터 삭제완료";
    }

    /**
     * 
     */
}