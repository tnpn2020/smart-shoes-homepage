
<meta name="naver-site-verification" content="2e4dbf6ef28a4577a490c0b40b5230cb38423e58" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>
    var user_idx = "<?php echo $is_login; ?>";
    obj.link = <?php echo json_encode($this->file_path->get_path(),JSON_UNESCAPED_UNICODE);?>;
    var sub_link = <?php echo json_encode($this->sub_file_path->get_path(),JSON_UNESCAPED_UNICODE);?>;
    obj.link = Object.assign(obj.link, sub_link);
</script>
<script type="text/javascript" src="<?php echo $this->project_path;?>/js/header.js<?php echo $this->version;?>"></script>
<div class="loading"><img class="loading_img" src ="https://lbcontents.s3.ap-northeast-2.amazonaws.com/images/IPIACOSMETIC/Spinner.gif"></div>

<!-- 헤더 -->
<header class="header">
    <div class="bd-lg">
        <div class="header-content">
            <div class="logo">
                <a href="?param=index"><h1><img src="<?php echo $this->project_path;?>img/logo.png"></h1></a>
            </div>
            <nav class="nav">
                <ul class="nav-list">
                    <li><a href="?param=index" <?php echo (!isset($_GET['param']) || $_GET['param'] == 'index') ? 'class="active"' : ''; ?>>홈</a></li>
                    <li><a href="?param=service" <?php echo (isset($_GET['param']) && $_GET['param'] == 'service') ? 'class="active"' : ''; ?>>서비스 소개</a></li>
                    <!-- <li><a href="?param=care" <?php echo (isset($_GET['param']) && $_GET['param'] == 'care') ? 'class="active"' : ''; ?>>케어 서비스</a></li> -->
                    <li><a href="?param=promotion" <?php echo (isset($_GET['param']) && $_GET['param'] == 'promotion') ? 'class="active"' : ''; ?>>홍보관</a></li>
                    <li><a href="?param=notice" <?php echo (isset($_GET['param']) && $_GET['param'] == 'notice') ? 'class="active"' : ''; ?>>공지사항</a></li>
                    <li><a href="?param=contact" <?php echo (isset($_GET['param']) && $_GET['param'] == 'contact') ? 'class="active"' : ''; ?>>문의하기</a></li>
                    <div class="header-btn">
                        <li><a href="?param=apply" class="btn-apply <?php echo (isset($_GET['param']) && $_GET['param'] == 'apply') ? 'active' : ''; ?>">신청서</a></li>
                        <li><a href="" class="btn-apply <?php echo (isset($_GET['param']) && $_GET['param'] == 'apply') ? 'active' : ''; ?>">서비스 연결</a></li>
                    </div>
                </ul>
            </nav>
            <div class="menu-toggle" id="toggleBtn">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    
    <!-- 모바일 메뉴 -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-content">
            <ul class="mobile-nav-list">
                <li><a href="?param=index" <?php echo (!isset($_GET['param']) || $_GET['param'] == 'index') ? 'class="active"' : ''; ?>>홈</a></li>
                <li><a href="?param=service" <?php echo (isset($_GET['param']) && $_GET['param'] == 'service') ? 'class="active"' : ''; ?>>서비스 소개</a></li>
                <li><a href="?param=promotion" <?php echo (isset($_GET['param']) && $_GET['param'] == 'promotion') ? 'class="active"' : ''; ?>>홍보관</a></li>
                <li><a href="?param=notice" <?php echo (isset($_GET['param']) && $_GET['param'] == 'notice') ? 'class="active"' : ''; ?>>공지사항</a></li>
                <li><a href="?param=contact" <?php echo (isset($_GET['param']) && $_GET['param'] == 'contact') ? 'class="active"' : ''; ?>>문의하기</a></li>
                <li><a href="?param=apply" class="btn-apply <?php echo (isset($_GET['param']) && $_GET['param'] == 'apply') ? 'active' : ''; ?>">신청서</a></li>
                <li><a href="?param=apply" class="btn-apply <?php echo (isset($_GET['param']) && $_GET['param'] == 'apply') ? 'active' : ''; ?>">서비스 연결</a></li>
            </ul>
        </div>
    </div>
    
    <!-- 모바일 메뉴 오버레이 -->
    <div class="mobile-menu-overlay" id="blank"></div>
</header>
