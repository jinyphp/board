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
 * 새로운 계시물을 입력받습니다.
 */
class TableNew extends \Jiny\Board\State\Table
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

    /**
     * 처리로직
     */
    public function main()
    {
        $this->builder(); // 폼양식 빌드
        return $this->resource($vars=[]); // 화면 출력
    }

    // 신규삽입, ~~/new
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

        // 공용요소
        $form->fields [] = $form->hidden(['name'=>"mode", 'value'=>"newup" ]);
        $form->fields [] = $form->hidden(['name'=>"csrf", 'value'=>\jiny\board\csrf()->new() ]);
        
        // 폼 요소를 생성
        $form->setFields( $this->conf['new']['fields'] );
        /*
        foreach ($this->conf['new']['fields'] as $id => $field) { 
            foreach($field as $tag => $el) {
                if(method_exists($form, $tag)) {
                    $form->fields[$id][$tag] = $form->$tag($el, $id);
                } else {
                    $form->fields[$id][$tag]= $el;
                }
            }
        }
        */
    }



    /**
     * 화면처리 리소스
     */
    private function resource($vars=[])
    {
        $file = $this->resourcePath();
        $body = \jiny\html_get_contents($file, $vars);
        return $body;
    }

    private function resourcePath()
    {
        return "..".$this->conf['new']['resource'];
    }


}