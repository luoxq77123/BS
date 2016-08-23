<style>
    .relation_dialog {
	text-align: center;
    }
    .relation_dialog .hide {
	display: none;
    }
</style>
<div class="dialog_title">
</div>
<div class="chubian_name">

</div>

<div id="relation_dialog_container">
<form action="<?php echo $this->Html->url(array('controller' => 'relationship', 'action' => 'operater', 'GUID' => $guid)); ?>" id="relation_dialog_form" name="relation_dialog_form" method="post" accept-charset="utf-8">                           
<table cellspacing="1" cellpadding="0" align="left" id="list" class="relation_dialog xiangxitable" style="width: 600px; left: 0px;">		    		    
    <tbody><tr class="">
	    <th>
		序号
	    </th>
	    <th>
		精编节目
	    </th>
	    <th>提交时间</th>
	    <th>节目时长</th>
	    <th>操作</th>
	</tr>
	</tbody></table>
    	<input type="hidden" value="1" class="submit_type" name="data[submit_type]"/>
	 </form>
</div>