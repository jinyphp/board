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

    /**
     * 페이지네이션 생성자
     * 전체 갯수 초기화
     */
    public function __construct($total)
    {
        $this->total = $total;
    }

    /**
     * 싱글턴
     */
    public static $_instance;
    public static function instance($args=null)
    {
        if (!isset(self::$_instance)) {        
            //echo "객체생성\n";
            // print_r($args);   
            self::$_instance = new self($args); // 인스턴스 생성
            if (method_exists(self::$_instance,"init")) {
                self::$_instance->init();
            }
            return self::$_instance;
        } else {
            //echo "객체공유\n";
            return self::$_instance; // 인스턴스가 중복
        }
    }

    public $num = 10;    // 한페이지의 리스트수
    
    public $block = 10;  // 페이지 블럭 크기

    private $_limit=1;  // 현재의 위치
    public function setLimit($limit)
    {
        $this->_limit = $limit;
    }

    private $total;  // 전체 데이터 수
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    
    /**
     * 전체 리스트수
     */
    private $_totallist;
    private function lists()
    {
        $this->_totallist = intval( $this->total / $this->num );
        if ($this->total % $this->num) $this->_totallist += 1;
        return $this;
    }

    /**
     * 현재 리스트 위치
     */
    private $current_list;
    private function currentList($limit=0)
    {
        if ($limit) {
            $this->current_list = intval( $limit / $this->num );
        } else {
            $this->current_list = 1;
        }
        
        return $this;
    }

    
    /**
     * 전체 블럭 수
     */
    private $_totalblock;
    private function blocks()
    {
        $this->_totalblock = intval($this->_totallist / $this->block );
        return $this;
    }

    /**
     * 현재 블럭 위치
     */
    private $current_block;
    private function currentBlock()
    {
        $this->current_block = intval( $this->current_list / $this->block );
        return $this;
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

    
    public $title =[
        'first' => "처음",
        'prev' => "이전",
        'next' => "다음",
        'last' => "마지막"
    ];

    

    public function __invoke($limit){
        return $this->build($limit);
    }

    public function build($limit=0) : string
    {
        if (!$limit) $limit = $this->_limit;

        $this->lists(); // 전페 리스트 수
        $this->blocks(); // 전체 블럭 수
        $this->currentList($limit); // 현재 위치의 list 값 체크
        $this->currentBlock();  // 현제 위치의 block값 체크

        // 처음 데이터가 아닌경우, 처음으로 이동 버튼 생성.
        $pageMenu = "<ul class='pagination justify-content-center'>";
        $pageMenu .= $this->first($limit);
        $pageMenu .= $this->prev();
        $pageMenu .= $this->pages($limit);
        $pageMenu .= $this->next();
        $pageMenu .= $this->last();
        $pageMenu .= "</ul>";
        
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
        if($max > $this->_totallist) $max = $this->_totallist;

        for(;$i<=$max; $i++){
            $j = ($i-1) * $this->num;
            if($limit >= $j && $limit < $j + $this->num){
                $this->page[$i] = $j;
                // $str .= "<span><b>$i</b></span>";
                // $str .= $this->item($i);
                $str .= $this->itemActive($i, $j);
			} else {
                $this->page[$i] = $j;
                $str .= $this->item($i, $j);
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
            // return $this->link($this->title['first'], 0);
            return $this->item($this->title['first'], 0);
        } else {
            return $this->itemDisable($this->title['first'], 0);
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
            // return $this->link($this->title['prev'], $j);
            return $this->item($this->title['prev'], $j);
		} else {
            return $this->itemDisable($this->title['prev']);
        }
    }

    private function item($title, $i)
    {
        $href = "javascript:board_page($i);";
        $li = "<li class='page-item'><a class='page-link' href='".$href."'>".$title."</a></li>";
        return $li;
    }

    private function itemActive($title, $i)
    {
        $href = "javascript:board_page($i);";
        // $li = "<li class='page-item'><a class='page-link' href='".$href."'>".$title."</a></li>";
        $span = "<span class='page-link'>".$title."<span class='sr-only'>(current)</span></span>";
        $li = "<li class='page-item active' aria-current='page'>".$span."</li>";
        return $li;
    }
    private function itemDisable($title)
    {
        $li = "<li class='page-item disabled'>
      <a class='page-link' href='#' tabindex='-1' aria-disabled='true'>".$title."</a>
    </li>";
        return $li;
    }

    /**
     * 다음 데이터 링크
     */
    private function next()
    {
        // 다음 블럭이 있는 경우, 표시
        $i = ($this->current_block +1) * $this->block +1;
        if ($i<=$this->_totallist) {
            $j = ($i-1) * $this->num;
            $this->page['next'] = $j;
            //return $this->link($this->title['next'], $j);
            return $this->item($this->title['next'], $j);
        } else {
            return $this->itemDisable($this->title['next']);
        }
    }

    /**
     * 마지막 데이터 링크
     */
    private function last()
    {
        // 다음 블럭이 있는 경우, 표시
        $i = ($this->current_block +1) * $this->block +1;
        if ($i<=$this->_totallist) {
            $j = ($this->_totallist-1) * $this->num;
            $this->page['last'] = $j;
            // return $this->link($this->title['last'], $j);
            return $this->item($this->title['last'], $j);
        } else {
            return $this->itemDisable($this->title['last']);
        }
    }

    private function link($title, $i)
    {
        $query = "<span><a href='?limit=$i'>".$title."</a></span>"; // queryString
        $javascript = "<li class='page-item'><a class='page-link' href='javascript:board_page($i);'>".$title."</a></li>";
        return $javascript;
    }

    

    /**
     * 페이지네이션 
     * 배열저장
     */
    public $page = [];
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