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

// 페이지네이션 계산로직
class Pagenation {

    public $num = 10;    // 한페이지의 리스트수
    public $block = 10;  // 페이지 블럭 크기

    public $limit;  // 현재의 위치
    public $total;  // 전체 데이터 수

    private $totalList;
    private $totalBlock;

    private $current_list;
    private $current_block;

    public $title =[
        'first' => "처음",
        'prev' => "이전",
        'next' => "다음",
        'last' => "마지막"
    ];

    public $page = [];

    /**
     * 페이지네이션 생성자
     * 전체 갯수 초기화
     */
    public function __construct($total)
    {
        $this->total = $total;
    }

    /**
     * 한페이지 출력 리스트수
     */
    public function setNum($num)
    {
        $this->num = $num;
        return $this;
    }

    /**
     * 한페이지 리스트 블럭
     */
    public function setBlock($block)
    {
        $this->block = $block;
        return $this;
    }


    /**
     * 전체 리스트수
     */
    private function lists()
    {
        $this->totalList = intval( $this->total / $this->num );
        if ($this->total % $this->num) $this->totalList += 1;
        return $this;
    }

    /**
     * 전체 블럭 수
     */
    private function blocks()
    {
        $this->totalBlock = intval($this->totalList / $this->block );
        return $this;
    }

    /**
     * 현재 리스트 위치
     */
    private function currentList($limit=0)
    {
        $this->current_list = intval( $limit / $this->num );
        return $this;
    }

    /**
     * 현재 블럭 위치
     */
    private function currentBlock()
    {
        $this->current_block = intval( $this->current_list / $this->block );
        return $this;
    }


    public function __invoke($limit){
        return $this->make($limit);
    }

    public function make($limit) : string
    {
        $this->lists(); // 전페 리스트 수
        $this->blocks(); // 전체 블럭 수
        $this->currentList($limit); // 현재 위치의 list 값 체크
        $this->currentBlock();  // 현제 위치의 block값 체크

        // 처음 데이터가 아닌경우, 처음으로 이동 버튼 생성.
        $pageMenu = "";
        $pageMenu .= $this->first($limit);
        $pageMenu .= $this->prev();
        $pageMenu .= $this->pages($limit);
        $pageMenu .= $this->next();
        $pageMenu .= $this->last();
        
		return $pageMenu;
    }


    /**
     * 페이지 목록 계산
     */
    private function pages($limit)
    {
        $str = "";
        if($this->current_block) $i = $this->current_block * $this->num +1; else $i = 1;
        
        $max = $i + $this->block -1;
        if($max > $this->totalList) $max = $this->totalList;

        for(;$i<=$max; $i++){
            $j = ($i-1) * $this->num;
            if($limit >= $j && $limit < $j + $this->num){
                $this->page[$i] = $j;
				$str .= "<span><b>$i</b></span>";
			} else {
                $this->page[$i] = $j;
                $str .= $this->link($i, $j);
            } 
        }

        return $str;
    }

    /**
     * 처음 데이터 링크
     */
    private function first($limit)
    {
		if($limit != 0) {
            $this->page['first'] = 0;
            return $this->link($this->title['first'], 0);
        }
    }

    /**
     * 이전 데이터 링크
     */
    private function prev()
    {
        if( $this->current_block >0) {
            $i = $this->current_block * $this->block;
            $j = $i * ($this->num-1);

            $this->page['prev'] = $j;
            return $this->link($this->title['prev'], $j);
		}
    }

    /**
     * 다음 데이터 링크
     */
    private function next()
    {
        // 다음 블럭이 있는 경우, 표시
        $i = ($this->current_block +1) * $this->block +1;
        if ($i<=$this->totalList) {
            $j = ($i-1) * $this->num;
            $this->page['next'] = $j;
            return $this->link($this->title['next'], $j);
        }
    }

    /**
     * 마지막 데이터 링크
     */
    private function last()
    {
        // 다음 블럭이 있는 경우, 표시
        $i = ($this->current_block +1) * $this->block +1;
        if ($i<=$this->totalList) {
            $j = ($this->totalList-1) * $this->num;
            $this->page['last'] = $j;
            return $this->link($this->title['last'], $j);
        }
    }

    private function link($title, $j)
    {
        /*
        return "<span >
                    <a href='?limit=$j'>".$title."</a>
                </span>";
        */
        return "<span >
                    <a href='javascript:pagenation($j);'>".$title."</a>
                </span>";
    }

    /**
     * 페이지네이션 
     * 배열저장
     */
    public function arr($limit)
    {
        $this->lists(); // 전페 리스트 수
        $this->blocks(); // 전체 블럭 수
        $this->currentList($limit); // 현재 위치의 list 값 체크
        $this->currentBlock();  // 현제 위치의 block값 체크

        $this->first($limit); // 처음 데이터가 아닌경우, 처음으로 이동 버튼 생성.
        $this->prev();
        $this->page($limit);
        $this->next();
        $this->last();

        return $this->page;
    }

    /**
     * 
     */
}