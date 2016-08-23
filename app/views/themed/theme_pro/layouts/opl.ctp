<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>内容审核系统</title>
<?php echo $this->Html->css('style.css');?>

<script type="text/javascript" src="<?php echo $this->webroot;?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/main.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/jquery.lazyload.js"></script>

<script type="text/javascript" src="<?php echo $this->webroot;?>js/My97DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot;?>js/artDialog/skins/idialog.css" />
<script type="text/javascript" src="<?php echo $this->webroot;?>js/artDialog/artDialog.min.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/jsonjs/json2.js"></script>
<script type="text/javascript" src="<?php echo $this->webroot;?>js/slymaster/jquery.sly.js"></script>

<style type="text/css">
.clear{
	clear: both;
}
.hide{
	display: none;
}
table {
  max-width: 100%;
  background-color: transparent;
  border-collapse: collapse;
  border-spacing: 0;
}
.table {
  width: 100%;
  margin-bottom: 20px;
}

.table th,
.table td {
  padding: 8px;
  line-height: 20px;
  text-align: left;
  vertical-align: top;
  border-top: 1px solid #dddddd;
}

.table th {
  font-weight: bold;
}

.table thead th {
  vertical-align: bottom;
}

.table caption + thead tr:first-child th,
.table caption + thead tr:first-child td,
.table colgroup + thead tr:first-child th,
.table colgroup + thead tr:first-child td,
.table thead:first-child tr:first-child th,
.table thead:first-child tr:first-child td {
  border-top: 0;
}

.table tbody + tbody {
  border-top: 2px solid #dddddd;
}

.table .table {
  background-color: #ffffff;
}

.table-condensed th,
.table-condensed td {
  padding: 4px 5px;
}

.table-bordered {
  border: 1px solid #dddddd;
  border-collapse: separate;
  *border-collapse: collapse;
  border-left: 0;
/*  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;*/
}

.table-bordered th,
.table-bordered td {
  border-left: 1px solid #dddddd;
}

.table-bordered caption + thead tr:first-child th,
.table-bordered caption + tbody tr:first-child th,
.table-bordered caption + tbody tr:first-child td,
.table-bordered colgroup + thead tr:first-child th,
.table-bordered colgroup + tbody tr:first-child th,
.table-bordered colgroup + tbody tr:first-child td,
.table-bordered thead:first-child tr:first-child th,
.table-bordered tbody:first-child tr:first-child th,
.table-bordered tbody:first-child tr:first-child td {
  border-top: 0;
}

.table-bordered thead:first-child tr:first-child > th:first-child,
.table-bordered tbody:first-child tr:first-child > td:first-child,
.table-bordered tbody:first-child tr:first-child > th:first-child {
/*  -webkit-border-top-left-radius: 4px;
          border-top-left-radius: 4px;
  -moz-border-radius-topleft: 4px;*/
}

.table-bordered thead:first-child tr:first-child > th:last-child,
.table-bordered tbody:first-child tr:first-child > td:last-child,
.table-bordered tbody:first-child tr:first-child > th:last-child {
/*  -webkit-border-top-right-radius: 4px;
          border-top-right-radius: 4px;
  -moz-border-radius-topright: 4px;*/
}

.table-bordered thead:last-child tr:last-child > th:first-child,
.table-bordered tbody:last-child tr:last-child > td:first-child,
.table-bordered tbody:last-child tr:last-child > th:first-child,
.table-bordered tfoot:last-child tr:last-child > td:first-child,
.table-bordered tfoot:last-child tr:last-child > th:first-child {
/*  -webkit-border-bottom-left-radius: 4px;
          border-bottom-left-radius: 4px;
  -moz-border-radius-bottomleft: 4px;*/
}

.table-bordered thead:last-child tr:last-child > th:last-child,
.table-bordered tbody:last-child tr:last-child > td:last-child,
.table-bordered tbody:last-child tr:last-child > th:last-child,
.table-bordered tfoot:last-child tr:last-child > td:last-child,
.table-bordered tfoot:last-child tr:last-child > th:last-child {
/*  -webkit-border-bottom-right-radius: 4px;
          border-bottom-right-radius: 4px;
  -moz-border-radius-bottomright: 4px;*/
}

.table-bordered tfoot + tbody:last-child tr:last-child td:first-child {
/*  -webkit-border-bottom-left-radius: 0;
          border-bottom-left-radius: 0;
  -moz-border-radius-bottomleft: 0;*/
}

.table-bordered tfoot + tbody:last-child tr:last-child td:last-child {
/*  -webkit-border-bottom-right-radius: 0;
          border-bottom-right-radius: 0;
  -moz-border-radius-bottomright: 0;*/
}

.table-bordered caption + thead tr:first-child th:first-child,
.table-bordered caption + tbody tr:first-child td:first-child,
.table-bordered colgroup + thead tr:first-child th:first-child,
.table-bordered colgroup + tbody tr:first-child td:first-child {
/*  -webkit-border-top-left-radius: 4px;
          border-top-left-radius: 4px;
  -moz-border-radius-topleft: 4px;*/
}

.table-bordered caption + thead tr:first-child th:last-child,
.table-bordered caption + tbody tr:first-child td:last-child,
.table-bordered colgroup + thead tr:first-child th:last-child,
.table-bordered colgroup + tbody tr:first-child td:last-child {
  /*-webkit-border-top-right-radius: 4px;*/
          /*border-top-right-radius: 4px;*/
  /*-moz-border-radius-topright: 4px;*/
}

.table-striped tbody > tr:nth-child(odd) > td,
.table-striped tbody > tr:nth-child(odd) > th {
  background-color: #f9f9f9;
}

.table-hover tbody tr:hover > td,
.table-hover tbody tr:hover > th {
  background-color: #f5f5f5;
}

.list-table{
	margin-left: 1px;
	margin-bottom: 1px;
	margin-top: 5px;
}

.topnav .opl-list a{
	background: url("/img/czjl.jpg") no-repeat scroll left top transparent!important;
}
.topnav .opl-list a.on{
  background: url("/img/czjl_on.jpg") no-repeat scroll left top transparent!important;
}
.topnav .opl-stats a{
	background: url("/img/cztj.jpg") no-repeat scroll left top transparent!important;
}
.topnav .opl-stats a.on{
  background: url("/img/cztj_on.jpg") no-repeat scroll left top transparent!important;
}
.show_stats_search .sDrop{
    height: 375px!important;
}
.show_stats_search .sDrop ul{
    height: 295px!important;
}
.show_log_search .sDrop{
    height: 532px!important;
}
.show_log_search .sDrop ul{
    height: 440px!important;
}
.show_stats_search .sDrop li,.show_log_search .sDrop li{
  margin-bottom: 20px;
  height: auto;
}
.export_excel{
    background: url("/img/export_excel.png") no-repeat scroll left top transparent!important;
}
</style>
<script type="text/javascript">
function setArtDialog(message){
    art.dialog({ 
	    title: '提示',
	    content: message,
	    lock: true,
	    okValue: '确认',  
	    ok: function () {
		   return true;
	    }
    });
}
</script>
</head>
<body>
<div class="top">
    <div class="topinner">
	<div class="wrap of">
		<?php echo $this->Html->image('logo.jpg',array('class'=>'logo','alt'=>'内容生产服务平台审核系统'))?>
		<div class="fr">
		           欢迎您:
		      <span class="red">[<?php echo $userInfo['UserName'];?>]</span> |
		    <a href="/">返回审核</a> |
			<a href="<?php echo $this->Html->url(array('controller' => 'users','action' => 'logout')); ?>">退出</a>
			| 
		</div>
	</div>
	</div>
</div>
<div class="wrap">
	<div class="content">
		<div class="contenttopbg">
			<div class="topnav">
				<ul>					
					<li class="jmlb opl-list">
						<a href="<?php echo $this->Html->url(array('controller' => 'operatelog', 'action' => 'index', 'start_date' => date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 40), 'end_date' => date('Y-m-d H:i:s'))); ?>" <?php if(isset($action) && $action == 'show_log'):?>class="on"<?php endif; ?>>操作日志</a>
					</li>
					<li class="wdsh opl-stats">
					    <a href="/operatelog/stats" <?php if(isset($action) && $action == 'show_stats'):?>class="on"<?php endif; ?>>操作统计</a>
					</li>
				</ul>
			    <div class="fr">
                	<div class="z-searchBox <?php echo $action; ?>_search">
                      <?php if(isset($action) && $action == 'show_log'):?>
                	    <form action="/operatelog" id="NameFinding" accept-charset="utf-8">
							         <input type="text" class="sTxt" name="programname" placeholder="请输入节目名称" value="<?php echo isset($programname)?$programname:""; ?>">
                    	    <input type="submit" class="sBtn" value="" />
                    	    <span class="sArral" title="高级搜索"></span>
                    	</form>                   	                	
                        <div class="sDrop" style="display:none">
                            <form action="/operatelog" accept-charset="utf-8">                         
                        	<ul>
                        		<li>
                                	<div class="t"><span>完成时间：</span></div>
                                    <div class="c">
                  										<input name="start_date" onFocus="WdatePicker({startDate:'%y-%M-%d 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true})" class="WdateFind" value="<?php echo $start_date; ?>" id="StartTime" type="text">
                  										<span>到</span>
                                    	<input name="end_date" onFocus="WdatePicker({startDate:'%y-%M-%d 23:59:59',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true})" class="WdateFind" value="<?php echo $end_date; ?>" id="EndTime" type="text"> 
                                    </div>
                                </li>
                        		<li>
                                	<div class="t"><span>节目标题：</span></div>
                                    <div class="c">
										                  <input type="text" class="longIpt" name="programname" value="<?php echo isset($programname)?$programname:""; ?>">
                                    </div>
                                </li>
                                <li>
                                	<div class="t"><span>GUID：</span></div>
                                    <div class="c">
										                    <input type="text" class="longIpt" name="programguid" value="<?php echo isset($programguid)?$programguid:""; ?>">
                                    </div>
                                </li>
                                <li>
                                	<div class="t"><span>操作人：</span></div>
                                    <div class="c">
										                    <input type="text" class="longIpt" name="operatorname" value="<?php echo isset($operatorname)?$operatorname:""; ?>">
                                    </div>
                                </li>
				 <li>
                                	<div class="t"><span>栏目名称：</span></div>
                                     <div class="c">
				 <input type="text" class="longIpt" name="excolumn" value="<?php echo isset($excolumn)?$excolumn:""; ?>">
                                    </div>
                                  </li>
				 <li>
                                	<div class="t"><span>频道名称：</span></div>
                                     <div class="c">
				 <input type="text" class="longIpt" name="exchannel" value="<?php echo isset($exchannel)?$exchannel:""; ?>">
                                    </div>
                                  </li>
                                <li>
                                	<div class="t"><span>操作步骤：</span></div>
                                    <div class="c">
                                      <?php foreach ($operatetypes as $otype=>$oname):?>
                                        <label style="display:inline-block;width:150px;"><input <?php if(isset($operatetype) && in_array($otype, $operatetype)) echo 'checked="checked"'; ?> type="checkbox" name="operatetype[]" value="<?php echo $otype; ?>" /> <?php echo $oname; ?></label>
                                      <?php endforeach; ?>
                                      <div class="clear"></div>
                                      </div>
                                  </li>
				<li>
                                    <div class="t"><span>平台：</span></div>
                                    <div class="c">
                                        <select name="platform">
                                          <option value="-1">所有</option>
					  <?php foreach($platform as $k => $v) { ?>
					  <option <?php if($k == $platform_select){?>selected="selected" <?php }?>value="<?php echo $k;?>"><?php echo $v;?></option>
					  <?php }?>
                                        </select>
                                    </div>
                                </li>
                            	</ul>
                            <div class="btns">
                                <input class="submit" name="my" value="" type="submit"> 
                            	<a href="#" class="cancl"></a>
                            </div>                                        
                            <div class="arral"></div>     
                            </form>                     
                        </div>
                        <?php elseif(isset($action) && $action == 'show_stats'): ?>
                        <form action="/operatelog/stats" id="NameFinding" accept-charset="utf-8">
                            <input type="text" class="sTxt" name="operatorname" placeholder="请输入操作人名" value="<?php echo isset($operatorname)?$operatorname:""; ?>">
                            <input type="submit" class="sBtn" value="" />
                            <span class="sArral" title="高级搜索"></span>
                        </form>                                         
                        <div class="sDrop" style="display:none">
                            <form action="/operatelog/stats" accept-charset="utf-8">                         
                            <ul>
                                <li>
                                    <div class="t"><span>完成时间：</span></div>
                                    <div class="c">
                                        <input name="start_date" onFocus="WdatePicker({startDate:'%y-%M-%d 00:00:00',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true})" class="WdateFind" value="<?php echo $start_date; ?>" id="StartTime" type="text">
                                        <span>到</span>
                                        <input name="end_date" onFocus="WdatePicker({startDate:'%y-%M-%d 23:59:59',dateFmt:'yyyy-MM-dd HH:mm:ss',alwaysUseStartDate:true})" class="WdateFind" value="<?php echo $end_date; ?>" id="EndTime" type="text"> 
                                    </div>
                                </li>
                                <li>
                                    <div class="t"><span>操作人：</span></div>
                                    <div class="c">
                                        <input type="text" class="longIpt" name="operatorname" value="<?php echo isset($operatorname)?$operatorname:""; ?>">
                                    </div>
                                </li>
                                <li>
                                    <div class="t"><span>操作步骤：</span></div>
                                    <div class="c">
                                        <?php foreach ($operatetypes as $otype=>$oname):?>
                                        <label style="display:inline-block;width:150px;"><input <?php if(isset($operatetype) && in_array($otype, $operatetype)) echo 'checked="checked"'; ?> type="checkbox" name="operatetype[]" value="<?php echo $otype; ?>" /> <?php echo $oname; ?></label>
                                        <?php endforeach; ?>
                                        <div class="clear"></div>
                                    </div><div class="clear"></div>
                                </li>
                                <li>
                                    <div class="t"><span>排序：</span></div>
                                    <div class="c">
                                        <select name="sort">
					    <option value="6"<?php if ($sort == '6') echo ' selected=selected';?>>操作类型</option>
                                          <option value="1"<?php if ($sort == '1') echo ' selected=selected';?>>用户名</option>
                                          <option value="2"<?php if ($sort == '2') echo ' selected=selected';?>>完成任务数</option>
                                          <option value="3"<?php if ($sort == '3') echo ' selected=selected';?>>完成任务时长</option>
                                          <option value="4"<?php if ($sort == '4') echo ' selected=selected';?>>打回任务数</option>
                                          <option value="5"<?php if ($sort == '5') echo ' selected=selected';?>>打回任务时长</option>
                                        </select>
                                    </div>
                                </li>
				<li>
                                    <div class="t"><span>平台：</span></div>
                                    <div class="c">
                                        <select name="platform">
                                          <option value="-1">所有</option>
					  <?php foreach($platform as $k => $v) {?>
					  <option <?php if($k == $platform_select){?>selected="selected" <?php }?>value="<?php echo $k;?>"><?php echo $v;?></option>
					  <?php }?>
                                        </select>
                                    </div>
                                </li>
                            </ul>
                            <div class="btns">
                                <input class="submit" name="my" value="" type="submit"> 
                                <a href="#" class="cancl"></a>
                            </div>                                        
                            <div class="arral"></div>     
                            </form>                     
                        </div>
                        <?php endif; ?>                                     
                    </div>

                    <script type="text/javascript">						
						            /*搜索框的操作*/
                        $(document).ready(function(){
                            var iptDafaultTxt = $("#NameFinding").find("input[name='programname']").val();
                            var $stext = $(".z-searchBox").find(".sTxt");
                            if($stext.val()==""){
                                $stext.val(iptDafaultTxt);
                            }       
                            $stext.focus(function(){
                                $(this).parents(".z-searchBox").addClass("z-searchBox-on");
                                if($(this).val() == iptDafaultTxt || ""){
                                    $(this).val("");
                                } else {
                                    return; 
                                }
                            }).blur(function(){
                                $(this).parents(".z-searchBox").removeClass("z-searchBox-on");
                                if($(this).val() == ""){
                                    $(this).val(iptDafaultTxt); 
                                }   
                            });
                            $("#NameFinding").submit(function(){
                                if($(this).find("input[name='programname']").val() == iptDafaultTxt){
                                    return false;
                                }
                                return true;
                            });
                            $("#NameFinding").find(".sArral").click(function(e){
                                e.stopPropagation();
                                $("#NameFinding").siblings(".sDrop").toggle().find(".btns > a").click(function(){
                                    $(".btns").parents(".sDrop").hide();
                                });
                            });
                            $(document).bind("click", function(event) {
                                if($(event.target).parents().hasClass("sDrop")){
                                    return;
                                } else {
                                    $(".sDrop").hide();
                                }
                            });
                        });
                    </script>
                    <a href="<?php echo $_SERVER['REQUEST_URI']; ?>&export=excel" class="export_excel" title="导出Excel">导出Excel</a>
			    </div>
			</div>
			<div id="content">
			    <?php echo $content_for_layout; ?>
			    <div class="clear"></div>
			</div>
		</div>
		<div class="contentbottombg"></div>
	</div>
</div>
<script type="text/javascript">
var notice = '<?php echo $this->Session->flash('flash'); ?>';
if(notice){
	setArtDialog(notice);
}
</script>
<script type="text/javascript">
//遮罩
$(document).ready(function(){
    $(document).bind("contextmenu",function(e){
        return false;
    });
});
</script>
</body>
</html>
<?php //echo $this->element('sql_dump'); ?>