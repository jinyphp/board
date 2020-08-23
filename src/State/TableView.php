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
 * 선택한 계시물을 출력합니다.
 */
class TableView extends \Jiny\Board\State\Table
{
    use \Jiny\Board\PreFix; 

    private $db;
    private $parser;
    private $table;
    // private $conf;
    use \Jiny\Board\Config;

    public function __construct($conf=null)
    {
        $dbinfo = \jiny\dbinfo();
        $this->db = \jiny\mysql($dbinfo);

        if ($conf) $this->conf = $conf;
        $this->table = $conf['table']; // 테이블명 설정"members";
        $this->csrf = "hello";

        //print_r($conf);
        //exit;
    }

    /**
     * 처리로직
     */
    public function main($id)
    {
        if(is_numeric($id)) {
            if ($row = $this->read($id)) {
                $this->builder($row);
                return $this->resource(['data'=>$row]);
            }
            $msg = $id." 데이터를 읽어 처리할 수 없습니다.";
        } else {
            $msg = $id." 는 숫자로 입력되어야 합니다.";
        }
        
        return $this->error($msg);
    }

    /**
     * GET 요청
     */
    public function GET($id)
    {
        if(is_numeric($id)) {
            if ($row = $this->read($id)) {
                $this->builder($row);
                return $this->resource(['data'=>$row]);
            }
            $msg = $id." 데이터를 읽어 처리할 수 없습니다.";

        } else {
            $msg = $id." 는 숫자로 입력되어야 합니다.";
        }
        
        return \json_encode(
            ['code'=>'400','message'=>$msg],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
    }

    /**
     * html 코드빌더
     */
    private function builder($row)
    {

    }

    /**
     * 화면처리 리소스
     */
    private function resource($vars=[])
    {
        if(isset($this->conf['view']['title'])) {
            $vars['title'] = $this->conf['view']['title'];
        }

        $file = $this->resourcePath();
        $body = \jiny\html_get_contents($file, $vars);

        $codes = $this->setPrefix("{{", "}}")->preFixs($body);
        if (\is_array($codes)) {
            foreach ($codes as $key) {
                $body = str_replace("{{".$key."}}", $vars['data'][$key], $body);
            }
        }        

        return $body;
    }

    private function resourcePath()
    {
        if (isset($this->conf['view']['resource'])) {
            return "..".$this->conf['view']['resource'];
        }

        return "../vendor/jiny/board/resource/view.html";        
    }

    /**
     * 데이터베이스 read
     */
    private function read($id)
    {
        // 출력할 필드 선택
        $fields = $this->setField();
        return $this->db->select($this->table, array_keys($fields))->id($id);
    }

    private function setField()
    {
        if (isset($this->conf['view']['fields'])) {
            return $this->conf['view']['fields'];
        }

        return [];
    }

}