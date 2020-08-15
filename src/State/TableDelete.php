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
 * 계시물을 삭제 합니다.
 * POST, DELETE 응답
 */
class TableDelete extends \Jiny\Board\State\Table
{
    private $db;
    private $parser;
    private $table;
    public function __construct($conf)
    {
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        if ($conf) $this->conf = $conf;

        $this->table = $conf['table']; // 테이블명 설정"members";
    }

    /**
     * 처리로직 Submit
     */
    public function main()
    {
        if(isset($_POST['id'])) {
            $id = $_POST['id'];
            if(is_numeric($id)) {
                if (\jiny\board\csrf()->is()) {
                    \jiny\board\csrf()->clear();
                    $this->dataDelete($id); // 삭제
                    \jiny\board\redirect($this->conf['uri']); // 리다이렉션
                }
                $msg = "CSRF 불일치";
            }
            $msg = $id." 는 숫자로 입력되어야 합니다.";
        }

        $msg = "삭제할 id가 선택되지 안았습니다.";
        return $this->error($msg);
    }

    /**
     * 메소드 응답: POST
     * 오류출력 포함
     */
    public function POST($body)
    {
        if ($msg = $this->isID($body->id)) {
            if ( $body->csrf == $_SESSION['_csrf'] ) {
                \jiny\board\csrf()->clear();
                $this->dataDelete($body->id);
                $msg = $body->id."가 정상적으로 삭제되었습니다.";
                return \json_encode(['code'=>'200','message'=>$msg]);
            }
            $msg = "CSRF 불일치";
        }

        // 오류화면 출력
        return $this->error($msg);
    }

    /**
     * 메소드 응답: DELETE
     */
    public function DELETE($body)
    {
        if ($msg = $this->isID($body->id)) {
            if ( $body->csrf == $_SESSION['_csrf'] ) {
                \jiny\board\csrf()->clear();
                $this->dataDelete($body->id);
                $msg = $body->id."가 정상적으로 삭제되었습니다.";
                return \json_encode(['code'=>'200','message'=>$msg]);
            }
            $msg = "CSRF 불일치";
        }

        return \json_encode(['code'=>'400','message'=>$msg]);
    }

    /**
     * id 유효성을 검사합니다.
     */
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
        return $msg; // 오류 메시지 반환
    }

    /**
     * 데이터베이스 작업을 실행합니다.
     */
    private function dataDelete($id)
    {
        $this->db->delete($this->table)->id($id);
    }

    /**
     * 
     */
}