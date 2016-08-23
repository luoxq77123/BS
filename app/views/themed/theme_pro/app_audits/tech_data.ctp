<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>技审信息</title>
<?php echo $this->Html->css('tech_style.css');?>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/jquery1.7.2.js"></script>

</head>
<body>				
	<div class="sjjg">		     
	    <?php if ($techAudit): ?>  												    
		<table class="xiangxitable" cellpadding="0" cellspacing="0">			
			<tr>
				<th>序号</th>
				<th>位置</th>
				<th>类型</th>
				<th>描述</th>
			</tr>			
			<?php 			   
			    $number = 0;
				foreach ($techAudit as $tempTechAudit):
				    $number++;
			?>
			<tr>
				<td><span class="xuhao"><?php echo $number;?></span></td>
				<td class="t">
				     <?php echo $this->Html->image('ico4.png',array('width'=>'18','height'=>'19'))?>
				     <?php
				         $clipIn = (int)$tempTechAudit['ClipIn'];
				         $clipOut = (int)$tempTechAudit['ClipOut'];
				     ?>
                     <a href="javascript:setTrimInAndOut(<?php echo $clipIn?>,<?php echo $clipOut?>)">
				     <?php echo $this->ViewOperation->formatTimeLength($clipIn);?>
				     ----
				     <?php echo $this->ViewOperation->formatTimeLength($clipOut);?>
				     </a>
				     
				</td>
				<td>
				    <?php 
				       $techClip = (int)$tempTechAudit['ClipClass'];
				       echo $techClass[$techClip];
				    ?>
				</td>
				<td>
				    <?php 
				       $techType = (int)$tempTechAudit['BugType'];
				       echo $techState[$techType];
				    ?>
				</td>
			</tr>
			<?php endforeach;?>								
		</table>
	    <div class="page">
			<?php $this->Paginator->options(array(
				                         //'url' => array('controller' => 'audit_tasks','action' => 'techData','taskid' => $taskID),
			                             'model' => 'Tech',
	  		                             'update' => '#tech',
	  		                             'evalScripts' => true//,
//	  		                             'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
//	  		                             'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
	  		                             )); 
	  		?>
	  		<?php $techPageParams = $this->Paginator->params('Tech');?>
			<div class="fl">
				每页 
				<span class="red"><?php echo $techPageParams['options']['limit'];?></span> 
				条 页次:<span class="red">
				<?php echo $this->Paginator->counter(array(
						      'model' => 'Tech',
                              'format' => '%page%/%pages%'
                         ));
                ?>
				</span>
				页
		   </div>
		   <div class="fr">
			<?php 
				echo $this->Paginator->first('首页',array('model' => 'Tech','tag' => 'span','class'=>'a' ));	       
		        echo $this->Paginator->prev('上一页', array(), null, array('model' => 'Tech','tag' => 'span','class' => 'b'));
		        echo $this->Paginator->numbers(array('model' => 'Tech','before' => '','tag' => 'span','class' => 'number','separator' => '','modulus'=>4));
		        echo $this->Paginator->next('下一页', array(), null, array('model' => 'Tech','tag' => 'span','class' => 'b'));		   
		        echo $this->Paginator->last('尾页',array('model' => 'Tech','tag' => 'span','class'=>'a' ));
		   ?>
		   </div>		   	
	</div>
	<?php else:?>
	<div class="sjjg sh_noDate"></div> 
	<?php endif;?>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $(document).bind("contextmenu",function(e){
        return false;
    });
});
///播放器控制（设置出入点）
function setTrimInAndOut(llTrimIn,llTrimOut){
	var player=window.parent.document.getElementById('EncoderPlayer');
    player.SetTrimEntry(llTrimIn);
	player.SetTrimExit(llTrimOut);
}

$(".xiangxitable tr:odd").addClass("odd");
$(".xiangxitable tr").hover(
function(){
	$(this).addClass("hover");
},
function(){
	$(this).removeClass("hover");
}
);
</script>
</body>
</html>