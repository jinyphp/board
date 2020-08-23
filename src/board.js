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

    //alert(document.location.href);

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
        function api_delete(e){
            e.preventDefault();
            let formId = document.querySelector("input[name=id]");
            let formCSRF = document.querySelector("input[name=csrf]");
            let data = { 
                mode: 'remove',
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
                mode: 'remove',
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

            if(!validate(form)) {
                let msg = document.querySelector(".error-message");
                msg.classList.add("alert");
                msg.classList.add("alert-danger");

                msg.textContent = "입력하신 정보가 정확하지 않습니다.";   
                return;          
            }
            
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
            if(!validate(form)) {
                let msg = document.querySelector(".error-message");
                msg.classList.add("alert");
                msg.classList.add("alert-danger");

                msg.textContent = "입력하신 정보가 정확하지 않습니다.";   
                return;          
            } 

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

            
            if(!validate(form)) {
                let msg = document.querySelector(".error-message");
                msg.classList.add("alert");
                msg.classList.add("alert-danger");

                msg.textContent = "입력하신 정보가 정확하지 않습니다.";   
                return;          
            }
                                 

            let obj = formObject(form);
            ajaxJsonPost(obj, function(data){
                console.log(data);
                let res = JSON.parse(data);
                if(res.code == 400) {
                    let msg = document.querySelector(".error-message");
                    msg.textContent = res.message;
                    msg.attributes("display","hidden");
                   
                } else {
                    window.location.reload();
                }
                
            });
        });
    }
}


function validate_require(element) {
    if (element.attributes.required) {
        //console.log("필수입력 =" + element.name);
        if(!element.value) {
            // console.log(element.title + " 입력해 주세요.");
            msg = "필수 입력 항목입니다.";
            validate_message(element, msg);

            return false; // 유효성 실패
        }
    } 
    return true;
}

function validate_typeEmail(element) {
    function validateEmail(email) 
    {
        var re = /\S+@\S+\.\S+/;
        return re.test(email);
    }

    if(!validateEmail(element.value)) {
        
        console.log("유효한 이메일 주소가 아닙니다.");
        msg = "유효한 이메일 주소가 아닙니다. 정확한 이메일을 입력해 주세요.";
        validate_message(element, msg);

        return false; // 유효성 실패
    } 
    
    console.log("정상적인 이메일 주소 입니다.");
    validate_message(element, "");
    return true;
}

function validate_minlength(element,k) {
    if(element.value.length < element.dataset[k]) {
        console.log(element.title + "는 최소 " + element.dataset[k] + "자 이상 입력을 해야 합니다.");
        msg = element.title + "는 최소 " + element.dataset[k] + "자 이상 입력을 해야 합니다.";
        validate_message(element, msg);
        return false; // 유효성 실패
    } 
    
    validate_message(element, "");
    return true;
}

function validate_maxlength(element) {
    console.log(element.attributes.maxlength);
    if (element.attributes.maxlength) {
        if(element.value.length > element.attributes.maxlength) {
            console.log(element.title + "는 " + element.attributes.maxlength + "자 이상 입력할 수 없습니다.");
            msg = element.title + "는 " + element.attributes.maxlength + "자 이상 입력할 수 없습니다.";
            validate_message(element, msg);
            return false; // 유효성 실패
        }
    }
    
    validate_message(element, "");
    return true;
}

function validate_min(element) {
    if (element.attributes.min) {
        if(element.value.length > element.attributes.min) {
            console.log(element.title + "는 " + element.attributes.min + "을 넘을 수 없습니다.");
            msg = element.title + "는 " + element.attributes.min + "을 넘을 수 없습니다.";
            validate_message(element, msg);
            return false; // 유효성 실패
        }
    }

    validate_message(element, "");
    return true;
}

function validate_min(element) {
    console.log(element.attributes.min);
    if (element.attributes.min) {
        if(element.value.length > element.attributes.min) {
            console.log(element.title + "는 최소" + element.attributes.min + " 이상 이어야 합니다.");
            msg = element.title + "는 최소" + element.attributes.min + " 이상 이어야 합니다.";
            validate_message(element, msg);
            return false; // 유효성 실패
        }
    }

    validate_message(element, "");
    return true;
}

function validate_message(element, msg) {
    if(msg) {
        element.style.border = "1px solid INDIANRED";
    } else {
        element.style.border = "";
    }
    

    let errorMessage;
    errorMessage = element.parentNode.querySelector("div.form-error");
    if(errorMessage) {
        // 기존 요소 사용
        errorMessage.textContent = msg;
    } else {
        // 요소 생성추가
        errorMessage = document.createElement("div");
        errorMessage.classList.add("form-error");
        element.parentNode.appendChild(errorMessage);
    }


    errorMessage.textContent = msg;
    //errorMessage.classList.add("alert");
    //errorMessage.classList.add("alert-danger");

}

function validate(form)
{
    //console.log(form);
    var elements = form.elements;
    var msg;
    for(var i=0; i<elements.length; i++) {
        if(elements[i].name.substring(0,5) == "data[") {
            console.log(elements[i]);
            
            // 필수입력 검사
            if(!validate_require(elements[i])) return false; 

            // 타입검사
            console.log(elements[i].type);
            if(elements[i].type == "email") {
                //console.log("이메일 타입입니다.");
                if(!validate_typeEmail(elements[i])) return false; 

            } else if (elements[i].type == "number") {
                //console.log("숫자 타입입니다.");
                if(!validate_min(elements[i])) return false; //html5 속성유효성
                if(!validate_max(elements[i])) return false; //html5 속성유효성

            } else if (elements[i].type == "password") {
                //console.log("패스워드 타입입니다.");
            } else if (elements[i].type == "text") {
                //console.log("택스트 타입입니다.");
                if(!validate_maxlength(elements[i])) return false; //html5 속성유효성

            }

            // data 검사
            for(var k in elements[i].dataset )
            {
                if (k == "minlength") {
                   if(!validate_minlength(elements[i], k)) return false; 
                }
            }
            //
        }
        
    }
    
    return true; // 성공
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