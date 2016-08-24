<div class="box" id="xx" style="display:block;">
    <div class="boxbottombg" id="data_list">
        <div class="container" style="width:1262px; overflow:hidden;">
            <div class="xiangxitable_wrap" style="width:2000px">
                <form action="<?php echo $this->Html->url(array('action' => 'unlock')); ?>" method="post" id="unlock_form" name="unlock_form">
                    <table cellpadding="0" cellspacing="1" class="xiangxitable" id="xxvList" align="left">		    		    
                        <tr>
                            <th class="first">
                                <!--<input onclick="selectall('ids');" type="checkbox" id="check_boxes" />-->
                            </th>
                            <th width="300"> 节目名称 </th>
                            <th>类型</th>
                            <th width="70">提交时间</th>
                            <th>操作人员</th>
                            <th>操作状态</th>
                            <th>频道</th>
                            <th width="60">栏目</th>
                            <th>GUID</th>
                        </tr>

			<?php
			foreach ($relationship_list as $pgmData) :
//					         $auditState = (int)$pgmData['tempauditstate'];
//					         $tmpTaskID = (int)$pgmData['taskid'];
//					         if ($auditState != NOT_AUDIT_TASK_STATE){
//					            $tmpTaskID = -1;
//					         }
			    ?>
    			<tr class="tr_data">
				<?php if ($pgmData['PGMTYPE'] === '粗编') { ?>
				    <td>
					    <input class="ids" type="checkbox" name="id[<?php echo $pgmData['PGMGUID']; ?>]" />
				    </td>
				<?php } else { ?>
				<script>
				    $('#xxvList th.first').remove();
				</script>
			    <?php } ?>
    			<td title="<?php echo $pgmData['PGMNAME']; ?>">
				<?php
				if ($pgmData['PGMTYPE'] === '粗编') {
                    if($pgmData['SATUTS']==2){
                        echo $pgmData['PGMNAME'];
                    }else{
                        echo '<a href=' . $this->Html->url(array('controller' => 'relationship', 'action' => 'operater', 'GUID' => $pgmData['PGMGUID'])) . '>' . $this->ViewOperation->subStringFormat($pgmData['PGMNAME'], DETAIL_NAME_NUM) . '</a>';
                    }
				} else {
				    echo $this->ViewOperation->subStringFormat($pgmData['PGMNAME'], DETAIL_NAME_NUM);
				}
				?>
    			<td><?php echo $pgmData['PGMTYPE']; ?></td>
    			<td><?php echo substr($pgmData['PGMSUBMITTIME'], 0, 19); ?></td>
    			<td><?php echo $pgmData['OPERATER']; ?></td>
    			<td><?php echo $pgmData['OPERATESTATE']; ?></td>
    			<td><?php echo $pgmData['CHANNELNAME']; ?></td>
    			<td><?php echo $pgmData['COLUMNNAME']; ?></td>
    			<td><?php echo $pgmData['PGMGUID']; ?></td>
    			</tr>
			<?php endforeach; ?>
                    </table>
                </form>
                <div style="clear:both"></div>
            </div>
            <div class="hScrollPane_dragbar" >
                <div class="hScrollPane_draghandle"></div>
            </div>
        </div>	
        <div class="page">		
	    <?php
	    //分析查询参数
	    $url = array('controller' => 'relationship',
		'action' => 'index',
		'PgmName' => $findingArr['post_keyword']['PgmName'],
		'channelname' => $findingArr['post_keyword']['channelname'],
		'columnname' => $findingArr['post_keyword']['columnname'],
		'PgmName' => $findingArr['post_keyword']['PgmName'],
		'begintime' => $findingArr['post_keyword']['begintime'],
		'endtime' => $findingArr['post_keyword']['endtime'],
		'pgmtype' => $findingArr['post_keyword']['pgmtype'],
		'operater' => $findingArr['post_keyword']['operater'],
		'operatestate' => $findingArr['post_keyword']['operatestate'],
	    );
	    $this->Paginator->options(array(
		'url' => $url,
		'model' => 'Relationship',
	    ));
	    ?>
	    <?php $pageParams = $this->Paginator->params('Relationship');
	    ?>
            <div class="fl">
                共 
                <span class="red"><?php echo $pageParams['count']; ?></span>
                条,每页
                <span class="red"><?php echo $pageParams['options']['limit']; ?></span> 
                条,当前页次:
                <span class="red">
                <?php
                    echo $this->Paginator->counter(array(
                        'model' => 'Relationship',
                        'format' => '%page%/%pages%'
                    ));
                ?>
                </span>
                页
            </div>
            <div class="fr">
		<?php
		echo $this->Paginator->first('首页', array('tag' => 'span', 'separator' => '', 'class' => 'a'));
		echo $this->Paginator->prev('上一页', array('xx' => 'xxx'), null, array('tag' => 'span', 'class' => 'b'));
		echo $this->Paginator->numbers(array('before' => '', 'tag' => 'span', 'class' => 'number', 'separator' => '', 'modulus' => 4));
		echo $this->Paginator->next('下一页', array(), null, array('tag' => 'span', 'class' => 'b'));
		echo $this->Paginator->last('尾页', array('tag' => 'span', 'class' => 'a'));
		?>
            </div>			
        </div>
    </div>
</div>
<script type="text/javascript">
    function unlock() {
	//检查checkbox个数，不能超过5个
	var count = $("input[class='ids']:checked").size();
	if (count > 5) {
	    setArtDialog('最多只能选择5个节目解锁');
	}
	else if (count < 1) {
	    setArtDialog('请至少选择一个节目解锁');
	} else {
	    $('form#unlock_form').submit();
	}
    }
    var table_width = $(".xiangxitable").width();
    if (table_width < 1262) {

	$(".xiangxitable").width(1262);
	$(".hScrollPane_dragbar").hide(1)
    }
        $(document).ready(function() {
	$(".jmlb").addClass("on");
    });
</script>
