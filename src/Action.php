<?php

namespace Jiny\Board;

class Action
{
    private $data;
    private $baseURI;

    private $enableView;

    public function __construct($board)
    {
        // 계시판 데이터처리 연결
        $this->data = $board;
    }

    public function setBaseURI($uri)
    {
        $this->baseURI = $uri;
        return $this;
    }

    public function setView()
    {
        $this->enableView = true;
        return $this;
    }

    private $linkKey;
    private $linkHref;
    public function links($key, $href)
    {
        $this->linkKey = $key;
        $this->linkHref = $href;
        return $this;
    }

    /**
     * 목록을 출력합니다.
     */
    public function list($viewFile="board")
    {
        $this->data->setLimit();                           // 출력위치 지정
        $count = $this->data->count();                     // 전체 글수
        $rows = $this->data->load()->setLinks($this->linkKey, $this->linkHref)->get();    // 데이터처리

        
        $b = new \Jiny\Html\Bootstrap($this);   // 부트스트랩 테이블
        $list = $b->tableHover($rows, [
            'thead' => "thead-light"
        ]);

        // 뷰에 전달되는 데이터
        $viewData['menus'] = menu();
        $viewData['data'] = [
            'list' => $list,
            'count' => $count,
            'pagenation' => $b->pagenation(
                $this->data->pagenation(), 
                $this->data->getLimit()
            ),
            'new' => $b->butten("Add", ['type'=>"btn-primary", 'id'=>"board_new", 'align'=>"right"])
        ];

        // 뷰 호출
        return view($viewFile, $viewData);
    }

    private $match;
    public function matching()
    {
        $this->match = true;
        return $this;
    }

    /**
     * 새로운 글을 작성합니다.
     */
    public function new($viewFile="board_new")
    {
        if (empty($_POST)) {
            $viewData['menus'] = menu();
            return view($viewFile, $viewData);

        } else {
            // 데이터를 저장합니다.
            $data = [];
            foreach ($_POST as $key => $value) {
                if($key == "regdate") {
                    $data[$key] = date("Y-m-d H:i:s");
                    continue;
                }

                $data[$key] = htmlspecialchars(strip_tags($value));
            }
            
            $this->data->insert($data, $this->match);
            header('Location: '.$this->baseURI);

        }

    }


    public function view($id, $viewFile="board_view")
    {
        // 읽기
        if ($row = $this->data->read($id) ) {
            $viewData['menus'] = menu();
            $viewData['data'] = $row;
            return view($viewFile, $viewData);
        }
    }

    public function edit($id, $viewFile="board_edit")
    {
        if(\Jiny\Board\_method() == "PUT") {

            $this->data->update($id, $_POST);
            if($this->enableView) {
                // 뷰 모드로 이동합니다.
                header('Location: '.$this->baseURI."/".$id);
            } else {
                // 목록으로 이동합니다.
                header('Location: '.$this->baseURI);
            }
            
            return;

        } else {
            if ($row = $this->data->read($id) ) {
                $viewData['menus'] = menu();
                $viewData['data'] = $row;
                return view($viewFile, $viewData);
            }
        }
    }


    public function delete($id)
    {
        if (\Jiny\Board\_method() == "DELETE") {
            $this->data->delete($id);
            header('Location: '.$this->baseURI);
        }
    }

    /**
     * 
     */

}