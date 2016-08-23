<script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery.form.min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot; ?>js/paixu.js"></script>
<!--该粗编的相信信息开始-->
<?php

function FormatSeconds($sec) {
    $temp = explode('.', $sec);
    $array['left'] = '';
    if (isset($temp[1])) {
	$array['left'] = ('0.' . $temp[1]) * 25;
    }
    $sec = $temp[0] % (24 * 3600);
    $array['hours'] = floor($sec / 3600);
    $remainSeconds = $sec % 3600;
    $array['minutes'] = floor($remainSeconds / 60);
    $array['seconds'] = intval($sec - $array['hours'] * 3600 - $array['minutes'] * 60);
    return $array;
}
?>
    <table id="tbHide" style="display:none">  
            <tr id="trHide"></tr>  
        </table>  
<div class='relationship_info'>
    <div class="float"><span class="bold">节目名称:</span><a title="<?php echo $relationship_info[0]['PGMNAME']; ?>" class="pgmname" target="blank" href="<?php echo $this->Html->url(array('action' => 'player', 'GUID' => $guid)); ?>"><?php echo $this->ViewOperation->subStringFormat($relationship_info[0]['PGMNAME'], 26); ?></a><span></div>
    <div class="float"><span class="bold">频道:</span><?php echo $relationship_info[0]['CHANNELNAME']; ?></div>
    <div class="float"><span class="bold">栏目信息:</span><?php echo $relationship_info[0]['COLUMNNAME']; ?></div>
    <div class="float"><span class="bold">提交时间:</span><?php echo substr($relationship_info[0]['PGMSUBMITTIME'], 0, 19); ?></div>
    <div class="float"><span class="bold">勾选条数:</span><span class="num">0</span></div>
    <a class="refresh" onclick="refresh_submit();" title="点击刷新" href="javascript:void(0)">刷新</a>
    <a class="clearall" onclick="clearall();" title="点击清空未选中" href="javascript:void(0)">清空未选中</a>
    <div class="big_length" style="display: none;">
	<?php echo $relationship_info[0]['PGMLENGTH']; ?>
    </div>
    <div class="pgmlength float" style="display: none;">节目时长:<?php
	$time_array = FormatSeconds($relationship_info[0]['PGMLENGTH'] / 25);
	echo ($time_array['hours'] ? $time_array['hours'] : '00') . ':' . ($time_array['minutes'] ? $time_array['minutes'] : '00') . ':' . ($time_array['seconds'] ? $time_array['seconds'] : '00') . ($time_array['left'] ? '.' . $time_array['left'] : '');
	?></div>
</div>
<!--该粗编的相信信息结束-->
<form action="<?php echo $this->Html->url(array('controller' => 'relationship', 'action' => 'operater', 'GUID' => $guid)); ?>" id="ad_search" name="ad_search" method="post" accept-charset="utf-8">                             
    <div class="box" id="xx" style="display:block;">
        <div class="boxbottombg" id="data_list">
            <div class="container" style="width:1265px; overflow:hidden;">
		<!--                <div class="xiangxitable_wrap" style="width:2000px">-->
                <div class="xiangxitable_wrap" id="relationship_area">
                    <table style="width: 1245px;" cellpadding="0" cellspacing="1" class="xiangxitable" id="list" align="left">		    		    
                        <tr>
			    <th width="5%">
				序号
			    </th>
                            <th width="5%">
                                <input id="check_boxes_ids" onclick="selectall('ids')" type="checkbox" />
                            </th>
                            <th width="40%">
                                节目名称
                            </th>
                            <th width="10%">提交时间</th>
                            <th width="10%">频道</th>
                            <th width="10%">栏目</th>
                            <th width="20%">GUID</th>
                        </tr>
			<?php
			$i = 0;
			foreach ($relationship_list as $pgmData) :
			    $i++;
			    ?>
    			<tr class="tr_data">
    			    <td class="xuhao">
				    <?php echo $i; ?>
    			    </td>
    			    <td class="check hide">
    				<input class="ids" type="checkbox" name="id[<?php echo $pgmData['PGMGUID']; ?>]" />
    			    </td>
    			    <td class="title" title="<?php echo $pgmData['PGMNAME']; ?>">
    				<a class="pgmname" target="blank" href="<?php echo $this->Html->url(array('action' => 'player', 'GUID' => $guid)); ?>">
					<?php
					echo $this->ViewOperation->subStringFormat($pgmData['PGMNAME'], DETAIL_NAME_NUM);
					?>
    				</a>
    			    </td>
    			    <td class="submittime"><?php echo substr($pgmData['PGMSUBMITTIME'], 0, 19); ?></td>
    			    <td class="channel hide">
				    <?php echo $pgmData['CHANNELNAME']; ?>
    			    </td>
    			    <td  class="columnname hide">
				    <?php echo $pgmData['COLUMNNAME']; ?>
    			    </td>
    			    <td  class="pgmguid hide">
				    <?php echo $pgmData['PGMGUID']; ?>
    			    </td>
    			    <td class="small_length" style="display:none;">
				    <?php echo $pgmData['PGMLENGTH']; ?>
    			    </td>
    			    <td class="pgmlength">
				    <?php
				    $time_array = FormatSeconds($pgmData['PGMLENGTH'] / 25);
				    echo ($time_array['hours'] ? $time_array['hours'] : '00') . ':' . ($time_array['minutes'] ? $time_array['minutes'] : '00') . ':' . ($time_array['seconds'] ? $time_array['seconds'] : '00') . ($time_array['left'] ? '.' . $time_array['left'] : '');
				    ?>
    			    </td>
			    	<td class="operate">
			<input type="button" name="btnUp"   value="上移" onclick="move_this(this)">
			<input type="button" name="btnDown" value="下移" onclick="move_this(this)">  
			<input type="button" name="trbegin" value="顶" onclick="move_this(this)">  
			<input type="button" name="trend" value="底" onclick="move_this(this)">
			<input size="2" type="text" name="no">
			<input type="button" name="define" value="排序" onclick="move_this(this)">
		</td>
    			</tr>
			<?php endforeach; ?>
                    </table>
                    <div style="clear:both"></div>
                </div>
		<div class="boxfoot">
		    <input onclick="re_submit_check();" type="text" name="data[submit]"  title="关联完成" class="re_submit button submit"/>
		    <input onclick="get_back();" type="text"  title="返回" class="back button add"/>
		</div>
            </div>	
        </div>
    </div>
    <!--下面的查询开始-->
    <div class="boxframe">
        <div class="boxcontent">
            <table class="table">

                <tr><td><label>名称：</label><input name="data[PgmName]" value="<?php echo $findingArr['post_keyword']['PgmName']; ?>" class="input" type="text"/></td>
		    <td><label>频道：</label>
			<select class="input" name="data[channelname]">
                            <option value="">
                                全部
                            </option>
			    <?php
			    $selected = false;
			    foreach ($findingArr['channel_list'] as $k => $v) {
				if ($v['NAMEFORNODE'] === $findingArr['post_keyword']['channelname']) {
				    echo '<option selected="selected" value="' . $v['NAMEFORNODE'] . '">' . $v['NAMEFORNODE'] . '</option>';
				} else {
				    echo '<option value="' . $v['NAMEFORNODE'] . '">' . $v['NAMEFORNODE'] . '</option>';
				}
			    }
			    ?>
                        </select>
                    </td>
		    <td><label>栏目：</label><input name="data[columnname]" class="input" value="<?php echo $findingArr['post_keyword']['columnname']; ?>" type="text"/></td>
		    <td> <label>开始日期：</label> <?php echo $this->Form->input('begintime', array('onfocus' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})", 'label' => false, 'class' => 'WdateFind', 'value' => $findingArr['post_keyword']['begintime'], 'div' => false)); ?></td>
		    <td><label>结束日期：</label><?php echo $this->Form->input('endtime', array('onfocus' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})", 'label' => false, 'class' => 'WdateFind', 'value' => $findingArr['post_keyword']['endtime'], 'div' => false)); ?></td>
		    <td><label>操作人：</label><input value="<?php echo $findingArr['post_keyword']['operater']; ?>" name="data[operater]" class="input" type="text"/></td>
		    <td><input title="查询" onclick="search_submit();" name="data[reserve_submit]" value="" class="search button submit" type="button"><input title="添加到关联区域" onclick="reserv_add('reserv_ids');" value="" class="button add" type="button"></td>

		<input name="data[pgmtype]" value="0" type="hidden">
		<input type="hidden" value="" class="submit_type" name="data[submit_type]"/>
		</tr></table>
        </div>
       <!-- <div class="boxfoot"><input onclick="$('#ad_search').submit();" name="data[reserve_submit]" value="查询" class="button submit" type="test"><input onclick="reserv_add('reserv_ids');" value="添加" class="button add" type="button"></div>-->
    </div>
    <!--查询结束-->
    <!--查询结果开始-->
    <div class="xiangxitable_wrap" id="xiangxitable_search">
        <table cellpadding="0" cellspacing="1" class="xiangxitable" id="xxvList" align="left" style="width:1245px">		    		    
	    <tr>
                <th width="5%">
                    <input id="check_boxes_reserv_ids" onclick="selectall('reserv_ids')" type="checkbox" />
                </th> 
                <th width="45%">
                    节目名称
                </th>
                <th width="10%">提交时间</th>
                <th width="10%">频道</th>
                <th width="10%">栏目</th>
                <th width="20%">GUID</th>
            </tr>

	    <?php
	    if (isset($reserv_list)) {
		foreach ($reserv_list as $pgmData) :
//					         $auditState = (int)$pgmData['tempauditstate'];			
//					         $tmpTaskID = (int)$pgmData['taskid'];
//					         if ($auditState != NOT_AUDIT_TASK_STATE){
//					            $tmpTaskID = -1;
//					         }			           			        
		    ?>
		    <tr class="tr_data">
			<td class="hide check">
			    <input class="reserv_ids" type="checkbox" name="id[<?php echo $pgmData['PGMGUID']; ?>]" />
			</td>
			<td class="pgmname" title="<?php echo $pgmData['PGMNAME']; ?>">
			    <a class="pgmname" target="blank" href="<?php echo $this->Html->url(array('action' => 'player', 'GUID' => $guid)); ?>">
				<?php
				echo $this->ViewOperation->subStringFormat($pgmData['PGMNAME'], DETAIL_NAME_NUM);
				?>
			    </a>
			</td>
			<td class="submittime"><?php echo substr($pgmData['PGMSUBMITTIME'], 0, 19); ?></td>
			<td class="channel hide">
			    <?php echo $pgmData['CHANNELNAME']; ?>
			</td>
			<td  class="columnname hide">
			    <?php echo $pgmData['COLUMNNAME']; ?>
			</td>
			<td  class="pgmguid hide">
			    <?php echo $pgmData['PGMGUID']; ?>
			</td>
			<td class="small_length" style="display:none;">
			    <?php echo $pgmData['PGMLENGTH']; ?>
			</td>
			<td class="pgmlength">
			    <?php
			    $time_array = FormatSeconds($pgmData['PGMLENGTH'] / 25);
			    echo ($time_array['hours'] ? $time_array['hours'] : '00') . ':' . ($time_array['minutes'] ? $time_array['minutes'] : '00') . ':' . ($time_array['seconds'] ? $time_array['seconds'] : '00') . ($time_array['left'] ? '.' . $time_array['left'] : '');
			    ?>
			</td>
		    </tr>
		    <?php
		endforeach;
	    }
	    ?>
        </table>
        <div style="clear:both"></div>
    </div>
</form>
<div class="relation_dialog" style="display: none;">
    <?php echo $this->element('relation_dialog', array('findarr' => $findingArr)); ?>
</div>
<!--查询结果结束-->
<script type="text/javascript">
//				    var relationship_height = $('#relationship_area').height();
//				    if (relationship_height < 500) {
//					$('#relationship_area').css({'height': 'auto'});
//				    }
	var table_width = $(".xiangxitable").width();
	if (table_width < 1245) {

	    $(".xiangxitable").width(1245);
	    $(".hScrollPane_dragbar").hide(1);
	}
	$(".container").hScrollPane({
	    mover: "table",
	    moverW: function() {
		return $(".xiangxitable").width();
	    }(),
	    showArrow: true,
	    handleCssAlter: "draghandlealter",
	    mousewheel: {moveLength: 207}
	});
	function selectall(name) {
	    var i = 0;
	    if ($("#check_boxes_" + name).prop("checked")) {
		$("input[class='" + name + "']").each(function() {
		    $(this).prop("checked", true);
		    i++;
		});
	    } else {
		$("input[class='" + name + "']").each(function() {
		    $(this).prop("checked", false);
		});
		i = 0;
	    }
	    if (name === 'ids')
		$('.relationship_info .num').html(i);

	}
//取消每一个选项菜单，全选按钮不选中
	$(function()
	{
	    var name = "ids";
	    var name_two = "reserv_ids";
	    $(document).on("click", "input[class='" + name + "']", function()
	    {
		allckeck(name) ? $("#check_boxes_" + name).prop("checked", true) : $("#check_boxes_" + name).prop("checked", false);
		var num = parseInt($('.relationship_info .num').html());
		var is_checked = $(this).prop('checked');
		if (is_checked) {
		    $('.relationship_info .num').html(num + 1);
		} else if (num > 0 && !is_checked) {
		    $('.relationship_info .num').html(num - 1);
		}
	    });
	    $(document).on("click", "input[class='" + name_two + "']", function()
	    {
		allckeck(name_two) ? $("#check_boxes_" + name_two).prop("checked", true) : $("#check_boxes_" + name_two).prop("checked", false);
	    });

	});
//判断是否全选
	function allckeck(name)
	{
	    var flag = $("input[class='" + name + "']").not(":checked").length ? false : true;
	    return flag;
	}
	function reserv_add(selector) {
	    var append_content = '';
	    var condition = true;
	    var is_alert = true;
	    var i = 0;
	    var mod = '';
	    var array = [];
	    $("input[class='" + selector + "']").each(function() {
		if ($(this).prop('checked')) {
		    //判断关联区域该值是否已经存在 如果存在就不添加
		    condition = is_exists($(this).attr('name'));
		    if (condition === '0') {
			mod = (i % 2) === 0 ? '' : 'odd';
			var parent = $(this).parent().parent();
			$(this).attr('class', 'ids');
			array[i] = $(this).attr('name');
			append_content = append_content + '<tr class="tr_data ' + mod + '">' + parent.html() + '</tr>';
			parent.remove();
			i++;
		    } else {
			if (is_alert) {
			    setArtDialog('添加节目中含有已存在节目！');
			    is_alert = false;
			}
		    }
		}
	    });
	    var title = $('#list tr').eq(0);
	    var title_content = title.clone();
	    title.remove();
	    $('#list').prepend(append_content);
	    $('#list').prepend(title_content);
	    //遍历数组也check上
	    for (var j = 0, len = array.length; j < len; j++) {
		$("#relationship_area input[name='" + array[j] + "']").prop('checked', true);
	    }
	    //重新计算选中条目
	    var num = $("#relationship_area input[class='ids']:checked").length;
	    $('.relationship_info .num').html(num);
	    //对关联区域里面的精编节目排序
	    order_by('#relationship_area .ids');
	}
	function order_by(selector) {
	    //遍历数组重新排序
	    var i = 1;
	    $(selector).each(function() {
		$(this).parent().siblings('.xuhao').remove();
		var append_html = "<td class='xuhao'>" + i + "</td>"
		$(this).parent().parent().prepend(append_html);
		//排序后每行颜色调整
		if (i % 2 === 0) {
		    $(this).parent().parent().addClass('odd');
		} else {
		    $(this).parent().parent().removeClass('odd');
		}
		i++;
	    });
	}
	function is_exists(selector) {
	    var item_is_exists = '0';
	    if (selector) {
		$('#relationship_area input.ids').each(function() {
		    if ($(this).attr('name') == selector) {
//						    item_is_exists = '1';
//						    return false;
			$(this).parent().parent().remove();
		    }
		});
	    }
	    return item_is_exists;
	}
	//返回请求
	function get_back() {
	    $.ajax({
		type: "POST",
		url: "<?php echo AJAX_DOMAIN; ?>" + "/relationship/get_back",
		data: {guid: "<?php echo $guid; ?>"},
		success: function(data) {
		    location.href = '<?php echo $this->Html->url(array('controller' => 'relationship', 'action' => 'index', 'begintime' => date('Y-m-d H:i:s', time() - 60 * 60 * 24), 'endtime' => date('Y-m-d H:i:s'))); ?>';
		}
	    });
	}
	function re_submit_check() {
//	relation_preview();
//					if ($('#relationship_area input.ids:checked').size() < 1) {
//					    setArtDialog('请至少选择一个关联节目');
//					    return false;
//					}
	    //弹出确认框
	    $('.relation_dialog table').width('670px');
	    //拼装弹出数据格式
	    var append_data = '';
	    var mod = 0;
	    var i = 0;
	    $('#relationship_area input[class=ids]').each(function() {
		if ($(this).prop('checked')) {
		    mod = (i % 2) === 0 ? '' : 'odd';
		    append_data += '<tr class="tr_data ' + mod + '">' + $(this).parent().parent().html() + '</tr>';
		    i++;
		}
	    });
	    $('.dialog_title').html('粗编节目名称:' + $('.relationship_info a.pgmname').html() + '</br>' + $('.relationship_info .pgmlength').html() + '</br>勾选条数:' + $('span.num').html());
	    $('.relation_dialog table tbody tr').eq(0).siblings('tr').remove();
	    $('.relation_dialog table tbody').append(append_data);
	    var relation_dialog = $('.relation_dialog').html();
	    var dialog = art.dialog({
		title: '节目信息',
		content: relation_dialog,
		lock: true,
		okValue: '提交',
		ok: function() {
		    re_submit_check_dialog();
		},
		cancelValue: '取消',
		cancel: function() {
		    var first_html = "<tr><th>序号</th><th width=400px>精编节目</th><th>提交时间</th><th>节目时长</th><th>操作</th></tr>";
		    $('.relation_dialog table tbody').html(first_html);
		    return true;
		}
	    });
	    //按节目提交时间排序 add1029
	    time_order();
	    
	    order_by('#relation_dialog_container .ids');
	    //重置下样式及弹出框中的项目为选中状态 add 1028
	    $('#list tr:first-child').find('th').eq(0).css('width','5%');
	    $('#list tr:first-child').find('th').eq(1).css('width','39%');
	    $('#list tr:first-child').find('th').eq(2).css('width','12%');
	    $('#list tr:first-child').find('th').eq(3).css('width','11%');
	}
	function re_submit_check_dialog() {
	    //粗编节目时长小于所有精编节目总和 就不让提交
//	    var big_length = $('.big_length').html() ? parseInt($('.big_length').html()) : 0;
//	    var small_length = 0;
//	    var i = 0;
//	    $('.content #relation_dialog_container .relation_dialog .small_length').each(function() {
//		i++;
//		small_length = small_length + parseInt($(this).html());
//	    });
//	    if (big_length < small_length) {
//		setArtDialog('精编节目时长超过粗编节目时长不能提交');
//		$('.relation_dialog table tbody').html('');
//		return false;
//	    }



//	    $('input[class=submit_type]').val('1');
//	    $('#xiangxitable_search').remove();
//	    $('#ad_search').submit();
	    
	    //add 1028
	    $('#list input.ids').prop('checked', true);
	    $('#relation_dialog_form').submit();
	}
	//查询submit
	function search_submit() {
	    var options = {
		target: '#xiangxitable_search',
		url: "<?php echo AJAX_DOMAIN; ?>" + "/relationship/operater/GUID:<?php echo $relationship_info[0]['PGMGUID']; ?>",
		type: 'POST',
		success: function() {
		}
	    };
	    $('#ad_search').ajaxSubmit(options);
	    return false;
	}
	//刷新submit
	function refresh_submit() {
	    //防止查询区域的节目提交到猴桃
	    filter_search_result();
	    var options = {
//					    target: '',
		url: "<?php echo AJAX_DOMAIN; ?>" + "/relationship/operater/GUID:<?php echo $relationship_info[0]['PGMGUID']; ?>/type:refresh",
		type: 'POST',
		success: function(data) {
//		    $('#relationship_area table.xiangxitable input.ids').not(":checked").parent().parent().remove();
		    //如果刷新后的数据有重复的就清除掉 add 1010
		    $.ajax({
			type: "POST",
			url: "<?php echo AJAX_DOMAIN; ?>" + "/relationship/get_except_json/GUID:<?php echo $relationship_info[0]['PGMGUID']; ?>/type:refresh",
			data: {guid: "<?php echo $guid; ?>"},
			success: function(datas) {
			    var jsons_array = eval(datas);
			    for (var i = 0; i < jsons_array.length; i++) {
				is_exists('id[' + jsons_array[i] + ']');
			    }
			    $('#relationship_area table.xiangxitable tbody').append(data);
			    order_by('#relationship_area .ids');
			}
		    });
		}
	    };
	    $('#ad_search').ajaxSubmit(options);
	    return false;
	}
	function filter_search_result() {
	    $('#xiangxitable_search input:checked').prop('checked', false);
	}
	function clearall() {
	    $('#relationship_area input.ids').not(":checked").parent().parent().remove();
	    var i = 0;
	    $('#relationship_area input.ids').each(function() {
		i++;
	    });
	    order_by('#relationship_area .ids');
	}
	function relation_preview() {
	    //防止查询区域的节目提交到猴桃
	    filter_search_result();
	    var options = {
//					    target: '',
		url: "<?php echo AJAX_DOMAIN; ?>" + "/relationship/relation_preview",
		type: 'POST',
		success: function(data) {
//		    $('#relationship_area table.xiangxitable input.ids').not(":checked").parent().parent().remove();
//		    $('#relationship_area table.xiangxitable').append(data);
		}
	    };
	    $('#ad_search').ajaxSubmit(options);
	    return false;
	}
</script>
<style>
    .unlock {
        display: none;
    }
    .input {
        border: 1px solid #C1C5C6;
        height: 20px;
	width: 105px;
    }
    .button {
        cursor: pointer;
    }
    .table{ width:100%; margin-top:10px;}
    .button{ background:#388ecb; color:#fff; border-radius:3px; padding:2px 3px; margin-right:10px;}
    .boxfoot{ text-align:right; padding-top:20px; padding-right: 20px;}
    .relationship_info {
	padding-left: 17px;
	width: 1245px;
	overflow: hidden;
	margin: 10px 0;
    }
    .bold {
	font-weight: bold;
	font-size: 13px;

    }
    .float {
	float: left;
	margin-right: 50px;
    }
    .relationship_info .float {
	margin-right: 40px;
    }
    .xiangxitable{ position:relative; margin:5px auto 0; border:1px solid #d2d2d2; }
    .xiangxitable th{height:33px; font-weight:100; border-bottom:1px solid #d2d2d2;padding:0; background:#e7e7e7;}
    .xiangxitable td{height:53px; text-align:center;background-color:#fff; word-break:keep-all; padding:0 10px}
    .xiangxitable a{margin-right:5px;}
    table{border-collapse:collapse;border-spacing:0;}


    .xiangxitable tr.odd td{background-color:#f5f5f5;}
    .xiangxitable tr.hover td{background-color:#fee3a0;}
    .xiangxitable_wrap
    {
	height:500px;
	overflow-y:auto;
	position:relative;
    }
    .boxcontent {
	padding-left: 10px;
    }
    #xiangxitable_search{
	height: auto;
    }

    .WdateFind {
	width: 140px;
    }
    #xxvList {
	margin-left: 12px;
    }
    #xx {
	background: none;
    }
    .table input.search {
	background: url("/img/r_button.gif") no-repeat scroll left top transparent;
	height: 30px;
	width: 62px;
    }
    .table input.add {
	background: url("/img/r_button.gif") no-repeat scroll -76px top transparent;
	height: 30px;
	width: 62px;
    }
    .boxfoot input.re_submit {
	background: url("/img/r_button.gif") no-repeat scroll left -30px transparent;
	height: 28px;
	width: 68px;
    }
    .boxfoot input.back {
	background: url("/img/r_button.gif") no-repeat scroll -85px -30px transparent;
	height: 28px;
	width: 55px;
    }
    #relationship_area {
	height: 600px;
    }
    #xiangxitable_search .pgmlength,#relationship_area .pgmlength {
	display: none;
    }
    #relation_dialog_container {
	height: 450px;
	overflow-y: auto;
	position: relative;
	/*width: 620px;*/
	width: 692px;
    }
    td.check {
	width: 68px;
    }
    td.title {
	width: 400px;
    }
    td.submittime {
	width: 200px;
    }
    td.channel {
	width: 100px;
    }
    td.columnname {
	width: 100px;
    }
    td.pgmguid {
	width: 400px;
    }
/*    #relation_dialog_container .xiangxitable td {
	text-align: center;
    }*/
    .boxfoot .button { margin: 0; }
    .clearall{ margin-left: 10px; }

    #xiangxitable_search {
	height: 600px;
	overflow-y: auto;
	position: relative;
	width: 1277px;
    }
    #relationship_area:hover {
	overflow-y:scroll;
	overflow-x:scroll;
    }
    table.relation_dialog {
	table-layout: fixed;
    }
    table.relation_dialog td.title {
	word-break:break-all;
    }
    #relationship_area .operate,#xiangxitable_search .operate { display: none; }
    .operate input[type=button] { 
	cursor: pointer; 
    margin-right: 2px;
    text-decoration: underline;
    }
    .operate input[type=text] { border: 1px solid #ccc; }
    
</style>
