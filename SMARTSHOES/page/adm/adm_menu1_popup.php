<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>메인팝업 관리</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu_1_popup.js<?php echo $version;?>"></script>
</head>
<body>
	<div class="wrap">
        <?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
        <?php include_once $dir."page/adm/include/adm_header.php";?>
			<div class="bd">
				<article class="body-container">
					<div class="body-head"><h2>팝업 관리</h2></div>
						<form class="form" id="form">
							<div class="out-tab-container st2 mt-2">
								<ul id="lang_list" data-wrap="lang_wrap" style="display:none">
									<!-- <li class="current"><a href="">한국어</a></li>
									<li><a href="">영어</a></li>
									<li><a href="">중국어</a></li> -->
								</ul>
							</div>
							<div class="row p-relative mt-2">
								<div class="col-md-11">
									<div class="body-box">
										<div class="table-container">
											<table class="table1">
												<thead>
													<tr>
														<th>
															<div class="insert insert-chk">
																<label class="check_label">
																	<input type="checkbox" id="all_check" onclick="all_check_list(this)"/>
																	<span class="checkmark"></span>
																</label>
															</div>
														</th>
														<th>번호</th>
														<th>PC</th>
														<!-- <th>모바일</th> -->
														<th class="col-tit">팝업명</th>
														<th>등록일</th>
														<th>사용여부</th>
													</tr>
												</thead>
												<tbody data-wrap="wrap" id="wrap">
													<!-- <tr>
														<td>
															<div class="insert insert-chk">
																<label class="check_label">
																	<input type="checkbox"/>
																	<span class="checkmark"></span>
																</label>
															</div>
														</td>
														<td class="col-num">1</td>
														<td class="col-img">
															<div class="table-thumb">
																<img src="<?php echo $this->project_admin_path;?>images/sample2.png" alt="상품이미지"/>
															</div>
														</td>
														<td class="col-img">
															<div class="table-thumb">
																<img src="<?php echo $this->project_admin_path;?>images/sample2.png" alt="상품이미지"/>
															</div>
															
														</td>
														
														<td class="col-tit">
															<div class="table-tit">
																<p class="tit"><span onclick="">임시배너1</span></p>
															</div>
														</td>
														<td class="col-long-num"><div class="table-date">20.03.18</div></td>
														<td class="col-short-num">
															<div>
																<select class="select-custom">
																	<option value="">사용</option>
																	<option value="">미사용</option>
																</select>
															</div>
														</td>
													</tr> -->
													<!-- 1 // -->
												</tbody>
											</table>
										</div>
										<div class="insert-wrap mt-1">
											<div class="insert insert-input-btn"><input class="btn-default btn-32" type="button" value="선택삭제" onclick="delete_confirm()"></div>
										</div>
									</div>
									<div class="btn-container align-right mt-3">
										<button type="button" class="btn btn-primary" onclick="move_upload()">팝업등록</button>
									</div>
								</div>

								<div class="col-md-1">
									<div class="sort-control-container">
										<div class="body-box">
											<div class="mt-2">
												<div class="insert mb-1"><input type="text" class="input-32" id="move_count" value="1" onkeyup="lb.validate_number(this)"/></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-top-max.png" onclick="btn_top()"/>최상</span></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-top.png" onclick="btn_up()"/>위</span></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-bt.png" onclick="btn_down()"/>아래</span></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-bt-max.png" onclick="btn_end()"/>최하</span></div>
											</div>
										</div>
										<div class="sort-control-insert-wrap">
											<div class="insert insert-input-btn mt-1"><input class="btn-primary" type="button" value="적용하기" onclick="btn_save()"></div>
											<div class="insert insert-input-btn mt-1"><input class="btn-default" type="button" value="초기화" onclick="btn_init()"></div>
										</div>
									</div>
								</div>
							</div>
                    	</form>
						
						
				</article>
			</div>
	</div>
	<div style="display:none;">
		<table>
			<tr data-copy="copy">
				<td>
					<div class="insert insert-chk">
						<label class="check_label">
							<input type="checkbox" data-attr="checkbox"/>
							<span class="checkmark"></span>
						</label>
					</div>
				</td>
				<td class="col-num" data-attr="number">1</td>
				<td class="col-img">
					<div class="table-thumb">
						<img src="<?php echo $this->project_admin_image_path;?>images/sample2.png" data-attr="img_pc_popup" alt="상품이미지"/>
					</div>
				</td>
				
				
				<td class="col-tit">
					<div class="table-tit">
						<p class="tit"><span onclick="" data-attr="name">임시배너1</span></p>
					</div>
				</td>
				<td class="col-long-num"><div class="table-date" data-attr="regdate">20.03.18</div></td>
				<td class="col-short-num">
					<div>
						<select class="" data-attr="is_use">
							<option value="1">사용</option>
							<option value="0">미사용</option>
						</select>
					</div>
				</td>
			</tr>
		</table>
		<li class="current" onclick="" data-copy="lang"><a data-attr="name">KOR</a></li>
	</div>
</body>
<!-- select2 -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->project_admin_path;?>layout/select2/css/select2.min.css"/>
<link rel="stylesheet" type="text/css" href="common_css/adm/adm_select.css?<?php echo $version;?>"/>
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>layout/select2/js/select2.full.min.js"></script>

</html>