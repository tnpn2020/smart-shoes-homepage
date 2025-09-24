//(function(global){
//	var lb = function(selector) {
//		
//		return new lb.fn.init(selector);
//	}
//
//	lb.fn = lb.prototype = {
//		constructor : lb,
//		init : function(selector){
//			this.ajax = function(){
//				
//			}
//		}
//	}
//
//	lb.fn.init.prototype = lb.fn;
//	
//	window.lb = lb;
//})(window);

(function(global){
	var lb = function(){
		this.obj = {
			// address:"http://192.168.0.21/" //재영이 PC
			// address:"http://192.168.200.151/" //재영이 test PC
			// address:"http://192.168.0.11/" //재영이 test2 PC
			//  address:"http://192.168.1.115/" //대리님이 PC
			// address:"http://192.168.0.56:88/" //진혁이 PC
			// address:"http://192.168.0.36/", //정환이 PC
			// address:"http://180.64.154.153:801/", //회사 외부IP
			// address:"http://localhost/",
			// address : "https://tmb.lbcontents.com/"
			address: window.location.protocol + "//" + window.location.hostname + ":" + window.location.port +"/"
		};
		console.log(this.obj.address);

		//************************************************************************************
		//ajax    APP 버전 AJAX 꼭 확인할것!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		//************************************************************************************
		this.ajax = function(obj){
			//obj : type 
			if(obj.type=="JsonAjaxPost"){
				// //form 을 생성 하여 보내고싶은 값을 list에 넣어 보낼수 있다.
				// //list, action, havior
				var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
				if (typeof(is_app) != "undefined" && is_app == true && varUA.match('android') != null) {
					console.log("lb.ajax 안드로이드 코드 실행");
					//안드로이드 일때 처리
					var json = {}; //안드로이드로 보낼 JSON OBJECT
					for(var key in obj.list){
						json[key] = obj.list[key];
					}
					
					if(typeof(obj.files) != "undefined"){
						window.android.script(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json, files : obj.files }));	
					}else{
						window.android.script(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json}));
					}
					
					

				} else if (typeof(is_app) != "undefined" && is_app == true && (varUA.indexOf("iphone")>-1||varUA.indexOf("ipad")>-1||varUA.indexOf("ipod")>-1)) {
					//IOS 일때 처리
					console.log("lb.ajax IOS 코드 실행");
					//안드로이드 일때 처리
					var json = {}; //안드로이드로 보낼 JSON OBJECT
					for(var key in obj.list){
						json[key] = obj.list[key];
					}
					// gu.log(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json }));
					if(typeof(obj.files) != "undefined"){
						webkit.messageHandlers.callbackHandler.postMessage(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json, files : obj.files }));	
					}else{
						webkit.messageHandlers.callbackHandler.postMessage(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json}));	
					}
					// webkit.messageHandlers.callbackHandler.postMessage(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json }));
				} else {
					//아이폰, 안드로이드 외 처리
					// gu.toast("lb.ajax 아이폰, 안드로이드 외 처리 코드 실행");
					var os_name = this.osCheck();
					if(os_name != "Unknown OS"){ //모르는 OS가 아니면 ajax 실행
						var xmlhttp = new XMLHttpRequest();
						var form = document.createElement("form");
						var formData = new FormData(form);
						for(var key in obj.list){
							if(typeof(obj.list[key])=="object"){
								for(var subkey in obj.list[key]){
									formData.append("json",  JSON.stringify(obj.list[key]));
								}
							}else{
								formData.append(key, obj.list[key]);
							}
						}
						xmlhttp.onreadystatechange = function(){
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
								obj.havior(xmlhttp.responseText);
							}
						}
						
						if(typeof(obj.sync)=="undefined"){
							obj["sync"]=true;
						}

						if(typeof(obj.action)=="undefined"){
							obj["action"]="/";
						}
						xmlhttp.open("post", obj.action, obj.sync);
						xmlhttp.send(formData); 
					}else{
						console.log("AJAX 통신불가(OS 모름)");
					}
				}
				
			}else if(obj.type=="POST" || obj.type=="post"){
				//form을 만들고 값을 담고 바로 전송 hidden형식으로 생성 시켜서 값을 넘긴다.
				//list, address
				var doc = document;
				var form = doc.createElement("form");

				for(var key in obj.list){
					var input = doc.createElement("INPUT");
					input.type="hidden";
					input.name=key;

					if(typeof(obj.list[key])=="object"){
						input.value=JSON.stringify(obj.list[key]);
					}else{
						input.value=obj.list[key];
					}

					form.appendChild(input);
				}
				form.setAttribute("method", "post");

				if(typeof(obj.address)=="undefined"){
					obj.address = "/";
				}
				form.action=obj.address;
				doc.body.appendChild(form);
				form.submit();
			}else if(obj.type == "AjaxFormPost"){
					var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
					if (typeof(is_app) != "undefined" && is_app == true && varUA.match('android') != null) {
						console.log("files");
						console.log(JSON.stringify(obj.files));
					
						var json = {}; //안드로이드로 보낼 JSON OBJECT
						for(var key in obj.list){
							json[key] = obj.list[key];
						}
						
						var elements = obj.elem.elements;
						for(var i=0; i<elements.length; i++){ //form의 값들도 json에 추가
							json[elements[i].name] = elements[i].value;
						}
						if(typeof(obj.files) != "undefined"){
							window.android.script(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json, files : obj.files }));	
						}else{
							window.android.script(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json}));
						}
					} else if ( typeof(is_app) != "undefined" && is_app == true && varUA.indexOf("iphone")>-1||varUA.indexOf("ipad")>-1||varUA.indexOf("ipod")>-1) {
						//IOS 일때 처리
						var json = {}; //안드로이드로 보낼 JSON OBJECT
						for(var key in obj.list){
							json[key] = obj.list[key];
						}
						
						var elements = obj.elem.elements;
						for(var i=0; i<elements.length; i++){ //form의 값들도 json에 추가
							json[elements[i].name] = elements[i].value;
						}
						if(typeof(obj.files) != "undefined"){
							webkit.messageHandlers.callbackHandler.postMessage(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json, files : obj.files }));	
						}else{
							webkit.messageHandlers.callbackHandler.postMessage(JSON.stringify(({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json})));
						}
						// webkit.messageHandlers.callbackHandler.postMessage(JSON.stringify({action:"http_util", server_address: obj.action ,response_method: obj.response_method, param:json }));
					} else {
						//아이폰, 안드로이드 외 처리
						var os_name = this.osCheck();
						if(os_name != "Unknown OS"){ //모르는 OS가 아니면 ajax 실행
							//form 엘리먼트를 넣고 전송 시킨다.
							//list, action, elem,havior
							var xmlhttp = new XMLHttpRequest();
							var formData = new FormData(obj.elem);
							
							if(obj.list){
								for(var key in obj.list){
									formData.append(key, obj.list[key]);
								}
							}
							

							xmlhttp.onreadystatechange = function(){
								if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
									obj.havior(xmlhttp.responseText);
								}
							}

							xmlhttp.open("post", obj.action, true);
							xmlhttp.send(formData);
						}else{
							alert("AJAX 통신불가(OS 모름)");
						}
					}
				}
			}

		//************************************************************************************
		//file 처리
		//************************************************************************************
		this.file_name_get = function(obj){
			var filename = null;
			if(window.FileReader){
				if(typeof(obj.files[0])!="undefined"){
					filename = obj.files[0].name;
				}
			}else{
				filename = obj.value.split('/').pop().split('\\').pop();
			}

			return filename;
		}

		this.file_check = function(obj, list) {
			//파일 확장자 및 용량 체크
			//확장자 체크 : list.type[]배열 형식 , 확장자 에러 list.error , 파일크기 에러체크 list.sizeError, list.size
			var thumbext = obj.value; //파일을 추가한 input 박스의 값
			thumbext = thumbext.slice(thumbext.indexOf(".") + 1).toLowerCase(); //파일 확장자를 잘라내고, 비교를 위해 소문자로 만듭니다.

			var fileFlag = false;
			if(thumbext==""){
				return false;
			}else{
				for(var i=0;i<list.type.length;i++){
					if(list.type[i]===thumbext){
						fileFlag=true;
					}
				}

				if(fileFlag==false){
					list.error();//확장자 에러문구
					obj.value="";
					return false;
				}

				//파일 크기 체크
				//var MAX_SIZE = 3145728;
				var MAX_SIZE = list.size;
				if (MAX_SIZE<obj.files[0].size)
				{
					if (obj.value!="")
					{
						list.sizeError();
						obj.value="";
					}
					return false;
				}
			}

			return true;
		}

		this.getFileExtension = function(filename) {
			return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
		}

		this.file_check_v2 = function(obj){
			//error 0 : 첨부파일  확장자가 없음 1:확장자 에러 2:파일크기에러
			var object = obj.event.target;
			var thumbext = object.value; //파일을 추가한 input 박스의 값
			thumbext = thumbext.slice(thumbext.indexOf(".") + 1).toLowerCase(); //파일 확장자를 잘라내고, 비교를 위해 소문자로 만듭니다.
			var fileFlag = false;
			if(thumbext==""){
				obj.error(0);
				return false;
			}else{
				for(var i=0;i<obj.extension.length;i++){
					if(obj.extension[i]===thumbext){
						fileFlag=true;
					}
				}

				if(fileFlag==false){
					obj.error(1);//확장자 에러문구
					object.value="";
					return false;
				}

				//파일 크기 체크
				//var MAX_SIZE = 3145728;
				var MAX_SIZE = obj.size;
				if (MAX_SIZE<object.files[0].size)
				{
					if (object.value!="")
					{
						obj.error(2);
						object.value="";
					}
					return false;
				}
			}

			if(typeof(obj.thumnail)!="undefined"){
				var reader = new FileReader();
				reader.__proto__.lb = this;
				reader.onload = function(){
					var output = null;
					if(typeof(obj.thumnail)=="object"){
						output = obj.thumnail;
					}else{
						output = this.lb.getElem(obj.thumnail);
					}
					output.src = reader.result;
				};
				reader.readAsDataURL(event.target.files[0]);
			}

			return true;
		}

		//************************************************************************************
		//데이터 처리
		//************************************************************************************

		this.uuid = function(){
			var dt = new Date().getTime();
			var uid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
				var r = (dt + Math.random()*16)%16 | 0;
				dt = Math.floor(dt/16);
				return (c=='x' ? r :(r&0x3|0x8)).toString(16);
			});
			return uid;
		}

		this.mobileCheck = function() { 
			if( navigator.userAgent.match(/Android/i)
				|| navigator.userAgent.match(/webOS/i)
				|| navigator.userAgent.match(/iPhone/i)
				|| navigator.userAgent.match(/iPad/i)
				|| navigator.userAgent.match(/iPod/i)
				|| navigator.userAgent.match(/BlackBerry/i)
				|| navigator.userAgent.match(/Windows Phone/i)
			){
				return true;
			}
				else {
				return false;
			}
		}

		//************************************************************************************
		//엘리먼트 관리
		//************************************************************************************
		this.getElem = function(id){
			return document.getElementById(id);
		}

		this.iframe = function(obj){
			//하위 iframe 엘리먼트 get window
			//obj elem, havior
			var y = (obj.elem.contentWindow || obj.elem.contentDocument);
			obj.havior(y);
		}

		this.queryAll = function(query){
			return document.querySelectorAll('['+query+']');
		}

		this.createElem = function(name){
			return document.createElement(name);
		}

		this.traverse = function(Elem,havior){
            function traver(elem,callback){
                callback(elem);
                var c = elem.childNodes;
                for(var i=0;i<c.length;i++){
                    traver(c[i],callback);
                }
            }

            traver(Elem,havior);
        }

		this.getName = function(name){
			return document.getElementsByName(name);
		}

		this.query = function(name){
			return document.querySelector(name);
		}

		//************************************************************************************
		//쿠키 관리
		//************************************************************************************
		this.cookie = function(obj){
			//type = set 과 get , day , name, value
			if(obj.type=="set"){
				var d = new Date();
				d.setTime(d.getTime() + (obj.day*24*60*60*1000));
				var expires = "expires="+ d.toUTCString();
				if(this.brower_check()=="safari"){
					obj.value=encodeURI(obj.value);
				}
				document.cookie = obj.name + "=" + obj.value + ";" + expires + ";path=/";
			}else if(obj.type=="get"){
				var return_value=false;
				var name = obj.name + "=";
				var decodedCookie = decodeURIComponent(document.cookie);
				var ca = decodedCookie.split(';');
				for(var i = 0; i <ca.length; i++) {
					var c = ca[i];
					
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					
					if (c.indexOf(name) == 0) {
						var str = c.substring(name.length, c.length);
						str = decodeURI(str);
						obj.havior(str);
						return_value=true;
					}
				}
				return return_value;
				
			}else if(obj.type=="get_not_decode"){
				var return_value=false;
				var name = obj.name + "=";
				var decodedCookie = document.cookie;
				var ca = decodedCookie.split(';');
				for(var i = 0; i <ca.length; i++) {
					var c = ca[i];
					
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					
					if (c.indexOf(name) == 0) {
						obj.havior(c.substring(name.length, c.length));
						return_value=true;
					}
				}
				return return_value;
			}else if(obj.type=="del"){
				document.cookie = obj.name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			}
		}

		//************************************************************************************
		//시간비교
		//************************************************************************************
		this.dates = function(obj){
			var dates = {
				convert:function(d) {
					// Converts the date in d to a date-object. The input can be:
					//   a date object: returned without modification
					//  an array      : Interpreted as [year,month,day]. NOTE: month is 0-11.
					//   a number     : Interpreted as number of milliseconds
					//                  since 1 Jan 1970 (a timestamp) 
					//   a string     : Any format supported by the javascript engine, like
					//                  "YYYY/MM/DD", "MM/DD/YYYY", "Jan 31 2009" etc.
					//  an object     : Interpreted as an object with year, month and date
					//                  attributes.  **NOTE** month is 0-11.
					return (
						d.constructor === Date ? d :
						d.constructor === Array ? new Date(d[0],d[1],d[2]) :
						d.constructor === Number ? new Date(d) :
						d.constructor === String ? new Date(d) :
						typeof d === "object" ? new Date(d.year,d.month,d.date) :
						NaN
					);
				},
				compare:function(a,b) {
					// Compare two dates (could be of any type supported by the convert
					// function above) and returns:
					//  -1 : if a < b
					//   0 : if a = b
					//   1 : if a > b
					// NaN : if a or b is an illegal date
					// NOTE: The code inside isFinite does an assignment (=).
					return (
						isFinite(a=this.convert(a).valueOf()) &&
						isFinite(b=this.convert(b).valueOf()) ?
						(a>b)-(a<b) :
						NaN
					);
				},
				inRange:function(d,start,end) {
					// Checks if date in d is between dates in start and end.
					// Returns a boolean or NaN:
					//    true  : if d is between start and end (inclusive)
					//    false : if d is before start or after end
					//    NaN   : if one or more of the dates is illegal.
					// NOTE: The code inside isFinite does an assignment (=).
				   return (
						isFinite(d=this.convert(d).valueOf()) &&
						isFinite(start=this.convert(start).valueOf()) &&
						isFinite(end=this.convert(end).valueOf()) ?
						start <= d && d <= end :
						NaN
					);
				},
				diff : function(type,date1,date2){
					var diff_date = null;
					var date_diff = date2-date1;
					var currDay = 24 * 60 * 60 * 1000;// 시 * 분 * 초 * 밀리세컨
					var currMonth = currDay * 30;// 월 만듬
					var currYear = currMonth * 12; // 년 만듬


					if(type=="day"){
						diff_date = parseInt(date_diff/currDay);
					}else if(type=="month"){
						diff_date = parseInt(date_diff/curMonth);
					}else if(type=="year"){
						diff_date = parseInt(date_diff/currYear);
					}

					return diff_date;
				}
			}
			
			var result = null;

			if(obj.set=="compare"){
				result = dates.compare(obj.a,obj.b);
			}else if(obj.set=="convert"){
				result = dates.convert(obj.d);
			}else if(obj.set=="inRange"){
				result = dates.inRange(obj.d,obj,start,obj.end);
			}else if(obj.set=="diff"){
				result = dates.diff(obj.type,obj.date01,obj.date02);
			}

			return result;
		}

		//************************************************************************************
		//정규식
		//************************************************************************************

		this.reg = function(obj){
			var regStr = null;
			if(obj.name=="특수문자"){//특수문자인지 체크
				regStr = /[~!@\#$%<>^&*\()\-=+_\’]/gi;
			}else if(obj.name=="email"){//이메일 형식인지 체크 
				regStr = /[0-9a-zA-Z][_0-9a-zA-Z-]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/;
			}else if(obj.name=="number"){
				regStr = /[^0-9]/g;
			}else if(obj.name=="phone"){ // 핸드폰번호 체크, 성공예시 : 010-XXXX-XXXX 또는 010XXXXXXXX
				obj.str = obj.str.split('-').join('');
				regStr = /(01[016789])([1-9]{1}[0-9]{2,3})([0-9]{4})$/;
			}
			return regStr.test(obj.str);
		}

		//숫자,문자,특수문자 최소8자리 체크
		this.password = function(value){
			return /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/.test(value)
		}
		//ID 체크 정규식
		this.id = function(value){
			return /^[A-Za-z0-9+]*$/.test(value);
		}

		this.numberCommas = function(x){
            //콤마 없엘려면 replace(/,/g,"");
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
		
		this.reg_replace = function(obj){
			var data = null;
			if(obj.type=="commas"){
				data = obj.str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}else if(obj.type=="remove_commas"){
				data = obj.str.toString().replace(/,/g,"");
			}

			return data;
		}

		this.nl2br = function(){
			var str = this.toString();
			if (typeof str === 'undefined' || str === null) {
				return ''
			}
			
			// Adjust comment to avoid issue on locutus.io display
			
			var convert = (str + '').replace(/(\r\n|\n\r|\r|\n)/g, '<br/>' + '$1');
			return convert.replace(/ /g, '\u00a0');
		}

		//************************************************************************************
		//네비게이터
		//************************************************************************************
		this.get_language = function() {
			return navigator.language || navigator.userLanguage;
		}

		//************************************************************************************
		//파라메터 가져오기
		//************************************************************************************
		this.getParam = function(name) {
			name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
			return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}

		this.brower_check = function() {
			var agent = navigator.userAgent;
			var browser = null;
			if(agent.indexOf("Safari")!=-1){
				browser = "safari";
			}

			return browser;
		}

		this.traverse = function(Elem,havior){
            function traver(elem,callback){
                callback(elem);
                var c = elem.childNodes;
                for(var i=0;i<c.length;i++){
                    traver(c[i],callback);
                }
            }

            traver(Elem,havior);
        }
		

		this.auto_view = function(obj){
			//wrap:: 붙을 부모 element Tagid
			//copy:: 복사해올 element Tagid
			//location:: wrap에 붙일 위치 0=위에서부터, 1=밑에서부터
			//array :: json Array
			//attr :: 컬럼과 같은 속성의 값내용

			//json array 값을 가져와 해당 컬럼이름을 통해서 값을 채운다.
			//복사할 html 의 태그는 //data-copy 로 이름을 넣는다.
			//추가적으로 값을 채워야할 경우는 add_process를 통해 값을 채워넣는다.
			//copy된 내용내에 값을 더 채워 넣어야 할경우는 data-copy-sub 를 붙여 값을 채운다.
			//html내에 data-copy-sub가 여러개라면 값을 배열로 전달한다.
			//wrap엘리먼트 또한 전달된 json array에서 따로 붙어야하는경우 data-wrap-elem 으로 값을 붙여넣는다.
			this.__proto__ = obj;
			this.all_copy_elem = [];
			for(var j=0;j<this.json.length;j++){
				var data = this.json[j];
				var copy_html = document.querySelector("[data-copy='"+this.copy+"']");
				var wrap_elem = document.querySelector("[data-wrap='"+this.wrap+"']");//copy 된 element가 들어갈 wrap 엘리먼트
				var copy_elem = document.createElement(copy_html.tagName);
				if(copy_html.className!=""){
                    copy_elem.className = copy_html.className;
				}

				if(copy_html.style.cssText!=""){
					copy_elem.style.cssText = copy_html.style.cssText;
					copy_elem.style.display="";
				}

				if(typeof(this.copy_event)!="undefined"){
					this.copy_event(copy_elem,data);
				}

				copy_elem = copy_html.cloneNode(true);
				if(copy_elem.style.display=="none"){
					copy_elem.style.display="block";
				}
				this.all_copy_elem.push(copy_elem);
				
				var attr_array = JSON.parse(this.attr);
				for(var k=0;k<attr_array.length;k++){
					var list = copy_elem.querySelectorAll("["+attr_array[k]+"]");
					this["data"] = data;
					if(typeof(this.havior)!="undefined"){
						//데이터값을 바로 삽입할 경우
						for(var i=0;i<list.length;i++){
							var name = $(list[i]).attr(attr_array[k]);
							this.havior(list[i],data,name,copy_elem);
						}
					}else{
						for(var i=0;i<list.length;i++){
							var name = $(list[i]).attr(attr_array[k]);
							list[i].innerHTML = data[name];
						}
					}
				}

				if(typeof(this.location)=="undefined"){
					wrap_elem.appendChild(copy_elem);
				}else{
					if(this.location=="0"){
						wrap_elem.prepend(copy_elem);
					}else if(this.location=="1"){
						wrap_elem.appendChild(copy_elem);
					}
				}

				if(j==(this.json.length-1)){
					if(typeof(this.end)!="undefined"){
						this.end();
					}
				}

			}
		}
		this.setCopyAttr = function(elem){
			for(var i=0;i<this.all_copy_elem.length;i++){
				if(this.all_copy_elem[i].isEqualNode(elem)){
					
				}
			}
		}
		this.clear_wrap = function(elem){
			if(elem != null){
				var wrap_elem = elem
				var childNodes = wrap_elem.childNodes;
				while(childNodes.length != 0){
					wrap_elem.removeChild(childNodes[0]);//헤더 자식노드 삭제
				}
			}
		}

		this.osCheck = function(){
			var OSName="Unknown OS"; 
			if(navigator.appVersion.indexOf("Win")!=-1){
				return OSName="Windows"; 
			}else if(navigator.appVersion.indexOf("Mac")!=-1){
				return OSName="MacOS";
			}else if(navigator.appVersion.indexOf("X11")!=-1){
				return OSName="UNIX";
			}else if (navigator.appVersion.indexOf("Linux")!=-1){
				return OSName="Linux"; 
			}else{
				return OSName;
			}
		}
		this.leadingZeros = function(n, digits) {
            var zero = '';
            n = n.toString();
          
            if (n.length < digits) {
              for (var i = 0; i < digits - n.length; i++)
                zero += '0';
            }
            return zero + n;
		}
		this.numberWithCommas = function(x) {
			return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
		
		this.getDate = function(dateString){ //사파리 버그로인한 date객체 생성 함수
			if(dateString.length == 10){ //10자리면 날짜만 있는부분임 뒤에 시간을 채워줘야함
				dateString = dateString + " 00:00:00";
			}
			var data = dateString + "+09:00";
			data = data.replace(" ","T");
			data = data.replace(/\s/, 'T');
			var date = new Date(data);
			return date;
		}

		this.replaceAll = function(str, searchStr, replaceStr) {
			return str.split(searchStr).join(replaceStr);
		}

		this.focus = function(json){  // {id: elem_id}
			console.log(json);
			if(typeof(this.getElem(json.id)) != "undefined"){
				this.getElem(json.id).focus();
			}
		}
		
		this.validate_number = function(field){
			//숫자만 입력되게하고 숫자가 아닌 값은 제거
			var valid = "0123456789";
			var flag = true;
			var temp;
		
			for (var i=0; i<field.value.length; i++){
				temp = "" + field.value.substring(i, i+1);
				if (valid.indexOf(temp) == "-1") {flag = false;}
			}
			if (flag == false){
				field.value = field.value.replace(/[^0-9]/g, "");
				var focus_json = null;
				if(typeof(field.id) != "undefined" || typeof(field.id) != null){ //엘리먼트 id가 있을경우 focus
					focus_json = {id:field.id};
				}
				gu.alert({
					description : "숫자만 입력할 수 있습니다", //내용(string 문자열) 필수
					title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
					response_method : "lb.focus", //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
					response_param : focus_json //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
				});
			}
		}

		this.auto_view_v2 = function(obj){
			this.__proto__ = obj;
			this.all_copy_elem = [];
			for(var j=0;j<this.json.length;j++){
				var data = this.json[j];
				var copy_html = document.querySelector("[data-copy='"+this.copy+"']");
				var wrap_elem = document.querySelector("[data-wrap='"+this.wrap+"']");//copy 된 element가 들어갈 wrap 엘리먼트
				var copy_elem = document.createElement(copy_html.tagName);
				if(copy_html.className!=""){
                    copy_elem.className = copy_html.className;
				}

				if(copy_html.style.cssText!=""){
					copy_elem.style.cssText = copy_html.style.cssText;
					copy_elem.style.display="";
				}

				if(typeof(this.copy_event)!="undefined"){
					this.copy_event(copy_elem,data);
				}

				copy_elem = copy_html.cloneNode(true);
				if(copy_elem.style.display=="none"){
					copy_elem.style.display="block";
				}
				this.all_copy_elem.push(copy_elem);
				
				var attr_array = JSON.parse(this.attr);
				for(var k=0;k<attr_array.length;k++){
					var list = copy_elem.querySelectorAll("["+attr_array[k]+"]");
					this["data"] = data;
					if(typeof(this.havior)!="undefined"){
						//데이터값을 바로 삽입할 경우
						for(var i=0;i<list.length;i++){
							var name = $(list[i]).attr(attr_array[k]);

							var object = {
								elem : list[i],
								data : data,
								name : name,
								copy_elem : copy_elem
							}
							if(copy_elem.getAttribute("data-copy") != ""){
								copy_elem.setAttribute("data-copy","");
							}
							this.havior(object);
						}
					}else{
						for(var i=0;i<list.length;i++){
							var name = $(list[i]).attr(attr_array[k]);
							list[i].innerHTML = data[name];
						}
					}
				}
				if(typeof(this.location)=="undefined"){
					wrap_elem.appendChild(copy_elem);
				}else{
					if(this.location=="0"){
						wrap_elem.prepend(copy_elem);
					}else if(this.location=="1"){
						wrap_elem.appendChild(copy_elem);
					}
				}
				if(j==(this.json.length-1)){
					if(typeof(this.end)!="undefined"){
						this.end();
					}
				}

			}

			this.setCopyAttr = function(elem){
				for(var i=0;i<this.all_copy_elem.length;i++){
					if(this.all_copy_elem[i].isEqualNode(elem)){
						
					}
				}
			}
		}
	}
	window.lb = new lb();
})(window);