<style>
    .contenttopbg .fr {
	display: none;
    }
    .hang td{
	width: 200px;
	background-color: #F5F5F5;
    }
    .channel {
	/*float: left;*/
    }
    .column {
	display: none;
    }
    .hide{
	display: none;
    }
    .xiangxitable .append_column{
	text-align: left;
    }

    .xiangxitable tr.hide.hover td {
	background: none;
    }
    .xiangxitable tr.hide td {
	background-color: #fff;
    }
    .channel_area, .column_area{
	width: 600px;
	float: left;
	text-align: center;
	font-weight: bold;
    }
    .column_area {
	margin-right: 40px;
	width: auto;

    }
    select {
	border:1px solid #C1C5C6;
    }
    .select_area {
	overflow: hidden;
	padding: 10px;
    }
    .append_column {
	border-top: 1px solid #C1C5C6;
	padding-top: 15px;
    }
    .submit {
	background: url("/img/r_button.gif") no-repeat scroll left -33px transparent;
	height: 28px;
	width: 80px;
	cursor: pointer;
    }
    .channel {
    }

</style>
<div class="box" style="display:block;">
    <div class="boxbottombg" id="data_list">
        <div class="container" style="width:1262px; overflow:hidden;">
            <div class="xiangxitable_wrap" style="width:2000px">
                <form action="" method="post" id="unlock_form" name="unlock_form">
		    <div class="select_area">
			<div class="channel_area">
			    频道选择:
			    <select onchange="channel_change();" id="channel" class="input" name="data[channelname]">
				<option value="">
				    请选择
				</option>
				<?php
				foreach ($channel_list as $k => $v) {
				    echo '<option value="' . $v['NODEID'] . '">' . $v['NAMEFORNODE'] . '</option>';
				}
				?>
			    </select>
			</div>
			<div class="column_area">
			    栏目选择:
			    <select onchange="column_change();" id="column" class="input" name="data[columnname]">
				<option value="">
				    请先选择频道
				</option>
			    </select>
			</div>
			<div class="hide">
			    <input class="submit" type="submit" value="" />
			</div>
		    </div>
		    <div class="hide">
			<div class="append_column">
			</div>
		    </div>
                </form>
                <div style="clear:both"></div>
            </div>
            <div class="hScrollPane_dragbar" >
                <div class="hScrollPane_draghandle"></div>
            </div>
        </div>	
    </div>
</div>
<script type="text/javascript">
				$(document).ready(function() {
				    $(".jmlb").addClass("on");
<?php if (isset($success)) { ?>
    				    setArtDialog('操作成功');
<?php } ?>
				});
				function channel_change() {
				    var nodeid = $('#channel').val();
				    if (nodeid !== '') {
					$.ajax({
					    type: "POST",
					    url: "<?php echo AJAX_DOMAIN; ?>" + "/relationship/get_column",
					    data: {nodeid: nodeid},
					    success: function(data) {
						$('#column').html(data);
						$('.append_column input').prop('checked',false);
					    }
					});
				    } else {
					setArtDialog('请选择频道');
				    }
				}
				function column_change() {
				    var nodeid = $('#column').val();
				    if (nodeid !== '') {
					$.ajax({
					    type: "POST",
					    url: "<?php echo AJAX_DOMAIN; ?>" + "/relationship/get_all_column",
					    data: {nodeid: nodeid},
					    success: function(data) {
						$('.hide').show();
						$('.append_column').html(data);
						$('.append_column .channel').each(function() {
						    if ($(this).children('.column').children('input:checked').size() > 0) {
							$(this).children('.parent_checkboxes').prop('checked', true);
						    }
						});
					    }
					});
				    } else {
					setArtDialog('请选择栏目');
				    }
				}
				function toogle(obj) {
				    $(obj).siblings('.column').toggle();
				    if ($(obj).html() === '+') {
					$(obj).html('-');
					$(obj).attr('title', '点击收起');
				    } else {
					$(obj).html('+');
					$(obj).attr('title', '点击展开');
				    }
				}
				var table_width = $(".xiangxitable").width();
				if (table_width < 1262) {

				    $(".xiangxitable").width(1262);
				    $(".hScrollPane_dragbar").hide(1)
				}
				function select_parent(obj) {
				    if ($(obj).prop('checked')) {
					$(obj).parent().siblings('.parent_checkboxes').prop('checked', true);
				    }
				}
</script>

