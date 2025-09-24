<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>제품조회</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu3_inquiry.js<?php echo $version;?>"></script>
	
</head>
<body>
	<div class="wrap">
        <?php include_once $dir."page/adm/include/adm_menu3_aside.php";?>
        <?php include_once $dir."page/adm/include/adm_header.php";?>

			<div class="bd">
				<article class="body-container">
					<div class="body-head mb-1">
						<h2 style="display: inline-block;">제품조회</h2>
					</div>
					<form class="form">
						<div class="body-box mb-3">
							<div class="box-search-container">
								<div class="insert-wrap">
									<div class="insert insert-select">
										<select class="select-custom" type="text" id="search_type">
											<option value="1">제품명</option>
											<!-- <option>상태</option> -->
										</select>
									</div>
									<div class="insert insert-input"><input class="input-lg" type="text" id="search_text"/><input type="text" style="display:none;"/></div>
									<!-- <div class="insert insert-chk">
										<label class="check_label">상세검색
											<input type="checkbox">
											<span class="checkmark"></span>
										</label>
									</div> -->
									<!-- 상세검색을 check 하면 상세검색 전문이 나타납니다 // -->
								</div>
							</div>
							<!-- 상세검색 전문
							<div class="box-table-container mt-3">
								<dl class="box-tbody">
									<dt class="box-th box-head"><p>기간</p></dt>
									<dd class="box-td">
										<ul class="insert-wrap">
											<li class="insert">
												<select class="select-custom" type="text">
													<option>주문일</option>
													<option>입금일</option>
													<option>제품준비중</option>
													<option>배송일</option>
													<option>배송완료일</option>
													<option>취소일</option>
												</select>
											</li>
											<li class="insert insert-chk">
												<label class="check_label" for="0">전체
													<input type="radio" id="0" value="0" name="condition">
													<span class="checkmark radio"></span>
												</label>
											</li>
											<li class="insert insert-chk">
												<label class="check_label" for="1">오늘
													<input type="radio" id="1" value="1" name="condition">
													<span class="checkmark radio"></span>
												</label>
											</li>
											<li class="insert insert-chk">
												<label class="check_label" for="2">1개월
													<input type="radio" id="2" value="2" name="condition">
													<span class="checkmark radio"></span>
												</label>
											</li>
											
											<li class="insert insert-chk">
												<label class="check_label" for="3">3개월
													<input type="radio" id="3" value="3" name="condition">
													<span class="checkmark radio"></span>
												</label>
											</li>
											<li class="insert insert-chk">
												<label class="check_label" for="4">6개월
													<input type="radio" id="4" value="4" name="condition">
													<span class="checkmark radio"></span>
												</label>
											</li>
											<li class="insert insert-chk">
												<label class="check_label" for="5">1년
													<input type="radio" id="5" value="5" name="condition">
													<span class="checkmark radio"></span>
												</label>
											</li>
										</ul>
										<ul class="insert-wrap">
											<li class="insert insert-input datepick-wirte"><input class="input-32 input-xs" type="text"/><i></i></li>
											<li class="insert">~</li>
											<li class="insert insert-input datepick-wirte"><input class="input-32 input-xs" type="text"/><i></i></li>
										</ul>
									</dd>
								</dl>
							</div> -->
							<!-- 상세검색 전문 // -->
							<div class="insert-wrap align-center mt-3">
								<div class="insert insert-input-btn"><input class="btn-primary" type="button" value="검색" onclick="search()"/></div>
								<div class="insert insert-input-btn"><input class="btn-default" type="button" value="초기화" onclick="init_search()"/></div>
							</div>
						</div>
						<div class="body-out mb-3">
							<div class="out-tab-container">
								<ul>
									<li id="total_li" onclick="move_tab(0)"><a href="#" id="total_count">전체</a></li>
									<li id="indicate_li" onclick="move_tab(1)"><a href="#" id="indicate">표시(0)</a></li>
									<li id="not_indicate_li" onclick="move_tab(2)"><a href="#" id="not_indicate">미표시(0)</a></li>
								</ul>
							</div>
							<!-- <div class="insert-wrap mb-1">
								<div class="insert insert-select">
									<select class="select-custom" type="text" id="orderby">
										<option value="1" >등록일순</option>
										<option value="2" >수정일순</option>
									</select>
								</div>
								<div class="insert insert-input-btn"><input class="btn-default" type="button" value="새로고침"/></div>
							</div> -->
						</div>
						<div class="body-box">
							<div class="table-container">
								<table class="table1">
									<thead>
										<tr>
											<th>번호</th>
											<th>이미지</th>
											<th class="col-tit">제품명</th>
											<th>등록일</th>
											<th>수정일</th>
											<th>컬러옵션</th>
											<th>상태</th>
											<!-- <th>재고</th> -->
										</tr>
									</thead>
									<tbody id="wrap" data-wrap="wrap">
										<!-- <tr>
											<td class="col-num">1</td>
											<td class="col-img">
												<div class="table-thumb">
													<img src="<?php echo $this->project_admin_path;?>images/sample.png" alt="제품이미지"/>
												</div>
												
											</td>
											<script>
												
											</script>
											<td class="col-tit">
												<div class="table-tit">
													<p class="tit"><span onclick="">제품타이틀입니다.</span></p>
													<p class="ct mt-2">스마트폰 거치대 > 핑거밴드</p>
													<span class="table-btn"><input class="btn-default btn-32" type="button" value="수정"/></span>
												</div>
											</td>
											<td><div class="table-date">20.03.18</div></td>
											<td><div class="table-date">20.03.18</div></td>
											<td><div class="table-won">19,000</div></td>
											<td>
												<div>
													<select class="select-custom">
														<option value="">정상</option>
														<option value="">품절</option>
														<option value="">숨김</option>
													</select>
												</div>
											</td>
											<td>
												<div class="table-icon">
													<p></p>
												</div>
											</td>
											<td><div class="table-count">200</div></td>
										</tr> -->
										<!-- 1 // -->
									</tbody>
								</table>
							</div>
							<div class="pagination_container mt-3" id = "paging">
								<div class="page_item arrow prev">«</div>
								<div class="page_item active">1</div>
								<!-- <div class="page_item ">2</div> -->
								<div class="page_item arrow next">»</div>
							</div>
						</div>
					</form>
				</article>
			</div>
	</div>
	<div style="display:none;">
		<table>
			<tr data-copy="copy">
				<td class="col-num" data-attr="num">1</td>
				<td class="col-img">
					<div class="table-thumb">
						<img src="<?php echo $this->project_admin_path;?>images/sample.png" alt="제품이미지" data-attr="thumnail_file"/>
					</div>
					
				</td>
				<td>
					<div class="table-tit">
						<p class="tit"><span onclick="" data-attr="product_name">제품타이틀입니다.</span></p>
						<div class="ct-list mt-2" data-attr="category_view">
							<!-- <p class="ct">스마트폰 거치대 > 핑거밴드</p>
							<p class="ct">스마트폰 거치대 > 핑거밴드</p>
							<p class="ct">스마트폰 거치대 > 핑거밴드</p>
							<p class="ct">스마트폰 거치대 > 핑거밴드</p>
							<p class="ct">스마트폰 거치대 > 핑거밴드</p> -->
						</div>
						<span class="table-btn"><input class="btn-default btn-32" type="button" value="수정" data-attr="btn_modify"/></span>
					</div>
				</td>
				<td class="w_160"><div class="table-date" data-attr="regdate">20.03.18</div></td>
				<td class="w_160"><div class="table-date" data-attr="modify_regdate">20.03.18</div></td>
				<td class="w_64"><input class="btn-default btn-32" type="button" value="옵션" data-attr="btn_option"></td>
				<td><div data-attr="state">정상</div></td>
				<!-- <td><div class="table-count" data-attr="total_stock">200</div></td> -->
			</tr>
		</table>
	</div>
</body>
<!-- select2 -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->project_admin_path;?>layout/select2/css/select2.min.css"/>
<link rel="stylesheet" type="text/css" href="common_css/adm/adm_select.css?<?php echo $version;?>"/>
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>layout/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
        $('.select-custom').select2({
            minimumResultsForSearch: -1
        });
    });
</script>
