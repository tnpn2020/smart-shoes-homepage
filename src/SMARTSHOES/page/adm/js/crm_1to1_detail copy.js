$(document).ready(function(){
    page_init();
})


function page_init(){
    inquiry_idx = data.inquiry_idx;
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminCrm",
            param1:"get_1to1_detail",
            inquiry_idx : inquiry_idx,
        },
        action : lb.obj.address,
        // response_method : "",
        havior : function(result){
            console.log(result);
            result = JSON.parse(result);
            set_1to1_detail(result.value[0]);
        }    
    });
}

function set_1to1_detail(datas){
    console.log(datas);
    document.getElementById("d_title").innerHTML = datas.title;
    document.getElementById("d_regdate").innerHTML = datas.regdate;
    document.getElementById("d_content").innerHTML = datas.content;
    document.getElementById("d_answer").innerHTML = datas.answer;
}

function d_close(){
    console.log("???");
    close();
}

function save(){
    answer = document.getElementById("d_answer").value;
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminCrm",
            param1:"save_1to1_answer",
            inquiry_idx : inquiry_idx,
            answer : answer,
        },
        action : lb.obj.address,
        // response_method : "",
        havior : function(result){
            result = JSON.parse(result);
            if(result["result"]=="1"){
                alert("답변완료");
                close();
            }
        }    
    });
}

