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
		
		<div class="shleftbottom" style="height: 30px; margin-top: 3px;">
            <h3 class="title">
                <ul class="of">
                    <li>
                        <input type="checkbox" checked="checked" name="plat" value="入媒资库"/> 入媒资库
                    </li>
                </ul>
            </h3>
        </div>

        <div class="shright" style="width:612px;margin-left:1px;margin-top:0;">
            <div class="tabbottom" style="height:350px;margin:0px;">
                <div class="jmy" style="height:300px;">
                    <div id="meta">
                        <table cellpadding="0" cellspacing="0" class="xiangxitable" style="height:300px;">
                            <tr>
                                <td style="width:80px;">节目名称:</td>
                                <td style="height:50px;" class="nrms_cont">
                                    <div></div>
                                </td>
                            </tr>
                            <tr>
                                <td>栏目名称:</td>
                                <td style="height:50px;">
                                    <div></div>
                                </td>
                            </tr>
                            <tr>
                                <td>频道名称:</td>
                                <td style="height:50px;">
                                    <div></div>
                                </td>
                            </tr>
                            <tr>
                                <td>节目长度：</td>
                                <td style="height:50px;"></td>
                            </tr>
                            <tr>
                                <td>提交用户:</td>
                                <td style="height:50px;">
                                    <div></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
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
			    <div class="jmy" style="height:843px;">
				    <div id="meta" style="background:#f5f5f5;">
                        <table cellpadding="0" cellspacing="0" class="xiangxitable">
                            <tr>
                                <td style="background:#f5f5f5;">一级分类：</td>
                                <td style="background:#f5f5f5;">
                                    <select id="MAMClass" style="width:200px;height:30px;">
                                        <option value='-1'>请选择</option>
                                    <select/>
                                </td>
                            </tr>
                            <tr>
                                <td style="background:#f5f5f5;">二级分类：</td>
                                <td style="background:#f5f5f5;">
                                    <div>
                                        <div>
                                            <input type="text" id="dataStr" value="" readOnly="true" style="width:300px;height:30px;"><select onclick="change(this);" style="height:30px;"></select>
                                        </div>
                                        <div id="MAMSecondClass"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="background:#f5f5f5;">内容概要：</td>
                                <td style="height: 470px; padding:1px;background:#f5f5f5;">
                                    <textarea id="Summary" rows="30" cols="70" style="border-color:#FFFFFF;outline: none;"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td style="background:#f5f5f5;">关键词：</td>
                                <td style="height: 130px; padding: 5px;margin-top:10px;background:#f5f5f5;">
                                    <textarea id="Keywords" rows="8" cols="70" style="outline: none;"></textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="shleftbottom" style="height: 30px; background:url('');margin:30px 0 10px 150px;">
                            <div class=" bt">
                                <input id="tg_commit" type="submit" value="通过" onclick=""/>
                                <input id="th_commit" type="submit" value="退回" onclick=""/>
                                <input id="bc_commit" type="submit" value="保存" onclick=""/>
                                <input id="qx_commit" type="submit" value="取消" onclick=""/>
                            </div>
                        </div>
                        <div class="page">
                            <div class="fl">
                                每页<span class="red">6</span>
                                条 页次:<span class="red">1/1</span>页
                            </div>
                            <div class="fr">
                                <span class="b">上一页</span>
                                <span class="b">下一页</span>
                            </div>
                        </div>
                    </div>
				</div>				
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