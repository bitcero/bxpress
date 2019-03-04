<script type="text/javascript">
    var bx_message = '<?php _e('Do you really want to delete selected reports?', 'bxpress'); ?>';
    var bx_select_message = '<?php _e('Select some report before!', 'bxpress'); ?>';
</script>
<h1 class="cu-section-title"><?php _e('Reports', 'bxpress'); ?></h1>

<form name="frmReports" id="frm-reports" method="POST" action="reports.php">
    <div class="cu-bulk-actions">
        <select name="action" id="bulk-top" class="form-control">
            <option value=""><?php _e('Bulk actions...', 'bxpress'); ?></option>
            <option value="read"><?php _e('Mark as read', 'bxpress'); ?></option>
            <option value="notread"><?php _e('Mark as not read', 'bxpress'); ?></option>
            <option value="delete"><?php _e('Delete', 'bxpress'); ?></option>
        </select>
        <button type="button" id="the-op-top" onclick="before_submit('frm-reports');" class="btn btn-info"><?php _e('Apply', 'bxpress'); ?></button>

        <ul class="nav nav-pills pull-right">
            <li>
                <a href="reports.php"><?php _e('Show All', 'bxpress'); ?></a>
            </li>
            <li>
                <a href="reports.php?show=1"><?php _e('Read', 'bxpress'); ?></a>
            </li>
            <li>
                <a href="reports.php?show=2"><?php _e('Not read', 'bxpress'); ?></a>
            </li>
        </ul>
    </div>

    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('Existing Reports', 'bxpress'); ?></h3>
        </div>
        <div class="table-responsive">
            <table class="outer" width="100%" cellspacing="1">
                <thead>
                <tr align="center">
                    <th widht="30"><input type="checkbox" id="checkall" onchange="$('#frm-reports').toggleCheckboxes(':not(#checkall)');"></th>
                    <th><?php _e('ID', 'bxpress'); ?></th>
                    <th align="right"><?php _e('Reported', 'bxpress'); ?></th>
                    <th><?php _e('Message', 'bxpress'); ?></th>
                </tr>
                </thead>
                <tfoot>
                <tr align="center">
                    <th><input type="checkbox" id="checkall2" onchange="$('#frm-reports').toggleCheckboxes(':not(#checkall2)');"></th>
                    <th><?php _e('ID', 'bxpress'); ?></th>
                    <th align="right"><?php _e('Reported', 'bxpress'); ?></th>
                    <th><?php _e('Message', 'bxpress'); ?></th>
                </tr>
                </tfoot>

                <tbody>
                <?php if (empty($reports)): ?>
                    <tr class="text-center">
                        <td colspan="4">
                            <span class="text-info"><?php _e('There are not reports registered yet!', 'bxpress'); ?></span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($reports as $report): ?>
                    <tr class="<?php echo tpl_cycle('even,odd'); ?>" valign="top">
                        <td align="center"><input type="checkbox" name="ids[]" id="item-<?php echo $report['id']; ?>" value="<?php echo $report['id']; ?>"></td>
                        <td align="center"><?php echo $report['id']; ?></td>
                        <td align="right" class="reporter" nowrap="nowrap">
                            <?php echo sprintf(__('By %s', 'bxpress'), '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $report['uid'] . '" target="_blank"><strong>' . $report['user'] . '</strong></a>'); ?><br>
                            <?php echo $report['date']; ?>
                        </td>
                        <td>
            <span class="report_message<?php echo $report['zapped'] ? ' read' : ''; ?>">
                <span class="brdcrm">
                    <a href="<?php echo $report['forum']['link']; ?>"><?php echo $report['forum']['name']; ?></a> →
                    <a href="<?php echo $report['topic']['link']; ?>"><?php echo $report['topic']['title']; ?></a> →
                    <a href="<?php echo $report['post']['link']; ?>"><?php echo sprintf(__('Post #%u', 'bxpress'), $report['post']['id']); ?></a>
                </span>
                <?php echo $report['report']; ?>
                <span class="cu-item-options">
                <a href="#" onclick="return select_option(<?php echo $report['id']; ?>,'delete','frm-reports');"><?php _e('Delete', 'bxpress'); ?></a>
                </span>
                <?php if ($report['zapped']): ?>
                    <span class="zapped">
                    <?php echo sprintf(__('Read on %s by %s', 'bxpress'), $report['zappedtime'], '<strong><a href="' . XOOPS_URL . '/userinfo.php?uid=' . $report['zappedby']['uid'] . '">' . $report['zappedby']['name'] . '</a>'); ?>
                </span>
                <?php endif; ?>
            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="cu-bulk-actions">
        <select name="actionb" id="bulk-bottom" class="form-control">
            <option value=""><?php _e('Bulk actions...', 'bxpress'); ?></option>
            <option value="read"><?php _e('Mark as read', 'bxpress'); ?></option>
            <option value="notread"><?php _e('Mark as not read', 'bxpress'); ?></option>
            <option value="delete"><?php _e('Delete', 'bxpress'); ?></option>
        </select>
        <button type="button" id="the-op-bottom" onclick="before_submit('frm-reports');" class="btn btn-indo"><?php _e('Apply', 'bxpress'); ?></button>

        <ul class="nav nav-pills pull-right">
            <li>
                <a href="reports.php"><?php _e('Show All', 'bxpress'); ?></a>
            </li>
            <li>
                <a href="reports.php?show=1"><?php _e('Read', 'bxpress'); ?></a>
            </li>
            <li>
                <a href="reports.php?show=2"><?php _e('Not read', 'bxpress'); ?></a>
            </li>
        </ul>
    </div>
<?php echo $xoopsSecurity->getTokenHTML(); ?>
    <input type="hidden" name="show" value="<?php echo $show; ?>">
</form>

