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
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/service.css<?php echo $this->version;?>"/>

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>


</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-service">
        <div class="container">
            <div class="sub-banner-con">
                <h1>서비스 소개</h1>
                <p>스마트신발의 혁신적인 서비스를 소개합니다.</p>
            </div>
        </div>
        <figure><img src="<?php echo $this->project_path;?>img/sub-service.png"></figure>
    </section>

    <!-- 미션 및 비전 섹션 -->
    <section class="mission-vision service-wrap">
        <div class="container bd-lg">
            <h2>미션 및 비전</h2>
            <p class="section-subtitle">스마트신발은 사회적 약자의 안전과 건강을 지키는 혁신적인 솔루션을 제공하여 <br class="pc">모두가 더 나은 삶을 살 수 있도록 돕는 것을 목표로 합니다.</p>
            
            <div class="mission-vision-content">
                <div class="mission-vision-text">
                    <div class="mission">
                        <h3>미션</h3>
                        <p>혁신적인 기술로 사회적 약자의 안전과 건강을 지키고, <br>보호자와의 연결을 강화하여 모두가 안심할 수 있는 환경을 만듭니다.</p>
                    </div>
                    <div class="vision">
                        <h3>비전</h3>
                        <p>모든 사회적 약자가 스마트 기술의 혜택을 누리며 <br>독립적이고 안전한 삶을 영위할 수 있는 세상을 만들어 갑니다.</p>
                    </div>
                </div>
                <div class="mission-vision-image">
                    <img src="<?php echo $this->project_path;?>/img/service-01.png" alt="미션 및 비전 이미지">
                    <img class="m-v-img2" src="<?php echo $this->project_path;?>/img/service-02.png" alt="미션 및 비전 이미지">
                </div>
            </div>
        </div>
    </section>

    <!-- 제품 개발 배경 섹션 -->
    <section class="development-background service-wrap">
        <div class="container bd-lg">
            <h2>제품 개발 배경</h2>
            
            <div class="development-steps">
                <div class="step">
                    <div class="step-title">
                        <div class="step-number">01</div>
                        <h3>문제 인식</h3>
                    </div>
                    <p>사회적 약자의 안전과 건강 관리에 대한 사회적 요구가 증가하고 있으나, 기존 솔루션은 사용성과 효율성이 부족했습니다.</p>
                </div>
                <div class="step">
                    <div class="step-title">
                        <div class="step-number">02</div>
                        <h3>사용자 중심 연구</h3>
                    </div>
                    <p>사회적 약자와 보호자의 실제 니즈를 심층 분석하여 일상생활에 자연스럽게 통합될 수 있는 솔루션을 연구했습니다.</p>
                </div>
                <div class="step">
                    <div class="step-title">
                        <div class="step-number">03</div>
                        <h3>혁신적 솔루션</h3>
                    </div>
                    <p>첨단 센서 기술과 AI를 활용한 스마트신발을 개발하여 사용자의 안전과 건강을 효과적으로 모니터링하는 솔루션을 제공합니다.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 사회적 가치 섹션 -->
    <section class="social-value service-wrap">
        <div class="container bd-lg">
            <div class="social-value-content">
                <div class="social-value-image">
                    <img src="<?php echo $this->project_path;?>/img/service-03.png" alt="사회적 가치 이미지">
                </div>
                <div class="social-value-text">
                    <h2>사회적 가치</h2>
                    <p>스마트신발은 단순한 제품을 넘어 <br>사회적 약자와 그 가족들의 삶의 질을 향상시키는 사회적 가치를 창출합니다.</p>
                    <ul class="value-list">
                        <li><span>01</span> <br>사회적 약자의 독립적인 생활 지원</li>
                        <li><span>02</span> <br>보호자의 심리적 부담 경감</li>
                        <li><span>03</span> <br>사회적 비용 절감 효과</li>
                        <li><span>04</span> <br>지역 사회와의 연계를 통한 안전망 구축</li>
                    </ul>
                    <!-- <span class="btn"><a href="?param=apply">서비스 신청하기 <i class="xi-angle-right-min"></i></a></span> -->
                </div>
            </div>
        </div>
    </section>


    <!-- 파트너십 섹션 -->
    <section class="partnership service-wrap">
        <div class="container bd-lg">
            <h2>파트너십</h2>
            <div class="partners-grid">
                <div class="partner-logo">
                    <img src="<?php echo $this->project_path;?>/img/partner-default.svg" alt="파트너 로고">
                </div>
                <div class="partner-logo">
                    <img src="<?php echo $this->project_path;?>/img/partner-default.svg" alt="파트너 로고">
                </div>
                <div class="partner-logo">
                    <img src="<?php echo $this->project_path;?>/img/partner-default.svg" alt="파트너 로고">
                </div>
                <div class="partner-logo">
                    <img src="<?php echo $this->project_path;?>/img/partner-default.svg" alt="파트너 로고">
                </div>
                <div class="partner-logo">
                    <img src="<?php echo $this->project_path;?>/img/partner-default.svg" alt="파트너 로고">
                </div>
            </div>
        </div>
    </section>

    <?php include_once $this->project_path."include/footer.php"; ?>
</body>
</html>
