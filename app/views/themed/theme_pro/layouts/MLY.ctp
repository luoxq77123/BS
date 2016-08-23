<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>内容审核系统</title>
	<?php echo $this->Html->css('style.css'); ?>

        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/main.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/jquery.lazyload.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/hScrollPane.js"></script>

        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/My97DatePicker/WdatePicker.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/amcharts/amcharts.js"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo $this->webroot; ?>js/artDialog/skins/idialog.css" />
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/artDialog/artDialog.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/jsonjs/json2.js"></script>
        <script type="text/javascript" src="<?php echo $this->webroot; ?>js/slymaster/jquery.sly.js"></script>

        <script type="text/javascript">
	    function setArtDialog(message) {
		art.dialog({
		    title: '提示',
		    content: message,
		    lock: true,
		    okValue: '确认',
		    ok: function() {
			return true;
		    }
		});
	    }
        </script>
    </head>
    <body>
        <iframe id="zz" style="display:none;" src="<?php echo $this->webroot; ?>js/tmp.html"></iframe>
        <div class="top">
            <div class="topinner">
                <div class="wrap of">
		    <?php
		    if (isset($relationship_info)) {
			echo $this->Html->image('logo2.jpg', array('class' => 'logo', 'alt' => '内容生产服务平台审核系统'));
		    } elseif (isset($relationship)) {
			echo $this->Html->image('logo1.jpg', array('class' => 'logo', 'alt' => '内容生产服务平台审核系统'));
		    } else {
			echo $this->Html->image('logo.jpg', array('class' => 'logo', 'alt' => '内容生产服务平台审核系统'));
		    }
		    ?>
                    <div class="fr">
                        欢迎您:
                        <span class="red">[<?php if (isset($userInfo['UserName'])) echo $userInfo['UserName']; ?>]</span> 
                        |<?php if (!isset($relationship)) { ?>
    			当前有 
    			<div style="display:none;"> 
    			    <form action="" id="StateFinding" name="StateFinding" method="post" onsubmit="return false;" accept-charset="utf-8">            
				    <?php echo $this->Form->input('ContentAuditState', array('value' => '0', 'label' => false, 'div' => false)); ?>
    			    </form>
    			</div>
    			<a class="red" href="#" onclick="cxSubmit(4, 3);" style="color:#ef0619;"><?php if (isset($layoutParams['allCount'])) echo $layoutParams['allCount']; ?></a>		         
    			条待审核节目 |
			<?php }; ?>
			<?php if (defined("OPERATELOG_SHOW") && OPERATELOG_SHOW && !isset($is_operator)): ?><a href="<?php echo $this->Html->url(array('controller' => 'operatelog', 'action' => 'index', 'start_date' => date('Y-m-d H:i:s', time() - 60 * 60 * 24 * 40), 'end_date' => date('Y-m-d H:i:s'))); ?>">操作记录</a> |<?php endif; ?>
			<?php if (!isset($relationship) || isset($relationship_operation)) {
			    ?>
			<!--add1018 for 关联权限-->
			<?php if(isset($user_relation_per) && $user_relation_per) {
			    ?>
    			<a href="<?php echo $this->Html->url(array('controller' => 'relationship', 'action' => 'index', 'begintime' => date('Y-m-d H:i:s', time() - 60 * 60 * 24), 'endtime' => date('Y-m-d H:i:s'))); ?>">节目关联</a> |
			<?php }?>
			    <?php } ?>
			<?php
			if (isset($relationship) && !isset($relationship_operation)) {
			    if (isset($is_operator)) {
				?>
				<a href="<?php echo $this->Html->url(array('controller' => 'relationship', 'action' => 'set_relation_column', 'guid' => $guid)); ?>">栏目关联</a> 	
			    <?php } else {
				?>
				<a href="<?php echo $this->Html->url(array('controller' => 'relationship', 'action' => 'set_relation_column')); ?>">栏目关联</a> 
				|<?php }
			}
			?>
			<?php
			if (isset($relationship) || isset($relationship_operation)) {
			    if (isset($is_operator)) {
				?>
				<a href="<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'auditList', 'guid' => $guid)); ?>">返回审核</a> 
			    <?php } else { ?>
				<a href="<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'auditList')); ?>">返回审核</a>
			    <?php
			    }
			} else {
			    ?>
    			<a href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'logout')); ?>">退出</a> 
<?php } ?>
			| 
                    </div>
                </div>
            </div>
        </div>
        <div class="wrap">
            <div class="content">
                <div class="contenttopbg">
                    <div class="topnav">
                        <ul <?php if (isset($relationship)) { ?>style="display:none;"<?php } ?>>			
				<?php $theLayoutMode = isset($layoutParams['layoutMode']) ? $layoutParams['layoutMode'] : ''; ?>				
                            <li class="jmlb">
				<?php if ($theLayoutMode == LAYOUT_MODE_NOT_CTD): ?>
    				<a href="#" onclick="onUpdateState(5)">节目列表</a>
				<?php else : ?>
    				<a href="#" onclick="cxSubmit(4, 3)">节目列表</a>
				<?php endif; ?>
                            </li>
                            <li class="wdsh">
<?php if (isset($layoutParams['model'])) { ?>
    				<a href="<?php echo $this->Html->url(array('controller' => 'batch_audit_tasks', 'action' => 'batchAudit', 'cmode' => $layoutParams['model'], 'cpage' => $layoutParams['page'])); ?>">我的审核</a>
			    <?php } ?>
			    </li>
                        </ul>
			<?php if(!isset($relationship_operation) && !isset($is_operator) && isset($relationship)) {
			 echo $this->element('search_dialog', array('findarr' => $findingArr));
			    ?>
			
			<?php }?>
                        <div class="fr">
                            <!--搜索框 开始 modifiedDate:2013-3-5-->
				<?php if (!isset($relationship)) { ?>
    			    <div class="z-searchBox">
    				<form action="<?php
					  if (isset($relationship)) {
					      echo $this->Html->url(array('controller' => 'relationship', 'action' => 'index'));
					  }
					  ?>" id="NameFinding" name="NameFinding" method="post" onsubmit="<?php if (!isset($relationship)) echo "retuen false;" ?>" accept-charset="utf-8">
					  <?php
					  $pgmNameValuen = "";
					  if (isset($findingArr) && strlen($findingArr['PgmName'])):
					      $pgmNameValuen = $findingArr['PgmName'];
					  endif;
					  ?> 
					  <?php echo $this->Form->input('PgmName', array('value' => $pgmNameValuen, 'label' => false, 'class' => 'sTxt', 'div' => false)); ?>                    	
					  <?php
					  if (!isset($relationship)) {
					      echo $this->Form->submit('', array('class' => 'sBtn', 'onclick' => 'cxSubmit(4,1)', 'label' => false, 'div' => false));
					  } else {
					      echo $this->Form->submit('', array('class' => 'sBtn', 'onclick' => "", 'label' => false, 'div' => false));
					  }
					  ?>
    				    <span class="sArral" title="高级搜索"></span>
    				</form> 
    <?php if (!isset($relationship)) { ?>                    
					<div class="sDrop" style="display:none">  
					    <form action="" id="Finding" name="Finding" method="post" onsubmit="return false;" accept-charset="utf-8">                         
						<ul>
						    <li>
							<div class="t"><span>节目提交时间：</span></div>
							<div class="c">
							    <?php
							    $startTimeValue = "";
							    $endTimeValue = "";
							    if (isset($findingArr) && strlen($findingArr['StartTime'])):
								$startTimeValue = $findingArr['StartTime'];
							    endif;
							    if (isset($findingArr) && strlen($findingArr['EndTime'])):
								$endTimeValue = $findingArr['EndTime'];
							    endif;
							    ?>
	<?php echo $this->Form->input('StartTime', array('onfocus' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})", 'label' => false, 'class' => 'WdateFind', 'value' => $startTimeValue, 'div' => false)); ?>
							    <span>到</span>
	<?php echo $this->Form->input('EndTime', array('onfocus' => "WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})", 'label' => false, 'class' => 'WdateFind', 'value' => $endTimeValue, 'div' => false)); ?> 
							</div>
						    </li>
						    <li>
							<div class="t"><span>节 目 状 态：</span></div>
							<div class="c">
							    <?php
							    $stateValue = NOT_AUDIT_TASK_STATE;
							    if (isset($findingArr) && strlen($findingArr['ContentAuditState'])):
								$stateValue = (int) $findingArr['ContentAuditState'];
							    endif;
							    $stateField = Configure::read('DEFAULT_STATE_FIELD');
							    echo $this->Form->select('ContentAuditState', $stateField, $stateValue, array('empty' => false));
							    ?>
							</div>
						    </li>
						    <li>
							<div class="t"><span>栏　　　目：</span></div>
							<div class="c">                             	   
							    <?php
							    $columnValue = null;
							    if (isset($findingArr) && strlen($findingArr['PgmColumnID'])):
								$columnValue = (int) $findingArr['PgmColumnID'];
							    endif;
							    if (isset($userInfo['UserColumn'])) {
								echo $this->Form->select('PgmColumnID', $userInfo['UserColumn'], $columnValue, array('empty' => '所有栏目'));
							    }
							    ?>
							</div>
						    </li>
						    <li>
							<div class="t"><span>节 目 标 题：</span></div>
							<div class="c">
							    <?php
							    $pgmNameValue = "";
							    if (isset($findingArr) && strlen($findingArr['PgmName'])):
								$pgmNameValue = $findingArr['PgmName'];
							    endif;
							    ?>
	<?php echo $this->Form->input('PgmName', array('value' => $pgmNameValue, 'label' => false, 'class' => 'longIpt', 'div' => false)); ?>
							</div>
						    </li>
						    <li>
							<div class="t"><span>编    辑    人：</span></div>
							<div class="c">
							    <?php
							    $creatorNameValue = "";
							    if (isset($findingArr) && strlen($findingArr['CreatorName'])):
								$creatorNameValue = $findingArr['CreatorName'];
							    endif;
							    ?>
						    <?php echo $this->Form->input('CreatorName', array('value' => $creatorNameValue, 'label' => false, 'class' => 'longIpt', 'div' => false)); ?>
							</div>
						    </li>
						</ul>
						<div class="btns">
	<?php echo $this->Form->submit('', array('class' => 'submit', 'name' => 'my', 'onclick' => 'cxSubmit(4,2)', 'label' => false, 'div' => false)); ?>
						    <a href="#" class="cancl"></a>
						</div>                                        
						<div class="arral"></div>     
					    </form>                     
					</div>
					<?php
				    }elseif (!isset($relationship_operation)) {
//					echo $this->element('search_dialog', array('findarr' => $findingArr));
				    }
				    ?>
    			    </div>
<?php } ?>
                            <script type="text/javascript">
	    /*搜索框的操作*/
	    var iptDafaultTxt = "请输入节目名称";
	    var $stext = $(".z-searchBox").find(".sTxt");
	    if ($stext.val() == "") {
		$stext.val(iptDafaultTxt);
	    }
	    $stext.focus(function() {
		$(this).parents(".z-searchBox").addClass("z-searchBox-on");
		if ($(this).val() == iptDafaultTxt || "") {
		    $(this).val("");
		} else {
		    return;
		}
	    }).blur(function() {
		$(this).parents(".z-searchBox").removeClass("z-searchBox-on");
		if ($(this).val() == "") {
		    $(this).val(iptDafaultTxt);
		}
	    });
	    $("#NameFinding").find(".sArral").click(function(e) {
		e.stopPropagation();
		$("#NameFinding").siblings(".sDrop").toggle().find(".btns > a").click(function() {
		    $(".btns").parents(".sDrop").hide();
		});
		var relation_dialog_content = $('.relation_search').html();
		if (relation_dialog_content) {
		    var dialog;
		    dialog = art.dialog({
			title: '查询',
			content: relation_dialog_content,
			lock: true,
			fixed: true,
			padding: 0
		    });
		    $('.cancel').click(function() {
			dialog.close();
		    })
		}
	    });
	    $(document).bind("click", function(event) {
		if ($(event.target).parents().hasClass("sDrop")) {
		    return;
		} else {
		    $(".sDrop").hide();
		}
	    });
                            </script>
                            <!--搜索框 结束-->
			    <?php if (!isset($relationship)) { ?>
    			    <a href="javascript:void(0);" class="shuaXin" onclick="window.location.reload()" title="刷新">刷新</a>
				<?php if (IS_ACCOUNT == 1): ?>
				    <?php if ($theLayoutMode == LAYOUT_MODE_NOT_CTD): ?>
	    			    <a href="#" onclick="onUpdateState(3);" class="tongji" title="统计">统计</a>
					<?php
				    else:
					if (isset($layoutParams)) {
					    ?>
					    <a href="<?php echo $this->Html->url(array('controller' => 'accounts', 'action' => 'statistics', 'cmode' => $layoutParams['model'], 'cpage' => $layoutParams['page'])); ?>" class="tongji" title="统计">统计</a>
					    <?php
					}
				    endif;
				    ?>
    <?php endif; ?>
<?php } elseif (isset($unlock) && $unlock) { ?>
    			    <a href="#" onclick="unlock();" class="unlock" title="解锁">解锁</a>
			<?php } ?>
                        </div>
                    </div>
                    <div id="content">
<?php //echo $this->Html->image('loading.gif', array('id' => 'busy-indicator'));   ?> 		
<?php echo $content_for_layout; ?>
                    </div>	
                </div>
                <div class="contentbottombg"></div>
            </div>
        </div>
        <script type="text/javascript">
	    var notice = '<?php echo $this->Session->flash('flash'); ?>';
	    if (notice) {
		setArtDialog(notice);
	    }
        </script>
        <script type="text/javascript">
	    //查询处理
	    function cxSubmit(value, tag) {
		//return false;
<?php if ($theLayoutMode == LAYOUT_MODE_NOT_CTD): ?>
    		onUpdateState(value, tag);
<?php else: ?>
    		submitAction(tag);
<?php endif; ?>
	    }
	    function submitAction(tag) {
		var numTag = parseInt(tag);
		if (numTag == 1) {
		    var pgmName = $(".z-searchBox").find(".sTxt").val();
		    if (pgmName == "请输入节目名称") {
			$(".z-searchBox").find(".sTxt").val("");
		    }
		    document.getElementById("NameFinding").action = "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'auditList')); ?>";
		    document.getElementById("NameFinding").submit();
		}
		else if (numTag == 2) {
		    document.getElementById("Finding").action = "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'auditList')); ?>";
		    document.getElementById("Finding").submit();
		}
		else if (numTag == 3) {
		    document.getElementById("StateFinding").action = "<?php echo $this->Html->url(array('controller' => 'contents', 'action' => 'auditList')); ?>";
		    document.getElementById("StateFinding").submit();
		}

	    }
        </script>
        <script type="text/javascript">
	    //遮罩
	    $(document).ready(function() {
<?php if (!isset($relationship)) { ?>
    		$(document).bind("contextmenu", function(e) {
    		    return false;
    		});
<?php } ?>
<?php if (isset($result_status)) { ?>
    		setArtDialog('<?php echo $result_status; ?>');
<?php } ?>
	    });
	    $("#zz").css({
		opacity: "0.6",
		position: "absolute",
		height: $("body").outerHeight(),
		width: "100%",
		backgroundColor: "#000",
		zIndex: "998"
	    });

	    //
	    $(".xiangxitable tr:odd").addClass("odd");
	    $(".xiangxitable tr").hover(
		    function() {
			$(this).addClass("hover");
		    },
		    function() {
			$(this).removeClass("hover");
		    }
	    );
	    $(".jmy ul.of li").hover(
		    function() {
			$(this).addClass("hover");
		    },
		    function() {
			$(this).removeClass("hover");
		    }
	    );
	    $(".shright").tab();

        </script>
    </body>
</html>
<?php //echo $this->element('sql_dump'); ?>