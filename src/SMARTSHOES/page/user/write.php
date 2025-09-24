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
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/write.css<?php echo $this->version;?>"/>
    <script src="<?php echo $this->project_path;?>js/write.js<?php echo $this->version;?>"></script>


    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>


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

    <!-- 글쓰기 섹션 -->
    <section class="write-section">
        <div class="bd-sm">
            <div class="write-content">

                    <!-- 글쓰기 폼 -->
                    <form class="write-form" id="form">

                        <div class="form-content">
                            <!-- 제목 입력 -->
                            <div class="form-group">
                                <label for="title">제목</label>
                                <input type="text" id="title" name="title" placeholder="제목을 입력하세요" required>
                            </div>

                            <!-- 작성자 정보 -->
                            <div class="form-grid2">
                                <div class="form-group">
                                    <label for="author">작성자</label>
                                    <input type="text" id="author" name="author" placeholder="작성자를 입력하세요" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">비밀번호</label>
                                    <input type="password" id="password" name="password" placeholder="비밀번호를 입력하세요" required>
                                    <small class="form-help">글 수정/삭제 시 필요합니다</small>
                                </div>
                            </div>

                            <div class="form-grid2">
                                <!-- 카테고리 선택 -->
                                <div class="form-group">
                                    <label for="category">분류</label>
                                    <select id="category" name="category" required>
                                        <option value="">분류를 선택하세요</option>
                                        <option value="product">제품문의</option>
                                        <option value="service">서비스문의</option>
                                        <option value="technical">기술문의</option>
                                        <option value="general">일반문의</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="email">이메일</label>
                                    <input type="email" id="email" name="email" placeholder="이메일을 입력하세요" required>
                                </div>
                            </div>

                            <!-- 내용 입력 (위지윅 에디터) -->
                            <div class="form-group">
                                <label for="content">내용</label>
                                <textarea id="content" name="content" required></textarea>
                            </div>

                            <!-- 파일 첨부 -->
                            <div class="form-group">
                                <label for="file">파일 첨부</label>
                                <input type="file" id="file" name="file[]" multiple>
                                <small class="form-help">최대 5개 파일, 각 파일 10MB 이하</small>
                            </div>

                            <!-- 개인정보 동의 -->
                            <div class="form-group checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="privacy" name="privacy">
                                    개인정보 수집 및 이용에 동의합니다
                                </label>
                                <small class="form-help">
                                    수집된 개인정보는 문의사항 처리 목적으로만 사용되며, 처리 완료 후 즉시 파기됩니다.
                                </small>
                            </div>
                        </div>
                    </form>

                    
                    <!-- 글쓰기 버튼 -->
                    <div class="write-header">
                        <div class="write-actions">
                            <button type="button" class="btn btn-primary" onclick="handleFormSubmit()">등록하기</button>
                            <button type="button" class="btn btn-secondary" onclick="history.back()">취소</button>
                            <!-- 개발용 버튼들 -->
                            <button type="button" class="btn btn-info" onclick="fillTestData()" style="margin-left: 10px;">테스트 데이터 입력</button>
                            <button type="button" class="btn btn-warning" onclick="clearTestData()">폼 초기화</button>
                        </div>
                    </div>

            </div>
        </div>
    </section>

    <?php include_once $this->project_path."include/footer.php"; ?>

</body>
</html>
