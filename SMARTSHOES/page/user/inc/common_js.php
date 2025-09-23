<script>
    var data = <?php echo $this->data; ?>;
    var user_idx = "<?php echo $is_login; ?>";
</script>
<script type="text/javascript" src="<?php echo $this->project_name;?>/common_js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $this->project_name;?>/common_js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $this->project_name;?>/common_js/lb.js<?php echo $this->version;?>"></script>
<script type="text/javascript" src="<?php echo $this->project_name;?>/common_js/gu.js<?php echo $this->version;?>"></script>
<script type="text/javascript" src="<?php echo $this->project_path;?>layout/js/jquery.fullpage.extensions.min.js"></script>
<script type="text/javascript" src="<?php echo $this->project_path;?>js/lang.js<?php echo $this->version;?>"></script>
<!-- <script type="text/javascript" src="<?php echo $this->project_path;?>js/header.js<?php echo $this->version;?>"></script> -->
<script type="text/javascript" src="<?php echo $this->project_path;?>js/side_btn.js<?php echo $this->version;?>"></script>
<script type="text/javascript" src="<?php echo $this->project_path;?>js/common.js<?php echo $this->version;?>"></script>
<!-- <script type="text/javascript" src="<?php echo $this->project_path;?>js/side_menu.js<?php echo $this->version;?>"></script> -->
<script type="text/javascript" src="<?php echo $this->project_path;?>layout/js/slick.min.js"></script>
<script src="https://kit.fontawesome.com/23bc37f133.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<?php include_once $this->project_path."include/modal.php"; ?>


<!-- slick -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    AOS.init();
</script>