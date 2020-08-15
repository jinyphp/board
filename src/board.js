/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// 공용변수 선언

let btnNewlink;
let btnNew;
let btnListlink;
let btnList;
let form;
let btnDeletePost;
let btnDelete;


// 페이지이동 신규삽입버튼
function setBtnBoardNewLink()
{
    btnNewlink = document.querySelector("#btn-board-newlink");
    if (btnNewlink) {
        btnNewlink.addEventListener('click', function (e) {
            e.preventDefault();
            // alert("new link");
            window.location.href += "/new";
        });
    }
}

// ajax 신규삽입 버튼
function setBtnBoardNew()
{
    btnNew = document.querySelector("#btn-board-new");
    if (btnNew) {
        btnNew.addEventListener('click', function (e) {
            e.preventDefault();
            // alert("new ajax");
            history.pushState("new",null,document.location.href); // back 버튼용 저장
            
            $.ajax({
                uri: document.location.href,
                type:"get",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Content-type","application/json");
                    xhr.setRequestHeader("mode","new");
                    xhr.setRequestHeader("Authorization","JWT");
                },
                success: function(data) {
                    $('#jiny-board').html(data);

                    // 목록 이동버튼 재설정
                    setBtnBoardListLink();
                    setBtnBoardList();

                    // submit버튼 재설정
                    setBtnBoard_submitPut();
                    setBtnBoard_submit();

                    // 삽입 submit 재설정
                    setBtnBoard_submitPost();

                }
            });
            
        });
    }
}

function setBtnBoardListLink()
{
    btnListlink = document.querySelector("#btn-board-listlink");
    if (btnListlink) {
        btnListlink.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = "/admin/members";
            //history.back();
        });
    }
}

function setBtnBoardList()
{
    btnList = document.querySelector("#btn-board-list");
    if (btnList) {
        btnList.addEventListener('click', function (e) {
            e.preventDefault();
            
            history.pushState("list",null,document.location.href); // back 버튼용 저장

            // window.location.reload();
            var endpoint = "/admin/members";
            
            $.ajax({
                uri: endpoint,
                type:"get",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader("Content-type","application/json");
                    xhr.setRequestHeader("mode","list");
                    xhr.setRequestHeader("Authorization","JWT");
                },
                success: function(data) {
                    $('#jiny-board').html(data);

                    // 삽입링크 재설정
                    setBtnBoardNew();
                    setBtnBoardNewLink();                    
                }
            });
            
        });
    }
}

// 문서보기 페이지 이동버튼
function btnBoardViewLink(id)
{
    window.location.href += "/" + id;
}

// 문서보기 페이지 ajax 버튼
function btnBoardView(id)
{   
    history.pushState("view/"+id,null,document.location.href); // back 버튼용 저장

    $.ajax({
        uri: document.location.href,
        type:"get",
        beforeSend: function(xhr) {
            xhr.setRequestHeader("Content-type","application/json");
            xhr.setRequestHeader("mode","view");
            xhr.setRequestHeader("id",id);
            xhr.setRequestHeader("Authorization","JWT");
        },
        success: function(data) {
            $('#jiny-board').html(data);

            // 목록 이동버튼 재설정
            setBtnBoardListLink();
            setBtnBoardList();
        }
    });
}

function btnBoardEditLink(id)
{
    window.location.href += "/edit/" + id;
}

// 계시판 수정 ajax 요청
function btnBoardEdit(id)
{   
    history.pushState("edit/"+id,null,document.location.href); // back 버튼용 저장
    $.ajax({
        uri: document.location.href,
        type:"get",
        beforeSend: function(xhr) {
            xhr.setRequestHeader("Content-type","application/json");
            xhr.setRequestHeader("mode","edit");
            xhr.setRequestHeader("id",id);
            xhr.setRequestHeader("Authorization","JWT");
        },
        success: function(data) {
            $('#jiny-board').html(data);

            // 목록 이동버튼 재설정
            setBtnBoardListLink();
            setBtnBoardList()

            // submit버튼 재설정
            setBtnBoard_submitPut();
            setBtnBoard_submit();

            // 삭제버튼 재설정
            setBtnBoard_delete();
            setBtnBoard_deletePost();

        }
    });
}

/**
 * 삭제버튼
 */

function setBtnBoard_delete()
{
    btnDelete = document.querySelector("#btn-board-delete");
    if (btnDelete) {
        btnDelete.addEventListener('click', api_delete );

        // application/json
        // delete method
        function api_delete(e){

            let formId = document.querySelector("input[name=id]");
            let formCSRF = document.querySelector("input[name=csrf]");
            let data = { 
                mode: 'destroy',
                id: formId.value,
                csrf: formCSRF.value
            };

            $.ajax({
                uri: document.location.href,
                type:"delete", // 요청메소드
                contentType: "application/json",
                data: JSON.stringify(data),
                success: function(data) {
                    const res = JSON.parse(data);
                    if(res.code == "200") {
                        document.location.reload();
                    } else {
                        console.log(res);
                        // $('#jiny-board').html(data);
                    }                
                }
            });
        }
        //
    };
}


function setBtnBoard_deletePost()
{
    btnDeletePost = document.querySelector("#btn-board-delete-post");
    if (btnDeletePost) {
        btnDeletePost.addEventListener('click', post_delete );
        function post_delete (e) {
            var formId = document.querySelector("input[name=id]");
            var formCSRF = document.querySelector("input[name=csrf]");
            var data = { 
                mode: 'destroy',
                id: formId.value,
                csrf: formCSRF.value
            };
            //console.log(data);

            $.ajax({
                uri: document.location.href,
                type:"post",
                contentType: "application/json",
                data: JSON.stringify(data),
                success: function(data) {
                    //console.log(data);
                    
                    const res = JSON.parse(data);
                    //console.log(res);
                    if(res.code == "200") {
                        document.location.reload();
                    } else {
                    
                        $('#jiny-board').html(data);;
                    }
                            
                }
            });
        }

    }
}





function formObject(form)
{
    let formdata = new FormData(form);
    let obj = {};
    formdata.forEach(function(value, key) {
        obj[key] = value;
    });
    return obj;
}

function ajaxJson(method, obj, success)
{
    $.ajax({
        type : method,
        uri: document.location.href,
        contentType: "application/json",
        data: JSON.stringify(obj),
        success : success
    });
}

function ajaxJsonPut(obj, success) {
    ajaxJson('put', obj, success);
}

function ajaxJsonPost(obj, success) {
    ajaxJson('post', obj, success);
}

/**
 * www-url-encode
 * 일반 post 방식의 submit 동작
 * Insert/Update의 main() 호출
 */
function setBtnBoard_submit()
{
    form = document.querySelector("form");
    var submit = form.querySelector("#btn-board-submit");
    if (submit) {
        submit.addEventListener('click', function(e){
            e.preventDefault();
            // alert("클릭 post Submit");
            form.submit();
        });
    }
}

/**
 * application/json
 * PUT 호출 동작
 */
function setBtnBoard_submitPut()
{
    form = document.querySelector("form");
    let submit = form.querySelector("#btn-board-submit-put");
    if (submit) {
        submit.addEventListener('click', function(e){
            e.preventDefault();
            let obj = formObject(form);
            ajaxJsonPut(obj, function(data){
                //console.log(data);
                window.location.reload();
            });
        });
    }
}

/**
 * application/json
 * POST 호출 동작
 */
function setBtnBoard_submitPost()
{
    form = document.querySelector("form");
    let submit = form.querySelector("#btn-board-submit-post");
    if (submit) {
        submit.addEventListener('click', function(e){
            e.preventDefault();
            // alert("클릭 json Post Submit");
            let obj = formObject(form);
            ajaxJsonPost(obj, function(data){
                //console.log(data);
                window.location.reload();
            });
        });
    }
}

/**
 * 페이지네이션 이동처리
 * 지정한 페이지로 목록을 갱신합니다.
 */
function board_page(limit)
{ 
    // History 저장, back 버튼 방지용
    history.pushState("list/"+limit,null,document.location.href);

    // ajax 호출
    $.ajax({
        uri: document.location.href,
        type:"get",
        beforeSend: function(xhr) {
            xhr.setRequestHeader("Content-type","application/json");
            xhr.setRequestHeader("mode","list");
            xhr.setRequestHeader("limit",limit);
            xhr.setRequestHeader("Authorization","JWT");
        },
        success: function(data) {
            $('#jiny-board').html(data);
            setBtnBoardNewLink(); // 삽입버튼 링크
            setBtnBoardNew(); // 삽입버튼 링크
        }
    });
}

// url에서 #123 해쉬값을 읽어와, 정수로 변환합니다.
function board_isHashNum()
{
    let num = location.hash.substring(1);
    return parseInt(num);
}

/**
 * 페이지 로딩 이벤트
 */
window.addEventListener("load", function(){
    // History 제어
    // Back button 처리
    history.pushState("list", null, document.location.href);

    // Hash 넘버 처리
    if(limit = board_isHashNum()) {
        // alert(limit);
    }
    
    // Butten Event 초기화
    setBtnBoardNewLink(); // 삽입버튼 링크
    setBtnBoardNew(); // 삽입버튼 링크
    setBtnBoardListLink(); // 목록버튼 링크
    setBtnBoardList(); // 목록버튼 링크

    setBtnBoard_submitPut(); // ~/new 페이지 접속시, submit버튼 설정
    setBtnBoard_submit(); // www-post
    setBtnBoard_submitPost(); // json-post
});


// History 조작: SPA
window.addEventListener("popstate", function(){
    //alert("back click");
    // console.log(history.length);
    if(history.length) {
        // console.log(history.state);
        if (history.state) {
            const state = history.state.split('/');
            //if(state[0] == "list") 
            window.location.reload();
        }        
    }
    
});

window.addEventListener("hashchange", function(){
    //alert("hash changed");
    if(limit = board_isHashNum()) {
        // alert(limit);
        board_page(limit);
    }    
});