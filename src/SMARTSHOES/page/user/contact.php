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
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/contact.css<?php echo $this->version;?>"/>
    

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>
    <script src="<?php echo $this->project_path;?>js/contact.js<?php echo $this->version;?>"></script>
    


</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-contact">
        <div class="container">
            <div class="sub-banner-con">
                <h1>문의하기</h1>
                <p>스마트신발에 대한 문의사항을 남겨주세요.</p>
            </div>
        </div>
        <figure><img src="<?php echo $this->project_path;?>img/sub-contact.png"></figure>
    </section>

    <!-- 게시판 섹션 -->
    <section class="board-section">
        <div class="container bd-lg">
            <div class="board-header">
                <!-- 검색 및 필터 -->
                <div class="board-search">
                    <div class="search-form">                        
                        <select class="filter-select">
                            <option value="title">제목</option>
                            <option value="content">내용</option>
                            <option value="author">작성자</option>
                        </select>
                        <div class="search-box">
                            <input type="text" placeholder="검색" class="search-input">
                            <button class="search-btn"><i class="xi-search"></i></button>
                        </div>
                    </div>
                </div>
                
                <div class="board-actions">
                    <a href="?param=write" class="btn btn-primary">글쓰기</a>
                </div>
                
            </div>


            <!-- 게시판 목록 -->
            <div class="board-list">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th class="col-number">번호</th>
                            <th class="col-category">분류</th>
                            <th class="col-title">제목</th>
                            <th class="col-author">작성자</th>
                            <th class="col-date">작성일</th>
                            <th class="col-status">상태</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-number">4</td>
                            <td class="col-category"><span class="category-badge product">제품문의</span></td>
                            <td class="col-title">
                                <a href="javascript:void(0)" onclick="showPasswordModal(5)">스마트신발 배터리 수명에 대해 문의드립니다</a>
                                <span class="comment-count">[2]</span>
                            </td>
                            <td class="col-author">김철수</td>
                            <td class="col-date">2025.01.15</td>
                            <td class="col-status"><span class="status-badge answered">답변완료</span></td>
                        </tr>
                        <tr>
                            <td class="col-number">3</td>
                            <td class="col-category"><span class="category-badge service">서비스문의</span></td>
                            <td class="col-title">
                                <a href="javascript:void(0)" onclick="showPasswordModal(4)">서비스 신청 절차를 알고 싶습니다</a>
                            </td>
                            <td class="col-author">이영희</td>
                            <td class="col-date">2025.01.14</td>
                            <td class="col-status"><span class="status-badge answered">답변완료</span></td>
                        </tr>
                        <tr>
                            <td class="col-number">2</td>
                            <td class="col-category"><span class="category-badge general">일반문의</span></td>
                            <td class="col-title">
                                <a href="javascript:void(0)" onclick="showPasswordModal(2)">홈페이지 개선 제안</a>
                            </td>
                            <td class="col-author">최지영</td>
                            <td class="col-date">2025.01.12</td>
                            <td class="col-status"><span class="status-badge answered">답변완료</span></td>
                        </tr>
                        <tr>
                            <td class="col-number">1</td>
                            <td class="col-category"><span class="category-badge product">제품문의</span></td>
                            <td class="col-title">
                                <a href="javascript:void(0)" onclick="showPasswordModal(1)">스마트신발 사이즈 문의</a>
                            </td>
                            <td class="col-author">정수민</td>
                            <td class="col-date">2025.01.11</td>
                            <td class="col-status"><span class="status-badge answered">답변완료</span></td>
                        </tr>
                    </tbody>
                </table>
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
