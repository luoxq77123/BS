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
			<textarea name="" id="ContentAuditNote" disabled="disabled"><?php echo $taskInfo['contentauditnote'];?></textarea>
			<div class="bt">		    			    
<!--			    <input id="tg_commit" type="submit" value="通过" onclick="commit(1)"/>-->
<!--			    <input id="th_commit" type="submit" value="退回" onclick="commit(2)"/>-->
<!--			    <input id="bc_commit" type="submit" value="保存" onclick="commit(4)"/>-->
				<a href="<?php echo $this->Html->url(array('controller' => 'contents','action' => $contentAction,'page'=>$layoutParams['page']));?>">取消</a>
			</div>
		</div>
	</div>
	<div class="shright">
		<div class="tabtop">
			<ul>
				<li id="ysj_jm"  style="display:none;">节目元数据</li>
				<li id="js_jm"   style="display:none;">技审结果</li>
			</ul>
			<div class="fr">
				<span>浏览模式</span><a href="<?php echo $this->Html->url(array('controller' => 'audit_tasks','action' => 'auditData',$taskInfo['taskid'],'cmode'=>$layoutParams['model'],'cpage'=>$layoutParams['page']));?>"></a>
			</div>
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
				<table cellpadding="0" cellspacing="1" class="xiangxitable">
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
<script type="text/javascript"> 

   //页面加载时
   $(document).ready(function(){

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
   }); 


   //码率切换 
   function change(value){
	   //更新技审信息
	   var frame = document.getElementById("myiframe");
	   var url = " <?php echo $this->Html->url(array('controller' => 'app_audits','action' => 'techData',$taskInfo['taskid']));?>"+"/"+value;     
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
     	                var theAlias =  "<?php echo $tempAlias;?>";
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
   function onSetLock(value,tag){
	   if(value==3){
		   url = "<?php echo $this->Html->url(array('controller'=>'accounts','action'=>'statistics','cpage'=>$layoutParams['page']));?>";
	       location.href = url;
	   }
	   else if(value==-1){
	       submitAction(tag);
	   }
   }
</script>