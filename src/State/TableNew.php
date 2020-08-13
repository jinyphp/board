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
class TableNew extends \Jiny\Board\State\Table
{
    private $db;
    private $parser;
    private $table;
    private $conf;
    //private $uri;
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
        return $this->GET();
    }

    // 신규삽입
    // ~~/new
    public function GET($id=0)
    {
        $this->builder(); // 폼양식 빌드
        return $this->resource($vars=[]); // 화면 출력
    }

    public function POST($id=0)
    {
        $this->builder(); // 폼양식 빌드
        return $this->resource($vars=[]); // 화면 출력
    }

    /**
     * html 코드빌더
     */
    private function builder()
    {
        $form = \jiny\html\form();
        $form->setAction($this->conf['uri']);
        $form->hidden(['name'=>"mode", 'value'=>"newup" ]);
        $form->hidden(['name'=>"csrf", 'value'=>\jiny\board\csrf()->new() ]);
        
        // print_r($this->conf['new']['fields']);
        // $form->text(['label'=>"이메일", 'name'=>"email", 'placeholder'=>"이메일을 입력해 주세요"]);
        // $form->text(['label'=>"패스워드", 'name'=>"password", 'placeholder'=>"패스워드 입력"]);
        foreach ($this->conf['new']['fields'] as $field) {
            $type = $field['type'];
            $form->$type($field);
        }
        //$form->submit(['value'=>"등록", 'class'=>"btn btn-primary"]);
    }

    /**
     * 화면처리 리소스
     */
    private function resource($vars=[])
    {

        $file = "..".$this->conf['new']['resource'];
        $body = \jiny\html_get_contents($file, $vars);

        return $body;
    }

    /*
    public function insert()
    {
        $validate = new \Jiny\Board\Validate($this->conf['new']['validate']);
        foreach($_POST['data'] as $key => $value) {
            $validate->filter($key, $value);
        }

        if ($validate->isPass()) {
            //echo "유효성 패스";
            $this->db->insert($this->table, $_POST['data'])->save();
            \jiny\board\redirect($this->conf['uri']);

        } else {
            //echo "재입력 필요";
            \jiny\board\redirect($this->conf['uri']);
        }
    }
    */




}