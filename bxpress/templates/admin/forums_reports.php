<script type="text/javascript">
    var bx_message = '<?php _e('Do you really want to delete selected reports?','bxpress'); ?>';
    var bx_select_message = '<?php _e('Select some report before!','bxpress'); ?>';
</script>
<h1 class="cu-section-title"><?php _e('Reports','bxpress'); ?></h1>

<form name="frmReports" id="frm-reports" method="POST" action="reports.php">
    <div class="bxpress_options">
        <select name="action" id="bulk-top">
            <option value=""><?php _e('Bulk actions...','bxpress'); ?></option>
            <option value="read"><?php _e('Mark as read','bxpress'); ?></option>
            <option value="notread"><?php _e('Mark as not read','bxpress'); ?></option>
            <option value="delete"><?php _e('Delete','bxpress'); ?></option>
        </select>
        <input type="button" id="the-op-top" value="<?php _e('Apply','bxpress'); ?>" onclick="before_submit('frm-reports');" />
        &nbsp; &nbsp;
        <a href="reports.php"><?php _e('Show All','bxpress'); ?></a> &nbsp; | &nbsp;
        <a href="reports.php?show=1"><?php _e('Read','bxpress'); ?></a> &nbsp; | &nbsp;
        <a href="reports.php?show=2"><?php _e('Not read','bxpress'); ?></a>
    </div>
<table class="outer" width="100%" cellspacing="1">
    <thead>
	<tr align="center"> 
            <th widht="30"><input type="checkbox" id="checkall" onchange="$('#frm-reports').toggleCheckboxes(':not(#checkall)');"></th>
            <th><?php _e('ID','bxpress'); ?></th>
            <th align="right"><?php _e('Reported','bxpress'); ?></th>
            <th><?php _e('Message','bxpress'); ?></th>
	</tr>
    </thead>
    <tfoot>
	<tr align="center"> 
            <th><input type="checkbox" id="checkall2" onchange="$('#frm-reports').toggleCheckboxes(':not(#checkall2)');"></th>
            <th><?php _e('ID','bxpress'); ?></th>
            <th align="right"><?php _e('Reported','bxpress'); ?></th>
            <th><?php _e('Message','bxpress'); ?></th>
	</tr>
    </tfoot>

    <?php foreach($reports as $report): ?>
    <tr class="<?php echo tpl_cycle("even,odd"); ?>" valign="top">
	<td align="center"><input type="checkbox" name="ids[]" id="item-<?php echo $report['id']; ?>" value="<?php echo $report['id']; ?>" /></td>
        <td align="center"><?php echo $report['id']; ?></td>
        <td align="right" class="reporter" nowrap="nowrap">
            <?php echo sprintf(__('By %s','bxpress'), '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$report['uid'].'" target="_blank"><strong>'.$report['user'].'</strong></a>'); ?><br />
            <?php echo $report['date']; ?>
        </td>
        <td>
            <span class="report_message<?php echo $report['zapped']?' read':''; ?>">
                <span class="brdcrm">
                    <a href="<?php echo $report['forum']['link']; ?>"><?php echo $report['forum']['name']; ?></a> →
                    <a href="<?php echo $report['topic']['link']; ?>"><?php echo $report['topic']['title']; ?></a> →
                    <a href="<?php echo $report['post']['link']; ?>"><?php echo sprintf(__('Post #%u','bxpress'), $report['post']['id']); ?></a>
                </span>
                <?php echo $report['report']; ?>
                <span class="cu-item-options">
                <a href="#" onclick="return select_option(<?php echo $report['id']; ?>,'delete','frm-reports');"><?php _e('Delete','bxpress'); ?></a>
                </span>
                <?php if($report['zapped']): ?>
                <span class="zapped">
                    <?php echo sprintf(__('Read on %s by %s','bxpress'), $report['zappedtime'], '<strong><a href="'.XOOPS_URL.'/userinfo.php?uid='.$report['zappedby']['uid'].'">'.$report['zappedby']['name'].'</a>'); ?>
                </span>
                <?php endif; ?>
            </span>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
    <div class="bxpress_options">
        <select name="actionb" id="bulk-bottom">
            <option value=""><?php _e('Bulk actions...','bxpress'); ?></option>
            <option value="read"><?php _e('Mark as read','bxpress'); ?></option>
            <option value="notread"><?php _e('Mark as not read','bxpress'); ?></option>
            <option value="delete"><?php _e('Delete','bxpress'); ?></option>
        </select>
        <input type="button" id="the-op-bottom" value="<?php _e('Apply','bxpress'); ?>" onclick="before_submit('frm-reports');" />
        &nbsp; &nbsp;
        <a href="reports.php"><?php _e('Show All','bxpress'); ?></a> &nbsp; | &nbsp;
        <a href="reports.php?show=1"><?php _e('Read','bxpress'); ?></a> &nbsp; | &nbsp;
        <a href="reports.php?show=2"><?php _e('Not read','bxpress'); ?></a>
    </div>
<?php echo $xoopsSecurity->getTokenHTML(); ?>
    <input type="hidden" name="show" value="<?php echo $show; ?>" />
</form>

