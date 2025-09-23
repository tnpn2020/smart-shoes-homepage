$(document).ready(function(){
    page_init();    
})

// get방식 페이지 파라미터 값
var page_param = data;


function page_init() {
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "AdminMenu4",
            param1: "request_technique_aid_detail",
            target: page_param.technique_aid_idx,
        },
        action: "index.php",
        havior: function(result) {
            // console.log(result);
            result = JSON.parse(result);
            response_technique_aid_detail(result);
        }
    })
}

function response_technique_aid_detail(result) {
    // console.log(result);
    if(result["result"] == "1") {
        content_init(result.value[0]);
    }
    else {
        alert("잘못된 접근입니다.");
        location.href = "?ctl=move&param=adm&param1=menu4_technique_aid";
    }
}

function content_init(data) {
    // console.log(data);
    var title = document.getElementById("title");
    var name = document.getElementById("name");
    var company = document.getElementById("company");
    var email = document.getElementById("email");
    var phone = document.getElementById("phone");
    var regdate = document.getElementById("regdate");
    var content = document.getElementById("content");

    title.innerHTML = data.title;
    name.innerHTML = data.name;
    company.innerHTML = data.company;
    email.innerHTML = data.email;
    phone.innerHTML = data.phone;
    regdate.innerHTML = data.regdate;
    content.innerHTML = data.content;

    //첨부파일
    if(data.file.length != 0){
        obj.elem.file_wrap.style.display = "";
        for(var i = 0; i < data.file.length; i++){
            var copy = obj.elem.file_copy.cloneNode(true);
            copy.querySelector('a').innerHTML = " " + data.file[i].real_name;
            var file_url = obj.link.product_file_path + data.file[i].file_name;
            copy.querySelector('a').setAttribute('onclick', 'file_download("' + data.file[i].real_name + '", "' + file_url + '")');

            obj.elem.file_wrap.appendChild(copy);
        }
    }
}

function file_download(file_name, url){
    lb.ajax({
        type: "post",
        list: {
            ctl: "CS",
            param1: "file_download",
            download_type: 1,
            realname: file_name,
            url: url,
        },
        address: "index.php"
    });
}


function technique_aid_list() {
    location.href = "?ctl=move&param=adm&param1=menu4_technique_aid";
}