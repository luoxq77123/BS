<div class="of">
	<a href="javascript:void(0)" id="leftMove"></a>
	<div class="showBox">	
		<ul>
		<?php 
           $theTaskID = (int)$selectedTaskID;
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
               <div>
                   <li class="on" title="<?php echo $tmpTaskName;?>">
                       <?php echo $this->Html->image('img_src.png',array('data-original'=>$newPicPath,'class'=>'lazy','width'=>'60','height'=>'40'))?>
				       <span>《<?php echo $this->ViewOperation->subStringFormat($tmpTaskName, 0, 8);?>》</span>
                       <b onclick="" class="lock"></b>
			       </li>
			   </div>
        <?php  else :?>
               <div>    
                   <li onclick="exchange(<?php echo $tmpTaskID;?>)" title="<?php echo $tmpTaskName;?>">
                       <?php echo $this->Html->image('img_src.png',array('data-original'=>$newPicPath,'class'=>'lazy','width'=>'60','height'=>'40'))?>
				       <span>《<?php echo $this->ViewOperation->subStringFormat($tmpTaskName, 0, 8);?>》</span>
                       <b title="解锁" onclick="unLockTask(<?php echo $tmpTaskID;?>,2)" class="lock"></b>
			       </li>			  
			   </div> 
        <?php  endif;?>
        <?php endforeach;?>			
		</ul>
	</div>
	<a href="javascript:void(0)" id="rightMove"></a>
  </div>
  <div class="fast"> 
     <a href="javascript:void(0)" class="fast_btn fast_btn_l"></a>
  	 <a href="javascript:void(0)" class="fast_btn fast_btn_r"></a>
  	 <div class="fast_range">
    	<div></div>
     </div>    
  </div>