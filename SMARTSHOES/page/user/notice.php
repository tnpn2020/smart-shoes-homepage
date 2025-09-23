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
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/notice.css<?php echo $this->version;?>"/>

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>
    <script src="<?php echo $this->project_path;?>js/notice.js<?php echo $this->version;?>"></script>



</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-promo">
        <div class="container">
            <div class="sub-banner-con">
                <h1>공지사항</h1>
                <p>스마트신발의 최신 소식과 공지를 확인하세요.</p>
            </div>
        </div>
        <figure><img src="<?php echo $this->project_path;?>img/sub-notice.png"></figure>
    </section>


    <!-- 공지사항 섹션 -->
    <section class="notice-section">
        <div class="container bd-lg">
            <!-- 검색 및 필터 -->
            <div class="notice-controls">
                <div class="search-box">
                    <input type="text" placeholder="검색" class="search-input">
                    <button class="search-btn"><i class="xi-search"></i></button>
                </div>
                <div class="category-filter">
                    <select class="filter-select">
                        <option value="">전체</option>
                        <option value="service">서비스 안내</option>
                        <option value="update">업데이트</option>
                        <option value="event">이벤트</option>
                    </select>
                </div>
            </div>

            <!-- 공지사항 목록 (동적으로 로드됨) -->
            <div class="notice-list">
                <!-- JavaScript로 동적 생성 -->
                <div class="loading-notice">
                    <p>공지사항을 불러오는 중...</p>
                </div>
            </div>

            <!-- 페이지네이션 -->
            <div class="paging-wrap">
                <a href="#" class="paging-btn paging-prev">
                    <span><i class="xi-angle-left"></i></span>
                </a>
                <div class="paging-num-box">
                    <a href="#" class="paging-num active">1</a>
                    <a href="#" class="paging-num">2</a>
                    <a href="#" class="paging-num">3</a>
                </div>
                <a href="#" class="paging-btn paging-next">
                    <span><i class="xi-angle-right"></i></span>
                </a>
            </div>
        </div>
    </section>

    <?php include_once $this->project_path."include/footer.php"; ?>
</body>
</html>
