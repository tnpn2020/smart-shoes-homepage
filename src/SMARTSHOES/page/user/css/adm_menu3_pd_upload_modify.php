<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>제품등록</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<?php include_once $this->dir."page/adm/inc/summernote.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu3_pd_upload_modify.js<?php echo $version;?>"></script>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/jscolor.js<?php echo $version;?>"></script>
	<style>
		.select-list{height:102px !important;}
		.note-editor .note-editing-area p{line-height: 0;}
	</style>

</head>
<body>
	<div class="wrap">
        <?php include_once $dir."page/adm/include/adm_menu3_aside.php";?>
        <?php include_once $dir."page/adm/include/adm_header.php";?>
			<div class="bd">
				<article class="body-container">
					<div class="body-head"><h2>제품수정</h2></div>
					<form class="form" id="form" onsubmit='return false;'>
						<div class="row">
							<!-- 좌 -->
							<div class="col-md-7">
								<div class="body-box mt-1">
									<div class="box-tit mb-1"><h3>기본정보</h3></div>
									<div class="box-table-container">

										<!-- <dl class="box-tbody">
											<dt class="box-th box-head">
												<p>카테고리 구분</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label">국내
																<input type="radio" name = "category_flag" checked/>
																<span class="checkmark"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label">글로벌
																<input type="radio" name = "category_flag"/>
																<span class="checkmark"></span>
															</label>
														</div>
													</div>
												</div>
											</dd>
										</dl> -->

										<!-- 언어별 설정에서 사용함으로 주석처리 -->
										<!-- <dl class="box-tbody">
											<dt class="box-th box-head">
												<p>상태</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label" for="normal_state">정상
																<input type="radio" id="normal_state" value="1" name="state" checked/>
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="soldout_state">품절
																<input type="radio" id="soldout_state" value="2" name="state"/>
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="hide_state">숨김
																<input type="radio" id="hide_state" value="3" name="state"/>
																<span class="checkmark radio"></span>
															</label>
														</div>
													</div>
												</div>
											</dd>
										</dl> -->
										<dl class="box-tbody" style="display:none;">
											<dt class="box-th box-head">
												<p>노출위치</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label">제품목록
																<input type="checkbox"/>
																<span class="checkmark"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label">베스트셀러
																<input type="checkbox"/>
																<span class="checkmark"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label">인기제품
																<input type="checkbox"/>
																<span class="checkmark"></span>
															</label>
														</div>
													</div>
												</div>
											</dd>
										</dl>
										<!-- <dl class="box-tbody">
											<dt class="box-th box-head">
												<p>제품명</p>
											</dt>
											<dd class="box-td">
												<div class="insert insert-input">
													<input type="text" class="input-sm"/>
												</div>
											</dd>
										</dl> -->
										<dl class="box-tbody" id= "category_wrap">
											<dt class="box-th box-head">
												<p>카테고리</p>
											</dt>
											<dd class="box-td">
												<div class="insert-wrap">
													<div class="insert insert-select">
														<select class="select-custom" type="text" id="main_category" name="main_category">
															<option>대분류</option>
														</select>
													</div>
													<div class="insert insert-select" id="category1_view" style="display:none;">
														<select class="select-custom" type="text" id="category_1" name="category_1" >
															<option>중분류</option>
														</select>
													</div>
													<div class="insert insert-select mt-1" id="category2_view" style="display:none;">
														<select class="select-custom" type="text" id="category_2" name="category_2" >
															<option>소분류</option>
														</select>
													</div>
													<div class="insert insert-select mt-1" id="category3_view" style="display:none;">
														<select class="select-custom" type="text" id="category_3" name="category_3">
															<option>세분류</option>
														</select>
													</div>
													<div class="insert-wrap mt-1">
														<div class="insert insert-input-btn"><input class="btn-primary btn-32" type="button" value="카테고리 등록" id="btn_category_add"></div>
													</div>
												</div>
												<div class="h-line mt-1"></div>
												<div class="insert insert-select">
													<select class="select-list" size="4" id="select_category">
														
													</select>
												</div>
												<div class="insert-wrap mt-1">
													<div class="insert insert-input-btn"><input class="btn-default btn-32" type="button" value="선택삭제" onclick="delete_category()"></div>
												</div>
												
											</dd>
										</dl>


										<!-- <dl class="box-tbody">
											<dt class="box-th box-head">
												<p>추가분류</p>
											</dt>
											<dd class="box-td">
												<div class="insert-wrap over">
													<div class="insert insert-chk">
														<label class="check_label">BEST
															<input type="checkbox" value="1" name="best" id="best"/>
															<span class="checkmark"></span>
														</label>
													</div>
													<div class="insert insert-chk">
														<label class="check_label">HOT
															<input type="checkbox" value="1" name="hot" id="hot"/>
															<span class="checkmark"></span>
														</label>
													</div>
													<div class="insert insert-chk">
														<label class="check_label">NEW
															<input type="checkbox" value="1" name="new" id="new"/>
															<span class="checkmark"></span>
														</label>
													</div>
													<div class="insert insert-chk">
														<label class="check_label">RECOMMEND
															<input type="checkbox" value="1" name="recommend" id="recommend"/>
															<span class="checkmark"></span>
														</label>
													</div>
												</div>
											</dd>
										</dl>
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>검색키워드</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<input type="text" class="input-sm" id="keyword" name="keyword"/>
												</div>
												<p class="mt-1 xsmall">','으로 구분하여 입력하세요.</p>
											</dd>
										</dl> -->

										
										<!-- <dl class="box-tbody">
											<dt class="box-th box-head">
												<p>상태</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label" for="state_normal">노출
																<input type="radio" value="1" id="state_normal" name="state" checked/>
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="state_sold_out">품절
																<input type="radio" value="2" id="state_sold_out" name="state"/>
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="state_hide">숨김
																<input type="radio" value="3" id="state_hide" name="state"/>
																<span class="checkmark radio"></span>
															</label>
														</div>
													</div>
												</div>
											</dd>
										</dl> -->
									</div>
								</div>
								<!-- 기본정보 // -->
							</div>
							<!-- 우 -->
							<div class="col-md-5" style="display:none">
								<div class="body-box mt-1">
									<div class="box-tit mb-1"><h3>판매정보</h3></div>
									<div class="box-table-container" id = "price_wrap">
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>판매가</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<input type="text" class="input-xs" id="price" name="price" onkeyup="lb.validate_number(this)"/>
													<span class="ml-1">원</span>
												</div>
											</dd>
										</dl>
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>제품 적립금</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<input type="text" class="input-xs" id="point_discount" name="point_discount" maxlength="2" onkeyup="lb.validate_number(this)"/>
													<span class="ml-1">%</span>
												</div>
											</dd>
										</dl>
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>할인</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label" for="no_discount">할인 없음
																<input type="radio" id="no_discount" value="0" name="is_discount" onclick="change_discount_view(this.value)" checked/>
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="percent_discount">할인율로 표기
																<input type="radio" id="percent_discount" value="1" name="is_discount" onclick="change_discount_view(this.value)" />
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="number_discount">금액으로 표기
																<input type="radio" id="number_discount" value="2" name="is_discount" onclick="change_discount_view(this.value)" />
																<span class="checkmark radio"></span>
															</label>
														</div>
													</div>
												</div>
											</dd>
											
										</dl>
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>할인율</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<input type="text" class="input-xs" name="discount_percent" id="discount_percent" onkeyup="lb.validate_number(this)" disabled/><span class="ml-1">%</span>
												</div>
											</dd>
										</dl>
										<!-- 할인율 // -->
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>할인금액</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<input type="text" class="input-xs" name="discount_price" id="discount_price" onkeyup="lb.validate_number(this)" disabled/><span class="ml-1">원</span>
												</div>
											</dd>
										</dl>
										<!-- 할인금액 // -->
										<!-- 할인율 or 할인금액 -->
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>재고관리</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label" for="no_stock">사용안함
																<input type="radio" id="no_stock" value="0" name="is_stock" onclick="return(false);" />
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="use_stock">단일 재고 사용
																<input type="radio" id="use_stock" value="1" name="is_stock" onclick="return(false);" />
																<span class="checkmark radio"></span>
															</label>
														</div>
													</div>
													<div class="insert insert-chk">
														<label class="check_label" for="use_option_stock">옵션 재고 사용
															<input type="radio" id="use_option_stock" value="2" name="is_stock" onclick="return(false);" />
															<span class="checkmark radio"></span>
														</label>
													</div>
													<div class="insert insert-chk">
														- 재고관리는 변경 할 수 없습니다.<br/><br/>
													</div>
												</div>
											</dd>
										</dl>
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>재고개수</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<input type="text" class="input-xs" name="total_stock" id="total_stock" onkeyup="lb.validate_number(this)" disabled/>
												</div>
												<div class="insert insert-chk">
													사용하지 않을경우 무제한
												</div>
											</dd>
										</dl>
										<!-- <dl class="box-tbody">
											<dt class="box-th box-head">
												<p>판매설정</p>
											</dt>
											<dd class="box-td">
												<div class="img-area-container cf">
													<div class="insert insert-img">
														<label class="check_label img-area">
															<input type="checkbox">
															<div class="img-area-bg">
																<p class="icon"><img src="<?php echo $this->project_admin_path;?>images/i-best01.png" alt="best01"></p>
															</div>
														</label>
													</div>
													<div class="insert insert-img">
														<label class="check_label img-area">
															<input type="checkbox">
															<div class="img-area-bg">
																<p class="icon"><img src="<?php echo $this->project_admin_path;?>images/i-best01.png" alt="best01"></p>
															</div>
														</label>
													</div>
												</div>
											</dd>
										</dl> -->
									</div>
								</div>
								<!-- 판매정보 // -->
							</div>
							<div class="col-md-5">
								<div class="body-box mt-1">
									<div class="box-tit mb-1"><h3>추가정보<input type="button" value="줄 제거" onclick="delete_row()"><input type="button" value="줄 추가" onclick="add_row()"></h3></div>
									<div class="box-table-container">
										<dl class="box-tbody">
											<table id="spec_table" class="box-p"  style="width:100%;">
												<tr>
													<td style="width:40%">구분</td>
													<td style="width:60%">데이터</td>
												</tr>
											</table>
										</dl>
									</div>
								</div>
								<!-- 판매정보 // -->
							</div>
						</div>
						<div class="h-line mt-3 mb-3"></div>
						<div class="out-tab-container st2">
							<ul data-wrap="lang_btn_wrap" id="lang_btn_wrap">
								<!-- <li class="current"><a>한국어</a></li>
								<li><a>영어</a></li>
								<li><a>중국어</a></li> -->
							</ul>
						</div>
						<!-- 정보 // -->
						<div class="row" data-wrap="lang_input_wrap" id="lang_input_wrap">
							
						</div>
					</form>
					<div class="btn-container align-right mt-3">
						<button type="button" class="btn btn-ghost" onclick="delete_product()">삭제</button>
						<button type="button" class="btn btn-ghost" onclick="move_product_list()">취소</button>
						<button type="button" class="btn btn-primary ml-1" onclick="request_save()">저장하기</button>
					</div>
				</article>
			</div>
	</div>
<div style="display:none">
		<li class="" data-copy="lang_btn_copy"><a data-attr="lang">한국어</a></li> <!-- 언어 버튼, 활성화면 li class = current--> 

		<!-- 언어별 데이터 input  -->
		<div data-copy="lang_input_copy" style="display:none;">
			<div class="row">
				<div class="col-md-7">
					<div class="body-box mt-3">
						<div class="box-tit mb-2"><h3>제품정보</h3></div>
						<div class="box-table-container">
							<dl class="box-tbody">
								<dt class="box-th box-head">
									<p>상태</p>
								</dt>
								<dd class="box-td">
									<div class="insert">
										<div class="insert-wrap">
											<div class="insert insert-chk">
												<label class="check_label" for="1" data-attr="condition_1_label">표시
													<input type="radio" value="1"  data-attr="condition_1" checked/>
													<span class="checkmark radio"></span>
												</label>
											</div>
											<div class="insert insert-chk">
												<label class="check_label" for="2" data-attr="condition_2_label">미표시
													<input type="radio" value="2"  data-attr="condition_2"/>
													<span class="checkmark radio"></span>
												</label>
											</div>
										</div>
									</div>
								</dd>
							</dl>
							<dl class="box-tbody">
								<dt class="box-th box-head">
									<p>제품명</p>
								</dt>
								<dd class="box-td">
									<div class="insert insert-input">
										<input type="text" class="input-sm" data-attr="product_name"/>
									</div>
								</dd>
							</dl>
							<dl class="box-tbody">
								<dt class="box-th box-head">
									<p>약칭</p>
								</dt>
								<dd class="box-td">
									<div class="insert insert-input">
										<input type="text" class="input-sm" data-attr="short_name" placeholder="제품 리스트페이지에서 보여줄 이름입니다."/>
									</div>
								</dd>
							</dl>
							<dl class="box-tbody">
								<dt class="box-th box-head">
									<p>추가정보</p>
								</dt>
								<dd class="box-td">
									<div class="insert insert-input">
										<input type="text" class="input-sm" data-attr="product_info" placeholder="제품상세페이지 제품명 밑에 들어가는 문구입니다."/>
									</div>
								</dd>
							</dl>
							<dl class="box-tbody" style="display:none">
								<dt class="box-th box-head">
									<p>제품코드</p>
								</dt>
								<dd class="box-td">
									<div class="insert insert-input">
										<input type="text" class="input-sm" data-attr="product_code"/>
									</div>
								</dd>
							</dl>
							<dl class="box-tbody" style="display:none">
								<dt class="box-th box-head">
									<p>제품요약정보</p>
								</dt>
								<dd class="box-td">
									<div class="insert insert-input">
										<input type="text" class="input-sm" data-attr="meta_description"/>
										<p style="margin-top: 5px; font-size: 14px;">※ SNS 공유시 노출되는 제품요약정보입니다.</p>
									</div>
								</dd>
							</dl>
							<dl class="box-tbody">
								<dt class="box-th box-head">
									<p>썸네일 이미지</p>
								</dt>
								<dd class="box-td">
									<!-- 이미지 추가 버튼 -->
									<div class="img-upload img-upload-main" style="overflow: hidden;">
										<span class="btn-wrap">
											<button class="btn-img-upload" ><strong></strong></button>
											<input type="file" id="upload" data-attr="thumnail_file">
										</span> 				
										<label for="upload" data-attr="thumnail_file_label"></label>
									</div>
									<!-- 이미지 추가 버튼 // -->
									<!-- 이미지가 첨부될 시 옆에 나열됩니다 -->
									<div data-attr="thumnail_wrap" style = "display:inline-block">
										<!-- <div class="img-upload" style="overflow: hidden;" >
											<img src="<?php echo $this->project_path;?>/images/sample.png" onerror="this.style.display='none'" alt="img_upload"/>
											<button class="delete-btn" type="button"></button>
										</div> -->
									</div>
									<!-- 이미지 1 // -->
									<p class="xsmall">jpg, png, gif 형식의 파일확장자<br>4MB 이하의 이미지 1장까지 첨부 가능(첨부시 기존파일삭제)</p>
									<p>※ 권장사이즈는 가로 : 376px 세로 : 376px 입니다.</p>
								</dd>
							</dl>
							<dl class="box-tbody">
								<dt class="box-th box-head">
									<p>제품 이미지</p>
								</dt>
								<dd class="box-td">
									<!-- 이미지 추가 버튼 -->
									<div class="img-upload img-upload-main" style="overflow: hidden;">
										<span class="btn-wrap" data-attr="product_file_wrap">
											<!-- <button class="btn-img-upload" href="#"><strong></strong></button> -->
											<input type="file" id="upload" data-attr="product_file">
										</span> 				
										<label for="upload" data-attr="product_file_label"></label>
									</div>
									<!-- 이미지 추가 버튼 // -->
									<!-- 이미지가 첨부될 시 옆에 나열됩니다 -->
									<div data-attr="product_wrap" style = "display:inline-block">
										<!-- <div class="img-upload" style="overflow: hidden;">
											<img src="<?php echo $this->project_path;?>/images/sample.png" onerror="this.style.display='none'" alt="img_upload"/>
											<button class="delete-btn" type="button"></button>
										</div> -->
									</div>
									<!-- 이미지 1 // -->
									<p class="xsmall">jpg, png, gif 형식의 파일확장자<br>4MB 이하의 이미지 첨부 가능</p>
									<p>※ 권장사이즈는 가로 : 462px 세로 : 462px 입니다.</p>
								</dd>
							</dl>
						</div>
					</div>
				</div>
				<!-- 제품정보 // -->
				<div class="col-md-5">
					<div class="body-box mt-1">
						<div class="box-tit mb-1"><h3>추가정보<input type="button" value="줄 제거" onclick="delete_row1()"><input type="button" value="줄 추가" onclick="add_row1()"></h3></div>
						<div class="box-table-container">
							<dl class="box-tbody">

								<table id="add_info_table" class="box-p"  style="width:100%;">
									<tr>
										<td style="width:30%">추가정보</td>
										<td style="width:70%">추가정보 내용</td>
									</tr>	
								</table>
							</dl>
						</div>
					</div>
					<!-- 판매정보 // -->
				</div>

				<div class="col-md-5" style="display:none">
					<div class="body-box mt-3">
						<div class="box-tit" style="display: inline-block;"><h3>색상정보</h3></div>
						<input class="btn-primary btn-32" type="button" style="margin-left: 5px;" value="색상 추가" onclick="add_color_input(this);">
						<div class="box-table-container tog-container mt-2 d-none" style="display:block" data-color-input-wrap>
							<dl class="box-tbody" data-color-li>
								<dt class="box-th box-head">
									<input class="btn-primary btn-32" type="button" value="색상 선택" onclick="open_color_modal(this);">
								</dt>
								<dd class="box-td">
									<div class="insert insert-textarea">
										<input type="text" data-color onkeyup="change_color_input(this);" maxlength="6" style="width: 90%;" placeholder="색상 선택 또는 RGB값을 입력해주세요.">
										<span style="color: red; font-size: 24px; margin-left: 5px; cursor: pointer;" onclick="delete_color_input(this);">x</span>
									</div>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>

			<div class="row" style="display:none">
				<div class="col-md-12">
					<div class="body-box mt-3">
						<div class="box-tit mb-2"><h3>제품설명</h3></div>
						<div class="insert insert-textarea mt-2">
							<div class="summernote" data-attr="sum_note">

							</div>
						</div>
					</div>
				</div>
				<!-- 제품정보 // -->
			</div>
		</div>

		<!-- 이미지 -->
		<div class="img-upload" style="overflow: hidden;" data-copy = "img_copy">
            <img src="" data-attr="img" alt="img_upload"/>
			<span class="return-btn" style="display:none;" data-attr="btn_delete_cancel">취소</span>
            <button  data-attr="del_btn" class="delete-btn" type="button"></button>
        </div>

		<!-- 색상 COPY -->
		<dl class="box-tbody" id="color_input_copy" data-color-li>
			<dt class="box-th box-head">
				<input class="btn-primary btn-32" type="button" value="색상 선택" onclick="open_color_modal(this);">
			</dt>
			<dd class="box-td">
				<div class="insert insert-textarea">
					<input type="text" data-color onkeyup="change_color_input(this);" maxlength="6" style="width: 90%;" placeholder="색상 선택 또는 RGB값을 입력해주세요.">
					<span style="color: red; font-size: 24px; margin-left: 5px; cursor: pointer;" onclick="delete_color_input(this);">x</span>
				</div>
			</dd>
		</dl>
</div>

<div class="modal user-modal" id="color_modal" style="display:none;">
	<div class="modal-wrap modal-sm">
		<div class="modal-container">
			<!-- 모달 본문 -->
			<div class="modal-container-inner">
				<div class="modal-head bold">색상 선택</div>
				<div class="modal-body">
					<form class="form" >
						<div class="box-table-container">
							<div class="mt-2">
							<!-- <input type="text" id="hue-demo" class="demo" data-control="hue" value="#ff6161"> -->
								<input type="text" data-jscolor="{}" value="#000000" data-color-input>
							</div>
						</div>
					</div>
				</form>
				<ul class="btn-container align-right">
					<li><button type="button" class="btn btn-ghost" onclick ="color_modal_hide()">취소</button></li>
					<li><button type="button" class="btn btn-primary" onclick="select_color();">선택</button></li>
				</ul>
			</div>
			<!-- 모달 본문 // -->
		</div>
	</div>
</div>

</body>
<!-- select2 -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->project_admin_path;?>layout/select2/css/select2.min.css"/>
<link rel="stylesheet" type="text/css" href="common_css/adm/adm_select.css?<?php echo $version;?>"/>
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>layout/select2/js/select2.full.min.js"></script>

<!-- yd custom -->
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/custom.js"></script>

</html>