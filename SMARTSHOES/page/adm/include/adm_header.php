<header class="adm_header">
	<div class="utill">
		<!-- <span onclick="go_billing()">시스템결제 바로가기</span>
		<span onclick="go_mall()">쇼핑몰 바로가기</span> -->
		<span onclick="logout()">LOGOUT</span>
	</div>
	<div class="category">
		<!-- <ul class="gnb">
			<li class="<?php $this->header_active('menu1')?>"><a href="?ctl=move&param=adm&param1=menu1_banner_main">기본설정</a></li>
			<li class="<?php $this->header_active('menu3')?>"><a href="?ctl=move&param=adm&param1=menu3_inquiry">제품관리</a></li>
			<li class="<?php $this->header_active('menu4')?>"><a href="?ctl=move&param=adm&param1=menu4_product_inquiry">게시판관리</a></li>
		</ul> -->
	</div>
</header>
<?php include_once $dir."page/adm/adm_alert_modal.php";?>
<!-- 로딩 elem -->
<?php include_once $dir."page/adm/include/adm_loading.php";?>
<!-- adm_header끝 -->