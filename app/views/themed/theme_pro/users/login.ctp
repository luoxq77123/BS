<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录</title>

<?php echo $this->Html->css('style.css');?>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/jquery1.7.2.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/main.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot;?>js/artDialog/skins/idialog.css" />
<script type="text/javascript" src="<?php echo $this->webroot;?>js/artDialog/artDialog.min.js"></script>

</head>

<body class="loginbody">
<div class="login">
	<div class="subbox">
	    <?php 
	        echo $this->Form->create('User'); 
	        echo $this->Form->input('name',array('label' => false,'class' => 'user','div' => false,'autocomplete' => 'off'));
	        echo $this->Form->input('password',array('label' => false,'class' => 'pwd','div' => false));
	        echo $this->Form->submit('',array('label' => false,'class' => 'sub','div' => false));
	        echo $this->Form->end();
	    ?>	    
	</div>
</div>
<script type="text/javascript">
var notice = '<?php echo $this->Session->flash('flash'); ?>';
if(notice){
	art.dialog({ 
	    title: '提示',
	    content: notice,
	    lock: true,
	    okValue: '确认',  
	    ok: function () {
		   return true;
	    }
    });
}
</script>
<script type="text/javascript">
$(document).ready(function(){
    $(document).bind("contextmenu",function(e){
        return false;
    });
});
</script>
</body>
</html>
