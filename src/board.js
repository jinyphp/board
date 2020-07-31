/**
 * 계시판 내용추가
 */
function board_new()
{
    // history
    
    const params = {
        mode: 'new',
        csrf: '<?= $csrf; ?>'
    }
    post(document.location.href, params);
}

/**
 * 계시판 내용수정
 */
function board_edit(id){
    const params = {
        id: id,
        mode: 'edit',
        csrf: '<?= $csrf; ?>'
    }
    post(document.location.href+"/"+id, params);
    /*
    document.getElementsByTagName("form")[0].id.value = id;
    document.getElementsByTagName("form")[0].mode.value = "edit";
    document.getElementsByTagName("form")[0].action = document.location.href+"/"+id;
    document.getElementsByTagName("form")[0].submit();
    */
}

function board_page(limit)
{
    const params = {
        limit: limit
    }
    post(document.location.href, params);
}

/**
 * 동적 post 요청
 */
function post(path, params, method='post') {

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    const form = document.createElement('form');
    form.method = method;
    form.action = path;
  
    for (const key in params) {
      if (params.hasOwnProperty(key)) {
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = key;
        hiddenField.value = params[key];
  
        form.appendChild(hiddenField);
      }
    }
  
    document.body.appendChild(form);
    form.submit();
}