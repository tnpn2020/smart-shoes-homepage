gu.alert({
    description : null, //내용(string 문자열) 필수
    title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
    response_method : null, //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
    response_param : null //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
});


gu.confirm({
    description : null, //내용(string 문자열) 필수
    title : null,  //제목(string 문자열) null이면 "알림"으로 처리함
    positive_method : null, //예를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
    negative_method : null, //아니오를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
    positive_param : null, //예를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
    negative_param : null, //아니오를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
});

//select박스 변경시 코드
$(lb.getElem("search_type")).trigger('change');

function widthPercent(data) {
    lb.traverse(data, function (elem) {
        if (elem.nodeName == "IMG") {
            var split = elem.style.cssText.split("width");
            if (split.length > 1) {
                elem.style.height = "auto";
            }
            var style = elem.style.cssText;
            elem.removeAttribute('style');
            elem.setAttribute('style',style);
        }
    });
}

function request_(){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"",
            param1:"",
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "reponse_", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            // console.log(result);
            result = JSON.parse(result);
            reponse_(result);
        }    
    });
}

function reponse_(result){
    if(result.result == "1"){
        console.log(result.value);
    }else{
        gu.alert({
            description : result.message, //내용(string 문자열) 
            title : null,  //제목(string 문자열) 
            response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
        });
    }
}

lb.ajax({
    type : "AjaxFormPost",
    list : {
        ctl:"",
        param1:"",
    },
    elem : obj.elem.form,
    action : lb.obj.address, //웹일경우 ajax할 주소
    response_method : "reponse_", //앱일경우 호출될 메소드
    havior : function(result){
        //웹일 경우 호출될 메소드
        // console.log(result);
        result = JSON.parse(result);
        reponse_(result);
    }
});



lb.auto_view({
    wrap: "cart_wrap",
    copy: "cart_copy",
    attr: '["data-attr"]',
    json: data,
    havior: add_cart,
});

function add_cart(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
    }
    if(name == "cart_idx"){
        var cart_selected = elem.children;
        var cart_selected_input = cart_selected[0];
        var cart_selected_label = cart_selected[1];
        cart_selected_input.setAttribute('class','cart_num');
        cart_selected_input.id = "selected"+cart_count;
        cart_selected_input.value = data.cart_idx;
        var select_count = document.getElementById('select_count');
        cart_selected_label.onclick = function(){
            if(cart_selected_input.checked == true){
                select_count.innerHTML = select_count.innerHTML*1 - 1;            
            }else{
                select_count.innerHTML = select_count.innerHTML*1 + 1;
            }
        }
        cart_selected_label.setAttribute('for',"selected"+cart_count);
    }else if(name == "product_img"){
        elem.src = obj.value.product_link + data.img;
    }else if(name == "product_name"){
        elem.innerHTML = data.name;
    }else if(name == "product_del"){
        elem.setAttribute('onclick','del_btn_act("one",'+data.cart_idx+')');
    }else if(name == "product_pay"){
        var sale_pay = elem.children[0];
        var total_pay = elem.children[1];
        sale_pay.id = "sale_pay_"+cart_count;
        total_pay.id = "total_pay_"+cart_count;
        sale_pay.setAttribute('class','sum_pay slprice');
        if(data.sale == null){
            //할인율이 없으면
            sale_pay.innerHTML = "<p>"+lb.numberWithCommas(data.price * data.count)+"</p>" + "<span class ='won'>원" + "</span>";
            
            total_pay.innerHTML = "";
        }else{
            sale_pay.innerHTML = "<span>"+lb.numberWithCommas(Math.floor( (data.price - (data.price * (data.sale * 0.01 ))) ) * data.count)+"</span>원";
            // elem_sale_price.innerHTML = "<span>"+lb.numberWithCommas(sale_price)+"</span>원";
            total_pay.innerHTML = "<p>"+lb.numberWithCommas(data.price * data.count)+"</p>" + "<span class ='won'>원" + "</span>";
        }     
    }else if(name == "count_num"){
        var minus_btn = elem.children[0];
        var count_num = elem.children[1];
        var plus_btn = elem.children[2];
        count_num.innerHTML = data.count;
        plus_btn.setAttribute('onclick','plus(this.parentNode.children[1],'+JSON.stringify(data)+','+cart_count+')');
        minus_btn.setAttribute('onclick','minus(this.parentNode.children[1],'+JSON.stringify(data)+','+cart_count+')');
        cart_count++;
    }else if(name == "stock"){
        if(data["cnt"] == 0){//재고가 없으면
            elem.innerHTML = "재고없음";
            elem.style.color = "red";
        }else{
            elem.innerHTML = data["cnt"] + "개 남음";
        }
    }
}