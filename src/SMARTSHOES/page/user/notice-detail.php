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
    
    <style>
    /* 공지사항 상세 페이지 스타일 */
    .notice-detail-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    
    .post-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 30px;
        border-bottom: 3px solid #dee2e6;
    }
    
    .post-title {
        margin-bottom: 20px;
    }
    
    .notice-badges {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .notice-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .notice-badge.important {
        background: #ff4757;
        color: white;
    }
    
    .notice-badge.service {
        background: #70a1ff;
        color: white;
    }
    
    .notice-badge.update {
        background: #5352ed;
        color: white;
    }
    
    .notice-badge.event {
        background: #ffa502;
        color: white;
    }
    
    .notice-badge.general {
        background: #7bed9f;
        color: white;
    }
    
    #notice-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
        line-height: 1.3;
        word-break: keep-all;
    }
    
    .post-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }
    
    #notice-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }
    
    .post-content {
        padding: 40px 30px;
    }
    
    #notice-content {
        font-size: 16px;
        line-height: 1.8;
        color: #2c3e50;
        margin-bottom: 30px;
    }
    
    #notice-content p {
        margin-bottom: 16px;
    }
    
    #notice-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        margin: 20px 0;
    }
    
    .attachments-section {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 15px 20px;
        margin-top: 30px;
    }
    
    .attachment-header {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .attachment-label {
        color: #495057;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .file-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        flex: 1;
    }
    
    .file-list li {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px 12px;
        transition: all 0.2s ease;
        list-style: none;
    }
    
    .file-list li:hover {
        border-color: #007bff;
        background: #f8f9fa;
    }
    
    .file-link {
        color: #495057;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: block;
    }
    
    .file-link:hover {
        color: #007bff;
        text-decoration: underline;
    }
    
    .file-size {
        color: #6c757d;
        font-size: 12px;
        margin-left: auto;
    }
    
    .post-actions {
        padding: 20px 30px;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    

    
    .loading-detail, .error-detail {
        text-align: center;
        padding: 60px 30px;
        color: #6c757d;
    }
    
    .error-detail {
        color: #dc3545;
    }
    
    @media (max-width: 768px) {
        .notice-detail-container {
            margin: 10px;
            border-radius: 8px;
        }
        
        .post-header {
            padding: 20px;
        }
        
        #notice-title {
            font-size: 24px;
        }
        
        .post-content {
            padding: 25px 20px;
        }
        
        .post-actions {
            padding: 15px 20px;
        }
        
        .attachments-section {
            margin-top: 20px;
            padding: 12px 15px;
        }
        
        .attachment-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .file-list {
            gap: 4px;
        }
    }
    </style>

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>
    <script src="<?php echo $this->project_path;?>js/notice-detail.js<?php echo $this->version;?>"></script>


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
            
        <!-- 공지사항 상세 섹션 -->
        <section class="view-section">
            <div class="bd-sm">
                <div class="notice-detail-container">
                    <!-- 로딩 메시지 -->
                    <div id="loading-detail" class="loading-detail">
                        <p>공지사항을 불러오는 중...</p>
                    </div>
                    
                    <!-- 에러 메시지 -->
                    <div id="error-detail" class="error-detail" style="display: none;">
                        <p id="error-message">오류가 발생했습니다.</p>
                        <div class="post-actions">
                            <div class="action-buttons">
                                <a href="?param=notice" class="btn btn-secondary">목록으로</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 공지사항 상세 템플릿 -->
                    <div id="notice-detail-template" style="display: none;">
                        <div class="post-header">
                            <div class="post-title">
                                <div class="notice-badges">
                                    <div id="notice-important-badge" class="notice-badge important" style="display: none;">중요</div>
                                    <div id="notice-badge" class="notice-badge">
                                        <span id="notice-category"></span>
                                    </div>
                                </div>
                                <h2 id="notice-title"></h2>
                            </div>
                            
                            <div class="post-meta">
                                <div class="post-info">
                                    <span id="notice-date">작성일: </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="post-content">
                            <div id="notice-content" class="content-body">
                                <!-- 내용이 여기에 들어감 -->
                            </div>
                            
                            <!-- 첨부파일 섹션 (내용 하단) -->
                            <div id="notice-attachments" class="attachments-section" style="display: none;">
                                <div class="attachment-header">
                                    <span class="attachment-label">첨부파일</span>
                                    <div id="notice-files" class="file-list">
                                        <!-- 파일 목록이 여기에 들어감 -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 목록으로 버튼 -->
                        <div class="post-actions">
                            <div class="action-buttons">
                                <a href="?param=notice" class="btn btn-secondary">목록으로</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        </div>
    </section>

    <?php include_once $this->project_path."include/footer.php"; ?>
</body>
</html>
