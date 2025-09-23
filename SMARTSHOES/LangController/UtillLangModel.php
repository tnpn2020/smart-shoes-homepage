<?php
    class UtillLangModel extends gf{
        private $param;
        private $dir;
        private $version;
        function __construct($init_object){
            $this->param = $init_object["json"];
            $this->dir = $init_object["dir"];
            $this->version = $init_object["version"];
            $this->result = array(
                "result" => null,
                "error_code" => null,
                "message" => null,
                "value" => null,
            );
        }

        function change_lang(){
            $param = $this->param;
            $this->setCookiesStr("synicsray_lang",(86400 * 30),$param["change_lang"]);
        }

        // /********************************************************************* 
        // // 함수 설명- 언어를 기준에 맞는 css 출력(page에서 사용)
        // // $page_type (sub:서브에 관련된 css가 있어야할경우, 아닐경우 null)
        // // 만든이: 안정환  수정: 조경민
        // *********************************************************************/
        function print_css($page_type,$main_lang,$version,$dir){
            if($page_type == "lang_css"){ //sub에 관련된 css가 있어야하면
                if($this->getCookiesStr("synicsray_lang") == $main_lang){
                    
                }elseif($this->getCookiesStr("synicsray_lang") == "2"){ 
                    include_once $dir."inc/css_en.php";
                }elseif($this->getCookiesStr("synicsray_lang") == "3"){ 
                    include_once $dir."inc/css_cn.php";
                }elseif($this->getCookiesStr("synicsray_lang") == "1"){
                    include_once $dir."inc/css_kr.php";
                }else{
                    // include_once $dir."inc/css_kr.php";
                }
            }else{
                // if($this->getCookiesStr("test") == "2"){ //언어가 영어면
                //     include_once $dir."inc/css_en.php";
                // }elseif($this->getCookiesStr("test") == "1"){ //언어가 한국어면
                //     include_once $dir."inc/css_kr.php";
                // }else{
                //     include_once $dir."inc/css_kr.php";
                // }
            }
            
        }

        function lang_check(){
            echo $this->getCookiesStr("synicsray_lang");
        }

        function font_link(){
            //중국어
            if($this->getCookiesStr("synicsray_lang") == "3"){
                echo '<link rel="preconnect" href="https://fonts.gstatic.com"><link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">';
            }else{ //나머지
                echo '<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">';
            }
        }

        function lang($page,$str){
            $lang = $this->getCookiesStr("synicsray_lang");
            $page = trim($page);
            $str = trim($str);
            if($lang=="" || $lang==null){
                if(isset($this->param["lang"] )){
                    $lang = strtoupper($this->param["lang"]);
                }else{
                    $lang = "1";
                }
            }

            $array = array(
                "header" => array(
                    "1" => array(
                        "회사소개" => "회사소개",
                        "사업분야" => "사업분야",
                        "제품소개" => "제품소개",
                        "고객센터" => "고객센터",
                        "연혁" => "연혁",
                        "조직도" => "조직도",
                        "오시는 길" => "오시는 길",
                        "생산 Process" => "생산 Process",
                        "허가 및 인증서" => "허가 및 인증서",
                        "제품문의" => "제품문의",
                        "기술지원" => "기술지원",
                    ),
                    "2" => array(
                        "회사소개" => "About Company",
                        "사업분야" => "Business Area",
                        "제품소개" => "Product Descriptions",
                        "고객센터" => "Customer Center",
                        "연혁" => "Company History",
                        "조직도" => "Organization Chart",
                        "오시는 길" => "Directions",
                        "생산 Process" => "Production Process",
                        "허가 및 인증서" => "Licenses and Certificates",
                        "제품문의" => "Product Inquiry",
                        "기술지원" => "Technical Support",
                    ),
                ),
                "index" => array(
                    "1" => array(
                        "현재보다 미래의 10년을 바라보는 기업" => "현재보다 미래의 10년을 바라보는 기업",
                        "지속적인 기술개발로 이루어낸 X-ray의 원천기술" => "지속적인 기술개발로 이루어낸 X-ray의 원천기술",
                        "시닉스레이" => "시닉스레이",
                        "시닉스레이는 지속가능한 고객 만족과 품질을 위해 연구하고 현실화하여 고객의 신뢰를 바탕으로 성장해 나갈 것입니다." => "시닉스레이는 지속가능한 고객 만족과 품질을 위해 연구하고 현실화하여 고객의 신뢰를 바탕으로 성장해 나갈 것입니다.",
				        "또한 현재보단 미래를 앞서 준비하는 기업으로써 품질에 가치를 최우선으로 두고 고객 만족을 위해 최선을 다 할 것입니다." => "또한 현재보단 미래를 앞서 준비하는 기업으로써 품질에 가치를 최우선으로 두고 고객 만족을 위해 최선을 다 할 것입니다.",
                        "제품문의" => "제품문의",
                        "시닉스레이 제품 문의 바로가기" => "시닉스레이 제품 문의 바로가기",
                        "기술지원" => "기술지원",
                        "시닉스레이 기술지원 요청 바로가기" => "시닉스레이 기술지원 요청 바로가기",
                        "찾아오시는 길" => "찾아오시는 길",
                        "시닉스레이 찾아오시는 길" => "시닉스레이 찾아오시는 길",
                    ),
                    "2" => array(
                        "현재보다 미래의 10년을 바라보는 기업" => "A company looking to the future 10 years rather than the present",
                        "지속적인 기술개발로 이루어낸 X-ray의 원천기술" => "Original technology of X-ray achieved through continuous technology development",
                        "시닉스레이" => "Synicsray",
				        "시닉스레이는 지속가능한 고객 만족과 품질을 위해 연구하고 현실화하여 고객의 신뢰를 바탕으로 성장해 나갈 것입니다." => "Synicsray will continue to grow based on customer trust by researching and realizing sustainable customer satisfaction and quality.",
				        "또한 현재보단 미래를 앞서 준비하는 기업으로써 품질에 가치를 최우선으로 두고 고객 만족을 위해 최선을 다 할 것입니다." => "In addition, as a company that thinks ahead and prepares for the future rather than the present, we will do our best for customer satisfaction by putting the value of quality first.",
                        "제품문의" => "Product Inquiry",
                        "시닉스레이 제품 문의 바로가기" => "Shortcut to Synicsray Product Inquiries",
                        "기술지원" => "Technical Support",
                        "시닉스레이 기술지원 요청 바로가기" => "Shortcut to Synicsray Technical Support",
                        "찾아오시는 길" => "Directions",
                        "시닉스레이 찾아오시는 길" => "Directions to Synicsray",
                    ),
                ),
                "company" => array(
                    "1" => array(
                        "회사소개" => "회사소개",
                        "연혁" => "연혁",
                        "조직도" => "조직도",
                        "오시는 길" => "오시는 길",
                        "시닉스레이, 지속적인 기술 개발로 성장하는 미래지향적 기업" => "시닉스레이, 지속적인 기술 개발로 성장하는 미래지향적 기업",
                        "2014년에 설립된 시닉스레이는 지속적인 기술 개발로 X-ray의 원천기술을 겸비한 ODM 제조사입니다. <br>지속가능한 고객 만족과 품질을 위해 공부하고 노력하여 고객의 신뢰를 바탕으로 성장해 나갈 것입니다." => "2014년에 설립된 시닉스레이는 지속적인 기술 개발로 X-ray의 원천기술을 겸비한 ODM 제조사입니다. <br>지속가능한 고객 만족과 품질을 위해 공부하고 노력하여 고객의 신뢰를 바탕으로 성장해 나갈 것입니다.",
				"또한 시닉스레이는 현재의 10년보단 미래의 10년을 미리 고민하고 준비하는 기업으로써 제품 판매에만 주력하지 않고 <br>품질에 가치를 최우선으로 두고 고객 만족을 위해 최선을 다 할 것입니다." => "또한 시닉스레이는 현재의 10년보단 미래의 10년을 미리 고민하고 준비하는 기업으로써 제품 판매에만 주력하지 않고 <br>품질에 가치를 최우선으로 두고 고객 만족을 위해 최선을 다 할 것입니다.",
                    ),
                    "2" => array(
                        "회사소개" => "About Company",
                        "연혁" => "Company History",
                        "조직도" => "Organization Chart",
                        "오시는 길" => "Directions",
                        "시닉스레이, 지속적인 기술 개발로 성장하는 미래지향적 기업" => "Synicsray, a future-oriented company growing through continuous technology development",
                        "2014년에 설립된 시닉스레이는 지속적인 기술 개발로 X-ray의 원천기술을 겸비한 ODM 제조사입니다. <br>지속가능한 고객 만족과 품질을 위해 공부하고 노력하여 고객의 신뢰를 바탕으로 성장해 나갈 것입니다." => "Founded in 2014, Synicsray is an ODM manufacturer with the original technology of X-ray through continuous technology development. <br>We will continue to grow based on customer trust by studying and striving for sustainable customer satisfaction and quality.",
				"또한 시닉스레이는 현재의 10년보단 미래의 10년을 미리 고민하고 준비하는 기업으로써 제품 판매에만 주력하지 않고 <br>품질에 가치를 최우선으로 두고 고객 만족을 위해 최선을 다 할 것입니다." => "In addition, as a company that thinks and prepares for the next 10 years rather than the present 10 years, <br>we will do our best for customer satisfaction by putting the value of quality first rather than focusing on product sales.",
                    ),
                ),
                "history" => array(
                    "1" => array(
                        "회사소개" => "회사소개",
                        "연혁" => "연혁",
                        "조직도" => "조직도",
                        "오시는 길" => "오시는 길",
                    ),
                    "2" => array(
                        "회사소개" => "About Company",
                        "연혁" => "Company History",
                        "조직도" => "Organization Chart",
                        "오시는 길" => "Directions",
                    ),
                ),
                "organization" => array(
                    "1" => array(
                        "회사소개" => "회사소개",
                        "연혁" => "연혁",
                        "조직도" => "조직도",
                        "오시는 길" => "오시는 길",
                    ),
                    "2" => array(
                        "회사소개" => "About Company",
                        "연혁" => "Company History",
                        "조직도" => "Organization Chart",
                        "오시는 길" => "Directions",
                    ),
                ),
                "map" => array(
                    "1" => array(
                        "회사소개" => "회사소개",
                        "연혁" => "연혁",
                        "조직도" => "조직도",
                        "오시는 길" => "오시는 길",
                        "서울 중랑구 봉화산로 123, 신내테크노타운 705호" => "서울 중랑구 봉화산로 123, 신내테크노타운 705호",
                        "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d790.2304740064269!2d127.08722762924504!3d37.60399716122331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357cba4c1a39f615%3A0x804fe7bddff842d3!2zKOyjvCnsi5zri4nsiqTroIjsnbQ!5e0!3m2!1sko!2skr!4v1661227700631!5m2!1sko!2skr" => "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d790.2304740064269!2d127.08722762924504!3d37.60399716122331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357cba4c1a39f615%3A0x804fe7bddff842d3!2zKOyjvCnsi5zri4nsiqTroIjsnbQ!5e0!3m2!1sko!2skr!4v1661227700631!5m2!1sko!2skr",
                    ),
                    "2" => array(
                        "회사소개" => "About Company",
                        "연혁" => "Company History",
                        "조직도" => "Organization Chart",
                        "오시는 길" => "Directions",
                        "서울 중랑구 봉화산로 123, 신내테크노타운 705호" => "#705, Sinnae Techno Town, 123, Bonghwasan-ro, Jungnang-gu, Seoul",
                        "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d790.2304740064269!2d127.08722762924504!3d37.60399716122331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357cba4c1a39f615%3A0x804fe7bddff842d3!2zKOyjvCnsi5zri4nsiqTroIjsnbQ!5e0!3m2!1sko!2skr!4v1661227700631!5m2!1sko!2skr" => "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3160.921761390443!2d127.08558611564882!3d37.604000329738646!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357cba4c1a39f615%3A0x804fe7bddff842d3!2zKOyjvCnsi5zri4nsiqTroIjsnbQ!5e0!3m2!1sen!2skr!4v1661228016868!5m2!1sen!2skr",
                    ),
                ),
                "process" => array(
                    "1" => array(
                        "사업분야" => "사업분야",
                        "생산 프로세스" => "생산 프로세스",
                        "허가 및 인증서" => "허가 및 인증서",
                        "도면배포 / 작업 지시" => "도면배포 / 작업 지시",
                        "원부자재 발주 및 입고" => "원부자재 발주 및 입고",
                        "가공부품 입고" => "가공부품 입고",
                        "입고 검사 및 품질 검사" => "입고 검사 및 품질 검사",
                        "조립" => "조립",
                        "세팅 및 측정" => "세팅 및 측정",
                        "1차 포장" => "1차 포장",
                        "2차 포장" => "2차 포장",
                        "출하 / 납품" => "출하 / 납품",
                    ),
                    "2" => array(
                        "사업분야" => "Business Area",
                        "생산 프로세스" => "Production Process",
                        "허가 및 인증서" => "Licenses and Certificates",
                        "도면배포 / 작업 지시" => "Drawing Distribution / Work Instruction",
                        "원부자재 발주 및 입고" => "Ordering and Warehousing of Raw and Subsidiary Materials",
                        "가공부품 입고" => "Warehousing of Processed Parts",
                        "입고 검사 및 품질 검사" => "Incoming Inspection and Quality Inspection",
                        "조립" => "Assembly",
                        "세팅 및 측정" => "Setting and Measuring",
                        "1차 포장" => "Primary Packing",
                        "2차 포장" => "Secondary Packing",
                        "출하 / 납품" => "Shipment / Delivery",
                    ),
                ),
                "certificate" => array(
                    "1" => array(
                        "사업분야" => "사업분야",
                        "생산 프로세스" => "생산 프로세스",
                        "허가 및 인증서" => "허가 및 인증서",
                        "인증서" => "인증서",
                    ),
                    "2" => array(
                        "사업분야" => "Business Area",
                        "생산 프로세스" => "Production Process",
                        "허가 및 인증서" => "Licenses and Certificates",
                        "인증서" => "Certificates",
                    )
                ),
                "product_list" => array(
                    "1" => array(
                        "제품소개" => "제품소개",
                    ),
                    "2" => array(
                        "제품소개" => "Product Descriptions",
                    )
                ),
                "product" => array(
                    "1" => array(
                        "제품소개" => "제품소개",
                    ),
                    "2" => array(
                        "제품소개" => "Product Descriptions",
                    )
                ),
                "cscenter_prd" => array(
                    "1" => array(
                        "고객센터" => "고객센터",
                        "제품문의" => "제품문의",
                        "기술지원" => "기술지원",
                        "번호" => "번호",
                        "제목" => "제목",
                        "기업/시설명" => "기업/시설명",
                        "작성자" => "작성자",
                        "작성일" => "작성일",
                        "검색" => "검색",
                        "글쓰기" => "글쓰기",
                        "제목을 입력하세요" => "제목을 입력하세요",
                    ),
                    "2" => array(
                        "고객센터" => "Customer Center",
                        "제품문의" => "Product Descriptions",
                        "기술지원" => "Technical Support",
                        "번호" => "Number",
                        "제목" => "Title",
                        "기업/시설명" => "Company/Facility Name",
                        "작성자" => "Writer",
                        "작성일" => "Date of Writing",
                        "검색" => "Search",
                        "글쓰기" => "Writing",
                        "제목을 입력하세요" => "Please enter a title",
                        )
                    ),
                "cscenter_tech" => array(
                    "1" => array(
                        "고객센터" => "고객센터",
                        "제품문의" => "제품문의",
                        "기술지원" => "기술지원",
                        "번호" => "번호",
                        "제목" => "제목",
                        "기업/시설명" => "기업/시설명",
                        "작성자" => "작성자",
                        "작성일" => "작성일",
                        "검색" => "검색",
                        "글쓰기" => "글쓰기",
                        "제목을 입력하세요" => "제목을 입력하세요",
                    ),
                    "2" => array(
                        "고객센터" => "Customer Center",
                        "제품문의" => "Product Descriptions",
                        "기술지원" => "Technical Support",
                        "번호" => "Number",
                        "제목" => "Title",
                        "기업/시설명" => "Company/Facility Name",
                        "작성자" => "Writer",
                        "작성일" => "Date of Writing",
                        "검색" => "Search",
                        "글쓰기" => "Writing",
                        "제목을 입력하세요" => "Please enter a title",
                        )
                ),
                "cscenter_write" => array(
                    "1" => array(
                        "고객센터" => "고객센터",
                        "제품문의" => "제품문의",
                        "기술지원" => "기술지원",
                        "문의하기" => "문의하기",
                        "문의유형" => "문의유형",
                        "문의유형선택" => "문의유형선택",
                        "작성자" => "작성자",
                        "작성자명 입력" => "작성자명 입력",
                        "기업/시설명" => "기업/시설명",
                        "기업명 입력" => "기업명 입력",
                        "이메일" => "이메일",
                        "이메일주소 입력" => "이메일주소 입력",
                        "연락처" => "연락처",
                        "비밀번호" => "비밀번호",
                        "비밀번호 입력" => "비밀번호 입력",
                        "제목" => "제목",
                        "제목을 입력하세요" => "제목을 입력하세요",
                        "문의내용" => "문의내용",
                        "첨부파일" => "첨부파일",
                        "개인정보 처리방침(필수)에 동의합니다." => "개인정보 처리방침(필수)에 동의합니다.",
                        "취소" => "취소",
                        "등록" => "등록",
                    ),
                    "2" => array(
                        "고객센터" => "Customer Center",
                        "제품문의" => "Product Descriptions",
                        "기술지원" => "Technical Support",
                        "문의하기" => "Contact us",
                        "문의유형" => "Inquiry type",
                        "문의유형선택" => "Select inquiry type",
                        "작성자" => "Writer",
                        "작성자명 입력" => "Please enter a writer",
                        "기업/시설명" => "Company/Facility Name",
                        "기업명 입력" => "Please enter a Company/Facility Name",
                        "이메일" => "Email",
                        "이메일주소 입력" => "Please enter a Email",
                        "연락처" => "Phone",
                        "비밀번호" => "Password",
                        "비밀번호 입력" => "Please enter a Password",
                        "제목" => "Title",
                        "제목을 입력하세요" => "Please enter a Title",
                        "문의내용" => "Inquiries",
                        "첨부파일" => "Attachments",
                        "개인정보 처리방침(필수)에 동의합니다." => "I agree to the Privacy Policy (required).",
                        "취소" => "Cancellation",
                        "등록" => "Registration",
                    )
                ),
                "footer" => array(
                    "1" => array(
                        "서울 중랑구 봉화산로 123, 신내테크노타운 705호" => "서울 중랑구 봉화산로 123, 신내테크노타운 705호",
                        "054.733.2192" => "054.733.2192",
                        "synics@syfm.co.kr" => "synics@syfm.co.kr",
                        "Copyright(c) DANCEINSIDESTUDIO all right reserved." => "Copyright(c) DANCEINSIDESTUDIO all right reserved.",
                    ),
                    "2" => array(
                        "서울 중랑구 봉화산로 123, 신내테크노타운 705호" => "#705, Sinnae Techno Town, 123, Bonghwasan-ro, Jungnang-gu, Seoul",
                        "054.733.2192" => "054.733.2192",
                        "synics@syfm.co.kr" => "synics@syfm.co.kr",
                        "Copyright(c) DANCEINSIDESTUDIO all right reserved." => "Copyright(c) DANCEINSIDESTUDIO all right reserved.",
                    )
                ),
            );
            
            return $array[$page][$lang][$str];
        }
    }


?>