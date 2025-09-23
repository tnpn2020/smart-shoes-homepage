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
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/promotion.css<?php echo $this->version;?>"/>

    
	    <!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>
    <script>
        obj.link = <?php echo json_encode($this->file_path->get_path(),JSON_UNESCAPED_UNICODE);?>;
    </script>
    <script type="text/javascript" src="<?php echo $this->project_path;?>js/promotion.js<?php echo $this->version;?>"></script>

</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-promo">
        <div class="container">
            <div class="sub-banner-con">
                <h1>홍보관</h1>
                <p>스마트신발이 참가한 다양한 전시회와 행사를 소개합니다.</p>
            </div>
        </div>
        <figure><img src="<?php echo $this->project_path;?>img/sub-promo.png"></figure>
    </section>

    <!-- 전시회 섹션 -->
    <section class="promos">
        <div class="container bd-lg">
            <!-- 동적으로 생성되는 홍보/행사 목록 -->
        </div>
    </section>

    <?php include_once $this->project_path."include/footer.php"; ?>
</body>
</html>
<?php
// 나머지 정적 콘텐츠 제거를 위해 여기서 종료
exit;
?>

            <div class="promo-item">
                <div class="promo-header">
                    <div class="promo-title">
                        <h2>2025 스마트 헬스케어 엑스포</h2>
                        <div class="award-badge">혁신상 수상</div>
                    </div>
                    <div class="promo-meta">
                        <span class="date"><i class="xi-calendar-check"></i>2023년 10월 15일 - 10월 18일</span>
                        <span class="location"><i class="xi-maker"></i>서울 코엑스</span>
                    </div>
                </div>
                <div class="promo-content">
                    <div class="promo-image-wrap">
                        <div class="promo-image-view">
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                        </div>
                    </div>

                    <div class="promo-text">
                        <p>관리자 텍스트 영역</p>
                    </div>
                </div>
                <div class="slide-nav-wrap">
                    <div class="promo-image-nav">
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                    </div>
                    <div class="slide_btn prevArrow"><i class="xi-angle-left-thin"></i></div>
                    <div class="slide_btn nextArrow"><i class="xi-angle-right-thin"></i></div>
                </div>
            </div>

            
            <div class="promo-item">
                <div class="promo-header">
                    <div class="promo-title">
                        <h2>2025 스마트 헬스케어 엑스포</h2>
                        <div class="award-badge">혁신상 수상</div>
                    </div>
                    <div class="promo-meta">
                        <span class="date"><i class="xi-calendar-check"></i>2023년 10월 15일 - 10월 18일</span>
                        <span class="location"><i class="xi-maker"></i>서울 코엑스</span>
                    </div>
                </div>
                <div class="promo-content">
                    <div class="promo-image-wrap">
                        <div class="promo-image-view">
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                            <div class="gallery-item">
                                <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                            </div>
                        </div>
                    </div>

                    <div class="promo-text">
                        <p>관리자 텍스트 영역</p>
                        <p>관리자 텍스트 영역</p>
                        <p>관리자 텍스트 영역</p>
                        <p>관리자 텍스트 영역</p>
                        <p>관리자 텍스트 영역</p>
                        <p>관리자 텍스트 영역</p>
                    </div>
                </div>
                <div class="slide-nav-wrap">
                    <div class="promo-image-nav">
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/expo-default.svg" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                        <div class="gallery-item">
                            <img src="<?php echo $this->project_path;?>/img/sub-contact.png" alt="2023 스마트 헬스케어 엑스포 이미지 3">
                        </div>
                    </div>
                    <div class="slide_btn prevArrow"><i class="xi-angle-left-thin"></i></div>
                    <div class="slide_btn nextArrow"><i class="xi-angle-right-thin"></i></div>
                </div>
            </div>

        </div>
    </section>

    <?php include_once $this->project_path."include/footer.php"; ?>


<script>
    
    $('.promo-image-view').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.promo-image-nav'
    });

    $('.promo-image-nav').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '.promo-image-view',
        focusOnSelect: true,
        prevArrow : $('.prevArrow'), 
        nextArrow : $('.nextArrow')
    });
</script>


</body>
</html>
