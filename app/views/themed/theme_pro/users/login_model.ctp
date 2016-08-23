<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>用户登录-方式选择</title>
<?php echo $this->Html->css('style.css');?>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/jquery1.7.2.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/main.js"></script>
</head>

<body>
<div class="cxtu" id="cxtuu">
	<div class="title">
		<h3 class="fl">登录系统方式</h3>
	</div>
	<?php echo $this->Form->create('Model',array('url'=>array('controller'=>'users','action'=>'loginModel')));?>
	<ul>
		<li class="of">
		    <span>选择方式:</span>	
            <?php echo $this->Form->radio('choose',array(CONTENT_TASK_TYPE=>'内审',TECH_TASK_TYPE=>'技审'),array('label'=>false,'legend'=>false,'value'=>'0'));?> 	    
	    </li>
	</ul>	
	<div class="bt">
	    <?php echo $this->Form->submit('确定',array('class' => 'sub','name'=>'my','label' => false,'div' => false));?>	    
		<a href="<?php echo $this->Html->url(array('controller' => 'users','action' => 'logout')); ?>" class="reset">取消</a>
	</div>
	<?php echo $this->Form->end();?>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $(document).bind("contextmenu",function(e){
        return false;
    });
});
</script>
</body>
</html>