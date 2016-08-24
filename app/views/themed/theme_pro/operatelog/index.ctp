
<div class="box">
    <table class="clear table table-striped table-bordered table-hover list-table">
        <thead>
            <tr>
                <th>节目名称</th>
                <th style="width:60px;">节目时长</th>
                <th style="width:60px;">操作人员</th>
                <th style="width:120px;">完成时间</th>
                <th style="width:100px;">完成操作</th>
                <th style="width:60px;">完成状态</th>
                <?php if(!defined("OPERATELOG_SHOW_EX") || OPERATELOG_SHOW_EX):?>
                <th>所属频道</th>
                <th>所属栏目</th>
                <?php else: ?>
                <th>媒资ID</th>
                <?php endif; ?>
                <th style="width:240px;">GUID</th>
		        <th style="width:60px;">平台</th>
            </tr>
        </thead>
        <tbody>
            <?php if($operatelogs):?>
            <?php foreach ($operatelogs as $operatelog):?>
            <tr>
                <td nowrap="wrap" style="max-width:400px;word-wrap:break-word;overflow:hidden" title="<?php echo $operatelog['Operatelog']['programname']; ?>"><?php echo $this->ViewOperation->subStringFormat($operatelog['Operatelog']['programname'], 20); ?></td>
                <td><?php echo format_length($operatelog['Operatelog']['pgmlength']); ?></td>
                <td><?php echo $operatelog['Operatelog']['operatorname']; ?></td>
                <td><?php echo $operatelog['Operatelog']['operatetime']; ?></td>
                <td><?php echo isset($operatetypes[$operatelog['Operatelog']['operatetype']]) ? $operatetypes[$operatelog['Operatelog']['operatetype']] : $operatelog['Operatelog']['operatetype']; ?></td>
                <td><?php echo isset($operateresults[$operatelog['Operatelog']['operateresult']]) ? $operateresults[$operatelog['Operatelog']['operateresult']] : $operatelog['Operatelog']['operateresult']; ?></td>
                <?php if(!defined("OPERATELOG_SHOW_EX") || OPERATELOG_SHOW_EX):?>
                <td><?php echo $operatelog['Operatelog']['exchannel']; ?></td>
                <td><?php echo $operatelog['Operatelog']['excolumn']; ?></td>
                <?php else:?>
                <td><?php echo $operatelog['Operatelog']['entityid']; ?></td>
                <?php endif; ?>
                <td><?php echo $operatelog['Operatelog']['programguid']; ?></td>
		        <td><?php echo $platform[$operatelog['Operatelog']['systemid']];?></td>
            </tr>
            <?php endforeach; ?>
            <?php else:?>
            <tr>
                <td style="text-align:center" colspan="<?php if(!defined("OPERATELOG_SHOW_EX") || OPERATELOG_SHOW_EX) echo '10'; else echo '8'; ?>">没有合适记录</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="page">
        <div class="fr">
        <?php
            $paginator->options(array('url' => $this->passedArgs));               
            echo $this->Paginator->first('首页',array('tag' => 'span','separator' => '','class'=>'a'));         
            echo $this->Paginator->prev('上一页', array(), null, array('tag' => 'span','class' => 'b'));
            echo $this->Paginator->numbers(array('before' => '','tag' => 'span','class' => 'number','separator' => '','modulus'=>4));
            echo $this->Paginator->next('下一页', array(), null, array('tag' => 'span','class' => 'b'));         
            echo $this->Paginator->last('尾页',array('tag' => 'span','class'=>'a' ));
        ?>
        </div>
    </div>
</div>
<?php // echo $this->element('sql_dump'); ?>