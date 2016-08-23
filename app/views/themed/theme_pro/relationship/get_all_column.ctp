<?php
$i = 0;
foreach ($channel_list as $v) {
    ?>
    <div class="channel">
    <input class="parent_checkboxes" name="ids[<?php echo $v['NODEID']?>]" type="checkbox" />
    <?php echo $v['NAMEFORNODE']; ?>
    <span title="点击展开" style="cursor: pointer;" onclick="toogle(this)">+</span>
	<?php foreach ($v['children_column'] as $vv) {
	    ?>
	    <div class="column" style="margin-left: 20px;">
		<input<?php if(in_array($vv['NODEID'], $new_relation_column)) {echo ' checked="checked"';}?> name="ids[<?php echo $vv['NODEID']; ?>]" type="checkbox" style="margin-right: 5px;" onclick="select_parent(this)"/><?php echo $vv['NAMEFORNODE'];?>
	    </div>
    <?php }
    ?>
    </div>
	<?php
	$i++;
    }
    ?>	    		    



