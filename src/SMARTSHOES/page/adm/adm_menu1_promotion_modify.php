<!DOCTYPE html>
<html lang="ko">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>홍보/행사 수정</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>
	
	<style>
	/* 입력 필드 스타일 수정 */
	.insert.insert-input input {
		width: 100%;
		height: 38px;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 0 12px;
		font-size: 14px;
		color: #333;
		background-color: #fff;
		transition: border-color 0.3s ease;
	}
	
	.insert.insert-input input:focus {
		outline: none;
		border-color: #007bff;
		box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
	}
	
	.insert.insert-input input::placeholder {
		color: #6c757d;
	}
	
	/* 텍스트에어리어 스타일 */
	.insert.insert-input textarea {
		width: 100%;
		min-height: 200px;
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 12px;
		font-size: 14px;
		color: #333;
		background-color: #fff;
		resize: vertical;
		font-family: inherit;
		line-height: 1.5;
		transition: border-color 0.3s ease;
	}
	
	.insert.insert-input textarea:focus {
		outline: none;
		border-color: #007bff;
		box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
	}
	
	.insert.insert-input textarea::placeholder {
		color: #6c757d;
	}
	
	/* 파일 입력 필드 스타일 */
	.insert.insert-input input[type="file"] {
		border: none;
		padding: 8px;
		background: transparent;
	}
	
	.insert.insert-input input[readonly] {
		background-color: #f8f9fa;
		cursor: default;
	}
	
	/* 체크박스 컨테이너 스타일 */
	.insert.insert-checkbox {
		padding: 10px 0;
	}
	
	.insert.insert-checkbox label {
		display: flex;
		align-items: center;
		cursor: pointer;
		font-size: 14px;
		color: #333;
		margin: 0;
	}
	
	.insert.insert-checkbox input[type="checkbox"] {
		width: 18px;
		height: 18px;
		margin-right: 10px;
		cursor: pointer;
	}
	
	/* 버튼 스타일 개선 */
	.file-upload.btn-primary {
		background-color: #007bff;
		color: white;
		border: 1px solid #007bff;
		padding: 8px 16px;
		border-radius: 4px;
		text-decoration: none;
		display: inline-block;
		font-size: 14px;
		font-weight: 500;
		transition: all 0.3s ease;
	}
	
	.file-upload.btn-primary:hover {
		background-color: #0056b3;
		border-color: #0056b3;
	}
	
	.file-upload.btn-ghost {
		background-color: transparent;
		color: #6c757d;
		border: 1px solid #6c757d;
		padding: 8px 16px;
		border-radius: 4px;
		text-decoration: none;
		display: inline-block;
		font-size: 14px;
		font-weight: 500;
		transition: all 0.3s ease;
	}
	
	.file-upload.btn-ghost:hover {
		background-color: #6c757d;
		color: white;
	}
	
	/* 메인 버튼 스타일 */
	.btn {
		padding: 10px 20px;
		border-radius: 4px;
		font-size: 14px;
		font-weight: 500;
		text-decoration: none;
		display: inline-block;
		text-align: center;
		transition: all 0.3s ease;
		border: 1px solid transparent;
		cursor: pointer;
	}
	
	.btn.btn-primary {
		background-color: #007bff;
		color: white;
		border-color: #007bff;
	}
	
	.btn.btn-primary:hover {
		background-color: #0056b3;
		border-color: #0056b3;
	}
	
	.btn.btn-secondary {
		background-color: #6c757d;
		color: white;
		border-color: #6c757d;
	}
	
	.btn.btn-secondary:hover {
		background-color: #545b62;
		border-color: #545b62;
	}
	
	.btn.btn-danger {
		background-color: #dc3545;
		color: white;
		border-color: #dc3545;
	}
	
	.btn.btn-danger:hover {
		background-color: #c82333;
		border-color: #bd2130;
	}
	
	/* 이미지 미리보기 스타일 */
	.card {
		border: 1px solid #dee2e6;
		border-radius: 4px;
		overflow: hidden;
		background-color: #fff;
	}
	
	.card-img-top {
		width: 100%;
		height: auto;
		display: block;
	}
	
	.card-body {
		padding: 15px;
	}
	
	.text-muted {
		color: #6c757d;
	}
	
	.text-truncate {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	
	.d-block {
		display: block;
	}
	
	.mt-1 {
		margin-top: 0.25rem;
	}
	
	.mt-2 {
		margin-top: 0.5rem;
	}
	
	.mb-1 {
		margin-bottom: 0.25rem;
	}
	
	.mb-3 {
		margin-bottom: 1rem;
	}
	</style>
	
	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<?php include_once $this->dir."page/adm/inc/summernote.php"; ?>
	<!-- include summernote css/js-->
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu1_promotion_modify.js<?php echo $version;?>"></script>

</head>

<body>
	<div class="wrap">
		<?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
		<?php include_once $dir."page/adm/include/adm_header.php";?>
		<div class="bd">
			<article class="body-container">
				<div class="body-head">
					<h2 id="page_title">홍보/행사 수정</h2>
				</div>
				
				<form id="promotion_form" name="promotion_form" enctype="multipart/form-data" method="post">
					<input type="hidden" name="idx" id="idx" value="">
					
					<div class="row">
						<div class="col-md-12">
							<div class="body-box">
								<div class="box-table-container">
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사명 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" id="event_name" name="event_name" placeholder="행사명을 입력해주세요">
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사기간 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" id="event_period" name="event_period" placeholder="예: 2024년 12월 10일 - 12월 13일">
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사장소 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" id="event_location" name="event_location" placeholder="행사장소를 입력해주세요">
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>수상 내역</p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" id="award_badge" name="award_badge" placeholder="수상 내역을 입력해주세요 (선택사항)">
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>활성화 상태</p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-checkbox">
												<label>
													<input type="checkbox" id="is_active" name="is_active" value="1" checked />
													<span>활성화</span>
												</label>
											</div>
										</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>

					<!-- 이미지 업로드 -->
					<div class="row mt-3">
						<div class="col-md-12">
							<div class="body-box">
								<div class="box-table-container">
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>메인 이미지</p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="file" id="main_image" name="main_image" 
													accept="image/*" style="display: none;">
												<input readonly="true" type="text" id="main_image_name"
													style="margin-right: 10px;" placeholder="메인 이미지를 선택해주세요">
												<label for="main_image" class="file-upload btn-primary"
													style="margin-right: 5px; cursor: pointer;">이미지 선택</label>
												<label class="file-upload btn-ghost" style="cursor: pointer;"
													onclick="clearMainImage()">삭제</label>
											</div>
											<!-- 기존 메인 이미지 표시 -->
											<div id="existing_main_image" class="mt-2" style="display: none;">
												<p class="mb-1"><strong>현재 메인 이미지:</strong></p>
												<img id="existing_main_image_img" src="" alt="현재 메인 이미지" 
													style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
												<br>
												<button type="button" class="btn btn-sm btn-danger mt-2" onclick="deleteExistingMainImage()">기존 이미지 삭제</button>
											</div>
											<!-- 새 메인 이미지 미리보기 -->
											<div id="main_image_preview" class="mt-2" style="display: none;">
												<p class="mb-1"><strong>새 메인 이미지 미리보기:</strong></p>
												<img id="main_image_preview_img" src="" alt="미리보기" 
													style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>서브 이미지</p>
											<small class="text-muted d-block">최대 20개까지 업로드 가능</small>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="file" id="sub_images" name="sub_images[]" 
													accept="image/*" multiple style="display: none;">
												<label for="sub_images" class="file-upload btn-primary"
													style="margin-right: 5px; cursor: pointer;">이미지 선택</label>
												<label class="file-upload btn-ghost" style="cursor: pointer;"
													onclick="clearSubImages()">전체 삭제</label>
											</div>
											<!-- 기존 서브 이미지들 표시 -->
											<div id="existing_sub_images" class="mt-2" style="display: none;">
												<p class="mb-1"><strong>현재 서브 이미지들:</strong></p>
												<div id="existing_sub_images_container" class="row">
													<!-- 기존 서브 이미지들이 여기에 표시됩니다 -->
												</div>
											</div>
											<!-- 새 서브 이미지 미리보기 -->
											<div id="sub_images_preview" class="mt-2" style="display: none;">
												<p class="mb-1"><strong>새 서브 이미지 미리보기:</strong></p>
												<div id="sub_images_container" class="row">
													<!-- 새 서브 이미지 미리보기가 여기에 표시됩니다 -->
												</div>
											</div>
										</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>

					<!-- 행사 내용 -->
					<div class="row mt-3">
						<div class="col-md-12">
							<div class="body-box">
								<div class="box-table-container">
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사 내용 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<textarea id="content" name="content" rows="10" placeholder="행사 내용을 입력해주세요"></textarea>
											</div>
										</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>

					<!-- 버튼 영역 -->
					<div class="row mt-4">
						<div class="col-md-12 text-center">

							<button type="button" class="btn btn-secondary" onclick="goToList()" style="margin-right: 10px;">
								<i class="fas fa-list"></i> 목록
							</button>
							<button type="button" class="btn btn-primary" onclick="submitForm()">
								<i class="fas fa-save"></i> 수정
							</button>
						</div>
					</div>
				</form>
			</article>
		</div>
	</div>

</body>

</html>
