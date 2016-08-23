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
			foreach ($relationship_list as $pgmData) :
			    ?>
    			<tr class="tr_data">
    			    <td class="hide check">
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