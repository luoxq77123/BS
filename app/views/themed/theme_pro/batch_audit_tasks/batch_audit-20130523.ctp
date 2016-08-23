<?php 
//判断从列表页过来的模式
if ($layoutParams['model']==THUMB_MODEL){				                	       
	$contentAction = 'auditList';
}
else {				                	
	$contentAction = 'detailList';
}
//获取wms地址
$wmsPrefixUrl = WMS_PREFIX.WMS_URL.WMS_SUFFIX;
?>

<?php if($listTaskInfo):?>
<div class="imgScroll" style="height:130px;">
    <div class="t_scrollbox" id="horizontal" style="margin-left:40px;margin-right:40px;">
        <div class="t_slyWrap" style="width:1200px;">	
                <?php $count = count($listTaskInfo);?>
                <?php if ($count > 5):?>	
				<div class="t_scrollbar">
					<div class="handle"></div>
				</div>
				<?php endif;?>
				<div class="t_sly" style="height:65px;" data-options='{ "horizontal": 1, "itemNav": "smart", "dragContent": 1, "startAt": <?php echo $taskIndex;?>, "scrollBy": 1 }'>
					<ul>			          
			        <?php 
                    foreach ($listTaskInfo as $oneTaskInfo):
                        $tmpTaskID = (int)$oneTaskInfo['taskid'];  
                        $tmpTaskName = $oneTaskInfo['pgmname'];  

			            $newPicPath = $this->ViewOperation->getPicPath($oneTaskInfo['picpath']);
//                      if ($tmpTaskID==$theTaskID):          
                    ?>    
                        <li onclick="exchange_t(<?php echo $tmpTaskID;?>)" title="<?php echo $tmpTaskName;?>" id="<?php echo $tmpTaskID;?>">
                             <?php echo $this->Html->image('img_src.png',array('data-original'=>$newPicPath,'class'=>'lazy','width'=>'60','height'=>'40'))?>
				             <span>《<?php echo $this->ViewOperation->subStringFormat($tmpTaskName, 0, 8);?>》</span>
                             <b title="解锁" class="lock"></b>
			            </li>
                    <?php endforeach;?>	  
					</ul>
				</div>
				<?php //if ($count > 5):?>			
				<ul class="t_pages"></ul>
				<?php //endif;?>			
		</div>
    </div>
</div>
<?php endif;?>
<script type="text/javascript">



function exchange_t(theID){
    document.getElementById('tg_commit').disabled=true;
	document.getElementById('th_commit').disabled=true;
	document.getElementById('bc_commit').disabled=true;
    document.getElementById('qx_commit').disabled=true;
	
    var curtaskid = <?php echo $taskInfo['taskid']?>;
    var datas = "curtaskid="+curtaskid;
    $.ajax({
		   type: "POST",
		   url: "<?php echo AJAX_DOMAIN;?>"+"/batch_audit_tasks/changeTask",
		   data: datas,
		   success: function(msg){
			   tmpUrl = "<?php echo $this->Html->url(array('controller'=>'batch_audit_tasks','action'=>'batchAudit'));?>";
			   url = tmpUrl+"/"+theID+"/cmode:"+"<?php echo $layoutParams['model'];?>"+"/cpage:"+"<?php echo $layoutParams['page'];?>";
			   location.href = url;
		   }
	});
}


$(".imgScroll").find("li").hover(function() {
	$(this).addClass("hover");
},function(){
	$(this).removeClass("hover");
});
$(function($){
	// 主要调用部分
	$(document).on('activated',function(event){
		var $section = $(".t_scrollbox");
		var $frame = $section.find('.frame'),
			$scrollbar = $section.find('.t_scrollbar');	
		
		$section.find(".t_slyWrap").each(function(i,e){
			var cont = $(this),
				frame = cont.find(".t_sly"),
				scrollbar = cont.find(".t_scrollbar"),
				pagesbar = cont.find(".t_pages"),
				options = frame.data("options");

			options = $.extend({},options,{
				scrollBar: scrollbar,
				pagesBar: pagesbar
			});

			frame.sly( options );

			//解锁
			$(".lock").click(function(e){
				if (event.stopPropagation) {
					event.stopPropagation();
				}
				else if (window.event) {
					window.event.cancelBubble = true;
				}

				var theid = $(this).parent().attr("id");
				$(this).parent().remove();
				frame.sly('reload');
				
				unLockTask(theid,2);
			});

		});
	}).trigger('activated');	
});
</script>



<script type="text/javascript">
$("img.lazy").lazyload();


function unLockTask(id,tag){
//	var contents="";
//	if(tag==1){
//		contents = "当前任务正在审核，确定解除锁定吗？";
//	}
//	else{
//		contents = "您好，确定将该任务解除锁定吗";
//	}
//
//	art.dialog({ 
//		   title: '消息',
//		   content: contents,
//		   lock: true,
//		   button: [
//	   			{
//		            value: '确定',
//		            callback: function () {
//		            	
//		            },  			          
//		            focus: true
//		        },			        
//		        {
//		            value: '取消'
//		        }
//		    ]
//  });
   unLock(id);
}
function unLock(id){
	var curtaskid = <?php echo $taskInfo['taskid']?>;
	var datas = "unlocktaskid="+id+"&curtaskid="+curtaskid;
	var url = "<?php echo $this->Html->url(array('controller'=>'batch_audit_tasks','action'=>'taskArea'));?>";
	$.ajax({
		type: "POST",
        dataType:"html", 
		evalScripts:true, 
//		model:"Content", 
		success:function (data, textStatus) {
			//$("#taskarea").html(data);
		}, 
		data: datas,
		url:url
	});
}
</script>

<div class="of">
	<div class="shleft">
		<div class="shlefttop">
			<div class="title">
				
				<select name="" onchange="changeFilealias(this.value,<?php echo $taskInfo['taskid'];?>)">
				    <?php
				    if ($fileAlias):				        				        			         				         
				        foreach ($fileAlias as $tempFileAlias) :
				            if ($tempFileAlias == $selectFileAlias) :		             
				    ?>
				               <option value= "<?php echo $tempFileAlias;?>" selected="selected"><?php echo $tempFileAlias;?></option>
				    <?php   else :?>				    
				               <option value= "<?php echo $tempFileAlias;?>"><?php echo $tempFileAlias;?></option>				    
				    <?php  
				            endif;
				        endforeach;
				    endif;
				    ?>	
				</select>
                                正在审核: 
                <a href="#"><?php echo $this->ViewOperation->subStringFormat($taskInfo['pgmname'], 0, AUDIT_NAME_NUM);?></a> 
			</div>
			<div class="play">
				<object classid="clsid:F7944BBA-9B19-44EF-B428-17D527982A2D" type="application/x-itst-activex" style="border:0px;width:590px;height:465px;" id="EncoderPlayer">				   
                </object>
			</div>
		</div>	
		<div class="shleftbottom">
			<h3 class="title">审核意见</h3>
			<textarea name="" id="ContentAuditNote"><?php echo $taskInfo['contentauditnote'];?></textarea>
			<div class="bt">		    			    
			    <input id="tg_commit" type="submit" value="通过" onclick="commit(1,<?php echo $taskInfo['taskid'];?>)"/>
			    <input id="th_commit" type="submit" value="退回" onclick="commit(2,<?php echo $taskInfo['taskid'];?>)"/>
			    <input id="bc_commit" type="submit" value="保存" onclick="commit(4,<?php echo $taskInfo['taskid'];?>)"/>
			    <input id="qx_commit" type="submit" value="取消" onclick="onUpdateState(1)"/>
			</div>
		</div>
	</div>
	<div class="shright">
		<div class="tabtop">
			<ul>
				<li id="ysj_jm"  style="display:none;">节目元数据</li>
				<li id="js_jm"   style="display:none;">技审结果</li>
			</ul>
		</div>
		
		<div class="tabbottom">			    	
		    <!-- 开始--元数据-->
			<div class="jmy" id="ysj_jmn" style="display:none;">
			    <?php echo $this->Html->image('fbpt.png',array('alt'=>'','class'=>'fbpt'))?>						    
				<div id="meta">
				<?php if ($layoutParams['userType'] != TECH_TASK_TYPE):?>
				<ul class="of">
				    <?php if ($platFormInfos):?>
				    <?php foreach ($platFormInfos as $platFormInfo) :?>
				         <?php 
				              $isSelected = (int)$platFormInfo['IsSelected'];
				              if ($isSelected == IS_SELECTED) :
				         ?>
				             <li>				                
				                <input type="checkbox" checked ="checked" id="<?php echo $platFormInfo['PlatFormID'];?>" name="plat" value="<?php echo $platFormInfo['PlatFormName'];?>"/>
				                <?php echo $platFormInfo['PlatFormName'];?>
				             </li>
				         <?php else :?>
				             <li>
				                <input type="checkbox" id="<?php echo $platFormInfo['PlatFormID'];?>" name="plat" value="<?php echo $platFormInfo['PlatFormName'];?>"/>
				                <?php echo $platFormInfo['PlatFormName'];?>
				             </li>
				         <?php endif;?>
				    <?php endforeach;?>
				    <?php endif;?>
				</ul>
				<table cellpadding="0" cellspacing="0" class="xiangxitable">
				    <?php if ($attributes): ?>
				    <?php foreach ($attributes as $attribute) :?>
				         <tr>
						     <td width="100"><?php echo $attribute['ItemName'];?>:</td>
						     <?php if ($attribute['ItemName']==PGM_LENGTH):?>
						         <td style="height:80px;">
						         <?php 
						              $length = (int)$attribute['Value'];
						           	  echo $this->ViewOperation->formatTimeLength($length);
						         ?>
						         </td>
						     <?php elseif ($attribute['ItemName']==PGM_NOTE):?>
						         <td style="height:80px;">
						            <div style="overflow-y:auto;height:80px;">
						            <?php echo $attribute['Value'];?>
						            </div>
						         </td>
						     <?php elseif ($attribute['ItemName']==VOICE_TIME_CODE):?>
						         <td style="height:80px;">
						            <div style="overflow-y:auto;height:80px;">
						            <?php ?>
						            </div>
						         </td>
						     <?php else:?>
						         <td style="height:80px;"><?php echo $attribute['Value'];?></td>
						     <?php endif;?>						     
					     </tr>				    
				    <?php endforeach;?>
				    <?php endif;?>			    
				</table>
				<div class="page">				    
				    <?php $this->Paginator->options(array(
				                         'url' => array('controller' => 'app_audits','action' => 'metaData',$taskInfo['taskid']),
				                         'model' => 'Content',
	  		                             'update' => '#meta',
	  		                             'evalScripts' => true//,
	  		                             //'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
	  		                             //'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
	  		                             )); 
	  		        ?>
	  		        <?php $metaPageParams = $this->Paginator->params('Content');?>
					<div class="fl">
						每页 
						<span class="red"><?php echo $metaPageParams['options']['limit'];?></span> 
						条 页次:<span class="red">
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
					    echo $this->Paginator->first('首页',array('model' => 'Content','tag' => 'span','class'=>'a' ));	       
		                echo $this->Paginator->prev('上一页', array(), null, array('model' => 'Content','tag' => 'span','class' => 'b'));
		                echo $this->Paginator->numbers(array('model' => 'Content','before' => '','tag' => 'span','class' => 'number','separator' => '','modulus'=>4));
		                echo $this->Paginator->next('下一页', array(), null, array('model' => 'Content','tag' => 'span','class' => 'b'));		   
		                echo $this->Paginator->last('尾页',array('model' => 'Content','tag' => 'span','class'=>'a' ));
		            ?>
		            </div>					
				</div>
				<?php endif;?>
				</div>				
			</div>
			<!-- 结束--元数据 -->
			
			<!-- 开始--计审 -->
			<div class="sjjg" id="js_jmn" style="display:none;">
			<div id="tech">			    
			    <iframe id="myiframe" width="100%" height="797" frameborder="0" scrolling="no" src="<?php echo $this->Html->url(array('controller' => 'app_audits','action' => 'techData',$taskInfo['taskid'],$selectFileAlias));?>">
			    </iframe>							
		    </div>
			</div>
		    <!-- 结束--计审 -->
		    
		</div>
	</div>
</div>
<?php echo $this->Js->writeBuffer();?>
<script language="javascript" for="EncoderPlayer" event="TrimInOutLoaded(lTrimIn, lTrimOut)" type="text/javascript">getTrimInOutLoaded(lTrimIn, lTrimOut);</script>
<script type="text/javascript">
$(document).ready(function(){
	$(".wdsh").addClass("on");

	<?php if (!TASK_AUDIT_TYPE):?>
        $("#ysj_jm").show();
        $("#js_jm").show();

        $("#ysj_jm").addClass("on");
        $("#js_jm").removeClass("on");
    
        $("#ysj_jmn").show();
        $("#js_jmn").hide();
    <?php else :?>
    <?php 
        if($layoutParams['userType'] == CONTENT_TASK_TYPE):
    ?> 
        $("#ysj_jm").show();
        $("#js_jm").hide();

        $("#ysj_jm").addClass("on");
        $("#js_jm").removeClass("on");
        
        $("#ysj_jmn").show();
        $("#js_jmn").hide();
    <?php
        elseif ($layoutParams['userType'] == TECH_TASK_TYPE):
    ?>
        $("#ysj_jm").hide();
        $("#js_jm").show();

        $("#ysj_jm").removeClass("on");
        $("#js_jm").addClass("on");
        
        $("#ysj_jmn").hide();
        $("#js_jmn").show();
    <?php
         endif;
    ?>
    <?php endif;?>


    var filedata = '<?php echo $taskFile;?>';
    filedata = filedata.replace(/\\/g,"\\\\");	
    var jsonObj = JSON.parse(filedata);
    setFileInfo(jsonObj);
});

function setFileInfo(data){
	var len = data.length;
	for(i = 0;i < len; i++){
    	var tmpOneObj = data[i];
    	var filenames = tmpOneObj.filename;
    	var clipclass = tmpOneObj.clipclass;
    	var channel = tmpOneObj.channel;

    	//获取并设置视频路径		
    	var filepath = "<?php echo addslashes($wmsPrefixUrl);?>"+filenames;
    	if(clipclass == 1){
    		onSetVideoFile(filepath);

    		//设置音频模式
    		var audiomodel = getAudioModel(len-1);
    		onSetAudioMode(audiomodel);
    	}
    	else {
    		onSetAudioFile(filepath,channel,clipclass);
    	}
    }
}
function getAudioModel(audiocount){
	if(audiocount == 1){
		return 0;
	}	
	else if(audiocount <= 2){
		return 1;
	}
	else if(audiocount <= 6){
		return 2;
 	}
	else if(audiocount <= 8){
		return 3;
	}
	return 1;
}
//播放器系列操作
function onSetVideoFile(strVideo){  //此函数在加载播放器后就立即调用。功能：设置选择的视频文件
    var player=document.getElementById('EncoderPlayer');       
    player.AddVideo(strVideo);        
}
function onSetAudioMode(sModeId){  //此函 数在加载播放器后就立即调用。功能：设置选择的音频文件
    var player=document.getElementById('EncoderPlayer');       
    player.SetAudioMode(sModeId);        
}
function onSetAudioFile(strAudioPath,sSrcAudioTrack,sDstAudioTrack){  //此函数在加载播放器后就立即调用。功能：设置选择的音频文件
    var player=document.getElementById('EncoderPlayer');       
    player.AddAudio(strAudioPath,sSrcAudioTrack,sDstAudioTrack);        
}

</script>
<script type="text/javascript"> 
function changeFilealias(value,taskid){
	var datas = "taskid="+taskid+"&filealias="+value;
	$.ajax({
		   type: "POST",
		   url: "<?php echo AJAX_DOMAIN;?>"+"/batch_audit_tasks/getFileInfo",
		   data: datas,
		   success: function(msg){
               //设置文件
			   var filedata = msg;//msg.replace(/\\/g,"\\\\");
			   var jsonObj = JSON.parse(filedata);
			   setFileInfo(jsonObj);

			   //更新技审
			   var frame = document.getElementById("myiframe");
			   var url = "<?php echo $this->Html->url(array('controller' => 'app_audits','action' => 'techData'));?>"+"/"+taskid+"/"+value;     
			   frame.src = url;
		   }
	});
}
function getTrimInOutLoaded(lTrimIn, lTrimOut){	 	   
	   var inValue = lTrimIn << 0;
	   var outValue = lTrimOut << 0;
	   
	   var data = "";
	   if(inValue!=-1 && outValue!=-1){
		   data = formatDate(lTrimIn)+"---"+formatDate(lTrimOut)+"  :";	   
	   }
	   else if(inValue==-1 && outValue!=-1){
		   message = '播放器未设置入点';
		   setArtDialog(message);

		   data = "---"+formatDate(lTrimOut)+"  :";
	   }
       else if(inValue!=-1 && outValue==-1){
 	       message = '播放器未设置出点';
 	       setArtDialog(message);
 	   
 	       data = formatDate(lTrimIn)+"---"+"  :";
	   }
       else{
 	       message = '播放器未设置出入点';
 	       setArtDialog(message);
       }
    
	   if(data){
		   var allData = "";
		   var preData = $("#ContentAuditNote").val();
		   if(preData){
			   allData = preData + "\n" + data;
		   }
		   else{
			   allData = data;
		   }
	       $("#ContentAuditNote").val(allData);
	   }
}
function formatDate(time){
	   var frameNum = <?php echo TASK_FRAME_NUM;?>;
	   var frameNumber = parseInt(frameNum);

	   var frame = time%frameNumber;
	   var seconds = Math.floor(time/frameNumber);

	   var second = seconds%60;
	   var minutess = Math.floor(seconds/60);

	   var minutes = minutess%60;
	   var hours = Math.floor(minutess/60);

	   return ((hours<10) ? "0" + hours : hours) + ":" +
			  ((minutes<10) ?"0" + minutes : minutes) + ":" +
			  ((second<10) ?"0" + second : second) + ":" +
			  ((frame<10) ?"0"+frame:frame);
}
  


//整个任务提交
function commit(state,taskid){
	   var inputs = document.getElementsByTagName("input");	 
	   var platNum = 0;  
	   var platData = "";
	   for(i=0 ; i<inputs.length ; i++){
		   if(inputs[i].name == "plat"){
			  var temp = document.getElementById(inputs[i].id).checked;
			  platData += "&"+inputs[i].id+"="+temp;
			  if(temp){
				  platNum++;
			  }
	       }
	   } 
	   
	   <?php if($layoutParams['userType'] != TECH_TASK_TYPE):?>
	   if (platNum == 0){
		   art.dialog({ 
			    title: '提示',
			    content: '请选择对应的发布平台',
			    lock: true,
			    okValue: '确认',  
			    ok: function () {
				   return true;
			    }
		    });
		   return;
	   }
	   <?php endif;?>


	   //锁定对应按钮
	   if(state != 4){
 	       document.getElementById('tg_commit').disabled=true;
 	       document.getElementById('th_commit').disabled=true;
 	       document.getElementById('bc_commit').disabled=true;
 	       document.getElementById('qx_commit').disabled=true;
       }

	   //通过或退回只能提交一次    	   
	   var note = $("#ContentAuditNote").val();
	   var datas = "ContentAuditNote="+note+"&TaskID="+taskid+"&State="+state;
	   datas = datas+platData; 
	
	   $.ajax({
			   type: "POST",
			   url: "<?php echo AJAX_DOMAIN;?>"+"/audit_handles/commitTask",
			   data: datas,
			   success: function(msg){
				   art.dialog({ 
						title: '提示',
						content: msg,
						lock: true,
						esc: false,
						cancelValue:'确认',
						cancel: function () {
							if(state != 4){
								url = "<?php echo $this->Html->url(array('controller'=>'batch_audit_tasks','action'=>'batchAudit','cmode'=>$layoutParams['model'],'cpage'=>$layoutParams['page']));?>";
							    location.href = url;
						    }
				        }
				   });
			   }
		});   
}
//进行任务状态更新，value：1->取消;3->统计;4->查询;5->节目列表
function onUpdateState(value,tag){
	if(value==1){
 	   art.dialog({ 
			   title: '消息',
			   content: '当前任务正在审核，是否确定退出审核？',
			   lock: true,
			   button: [
		   			{
			            value: '是',
			            callback: function () {
			            	tmpUpdateState(value);
			            },  			          
			            focus: true
			        },			        
			        {
			            value: '否'
			        }
			    ]
	     }); 
    }
    else{
    	tmpUpdateState(value,tag);
    }
}
function tmpUpdateState(value,tag){
	if(value!=0){
 	   document.getElementById('tg_commit').disabled=true;
 	   document.getElementById('th_commit').disabled=true;
 	   document.getElementById('bc_commit').disabled=true;
 	   document.getElementById('qx_commit').disabled=true;
    }

	var taskid = getCurTaskID();
	var datas = "TaskID="+taskid+"&SetID="+value;

    $.ajax({
		type: "POST",
		url: "<?php echo AJAX_DOMAIN;?>"+"/audit_handles/updateTaskState",
		data: datas,
		success: function(msg){
		    if(value==1){
				url = "<?php echo $this->Html->url(array('controller'=>'contents','action'=>$contentAction,'page'=>$layoutParams['page']));?>";
                location.href = url;
		    }	
		    else if(value==3){
			    url = "<?php echo $this->Html->url(array('controller'=>'accounts','action'=>'statistics','cmode'=>$layoutParams['model'],'cpage'=>$layoutParams['page']));?>";
			    location.href = url;
		    }
		    else if(value==4){
			    submitAction(tag);
		    }
		    else if(value==5){
		    	url = "<?php echo $this->Html->url(array('controller'=>'contents','action'=>$contentAction,'page'=>$layoutParams['page']));?>";
			    location.href = url;
		    }		 
	    }
	});
}
function getCurTaskID(){
	return <?php echo $taskInfo['taskid'];?>;
}
</script>