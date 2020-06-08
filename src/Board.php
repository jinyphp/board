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

class Board
{
    private $_ctrl; // 컨트롤
    private $_url;
    private $_endpoint;
    private $_board = "board";
    
    public $htmlList = __DIR__."/../_resource/board.php.html";
    public $htmlNew = __DIR__."/../_resource/board_new.php.html";
    public $htmlEdit = __DIR__."/../_resource/board_edit.php.html";

    public function __construct($ctrl=null)
    {
        if ($ctrl) $this->_ctrl = $ctrl;
        $this->_url = "/".\jiny\httpEndpoint()->first()."/";
        $this->_endpoint = \jiny\endpoint();
    }

    // Form 외부호출을 방지하기 위한 CSRF Salt값 설정
    protected $csrfSalt="hello";
    public function setSalt($salt)
    {
        $this->csrfSalt = $salt;
        return $this;
    }
    
    public function salt() { 
        return $this->csrfSalt; 
    }

    public function setTable($board)
    {
        $this->_board = $board;
        return $this;
    }

    private function csrfError()
    {
        return "csrf 불일치";
    }

    /**
     * board 계시판 로직을 처리합니다.
     */
    public function parser($action="list")
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['mode'])) {
                $method = "_".$_POST['mode'];
                if(method_exists($this,$method)) {
                    $this->$method();
                    // post redirect get pattern            
                    \jiny\board\redirect($this->_url);
                }
            }          
        } 
        //else {
        $method = "_".$action;
        if(method_exists($this,$method)) {
            return $this->$method();
        } else {
            return $this->_edit(\intval($action));
        }
        //}       
    }

    private function _newup()
    {
        if (\jiny\board\isCsrf()) {
            // 데이터삽입                  
            $crud = new \Jiny\Board\CRUD(); //db connecor 전달
            $crud->setTable($this->_board)->create($_POST['data']);
        } else {
            return $this->csrfError();
        }
    }

    private function _editup()
    {
        if (\jiny\board\isCsrf()) {
            $crud = new \Jiny\Board\CRUD(); //db connecor 전달
            $rows = $crud->setTable($this->_board)->update($_POST['data']['id'], $_POST['data']);
        } else {
            return $this->csrfError();
        }
    }

    private function _deleteup()
    {
        if (\jiny\board\isCsrf()) {
            $crud = new \Jiny\Board\CRUD(); //db connecor 전달
            $rows = $crud->setTable($this->_board)->delete($_POST['data']['id']);
        } else {
            return $this->csrfError();
        }
    }

    public $listFields = null;
    private function _list()
    {
        $crud = new \Jiny\Board\CRUD(); //db connecor 전달
        $crud->setTable($this->_board);
        if ($this->_pagenation) {
            $current = $this->pageStart();
            $crud->paging($this->_page_num, $current);
        }
        $rows = $crud->list($this->listFields);
        
        // 계시판 목록화면 출력
        $view = new \Jiny\Board\View($this);
        $view->setFile($this->htmlList);
        $tablelist = $view->table($rows);

        $total = $crud->total();
        $pagenation = new \Jiny\Board\Pagenation($total);
        
        $pages = $pagenation->setNum(5)->setBlock(5)->make($current);

        $body = $view->list($vars=['table'=>$tablelist, 'pagenav'=>$pages, 'start'=>$current]);
        $body .= "<script>".$this->javascriptNew()."</script>";
        $body .= "<script>".$this->javascript()."</script>"; // 페이지네이션

        return $body;
    }

    private $_pagenation;
    private $_page_start = 0;
    private $_page_num = 5;
    public function paging($num, $start)
    {
        $this->_pagenation = true; // 페이지네이션 활성화
        $this->_page_start = $start;
        $this->_page_num = $num;
    }
    private function pageStart()
    {
        if (isset($_POST['start'])) {
            return intval($_POST['start']);
        } else if (isset($_GET['start'])) {
            return intval($_GET['start']);
        } else return 0;
    }



    public $newFields = null;
    private function _new()
    {
        $view = new \Jiny\Board\View($this);
        $view->setFile($this->htmlNew);
        
        $body = $view->new($vars=[]);
        return $body; 
    }

    public $editFields = null;
    private function _edit(int $id=null)
    {
        // 자료읽기
        $crud = new \Jiny\Board\CRUD(); //db connecor 전달
        $rows = $crud->setTable($this->_board)->read($id, $this->editFields);

        if ($rows) {
            // 계시판 수정화면 출력
            $view = new \Jiny\Board\View($this);
            $view->setFile($this->htmlEdit);

            $body = $view->edit(['data'=>$rows]);
            $body .= "<script>".$this->javascriptEdit()."</script>";
            return $body;

        } else {
            return "Error] ".$id. "정보를 읽어 올 수 없습니다.";
        }
        
    }

    private function _delete()
    {
        return "잘못된 url 접근입니다.";
    }

    public function javascript()
    {
        return file_get_contents(__DIR__."/../_assets/board.js");
    }

    public function javascriptList()
    {
        $script = "
        var forms = document.getElementsByTagName('form'); 
        for(var i=0; i<forms.length;i++) forms[i].addEventListener('submit', function(){ 
            var hidden = document.createElement('input');  //create an extra input element
            hidden.setAttribute('type','hidden'); //set it to hidden so it doesn't break view 
            hidden.setAttribute('name','fragment');  //set a name to get by it in PHP
            hidden.setAttribute('value',window.location.hash); //set a value of #HASH
            this.appendChild(hidden); //append it to the current form
        });";

        return $script;
    }

    public function javascriptNew($byId="board_new")
    {
        $href = $this->_url."new";
       
        $script = "
        var board_new = document.getElementById('".$byId."');
        if (board_new) {
            board_new.onclick = function () {
                location.href = '".$href."';
            }
        }";

        return $script;
    }

    public function javascriptEdit($byId="board_update")
    {
        $script = "
        var board_update = document.getElementById('".$byId."');
        if (board_update) {
            board_update.onclick = function () {
                document.getElementsByName('mode')[0].value = \"editup\";
                document.getElementsByTagName(\"form\")[0].submit();
            }
        }";

        $script .= "
        var board_delete = document.getElementById('board_delete');
        if (board_delete) {
            board_delete.onclick = function () {
                document.getElementsByName('mode')[0].value = \"deleteup\";

                var result = confirm(\"정말 삭제하시겠습니까?\");
                if(result){
                    //alert(\"delete\");
                    document.getElementsByTagName(\"form\")[0].submit();
                }
            }
        }";
        return $script;
    }

}