<?php $thumbHref = $this->Html->url(array('controller'=>'contents','action'=>'auditList','page'=>$layoutParams['page']));?>
<?php 
    	function escapeValue($str = NULL){
	$str = str_replace("&apos;&apos;", "'", $str);
	    $str = str_replace('\&quot;', '&quot;', $str);
	    return $str;
	}
?>
<div class="box" id="xx" style="display:block;">
	<div class="boxbottombg" id="data_list">
		<div class="title">
			<h3>详细列表</h3>
			<div class="listOrThumb">
				<a href="javascript:void(0);" class="list on" >&nbsp;</a>
				<a href="<?php echo $thumbHref;?>" class="thumb">&nbsp;</a>
			</div>
			<?php if ($pgmAuditList):
                     $theState = (int)$pgmAuditList[0]['tempauditstate'];
			         if ($theState == NOT_AUDIT_TASK_STATE) :
			?>
		    <a href="javascript:void(0);" onclick="addWorkArea()" class="piLiangShengHeBt">批量审核</a>
		    <?php    endif; 
                  endif;
            ?>
		</div>
		<?php if ($pgmAuditList):?>	
		<div class="container" style="width:1262px; overflow:hidden;">
            <div class="xiangxitable_wrap" style="width:2000px">
				<table cellpadding="0" cellspacing="1" class="xiangxitable" id="xxvList" align="left">		    		    
					<tr>			    
						<th width="100">
						    <?php $theState = (int)$pgmAuditList[0]['tempauditstate']?>
						    <?php if ($theState == NOT_AUDIT_TASK_STATE) :?>
						    <input type="checkbox" id="cAll"/>
						    <?php endif;?>
						        图标
						</th>
						<th>标题</th>
						<?php if (IS_CNTV):?>
						<th>频道</th>
						<th>栏目</th>
						<?php endif;?>
						<th>编辑人</th>
						<th>创建时间</th>
						<th>节目时长</th>
						<th>审核人</th>
						<th>审核时间</th>
						<th>审核结果</th>
						<th>审核意见</th>
					</tr>
					
					<?php foreach ($pgmAuditList as $pgmData) :
					         $auditState = (int)$pgmData['tempauditstate'];			
					         $tmpTaskID = (int)$pgmData['taskid'];
					         if ($auditState != NOT_AUDIT_TASK_STATE){
					            $tmpTaskID = -1;
					         }			           			        
					?>
					<tr ondblclick="dbAudit('<?php echo $tmpTaskID;?>')" class="tr_data">
						<td>
						   <?php if ($auditState == NOT_AUDIT_TASK_STATE) :?>
						   <?php if ($selectedList && in_array($pgmData['taskid'], $selectedList)):?>
						        <input type="checkbox" checked ="checked" class="c" name="select_batch_list" id="<?php echo $pgmData['taskid'];?>"/>
		                   <?php else:?>  
		                        <input type="checkbox" class="c" name="select_batch_list" id="<?php echo $pgmData['taskid'];?>"/>
		                   <?php endif;?>
						   <?php endif;?>
						   
						   <?php $newPicPath = $this->ViewOperation->getPicPath($pgmData['picpath']);?>				 
						   <img src="<?php echo $newPicPath;?>" alt="" title="<?php echo escapeValue($pgmData['pgmname']);?>" style="width:62px;height:42px;"/>
						</td>
						<td title="<?php echo escapeValue($pgmData['pgmname']);?>">
						<?php 	
						     echo $this->ViewOperation->subStringFormat(escapeValue($pgmData['pgmname']),DETAIL_NAME_NUM);	                         
						?>
						</td>
						<?php if (IS_CNTV):?>
						<td><?php echo $pgmData['channelname'];?></td>
						<td><?php echo $pgmData['columnname'];?></td>
						<?php endif;?>
						
						<td><?php echo $pgmData['creatorname'];?></td>
						<td><?php echo substr($pgmData['taskcreatedate'], 0,19);?></td>
						<td>
						    <?php
						        $length = (int)$pgmData['pgmlength'];
						        echo $this->ViewOperation->formatTimeLength($length);
						    ?>
						</td>
						<td><?php echo $pgmData['auditorname'];?></td>
						<td><?php echo substr($pgmData['taskauditdate'], 0,19);?></td>
						<td>
						    <?php
						        $stateField = Configure::read('DEFAULT_STATE_FIELD');
						        echo $stateField[$auditState];
						    ?>
						</td>
						<td style="width:280px;">
						    <div style="margin:0 10px;">
						    <?php 		
						        $note = $pgmData['contentauditnote'];				     
		                        $newNote = preg_replace('/[\r\n]+/', '<br/>', $note);                   
						        echo $this->ViewOperation->subStringFormat($newNote,AUDIT_NOTES_NUM);
						    ?>
						    </div>
						</td>
					</tr>
					<?php endforeach;?>
				</table>
				<div style="clear:both"></div>
			</div>
			<div class="hScrollPane_dragbar" >
               <div class="hScrollPane_draghandle"></div>
            </div>
		</div>	
		<div class="page">		
		    <?php
		        $url = array('controller' => 'contents','action' => 'detailList');
		        $this->Paginator->options(array(
				                      'url' => $url,
		    				          'model' => 'Content',
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
$(document).ready(function(){
	$(".jmlb").addClass("on");
});

/*多选操作*/
var $iptList = $(".tr_data");
var $chkAll = $("#cAll");

$iptList.find("input").click(function() {
	chkAllChecked();
});
function chkAllChecked() {
	if ($iptList.find("input:checked").length == $iptList.find("input").length) {
		$chkAll.attr("checked", true);
	} else {
		$chkAll.attr("checked", false);
	}
}
chkAllChecked();

$chkAll.click(function(){
	if($(this).is(":checked")){
		$(".c").attr("checked","true");
	}else{
		$(".c").removeAttr("checked");
	}

	//刷新选中状态
	if(this.checked){
		setAllCookie(1);
	}
	else{
		setAllCookie(2);
	}
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
				             if(tag === 100) {
			       	   art.dialog({ 
						title: '提示',
						content: data.msg,
						lock: true,
						esc: false,
						cancelValue:'确认',
						cancel: function () {
				        }
				   });
				   return false;
			   }
				   if(tag!=0){
					   art.dialog({ 
							title: '提示',
							content: '无法进入审核，该任务已锁定',
							lock: true,
							esc: false,
							cancelValue:'确认',
							cancel: function () {
								 var url = "<?php echo $this->Html->url(array('controller' => 'contents','action' => 'auditList','page'=>$layoutParams['page']));?>";
								 location.href = url;
					        }
					   });
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
	$('#data_list input[name="select_batch_list"]').each(function(i){
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
$('#data_list input[name="select_batch_list"]').each(function(i){
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
			   			   if(tag === 100) {
			       	   art.dialog({ 
						title: '提示',
						content: data.msg,
						lock: true,
						esc: false,
						cancelValue:'确认',
						cancel: function () {
				        }
				   });
				   return false;
			   }
			   if(tag!=0){
				   art.dialog({ 
						title: '提示',
						content: '您所选择的部分任务已经锁定，无法加入工作区',
						lock: true,
						esc: false,
						cancelValue:'确认',
						cancel: function () {
							var url = "<?php echo $this->Html->url(array('controller' => 'contents','action' => 'auditList','page'=>$layoutParams['page']));?>";
							location.href = url;
				        }
				   });
			   }
			   else{
				   var url = "<?php echo $this->Html->url(array('controller' => 'contents','action' => 'auditList','page'=>$layoutParams['page']));?>";
				   location.href = url;
			   }			     	 
		   }
	});
}
</script>
<script type="text/javascript">
var table_width = $(".xiangxitable").width();
if(table_width < 1262){

	$(".xiangxitable").width(1262);
	$(".hScrollPane_dragbar").hide(1)
}
$(".container").hScrollPane({
	mover:"table",
	moverW:function(){return $(".xiangxitable").width();}(),
	showArrow:true,
	handleCssAlter:"draghandlealter",
	mousewheel:{moveLength:207}
});
</script>
