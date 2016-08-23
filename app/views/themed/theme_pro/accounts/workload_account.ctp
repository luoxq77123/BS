<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>工作量统计</title>
<?php echo $this->Html->css('workload_style.css');?>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/jquery1.7.2.js"></script>
</head>
<body>
<div class="workload_st" >
    <table class="xiangxitable gzltjlist" cellpadding="0" cellspacing="0">
		<tr>
			<th>用户名</th>
			<th>处理任务数</th>
			<th>通过数</th>
			<th>退回数</th>
		</tr>
		<?php if ($countDataList):?>
			<?php foreach ($countDataList as $aData):?>
		        <tr>
				    <td><?php echo $aData['AuditName'];?></td>
					<td><?php echo $aData['CountAll'];?></td>
					<td><?php echo $aData['CountPass'];?></td>
					<td><?php echo $aData['CountNotPass'];?></td>
				</tr>
			<?php endforeach;?>
		<?php endif;?>											
	</table>
	<div class="page">
		<?php
		if ($countDataList):		        
                $url = array(
                       'controller'=>'accounts',
                       'action' => 'workloadAccount');          
		        $this->Paginator->options(array(
				                  'url' => $url,
				                  'model' => 'Content',
	  		                      'update' => '#workload',
	  		                      'evalScripts' => true//,
	  		                      //'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
	  		                      //'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
	  		                      )); 
	  	?>
	  	<?php $pageParams = $this->Paginator->params('Content');?>
		<div class="fl">
			 共 
			<span class="red"><?php echo $pageParams['count']; ?></span>
			 条,每页
		    <span class="red"><?php echo $pageParams['options']['limit'];?></span> 
			条,当前页次:
			<span class="red">
			<?php echo $this->Paginator->counter(array(
						      'model' => 'Content',
                              'format' => '%page%/%pages%'
                  ));
            ?>
            </span>
                                         页
		</div>
	    <div class="fr">
		    <?php 
				  echo $this->Paginator->first('首页',array('model' => 'Content','tag' => 'span','separator' => '','class'=>'a'));	       
		          echo $this->Paginator->prev('上一页', array(), null, array('model' => 'Content','tag' => 'span','class' => 'b'));
		          echo $this->Paginator->numbers(array('model' => 'Content','before' => '','tag' => 'span','class' => 'number','separator' => '','modulus'=>4));
		          echo $this->Paginator->next('下一页', array(), null, array('model' => 'Content','tag' => 'span','class' => 'b'));		   
		          echo $this->Paginator->last('尾页',array('model' => 'Content','tag' => 'span','class'=>'a' ));
		    ?>
		</div>
		<?php endif;?>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $(document).bind("contextmenu",function(e){
        return false;
    });
});
$(".xiangxitable tr:odd").find("td").css("background-color","#f5f5f5");
</script>
</body>
</html>