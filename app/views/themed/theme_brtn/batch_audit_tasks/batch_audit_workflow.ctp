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
<div class="imgScroll">
  <div class="of">
	<a href="javascript:void(0)" id="leftMove"></a>
	<div class="showBox">
		<ul>
		<?php 
           $theTaskID = (int)$taskInfo['taskid'];
           foreach ($listTaskInfo as $oneTaskInfo):
               $tmpTaskID = (int)$oneTaskInfo['taskid'];  
               $tmpTaskName = $oneTaskInfo['pgmname'];  
               $picpath = $oneTaskInfo['picpath'];
               if (HTTP_LOCATION=='null'){
				    $newPicPath = $picpath;
			   }
			   else {
				    $newPicPath = str_replace('\\', '/', substr_replace($picpath, HTTP_LOCATION, 0,2));
			   }
               if ($tmpTaskID==$theTaskID):          
        ?>    
                   <li class="on" title="<?php echo $tmpTaskName;?>">
                       <?php echo $this->Html->image('img_src.png',array('data-original'=>$newPicPath,'class'=>'lazy','width'=>'60','height'=>'40'))?>
				       <span>《<?php echo $this->ViewOperation->subStringFormat($tmpTaskName, 0, 8);?>》</span>
			       </li>
        <?php  else :?>    
                   <li onclick="exchange(<?php echo $tmpTaskID;?>)" title="<?php echo $tmpTaskName;?>">
                       <?php echo $this->Html->image('img_src.png',array('data-original'=>$newPicPath,'class'=>'lazy','width'=>'60','height'=>'40'))?>				       <span>《<?php echo $this->ViewOperation->subStringFormat($tmpTaskName, 0, 8);?>》</span>
			       </li>     
        <?php  endif;?>
        <?php endforeach;?>			
		</ul>
	</div>
	<a href="javascript:void(0)" id="rightMove"></a>
  </div>
  <div class="fast"></div>
</div>
<?php endif;?>
<script type="text/javascript">
    $("img.lazy").lazyload();


	var left=location.search.split("left=")[1];
	$(".showBox>ul").css("margin-left",left+"px");
    $(".imgScroll").imgScroll();
    //切换任务
    function exchange(theID){
       document.getElementById('tg_commit').disabled=true;
 	   document.getElementById('th_commit').disabled=true;
 	   document.getElementById('bc_commit').disabled=true;
 	   document.getElementById('qx_commit').disabled=true;
 	   
   	   tmpUrl = "<?php echo $this->Html->url(array('controller'=>'batch_audit_tasks','action'=>'batchAudit'));?>";
       url = tmpUrl+"/"+theID+"/cmode:"+"<?php echo $layoutParams['model'];?>"+"/cpage:"+"<?php echo $layoutParams['page'];?>";
       location.href = url+"?left="+parseInt($(".showBox>ul").css("margin-left"));
    }
</script>

<div class="of">
	<div class="shleft">
		<div class="shlefttop">
			<div class="title">
				正在审核: <a href="#">《<?php echo $this->ViewOperation->subStringFormat($taskInfo['pgmname'], 0, AUDIT_NAME_NUM);?>》</a> 
				<select name="" onchange="change(this.value)">
				    <?php
				    if ($fileAlias):
				        $selectFileAlias = $fileAlias[0]; 
				        $defaultFileAlias= Configure::read('DEFAULT_FILE_ALIAS');				        
				        foreach ($defaultFileAlias as $oneDefault) :
				        	if (in_array($oneDefault, $fileAlias)):
				                $selectFileAlias = $oneDefault;
				            endif;
				        endforeach;
				        				        			         				         
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
			    <input id="tg_commit" type="submit" value="通过" onclick="commit(1)"/>
			    <input id="th_commit" type="submit" value="退回" onclick="commit(2)"/>
			    <input id="bc_commit" type="submit" value="保存" onclick="commit(4)"/>
			    <input id="qx_commit" type="submit" value="取消" onclick="onSetLock(1)"/>
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
				<table cellpadding="0" cellspacing="1" class="xiangxitable">
				    <?php if ($attributes): ?>
				    <?php foreach ($attributes as $attribute) :?>
				         <tr>
						     <td width="100"><?php echo $attribute['ItemName'];?>:</td>
						     <?php if ($attribute['ItemName']==PGM_LENGTH):?>
						         <td>
						         <?php 
						              $length = (int)$attribute['Value'];
						           	  echo $this->ViewOperation->formatTimeLength($length);
						         ?>
						         </td>
						     <?php elseif ($attribute['ItemName']==PGM_NOTE):?>
						         <td>
						            <div style="overflow-y:auto;height:165px;">
						            <?php echo $attribute['Value'];?>
						            </div>
						         </td>
						     <?php elseif ($attribute['ItemName']==VOICE_TIME_CODE):?>
						         <td>
						            <div style="overflow-y:auto;height:165px;">
						            <?php ?>
						            </div>
						         </td>
						     <?php else:?>
						         <td><?php echo $attribute['Value'];?></td>
						     <?php endif;?>						     
					     </tr>				    
				    <?php endforeach;?>
				    <?php endif;?>			    
				</table>
				</div>				
			</div>
			<!-- 结束--元数据 -->
			
			<!-- 开始--计审 -->
			<div class="sjjg" id="js_jmn" style="display:none;">
			<div id="tech">			    
			    <iframe id="myiframe" width="100%" height="797" frameborder="0" scrolling="no" src="<?php echo $this->Html->url(array('controller' => 'audit_tasks','action' => 'techData',$taskInfo['taskid'],$selectFileAlias));?>">
			    </iframe>							
		    </div>
			</div>
		    <!-- 结束--计审 -->
		    
		</div>
	</div>
</div>
<script language="javascript" for="EncoderPlayer" event="TrimInOutLoaded(lTrimIn, lTrimOut)" type="text/javascript">getTrimInOutLoaded(lTrimIn, lTrimOut);</script>
<script type="text/javascript"> 
   //页面加载时
   $(document).ready(function(){
	   $(".snt").hide();
	   $(".xx").hide();

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

   	   //设置播放器相关参数
   	   var strVideo = "";
       <?php  		    
   		    $firstFileStr = '';
   		    $auditCount = 0;
   		    $firstFile = array();
   		    if($taskFile):
   		    foreach ($taskFile as $tempFile) :
   		    	$tempAlias = $tempFile['filealias'];
   		    	if ($tempAlias == $selectFileAlias) :  		    		
   		    		$tempClip = (int)$tempFile['clipclass'];
   		    		if ($tempClip == IS_VIDEO_FILE):
   		    			$firstFileStrt = $tempFile['filename'];
   		    			//拼接Wms前缀
   		    			$firstFileStr = $wmsPrefixUrl.$firstFileStrt;
   		    		   	                
   	                else :
   	                	$firstFile[] = $tempFile;
   	                endif;
   		    	endif;
   		    endforeach;   		    
   		    if (!isset($firstFile[0])){			
   		     	$firstFile = array($firstFile);			
   		    }
   		    $auditCount = count($firstFile);
   		    
   		    endif;
   	    ?>
   	
   	    //设置视频
        strVideo = "<?php echo addslashes($firstFileStr);//addcslashes($firstFileStr, '\\');?>";    
   	    onSetVideoFile(strVideo); 

   	    //设置音频
        var auditCount = <?php echo $auditCount;?>;
    	if(auditCount == 1){
    		sModeId = 0;
    	}	
    	else if(auditCount <= 2){
   	 	    sModeId = 1;
   	    }
    	else if(auditCount <= 6){
   	    	sModeId = 2;
     	}
    	else if(auditCount <= 8){
   	    	sModeId = 3;
   	    }
    	else{
   		    sModeId = 1;
   	    }

   	    onSetAudioMode(sModeId);
     	<?php 
   	        if ($firstFile):
   	        foreach ($firstFile as $theTempFile) :
   	        	$channel = (int)$theTempFile['channel'];
   	        	$clipClass = (int)$theTempFile['clipclass'];
   	        	$audioFilet = $theTempFile['filename'];
   	        	//拼接wms前缀
   	        	$audioFile = $wmsPrefixUrl.$audioFilet;
    	?>  
   	            var strAudioPath = "<?php echo addslashes($audioFile);?>";
   	            var sSrcAudioTrack = <?php echo $channel;?>;
   	            var sDstAudioTrack = <?php echo $clipClass;?>;
   	            onSetAudioFile(strAudioPath,sSrcAudioTrack,sDstAudioTrack);
   	    <?php      	
   	        endforeach;
   	        endif;
   	    ?>


   	    //每隔单位时间提交一次时间戳
     	var timeOut = <?php echo LOCK_TIMEOUT*1000;?>;
   	    onSetLock(0);
     	setInterval("onSetLock(0)",timeOut);
   }); 


   //码率切换 
   function change(value){
	   //更新技审信息
	   var frame = document.getElementById("myiframe");
	   var url = " <?php echo $this->Html->url(array('controller' => 'audit_tasks','action' => 'techData',$taskInfo['taskid']));?>"+"/"+value;     
	   frame.src = url;

	   //设置播放器
	   var strVideo = "";
	   var audioCount = 0;
   	   <?php   	
   		    $theFileStr = '';
   		    if ($taskFile):
   		    foreach ($taskFile as $tempFile) : 
   		    	$tempClip = (int)$tempFile['clipclass'];
   		    	$tempAlias = $tempFile['filealias']; 		    	   		    		   		    	  		    		
   		    	$theFileStrt = $tempFile['filename'];
   		    	if ($tempClip == IS_VIDEO_FILE) :
   		    	    
   		    	     //拼接wms地址
   		    	     $theFileStr = $wmsPrefixUrl.$theFileStrt;
   	   ?>   	   
   	                var theAlias =  "<?php echo $tempAlias;?>";
   	                if(theAlias == value) {
   	                    //获取视频文件路径
   	   	            	strVideo = "<?php echo addslashes($theFileStr);?>";
   	    	        }   	           
   	   <?php 
   	            else:
   	   ?>
   	                var theAliast =  "<?php echo $tempAlias;?>";
                    if(theAliast == value) {
                        //获取音频数目
          	            audioCount++;
                    }
   	   <?php             	             		    
   		        endif;
   		    endforeach;   	
   		    endif;	    
   	   ?> 

   	    //设置视频
     	onSetVideoFile(strVideo); 
   	   
        //设置音频
        var sModeId=1;
        if(audioCount == 1){
        	sModeId = 0;
        }
        else if(audioCount <= 2){
        	sModeId = 1;
        }
        else if(audioCount <= 6){
        	sModeId = 2; 
        }
        else if(audioCount <= 8){
        	sModeId = 3;
        }
        else{
        	sModeId = 1;
        }

  	    onSetAudioMode(sModeId); 	  
  	    var theAlias;
  	    <?php   	
     		    if ($taskFile):
     		    foreach ($taskFile as $tempFile) : 
     		    	$tempClip = (int)$tempFile['clipclass'];     		    			    	   		    		   		    	  		    		
     		    	if ($tempClip != IS_VIDEO_FILE) :
     		    	    $tempAlias = $tempFile['filealias']; 
     		    	    
     		    	    $tempChannel = (int)$tempFile['channel'];
   	        	        $tempClipClass = (int)$tempFile['clipclass'];
   	        	        $tempAudioFilet = $tempFile['filename'];  	        	
   	        	        //拼接wms前缀
   	        	        $tempAudioFile = $wmsPrefixUrl.$tempAudioFilet;
     	?>   	   
     	                theAlias =  "<?php echo $tempAlias;?>";
     	                if(theAlias == value) {
     	                    //设置对应音频    	   	           
     	   	                var strAudioPath = "<?php echo addslashes($tempAudioFile);?>";
     	   	                var sSrcAudioTrack = <?php echo $tempChannel;?>;
     	   	                var sDstAudioTrack = <?php echo $tempClipClass;?>;
     	   	                onSetAudioFile(strAudioPath,sSrcAudioTrack,sDstAudioTrack);
     	    	        }   	           
     	<?php             	             		    
     		        endif;
     		    endforeach;   	
     		    endif;	    
     	?> 
          	  
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
   function commit(state){
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
	   var datas = "";
	   <?php if($taskInfo['taskid']):?>
	   datas = "ContentAuditNote="+note+"&TaskID="+<?php echo $taskInfo['taskid'];?>+"&State="+state;
	   <?php endif;?>
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
								window.opener=null;
								window.open('','_self');
								window.close();
						    }
							
				        }
				   });
			   }
		});
	   
   }

   //设置锁定
   function onSetLock(value){//value:0->加锁 ；1->解锁(取消)；2->解锁(浏览)；3->解锁(统计)；-1->解锁(查询)
       if(value==1){
    	   art.dialog({ 
   			   title: '消息',
   			   content: '取消当前任务还是取消所有任务？',
   			   lock: true,
   			   button: [
   		   			{
   			            value: '取消当前任务',
   			            callback: function () {
   			            	tmpSetLock(value,1);
   			            },  			          
   			            focus: true
   			        },
   			        {
   			            value: '取消所有任务',
   			            callback: function () {
   			            	tmpSetLock(value,2);
   			            }
   			        },
   			        {
   			            value: '关闭'
   			        }
   			    ]
   	       }); 
       }
       else{
    	   tmpSetLock(value,0);
       }                  
   }
   function tmpSetLock(value,cancel){//cancel为1：取消当前任务；为2：取消所有任务
	   if(value!=0){
    	   document.getElementById('tg_commit').disabled=true;
    	   document.getElementById('th_commit').disabled=true;
    	   document.getElementById('bc_commit').disabled=true;
    	   document.getElementById('qx_commit').disabled=true;
       }
	   var datas = "";
	   <?php if($taskInfo['taskid']):?>
	   datas = "TaskID="+<?php echo $taskInfo['taskid'];?>+"&SetID="+value+"&ModeID=batch"; 	
	   if(cancel!=0){
		   datas = datas +"&Cancel=" +cancel;
	   }
	   var taskIDList = '<?php echo $taskIDListStr?>';
	   datas = datas +"&ListStr=" +taskIDList;

       $.ajax({
			   type: "POST",
			   url: "<?php echo AJAX_DOMAIN;?>"+"/audit_handles/refresh",
			   data: datas,
			   success: function(msg){
			      if(value==1){
				      if(cancel==1){
				    	  url = "<?php echo $this->Html->url(array('controller'=>'batch_audit_tasks','action'=>'batchAudit','cmode'=>$layoutParams['model'],'cpage'=>$layoutParams['page']));?>";
				      }
				      else {
				    	  url = "<?php echo $this->Html->url(array('controller'=>'contents','action'=>$contentAction,'page'=>$layoutParams['page']));?>";
				      }				      
				      location.href = url;
				  }	
			      else if(value==3){
			    	  url = "<?php echo $this->Html->url(array('controller'=>'accounts','action'=>'statistics','cpage'=>$layoutParams['page']));?>";
			    	  location.href = url;
			      }	
			      else if(value==-1){
			    	  submitAction();
			      }		 
			   }
   	   });
       <?php endif;?>
   }	   
</script>