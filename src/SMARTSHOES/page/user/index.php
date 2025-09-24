<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">

	<title>스마트신발</title>

    <!-- META -->
    <?php include_once $this->dir."page/user/inc/meta.php"; ?>

    
    <!-- FONT -->
    <?php echo $this->utillLang->font_link(); ?>
    
    <!-- CSS -->
    <?php include_once $this->project_path."inc/common_css.php"; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/index.css<?php echo $this->version;?>"/>

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>
    <script src="<?php echo $this->project_path;?>js/index.js<?php echo $this->version;?>"></script>


</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 메인 상단 -->
    <section id="home" class="main-top">
        <div class="container bd-lg">
            <div class="main-top-content">
                <div class="main-top-text">
                    <h1>사회적 약자를 위한<br>스마트 신발 서비스</h1>
                    <p>스마트신발은 혁신적인 기술로 <br>사회적 약자의 안전과 건강을 지키는 새로운 솔루션입니다.</p>
                    <div class="main-top-buttons">
                        <a href="?param=service" class="btn btn-primary">서비스 알아보기</a>
                        <a href="?param=contact" class="btn btn-secondary">문의하기</a>
                    </div>
                </div>
                <div class="main-top-video">
                    <div class="video-wrap">
                        <iframe width="100%" height="auto" src="https://www.youtube.com/embed/5jSPTMdigtI?si=5b_2GkS3O91W38gj&mute=1&controls=0&autoplay=1"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 주요 기능 섹션 -->
    <section id="service" class="features">
        <div class="container bd-lg">
            <h2>스마트신발의 주요 기능</h2>
            <p class="section-subtitle">혁신적인 기술로 사용자의 안전과 건강을 지키는 스마트신발의 핵심 기능을 소개합니다</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <span></span>
                    <div class="feature-icon"><img src="<?php echo $this->project_path;?>img/main-icon-01.png"></div>
                    <h3>실시간 위치 추적</h3>
                    <p>내장된 GPS 센서를 통해 사용자의 위치를 실시간으로 추적하고 보호자에게 정보를 제공합니다.</p>
                </div>
                <div class="feature-card">
                    <span></span>
                    <div class="feature-icon"><img src="<?php echo $this->project_path;?>img/main-icon-02.png"></div>
                    <h3>건강 모니터링</h3>
                    <p>걸음 패턴과 활동량을 분석하여 사용자의 건강 상태를 모니터링합니다.</p>
                </div>
                <div class="feature-card">
                    <span></span>
                    <div class="feature-icon"><img src="<?php echo $this->project_path;?>img/main-icon-03.png"></div>
                    <h3>긴급 알림 시스템</h3>
                    <p>비정상적인 활동이 감지되면 보호자에게 즉시 알림을 전송합니다.</p>
                </div>
                <div class="feature-card">
                    <span></span>
                    <div class="feature-icon"><img src="<?php echo $this->project_path;?>img/main-icon-04.png"></div>
                    <h3>양방향 통신</h3>
                    <p>사용자와 보호자 간의 간편한 의사소통을 위한 케어톡 기능을 제공합니다.</p>
                </div>
                <div class="feature-card">
                    <span></span>
                    <div class="feature-icon"><img src="<?php echo $this->project_path;?>img/main-icon-05.png"></div>
                    <h3>데이터 분석</h3>
                    <p>수집된 데이터를 분석하여 사용자 맞춤형 건강 관리 솔루션을 제공합니다.</p>
                </div>
                <div class="feature-card">
                    <span></span>
                    <div class="feature-icon"><img src="<?php echo $this->project_path;?>img/main-icon-06.png"></div>
                    <h3>배터리 관리</h3>
                    <p>저전력 설계로 장시간 사용이 가능하며 배터리 상태를 실시간으로 확인할 수 있습니다.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="main-promo">
        <div class="bd-lg main-promo-wrap">
            <div class="main-promo-text">
                <div>
                    <h4>PROMOTION NEWS</h4>
                    <h3 id="promo-title">2025 스마트 헬스케어 엑스포</h3>
                    <p id="promo-period"><i class="xi-calendar-check"></i>2025년 10월 15일 - 10월 18일</p>
                    <p id="promo-location"><i class="xi-maker"></i>서울 코엑스</p>
                </div>
                <span class="btn"><a href="?param=promotion" id="promo-detail-link">자세히 보기 <i class="xi-angle-right-min"></i></a></span>
            </div>
            <figure id="promo-image">
                <img src="<?php echo $this->project_path;?>img/sub-notice.png" id="promo-main-img">
            </figure>
        </div>
    </section>



    <?php include_once $this->project_path."include/footer.php"; ?>

    <div style="display:none">
        
        <!-- 팝업 오토뷰 -->
        <div class="popup_con" data-copy="popup_copy">
            <div class="popup_img">
                <img data-attr="popup_img" id="popup_img" src="" alt="">
            </div>
            <div class="pop_btn">
                <div class="chk_con">
                    <input type="checkbox" name="popup" id="today" data-attr="checkbox">
                    <label for="today" data-attr="label">오늘보지않기</label>
                </div>

                <button data-attr="close">닫기</button>
            </div>
        </div>
    </div>
</body>
</html>
