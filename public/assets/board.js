var tbl = document.getElementById('tblMain');
            if (tbl != null) {
                for (var i = 0; i < tbl.rows.length; i++) {
                    for (var j = 0; j < tbl.rows[i].cells.length; j++)
                        tbl.rows[i].cells[j].onclick = function () { getval(this); };
                }
            }
            function getval(cel) {
                alert(cel.innerHTML);
            }

var board_new = document.getElementById('board_list');
if (board_new) {
    board_new.onclick = function () {
        // alert("등록");
        location.href = '/board';
    }
}

var board_new = document.getElementById('board_new');
if (board_new) {
    board_new.onclick = function () {
        // alert("등록");
        location.href = '/board/new';
    }
}

var board_new = document.getElementById('board_add');
if (board_new) {
    board_new.onclick = function () {
        alert("저장합니다.");
        // location.href = '/board/new';
        submit();
    }
}

var board_new = document.getElementById('board_delete');
if (board_new) {
    board_new.onclick = function () {
        document.getElementsByName('_method')[0].value = "DELETE";
        alert("삭제");
        // location.href = '/board/new';
    }
}

var board_new = document.getElementById('board_update');
if (board_new) {
    board_new.onclick = function () {
        document.getElementsByName('_method')[0].value = "UPDATE";
        alert("갱신");
        // location.href = '/board/new';
    }
}