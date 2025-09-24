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

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>


</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-contact">
        <div class="container">
            <div class="sub-banner-con">
                <h1>문의 수정</h1>
                <p>문의사항을 수정하거나 삭제할 수 있습니다.</p>
            </div>
        </div>
        <figure><img src="<?php echo $this->project_path;?>img/sub-contact.png"></figure>
    </section>

    <!-- 비밀번호 확인 섹션 (처음에는 이것만 보임) -->
    <section class="password-check-section" id="passwordCheckSection">
        <div class="bd-sm">
            <div class="write-content">
                <div class="password-check-form">
                    <h3>비밀번호 확인</h3>
                    <p>문의글을 수정하거나 삭제하려면 비밀번호를 입력해주세요.</p>
                    
                    <div class="form-group">
                        <label for="checkPassword">비밀번호</label>
                        <input type="password" id="checkPassword" placeholder="비밀번호를 입력하세요" required>
                    </div>
                    
                    <div class="password-actions">
                        <button type="button" class="btn btn-primary" onclick="checkPassword()">확인</button>
                        <button type="button" class="btn btn-secondary" onclick="history.back()">취소</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 수정 섹션 (비밀번호 확인 후 보임) -->
    <section class="edit-section" id="editSection" style="display: none;">
        <div class="bd-sm">
            <div class="write-content">
                <form class="write-form" id="editForm">
                    <input type="hidden" id="inquiryId" name="inquiry_id" value="">
                    
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

                        <!-- 내용 입력 -->
                        <div class="form-group">
                            <label for="content">내용</label>
                            <textarea id="content" name="content" required></textarea>
                        </div>

                        <!-- 기존 파일 목록 -->
                        <div class="form-group" id="existingFilesGroup" style="display: none;">
                            <label>기존 첨부파일</label>
                            <div id="existingFilesList"></div>
                        </div>

                        <!-- 새 파일 첨부 -->
                        <div class="form-group">
                            <label for="file">새 파일 첨부</label>
                            <input type="file" id="file" name="file[]" multiple>
                            <small class="form-help">최대 5개 파일, 각 파일 10MB 이하</small>
                        </div>
                    </div>
                </form>
                
                <!-- 수정/삭제 버튼 -->
                <div class="write-header">
                    <div class="write-actions">
                        <button type="button" class="btn btn-primary" onclick="updateInquiry()">수정하기</button>
                        <button type="button" class="btn btn-danger" onclick="deleteInquiry()">삭제하기</button>
                        <button type="button" class="btn btn-secondary" onclick="history.back()">취소</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include_once $this->project_path."include/footer.php"; ?>

    <script src="<?php echo $this->project_path;?>js/edit.js<?php echo $this->version;?>"></script>
</body>
</html>
