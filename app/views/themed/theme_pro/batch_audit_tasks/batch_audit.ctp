<?php
//判断从列表页过来的模式
if ($layoutParams['model'] == THUMB_MODEL) {
    $contentAction = 'auditList';
} else {
    $contentAction = 'detailList';
}
//获取wms地址
$wmsPrefixUrl = WMS_PREFIX . WMS_URL . WMS_SUFFIX;
$theTaskID = (int) $taskInfo['taskid'];



	/**
	 * sql值特使处理
	 */
	function escapeValue($str = NULL){
//			$str = str_replace('&', '&amp;', $str);
//	$str = str_replace('<', '&lt;', $str);
//	$str = str_replace('>', '&gt;', $str);
	$str = str_replace("&apos;&apos;", "'", $str);
	    $str = str_replace('\&quot;', '&quot;', $str);
	    return $str;
	}
?>
<?php if ($listTaskInfo): ?>
    <div class="imgScroll" style="height:130px;">
        <div class="t_scrollbox" id="horizontal" style="margin-left:40px;margin-right:40px;">
    	<div class="t_slyWrap" style="width:1200px;">	
		<?php $count = count($listTaskInfo); ?>
		<?php //if ($count > 5):?>
    	    <div class="t_scrollbar">
    		<div class="handle"></div>
    	    </div>
		<?php //endif; ?>
    	    <div class="t_sly" style="height:65px;" data-options='{ "horizontal": 1, "itemNav": "smart", "dragContent": 1, "startAt": <?php echo $taskIndex; ?>, "scrollBy": 1 }'>
    		<ul>	
			<?php
			foreach ($listTaskInfo as $oneTaskInfo):
			    $tmpTaskID = (int) $oneTaskInfo['taskid'];
			    $tmpTaskName = $oneTaskInfo['pgmname'];

			    $newPicPath = $this->ViewOperation->getPicPath($oneTaskInfo['picpath']);
			    ?>
			    <li onclick="exchange_t(<?php echo $tmpTaskID; ?>)" title="<?php echo escapeValue($tmpTaskName); ?>" id="<?php echo $tmpTaskID; ?>">
				<?php echo $this->Html->image('img_src.png', array('data-original' => $newPicPath, 'class' => 'lazy', 'width' => '60', 'height' => '40')) ?>
				<span><?php echo $this->ViewOperation->subStringFormat(escapeValue($tmpTaskName), AUDIT_ZONES_NUM); ?></span>
				<?php if ($tmpTaskID == $theTaskID): ?>
	    			<b title="解锁" class="lock" data-type="0"></b>
				<?php else: ?>
	    			<b title="解锁" class="lock" data-type="1"></b>
				<?php endif; ?>
			    </li>
			<?php endforeach; ?>	
    		</ul>
    	    </div>
    	    <ul class="t_pages"></ul>
    	</div>
        </div>
    </div>
<?php endif; ?>
<script type="text/javascript">
			$("img.lazy").lazyload();

			$(".imgScroll").find("li").hover(function() {
			    $(this).addClass("hover");
			}, function() {
			    $(this).removeClass("hover");
			});
			$(function($) {
// 主要调用部分
			    $(document).on('activated', function(event) {
				var $section = $(".t_scrollbox");
				$section.find(".t_slyWrap").each(function(i, e) {
					    var cont = $(this),
					    frame = cont.find(".t_sly"),
					    scrollbar = cont.find(".t_scrollbar"),
					    pagesbar = cont.find(".t_pages"),
					    options = frame.data("options");
				    options = $.extend({}, options, {
					scrollBar: scrollbar,
					pagesBar: pagesbar
				    });
				    frame.sly(options);

//解锁
				    frame.find(".lock").click(function(e) {
					if (e && e.stopPropagation) {
					    e.stopPropagation();
					}
					else {
					    window.event.cancelBubble = true;
					}

					var type = $(this).data('type');
//状态判断
// var contents="";
// if(type==0){
// contents = "当前任务正在审核，确定解除锁定吗？";
// }
// else{
// contents = "您好，确定将该任务解除锁定吗";
// }
//
// art.dialog({
// title: '消息',
// content: contents,
// lock: true,
// button: [
// {
// value: '确定',
// callback: function () {
					var unlockID = $(this).parent().attr("id");
					$(this).parent().remove();
					var itemNumber = frame.find("li").length;
					if (itemNumber > 0) {
					    frame.sly('reload');

//设置选中的任务
					    var curTaskID = <?php echo $theTaskID; ?>;
					    var activeLi = frame.find("#" + curTaskID);
					    frame.sly('activate', activeLi);
					}
					unlockTask(unlockID, type);
// },
// focus: true
// },
// {
// value: '取消'
// }
// ]
// });
				    });
				});
			    }).trigger('activated');
			});

			function unlockTask(id, tag) {
			    var datas = "unlocktaskid=" + id;
			    $.ajax({
				type: "POST",
				url: "<?php echo AJAX_DOMAIN; ?>" + "/audit_handles/unlockOperation",
				data: datas,
				success: function(msg) {
				    if (tag == 0) {
					url = "<?php echo $this->Html->url(array('controller' => 'batch_audit_tasks', 'action' => 'batchAudit', 'cmode' => $layoutParams['model'], 'cpage' => $layoutParams['page'])); ?>";
					location.href = url;
				    }
				}
			    });
			}
//任务切换
			function exchange_t(theID) {
			    document.getElementById('tg_commit').disabled = true;
			    document.getElementById('th_commit').disabled = true;
			    document.getElementById('bc_commit').disabled = true;
			    document.getElementById('qx_commit').disabled = true;
			    var curtaskid = <?php echo $theTaskID; ?>;
			    var datas = "outauditid=" + curtaskid;
			    $.ajax({
				type: "POST",
				url: "<?php echo AJAX_DOMAIN; ?>" + "/batch_audit_tasks/changeTask",
				data: datas,
				success: function(msg) {
				    tmpUrl = "<?php echo $this->Html->url(array('controller' => 'batch_audit_tasks', 'action' => 'batchAudit')); ?>";
				    url = tmpUrl + "/" + theID + "/cmode:" + "<?php echo $layoutParams['model']; ?>" + "/cpage:" + "<?php echo $layoutParams['page']; ?>";
				    location.href = url;
				}
			    });
			}
</script>

<div class="of">
    <div class="shleft">
	<div class="shlefttop">
	    <div class="title">
		<select name="" onchange="changeFilealias(this.value,<?php echo $theTaskID; ?>)">
		    <?php
		    if ($fileAlias):
			foreach ($fileAlias as $tempFileAlias) :
			    if ($tempFileAlias == $selectFileAlias) :
				?>
	    		    <option value= "<?php echo $tempFileAlias; ?>" selected="selected"><?php echo $tempFileAlias; ?></option>
			    <?php else : ?>	
	    		    <option value= "<?php echo $tempFileAlias; ?>"><?php echo $tempFileAlias; ?></option>
			    <?php
			    endif;
			endforeach;
		    endif;
		    ?>	
		</select>
		正在审核:
		<span><?php echo $this->ViewOperation->subStringFormat(escapeValue($taskInfo['pgmname']), AUDIT_NAME_NUM); ?></span>
	    </div>
	    <div class="play" id="play_div">
		<object classid="clsid:F7944BBA-9B19-44EF-B428-17D527982A2D" type="application/x-itst-activex" style="border:0px;width:590px;height:465px;" id="EncoderPlayer">
		</object>
	    </div>
	</div>
	<div class="shleftbottom">
	    <h3 class="title">审核意见</h3>
	    <textarea name="" id="ContentAuditNote"><?php echo $taskInfo['contentauditnote']; ?></textarea>
	    <div class="bt">
		<input id="tg_commit" type="submit" value="通过" onclick="commit(1,<?php echo $theTaskID; ?>)"/>
		<input id="th_commit" type="submit" value="退回" onclick="commit(2,<?php echo $theTaskID; ?>)"/>
		<input id="bc_commit" type="submit" value="保存" onclick="commit(4,<?php echo $theTaskID; ?>)"/>
		<input id="qx_commit" type="submit" value="取消" onclick="onUpdateState(1)"/>
	    </div>
	</div>
    </div>
    <div class="shright">
	<div class="tabtop">
	    <ul>
		<li id="ysj_jm" style="display:none;">节目元数据</li>
		<li id="js_jm" style="display:none;">技审结果</li>
	    </ul>
	</div>
	<div class="tabbottom">
	    <!-- 开始--元数据-->
	    <div class="jmy" id="ysj_jmn" style="display:none;">
		<?php echo $this->Html->image('fbpt.png', array('alt' => '', 'class' => 'fbpt')) ?>	
		<div id="meta">
		    <?php if ($layoutParams['userType'] != TECH_TASK_TYPE): ?>
    		    <ul class="of">
			    <?php if ($platFormInfos): 
				?>
				<?php foreach ($platFormInfos as $platFormInfo) : ?>
				    <?php
				    $isSelected = (int) $platFormInfo['IsSelected'];
				    if ($isSelected == IS_SELECTED) :
					?>
					<li>
					    <input type="checkbox" checked ="checked" id="<?php echo $platFormInfo['PlatFormID']; ?>" name="plat" value="<?php echo $platFormInfo['PlatFormName']; ?>"/>
					    <?php echo $platFormInfo['PlatFormName']; ?>
					</li>
				    <?php else : ?>
					<li>
					    <input type="checkbox" id="<?php echo $platFormInfo['PlatFormID']; ?>" name="plat" value="<?php echo $platFormInfo['PlatFormName']; ?>"/>
					    <?php echo $platFormInfo['PlatFormName']; ?>
					</li>
				    <?php endif; ?>
				<?php endforeach; ?>
			    <?php endif; ?>
    		    </ul>
			<?php if ($attributes): ?>
			    <table cellpadding="0" cellspacing="0" class="xiangxitable">
				<?php
				$editAttributes = Configure::read('editedAttributes');
				$timeAttributes = Configure::read('timeAttributes');
				$textAttributes = Configure::read('textAttributes');
				foreach ($attributes as $attribute) :
				    $tmpItemCode = $attribute['ItemCode'];
				    $tmpItemName = $attribute['ItemName'];
				    ?>
	    			<tr>
	    			    <td width="100"><?php echo $attribute['ItemName']; ?>:</td>
					<?php if (in_array($tmpItemName, $timeAttributes)): ?>
					    <td style="height:80px;">
						<?php
						$length = (int) $attribute['Value'];
						echo $this->ViewOperation->formatTimeLength($length);
						?>
					    </td>
					<?php elseif (in_array($tmpItemName, $textAttributes)): ?>
					    <td style="height:80px;">
						<div style="overflow-y:auto;word-break: break-all;height:80px;">
						    <?php echo $attribute['Value']; ?>
						</div>
					    </td>
					<?php elseif (in_array($tmpItemName, $editAttributes)): ?>
					    <td style="height:80px;" class="nrms_cont" id="<?php echo $tmpItemCode; ?>">
						<div><?php echo $attribute['Value']; ?></div>
					    </td>
					<?php else: ?>
					    <td style="height:80px;"><?php echo $attribute['Value']; ?></td>
					<?php endif; ?>	
	    			</tr>
				<?php endforeach; ?>	
			    </table>
			    <div class="page">
				<?php
				$this->Paginator->options(array(
				    'url' => array('controller' => 'app_audits', 'action' => 'metaData', $theTaskID),
				    'model' => 'Content',
				    'update' => '#meta',
				    'evalScripts' => true//,
//'before' => $this->Js->get('#busy-indicator')->effect('fadeIn', array('buffer' => false)),
//'complete' => $this->Js->get('#busy-indicator')->effect('fadeOut', array('buffer' => false)),
				));
				?>
				<?php $metaPageParams = $this->Paginator->params('Content'); ?>
				<div class="fl">
				    每页
				    <span class="red"><?php echo $metaPageParams['options']['limit']; ?></span>
				    条 页次:<span class="red">
					<?php
					echo $this->Paginator->counter(array(
					    'model' => 'Content',
					    'format' => '%page%/%pages%'
					));
					?>
				    </span>
				    页
				</div>
				<div class="fr">
				    <?php
				    echo $this->Paginator->first('首页', array('model' => 'Content', 'tag' => 'span', 'class' => 'a'));
				    echo $this->Paginator->prev('上一页', array(), null, array('model' => 'Content', 'tag' => 'span', 'class' => 'b'));
				    echo $this->Paginator->numbers(array('model' => 'Content', 'before' => '', 'tag' => 'span', 'class' => 'number', 'separator' => '', 'modulus' => 4));
				    echo $this->Paginator->next('下一页', array(), null, array('model' => 'Content', 'tag' => 'span', 'class' => 'b'));
				    echo $this->Paginator->last('尾页', array('model' => 'Content', 'tag' => 'span', 'class' => 'a'));
				    ?>
				</div>
			    </div>
			<?php endif; ?>
<?php endif; ?>
		</div>
	    </div>
	    <!-- 结束--元数据 -->
	    <!-- 开始--计审 -->
	    <div class="sjjg" id="js_jmn" style="display:none;">
		<div id="tech">
		    <iframe id="myiframe" width="100%" height="797" frameborder="0" scrolling="no" src="<?php echo $this->Html->url(array('controller' => 'app_audits', 'action' => 'techData', $theTaskID, $selectFileAlias)); ?>">
		    </iframe>
		</div>
	    </div>
	    <!-- 结束--计审 -->
	</div>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<script language="javascript" for="EncoderPlayer" event="TrimInOutLoaded(lTrimIn, lTrimOut)" type="text/javascript">getTrimInOutLoaded(lTrimIn, lTrimOut);</script>
<script type="text/javascript">
    var reviseDataObject = {};
    var revisedMetaData = "";
    function updataData(key, value) {
	reviseDataObject[key] = value;
	revisedMetaData = JSON.stringify(reviseDataObject);
    }
    /***过滤html敏感标签***/
function zhuangyi(val) {
    val = val.toString();
    val = val.replace(/&/g, "");
    val = val.replace(/</g, "&lt;");
    val = val.replace(/>/g, "&gt;");
    val = val.replace(/\"/g, "&quot;");
    val = val.replace(/\'/g, "&#39;");
    return val;
}
    function nrms_edit() {
	$(".nrms_cont").each(function() {
	    var _this = $(this);
	    var o_nrms_cont = _this.text();
	    if (o_nrms_cont == "") {
		_this.append('<input type="text" value="不能为空！" />');
	    }
	    /*判断输入是否合法*/
	    function iptOper() {
		_this.children("input").focus().blur(function() {
		    var nrmsCont = $(this).val();
		    nrmsCont = zhuangyi(nrmsCont);
		    if (nrmsCont == "") {
			$(this).val("不能为空！").addClass("error_inpt");
		    } else {
			if (nrmsCont != "不能为空！") {
			    $(this).before('<div>' + nrmsCont + '</div>').remove();
			    if (trim(o_nrms_cont) != trim(nrmsCont)) {
				var thecode = _this.attr("id");
				var val_submit = $(this).val();
				updataData(thecode, val_submit.replace(/&/g, ""));
			    }
			} else {
			    return;
			}
		    }
		})
	    }
	    iptOper();
	    /*修改输入*/
	    _this.delegate("div", "dblclick", function() {
		var oval = $(this).text();
		$(this).after('<input type="text" value="' + zhuangyi(oval) + '">').remove();
		iptOper();
	    })
	})
    }
    nrms_edit();

    function trim(str) {
	str = str.replace(/^(\s|\u00A0)+/, '');
	for (var i = str.length - 1; i >= 0; i--) {
	    if (/\S/.test(str.charAt(i))) {
		str = str.substring(0, i + 1);
		break;
	    }
	}
	return str;
    }
    $(document).ready(function() {
	$(".wdsh").addClass("on");

<?php if (!TASK_AUDIT_TYPE): ?>
    	$("#ysj_jm").show();
    	$("#js_jm").show();

    	$("#ysj_jm").addClass("on");
    	$("#js_jm").removeClass("on");
    	$("#ysj_jmn").show();
    	$("#js_jmn").hide();
<?php else : ?>
    <?php
    if ($layoutParams['userType'] == CONTENT_TASK_TYPE):
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
<?php endif; ?>


	var filedata = '<?php echo $taskFile; ?>';
	filedata = filedata.replace(/\\/g, "\\\\");
	var jsonObj = JSON.parse(filedata);
	setFileInfo(jsonObj);
    });


    function setFileInfo(data) {
	var len = data.length;
	for (i = 0; i < len; i++) {
	    var tmpOneObj = data[i];
	    var filename = tmpOneObj.filename;
	    var clipclass = parseInt(tmpOneObj.clipclass);
	    var channel = parseInt(tmpOneObj.channel);

//获取并设置视频路径
	    var filepath = "<?php echo addslashes($wmsPrefixUrl); ?>" + filename;
	    if (clipclass == 1) {
		onSetVideoFile(filepath);

//设置音频模式
		var audiomodel = getAudioModel(len - 1);
		onSetAudioMode(audiomodel);
	    }
	    else {
		var tmpChannel = parseInt(Math.log(channel) / Math.log(2));
		var tmpClipClass = parseInt(Math.log(clipclass) / Math.log(2));
		onSetAudioFile(filepath, tmpChannel, tmpClipClass);
	    }
	}
	onsetInitFinish();
    }
    function getAudioModel(audiocount) {
	if (audiocount == 1) {
	    return 0;
	}
	else if (audiocount <= 2) {
	    return 1;
	}
	else if (audiocount <= 6) {
	    return 2;
	}
	else if (audiocount <= 8) {
	    return 3;
	}
	return 1;
    }
//播放器系列操作
    function onSetVideoFile(strVideo) { //此函数在加载播放器后就立即调用。功能：设置选择的视频文件
	var player = document.getElementById('EncoderPlayer');
	player.AddVideo(strVideo);
    }
    function onSetAudioMode(sModeId) { //此函 数在加载播放器后就立即调用。功能：设置选择的音频文件
	var player = document.getElementById('EncoderPlayer');
	player.SetAudioMode(sModeId);
    }
    function onSetAudioFile(strAudioPath, sSrcAudioTrack, sDstAudioTrack) { //此函数在加载播放器后就立即调用。功能：设置选择的音频文件
	var player = document.getElementById('EncoderPlayer');
	player.AddAudio(strAudioPath, sSrcAudioTrack, sDstAudioTrack);
    }
    function onsetInitFinish() {
	var player = document.getElementById('EncoderPlayer');
	player.InitFinish();
    }
</script>
<script type="text/javascript">
    function changeFilealias(value, taskid) {
	var datas = "taskid=" + taskid + "&filealias=" + value;
	$.ajax({
	    type: "POST",
	    url: "<?php echo AJAX_DOMAIN; ?>" + "/batch_audit_tasks/getFileInfo",
	    data: datas,
	    success: function(msg) {
//重新加载播放器
		var playerDiv = document.getElementById("play_div");
		playerDiv.innerHTML = "<object classid=\"clsid:F7944BBA-9B19-44EF-B428-17D527982A2D\" type=\"application/x-itst-activex\" style=\"border:0px;width:590px;height:465px;\" id=\"EncoderPlayer\">" +
			"</object>";
//设置文件
		var filedata = msg;//msg.replace(/\\/g,"\\\\");
		var jsonObj = JSON.parse(filedata);
		setFileInfo(jsonObj);

//更新技审
		var frame = document.getElementById("myiframe");
		var url = "<?php echo $this->Html->url(array('controller' => 'app_audits', 'action' => 'techData')); ?>" + "/" + taskid + "/" + value;
		frame.src = url;
	    }
	});
    }
    function getTrimInOutLoaded(lTrimIn, lTrimOut) {
	var inValue = lTrimIn << 0;
	var outValue = lTrimOut << 0;
	var data = "";
	if (inValue != -1 && outValue != -1) {
	    data = formatDate(lTrimIn) + "---" + formatDate(lTrimOut) + " :";
	}
	else if (inValue == -1 && outValue != -1) {
	    message = '播放器未设置入点';
	    setArtDialog(message);

	    data = "---" + formatDate(lTrimOut) + " :";
	}
	else if (inValue != -1 && outValue == -1) {
	    message = '播放器未设置出点';
	    setArtDialog(message);
	    data = formatDate(lTrimIn) + "---" + " :";
	}
	else {
	    message = '播放器未设置出入点';
	    setArtDialog(message);
	}
	if (data) {
	    var allData = "";
	    var preData = $("#ContentAuditNote").val();
	    if (preData) {
		allData = preData + "\n" + data;
	    }
	    else {
		allData = data;
	    }
	    $("#ContentAuditNote").val(allData);
	}
    }
    function formatDate(time) {
	var frameNum = <?php echo TASK_FRAME_NUM; ?>;
	var frameNumber = parseInt(frameNum);

	var frame = time % frameNumber;
	var seconds = Math.floor(time / frameNumber);

	var second = seconds % 60;
	var minutess = Math.floor(seconds / 60);

	var minutes = minutess % 60;
	var hours = Math.floor(minutess / 60);

	return ((hours < 10) ? "0" + hours : hours) + ":" +
		((minutes < 10) ? "0" + minutes : minutes) + ":" +
		((second < 10) ? "0" + second : second) + ":" +
		((frame < 10) ? "0" + frame : frame);
    }


//整个任务提交
    function commit(state, taskid) {
	var inputs = document.getElementsByTagName("input");
	var platNum = 0;
	var platData = "";
	for (i = 0; i < inputs.length; i++) {
	    if (inputs[i].name == "plat") {
		var temp = document.getElementById(inputs[i].id).checked;
		if (temp) {
		    if (platNum == 0) {
			platData += inputs[i].id;
		    }
		    else {
			platData += "," + inputs[i].id;
		    }
		    platNum++;
		}
	    }
	}
<?php if ($layoutParams['userType'] != TECH_TASK_TYPE): ?>
    	if (platNum == 0) {
    	    art.dialog({
    		title: '提示',
    		content: '请选择对应的发布平台',
    		lock: true,
    		okValue: '确认',
    		ok: function() {
    		    return true;
    		}
    	    });
    	    return;
    	}
<?php endif; ?>


//锁定对应按钮
	if (state != 4) {
	    document.getElementById('tg_commit').disabled = true;
	    document.getElementById('th_commit').disabled = true;
	    document.getElementById('bc_commit').disabled = true;
	    document.getElementById('qx_commit').disabled = true;
	}

//通过或退回只能提交一次
	var note = $("#ContentAuditNote").val();
	var datas = "ContentAuditNote=" + note + "&TaskID=" + taskid + "&State=" + state;
	datas = datas + "&SelectPlatID=" + platData;
	datas = datas + "&UpdateMetaData=" + revisedMetaData.replace(/\+/g, '%2B');
	$.ajax({
	    type: "POST",
	    url: "<?php echo AJAX_DOMAIN; ?>" + "/audit_handles/commitTask",
	    data: datas,
	    success: function(msg) {
		art.dialog({
		    title: '提示',
		    content: msg,
		    lock: true,
		    esc: false,
		    cancelValue: '确认',
		    cancel: function() {
			if (state != 4) {
			    url = "<?php echo $this->Html->url(array('controller' => 'batch_audit_tasks', 'action' => 'batchAudit', 'cmode' => $layoutParams['model'], 'cpage' => $layoutParams['page'])); ?>";
			    location.href = url;
			}
		    }
		});
	    }
	});
    }
//进行任务状态更新，value：1->取消;3->统计;4->查询;5->节目列表
    function onUpdateState(value, tag) {
	if (value == 1) {
	    art.dialog({
		title: '消息',
		content: '当前任务正在审核，是否确定退出审核？',
		lock: true,
		button: [
		    {
			value: '是',
			callback: function() {
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
	else {
	    tmpUpdateState(value, tag);
	}
    }
    function tmpUpdateState(value, tag) {
	if (value != 0) {
	    document.getElementById('tg_commit').disabled = true;
	    document.getElementById('th_commit').disabled = true;
	    document.getElementById('bc_commit').disabled = true;
	    document.getElementById('qx_commit').disabled = true;
	}

	var taskid = getCurTaskID();
	var datas = "TaskID=" + taskid + "&SetID=" + value;

	$.ajax({
	    type: "POST",
	    url: "<?php echo AJAX_DOMAIN; ?>" + "/audit_handles/updateTaskState",
	    data: datas,
	    success: function(msg) {
		if (value == 1) {
		    url = "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => $contentAction, 'page' => $layoutParams['page'])); ?>";
		    location.href = url;
		}
		else if (value == 3) {
		    url = "<?php echo $this->Html->url(array('controller' => 'accounts', 'action' => 'statistics', 'cmode' => $layoutParams['model'], 'cpage' => $layoutParams['page'])); ?>";
		    location.href = url;
		}
		else if (value == 4) {
		    submitAction(tag);
		}
		else if (value == 5) {
		    url = "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => $contentAction, 'page' => $layoutParams['page'])); ?>";
		    location.href = url;
		}
	    }
	});
    }
    function getCurTaskID() {
	return <?php echo $theTaskID; ?>;
    }
</script>