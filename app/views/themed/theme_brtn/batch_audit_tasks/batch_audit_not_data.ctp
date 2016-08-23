<div class="imgScroll">
   <div class="imgScroll_noData">
      <?php echo $this->Html->image('imgScroll_noData.png',array('usemap'=>'#Map'))?>
      <map name="Map" id="Map">
         <area shape="rect" coords="307,25,372,42" href="#" onclick="submitAction(3)"/>
      </map>
  </div>
</div>

<div class="of">
	<div class="shleft">
		<div class="shlefttop">
			<div class="title">
				正在审核: <a href="#">无</a> 
				<select name="" onchange="" disabled="disabled">	
				</select>
			</div>
			<div class="play">
				<object classid="clsid:F7944BBA-9B19-44EF-B428-17D527982A2D" type="application/x-itst-activex" style="border:0px;width:590px;height:465px;" id="EncoderPlayer">				   
                </object>
			</div>
		</div>	
		<div class="shleftbottom">
			<h3 class="title">审核意见</h3>
			<textarea name="" id="ContentAuditNote" disabled="disabled"></textarea>
			<div class="bt">		    			    
			    <input id="tg_commit" type="submit" value="通过" onclick="" disabled="disabled"/>
			    <input id="th_commit" type="submit" value="退回" onclick="" disabled="disabled"/>
			    <input id="bc_commit" type="submit" value="保存" onclick="" disabled="disabled"/>
			    <input id="qx_commit" type="submit" value="取消" onclick="" disabled="disabled"/>
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
			    <div class="jmy sh_noDate1"></div>				
			</div>
			<!-- 结束--元数据 -->
			
			<!-- 开始--计审 -->
			<div class="sjjgnodata" id="js_jmn" style="display:none;">
			<div id="tech">
			     <div class="sjjgnodata sh_noDate"></div>
		    </div>
			</div>
		    <!-- 结束--计审 -->
		    
		</div>
	</div>
</div>
<script language="javascript" for="EncoderPlayer" event="TrimInOutLoaded(lTrimIn, lTrimOut)" type="text/javascript"></script>
<script type="text/javascript">
//页面加载时
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
});
</script>