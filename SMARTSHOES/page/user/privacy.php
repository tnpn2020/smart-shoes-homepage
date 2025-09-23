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
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/privacy.css<?php echo $this->version;?>"/>

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>
    <script src="<?php echo $this->project_path;?>js/privacy.js<?php echo $this->version;?>"></script>

</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-privacy">
        <div class="container">
            <div class="sub-banner-con">
                <h1 id="pageTitle">약관 및 정책</h1>
            </div>
        </div>
    </section>

    <!-- 약관 내용 섹션 -->
    <section class="privacy-section">
        <div class="container bd-sm">
            <div class="privacy-content">
                <div class="privacy-body">
                    <div class="loading-indicator" id="loading">
                        <p>내용을 불러오는 중입니다...</p>
                    </div>
                    
                    <div class="privacy-text" id="privacyContent" style="display: none;">
                        <!-- 약관 내용이 여기에 로드됩니다 -->
                    </div>
                    
                    <div class="error-message" id="errorMessage" style="display: none;">
                        <p>약관 내용을 불러오는데 실패했습니다.</p>
                        <button type="button" class="retry-btn" onclick="loadPrivacyContent()">다시 시도</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .sub-privacy {
        background: #333;
        color: white;
        padding: 30px 0;
    }

    .sub-privacy h1 {
        font-size: 28px;
        margin: 0;
        font-weight: 500;
    }

    .privacy-section {
        padding: 30px 0;
        background: #fff;
    }

    .privacy-content {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
    }

    .privacy-body {
        padding: 25px 30px;
    }

    .loading-indicator {
        text-align: center;
        padding: 40px 20px;
        color: #666;
    }

    .loading-indicator p {
        font-size: 14px;
        margin: 0;
    }

    .privacy-text {
        line-height: 1.6;
        color: #333;
        font-size: 14px;
    }

    .privacy-text h1, .privacy-text h2, .privacy-text h3 {
        color: #333;
        margin: 20px 0 10px 0;
        font-weight: 600;
    }

    .privacy-text h1 {
        font-size: 18px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 8px;
        margin-bottom: 20px;
    }

    .privacy-text h2 {
        font-size: 16px;
        margin-top: 25px;
    }

    .privacy-text h3 {
        font-size: 14px;
        margin-top: 15px;
    }

    .privacy-text p {
        margin: 10px 0;
        line-height: 1.6;
    }

    .privacy-text ul, .privacy-text ol {
        margin: 10px 0;
        padding-left: 20px;
    }

    .privacy-text li {
        margin: 5px 0;
        line-height: 1.6;
    }

    .privacy-text table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        border: 1px solid #ddd;
        font-size: 13px;
    }

    .privacy-text th, .privacy-text td {
        padding: 8px 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .privacy-text th {
        background: #f8f9fa;
        font-weight: 600;
    }

    .error-message {
        text-align: center;
        padding: 40px 20px;
        color: #666;
    }

    .error-message p {
        font-size: 14px;
        margin: 0 0 15px 0;
        color: #dc3545;
    }

    .retry-btn {
        background: #333;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .retry-btn:hover {
        background: #555;
    }

    /* 반응형 디자인 */
    @media (max-width: 768px) {
        .sub-privacy {
            padding: 20px 0;
        }

        .sub-privacy h1 {
            font-size: 24px;
        }

        .privacy-body {
            padding: 20px;
        }

        .privacy-text {
            font-size: 13px;
        }

        .privacy-text h1 {
            font-size: 16px;
        }

        .privacy-text h2 {
            font-size: 15px;
        }

        .privacy-text h3 {
            font-size: 14px;
        }
    }
    </style>

    <?php include_once $this->project_path."include/footer.php"; ?>
</body>
</html>
