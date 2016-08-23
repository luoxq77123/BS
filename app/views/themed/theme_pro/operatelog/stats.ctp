<div class="box">
    <table class="table table-striped table-bordered table-hover list-table">
        <thead>
            <tr>
                <th>用户名</th>
                <th>完成操作</th>
                <th>生产任务数</th>
                <th>生产任务时长</th>
                <th>打回任务数</th>
                <th>打回任务时长</th>
                <th>总工作量</th>
                <th>总工作时长</th>
                <th>平台</th>
            </tr>
        </thead>
        <tbody>
            <?php if($stats_users):
            $amount_total = 0;
            $amount_total_length = 0;
            $amount_total_backed = 0;
            $amount_total_length_backed = 0;
            $amount_total_finished = 0;
            $amount_total_length_finished = 0;
            foreach ($stats_users as $stats_user):?>
            <tr>
                <td><?php echo $stats_user['operatorname']; ?></td>
                <td><?php echo isset($operatetypes[$stats_user['operatetype']])?$operatetypes[$stats_user['operatetype']]:$stats_user['operatetype']; ?></td>
                <td><?php echo $stats_user['total']; ?></td>
                <td><?php echo format_length($stats_user['total_length']); ?></td>
                <td><?php echo $stats_user['total_backed']; ?></td>
                <td><?php echo format_length($stats_user['total_length_backed']); ?></td>
                <td><?php echo $stats_user['total_finished']; ?></td>
                <td><?php echo format_length($stats_user['total_length_finished']); ?></td>
                <td><?php echo $platform[$stats_user['systemid']]; ?></td>
            </tr>
            <?php 
            $amount_total += $stats_user['total']; 
            $amount_total_length += $stats_user['total_length']; 
            $amount_total_backed += $stats_user['total_backed'];
            $amount_total_length_backed += $stats_user['total_length_backed'];
            $amount_total_finished += $stats_user['total_finished'];
            $amount_total_length_finished += $stats_user['total_length_finished'];
            endforeach; ?>
            <tr>
                <td style="text-align:right" colspan="9">
                    共生产任务 <?php echo $amount_total; ?> 个，时长 <?php echo format_length($amount_total_length); ?> ；打回任务 <?php echo $amount_total_backed; ?> 个， 时长 <?php echo format_length($amount_total_length_backed); ?>
                    ；总工作任务 <?php echo $amount_total_finished; ?> 个， 时长 <?php echo format_length($amount_total_length_finished); ?>
                </td>
            </tr>
            <?php else:?>
            <tr>
                <td style="text-align:center" colspan="9">没有合适记录</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>