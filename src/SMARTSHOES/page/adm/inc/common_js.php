<script>
    var data = <?php echo $data;?>;
</script>
<script type="text/javascript" src="<?php echo $this->project_name;?>/common_js/jquery.js<?php echo $version;?>"></script>
<script type="text/javascript" src="<?php echo $this->project_name;?>/common_js/lb.js<?php echo $version;?>"></script>
<script type="text/javascript" src="<?php echo $this->project_name;?>/common_js/gu.js<?php echo $version;?>"></script>
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/common.js<?php echo $version;?>"></script>
<script>
    obj.link = <?php echo json_encode($this->file_path->get_path(),JSON_UNESCAPED_UNICODE);?>;
    var sub_link = <?php echo json_encode($this->sub_file_path->get_path(),JSON_UNESCAPED_UNICODE);?>;
    obj.link = Object.assign(obj.link, sub_link);
    console.log(obj.link);
</script>

