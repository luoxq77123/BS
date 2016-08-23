<?php $listHref = $this->Html->url(array('controller'=>'contents','action'=>'detailList','page'=>$layoutParams['page']));?>
<div class="box" id="snt" style="display:block;">
	<div class="boxbottombg" id="data_list_s">
        <div class="title">
            <?php if ($pgmAuditList):
                     $theState = (int)$pgmAuditList[0]['tempauditstate'];
			         if ($theState == NOT_AUDIT_TASK_STATE) :
			?>
            <input type="checkbox"/>
            <?php    endif; 
                  endif;
            ?>
			<h3>缩略图</h3>
			<div class="listOrThumb">
				<a href="<?php echo $listHref?>" class="list">&nbsp;</a>
				<a href="javascript:void(0);" class="thumb on" >&nbsp;</a>
			</div>
			<?php if ($pgmAuditList):
                     $theState = (int)$pgmAuditList[0]['tempauditstate'];
			         if ($theState == NOT_AUDIT_TASK_STATE) :
			?>
		    <a href="javascript:void(0);" onclick="addWorkArea()" class="piLiangShengHeBt"></a>
			<?php    endif; 
                  endif;
            ?>
		</div>	    	
	    <?php if ($pgmAuditList):?>
		<ul class="of" id="slvList">		    
		    <?php foreach ($pgmAuditList as $pgmData) :
			         $auditState = (int)$pgmData['tempauditstate'];			   
		    ?>
		    <li>
			        <?php 
			            $tmpTaskID = $pgmData['taskid'];
			            if ($auditState != NOT_AUDIT_TASK_STATE){
			            	$tmpTaskID = -1;
			            }			           			        
			        ?>
                    <a href="#" class="p" title="<?php echo $pgmData['pgmname'];?>" ondblclick="dbAudit('<?php echo $tmpTaskID?>')">
                        <?php $newPicPath = $this->ViewOperation->getPicPath($pgmData['picpath']);?>
				        <img src="<?php echo $newPicPath;?>" alt="" title="<?php echo $pgmData['pgmname'];?>" style="width:169px;height:121px;"/>
                    </a>
                    
                    <?php if ($auditState == NOT_AUDIT_TASK_STATE) :?>
                           <input type="checkbox" name="select_batch_list_s" id="<?php echo $pgmData['taskid'];?>"/>
                       <?php endif;?>
                           <script type="text/javascript">
                           <?php if ($selectedList && in_array($pgmData['taskid'], $selectedList)):?>
                               var checkid = "#"+"<?php echo $pgmData['taskid']?>";
                               $(checkid).attr("checked", true);                       
                           <?php endif;?>
                           </script>
                    
                    <a href="#" class="t" title="<?php echo $pgmData['pgmname'];?>">
                        <?php if ($auditState == NOT_AUDIT_TASK_STATE):
                                  echo $this->Html->image('ico5.png',array('alt'=>''));
				              elseif ($auditState == PASS_AUDIT_TASK_STATE):
				                  echo $this->Html->image('ico2.png',array('alt'=>''));
				              elseif ($auditState == RETURN_AUDIT_TASK_STATE):
				                  echo $this->Html->image('ico1.png',array('alt'=>''));
				              elseif ($auditState == IS_AUDITING_TASK_STATE):
				                  echo $this->Html->image('ico3.png',array('alt'=>''));
				              endif;
				        ?>
				        
                        <?php echo $this->ViewOperation->subStringFormat($pgmData['pgmname'], 0, THUMB_NAME_NUM);?>
                    </a>
                    <div class="of">
				         <span>编辑者：</span>
				         <?php echo $pgmData['creatorname'];?>
			        </div>
				    <div class="of">
				         <span>创建时间：</span>
				         <?php echo substr($pgmData['taskcreatedate'], 0,19);?>
				    </div>
				    <div class="of">
				         <span>节目时长：</span>
				         <?php
				           $length = (int)$pgmData['pgmlength'];
				           echo $this->ViewOperation->formatTimeLength($length);
				         ?>
				    </div>
				
				    <div class="of bt">	
				    <?php if ($auditState == NOT_AUDIT_TASK_STATE) :?>			
				        <a href="<?php echo $this->Html->url(array('controller' => 'audit_tasks','action' => 'auditData',$pgmData['taskid'],'cmode'=>THUMB_MODEL,'cpage'=>$layoutParams['page']));?>"> 
					       <?php echo $this->Html->image('sh.jpg',array('alt'=>'审核模式'))?>
					    </a>
					<?php else :?>
					    <a href="#">
					       <?php echo $this->Html->image('sh2.jpg',array('alt'=>'审核模式'))?> 
					    </a>	
					<?php endif;?>			
					    <a href="<?php echo $this->Html->url(array('controller' => 'audit_tasks','action' => 'browseData',$pgmData['taskid'],'cmode'=>THUMB_MODEL,'cpage'=>$layoutParams['page']));?>">
					    <?php echo $this->Html->image('ll.jpg',array('alt'=>'浏览模式'))?>
					    </a>
				    </div>
			</li>
			<?php endforeach;?>
		</ul>
		<div class="page">		
		    <?php
		        $url = array('controller' => 'contents','action' => 'auditList');
		        $this->Paginator->options(array(
				                         'url' => $url,
		    				             'model' => 'Content'
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
					 echo $this->Paginator->first('首页',array('tag' => 'span','separator' => '','class'=>'a'));	       
		             echo $this->Paginator->prev('上一页', array(), null, array('tag' => 'span','class' => 'b'));
		             echo $this->Paginator->numbers(array('before' => '','tag' => 'span','class' => 'number','separator' => '','modulus'=>4));
		             echo $this->Paginator->next('下一页', array(), null, array('tag' => 'span','class' => 'b'));		   
		             echo $this->Paginator->last('尾页',array('tag' => 'span','class'=>'a' ));
		        ?>
			</div>			
		</div>
		<?php else :?>
		<div class="noItem_bg"></div>
		<?php endif;?>		
	</div>
</div>
<script type="text/javascript">
$("#slvList > li").hover(
function(){
	$(this).addClass("hover").find(".checkIpt").show();
}, 
function(){
	if($(this).find("input:checked").length){	
		
	} else {
		$(this).removeClass("hover").find(".checkIpt").hide();
	}
});
/*多选操作*/
var $iptList = $("#slvList > li");
var $chkAll = $("#data_list_s .title input");
var chkall = 0;

$iptList.find("input").click(function() {
	chkAllChecked();
});
function chkAllChecked() {
	if ($iptList.find("input:checked").length == $iptList.find("input").length) {
		$chkAll.attr("checked", true);
		chkall = 1;
	} else {
		$chkAll.attr("checked", false);	
		chkall = 0;
	}
}
chkAllChecked();

$chkAll.click(function() {
	if(chkall == 1) {
		chkall = !chkall;
		$(this).attr("checked", false);	
		$iptList.removeClass("hover").find("input").attr("checked", false).end().find(".checkIpt").hide();
	} else {
		chkall = !chkall;
		$(this).attr("checked", true);
		$iptList.addClass("hover").find("input").attr("checked", true).end().find(".checkIpt").show();
	}


	//刷新选中状态
	if(this.checked){
		setAllCookie(1);
	}
	else{
		setAllCookie(2);
	}
});
</script>
<script type="text/javascript"> 
$(document).ready(function(){
	$(".jmlb").addClass("on");
});
function dbAudit(taskid){ 
	if(parseInt(taskid) != -1){	
		var datas = "";
		$.ajax({
			   type: "POST",
			   url: "<?php echo AJAX_DOMAIN;?>"+"/batch_audit_tasks/addWorkArea/"+taskid,
			   data: datas,
			   success: function(msg){	
				   var data = JSON.parse(msg);
				   var tag = parseInt(data.NotLockTag);
				   if(tag!=0){
					   setArtDialog('无法进入审核，该任务已锁定');

					   var url = "<?php echo $this->Html->url(array('controller' => 'contents','action' => 'auditList','page'=>$layoutParams['page']));?>";
					   location.href = url;
				   }
				   else{
					   var urls = "<?php echo $this->Html->url(array('controller' => 'batch_audit_tasks','action' => 'batchAudit'));?>";
				       urls = urls+"/"+taskid+"/cmode:"+"<?php echo THUMB_MODEL;?>"+"/cpage:"+"<?php echo $layoutParams['page'];?>";
					   location.href = urls;  
				   }			   	 
			   }
		});		
	}
}

function setAllCookie(tag){
	var data = "";
	var num = 0;
	$('#data_list_s input[name="select_batch_list_s"]').each(function(i){
		num++;		
		if(num==1){
			data = data + ""+ $(this).attr("id");
		}
		else{
			data = data +"," + $(this).attr("id");
		}		   
	}); 
	updateCookie(data,tag);
}
$('#data_list_s input[name="select_batch_list_s"]').each(function(i){
    $(this).click(function() {
    	var listid = $(this).attr("id");
		if(this.checked){
			updateCookie(listid,1);
		}
		else{
			updateCookie(listid,2);
		}
	});
});
function updateCookie(data,tag){//tag:1->选中;2为->取消选中
	var datas="Data="+data+"&Tag="+tag;
	$.ajax({
		   type: "POST",
		   url: "<?php echo AJAX_DOMAIN;?>"+"/contents/setBatchAuditList",
		   data: datas,
		   success: function(msg){
			  	 
		   }
	});
}

function addWorkArea(){
	var datas = "";
	$.ajax({
		   type: "POST",
		   url: "<?php echo AJAX_DOMAIN;?>"+"/batch_audit_tasks/addWorkArea",
		   data: datas,
		   success: function(msg){		
			   var data = JSON.parse(msg);
			   var tag = parseInt(data.NotLockTag);
			   if(tag!=0){
				   setArtDialog('您所选择的部分任务已经锁定，无法加入工作区');
			   }
			   var url = "<?php echo $this->Html->url(array('controller' => 'contents','action' => 'auditList','page'=>$layoutParams['page']));?>";
			   location.href = url;			     	 
		   }
	});
}
</script>