<h1 class="cu-section-title"><?php _e('Forums Management', 'bxpress'); ?></h1>

<form name="frmForums" id="frm-forums" method="post" action="forums.php">
<!-- Bulk operations -->
<div class="cu-bulk-actions">
    <select name="action" id="bulk-top" class="form-control">
        <option value=""><?php _e('Bulk actions...', 'bxpress'); ?></option>
        <option value="enable"><?php _e('Activate', 'bxpress'); ?></option>
        <option value="disable"><?php _e('Disable', 'bxpress'); ?></option>
        <option value="delete"><?php _e('Delete', 'bxpress'); ?></option>
    </select>
    <button type="button" id="the-op-top" class="btn btn-default" onclick="before_submit('frm-forums');"><?php _e('Apply', 'bxpress'); ?></button>

    <a href="forums.php?action=new" class="btn btn-success pull-right"><span class="fa fa-plus"></span> <?php _e('Create Forum', 'dtransport'); ?></a>
</div>
<!--//-->
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?php _e('Existing Forums', 'bxpress'); ?>
        </h3>
    </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th width="20" class="text-center">
                        <input type="checkbox" id="checkall" data-oncheck="chk-forums" data-checkbox="chk-forums">
                    </th>
                    <th width="50" class="text-center"><?php _e('ID', 'bxpress'); ?></th>
                    <th align="left"><?php _e('Name', 'bxpress'); ?></th>
                    <th width="50" class="text-center"><?php _e('Topics', 'bxpress'); ?></th>
                    <th width="50" class="text-center"><?php _e('Posts', 'bxpress'); ?></th>
                    <th class="text-center"><?php _e('Category', 'bxpress'); ?></th>
                    <th width="26" class="text-center"><?php _e('Active', 'bxpress'); ?></th>
                    <th width="26" class="text-center"><?php _e('Attachments', 'bxpress'); ?></th>
                    <th class="text-center"><?php _e('Order', 'bxpress'); ?></th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th width="20" class="text-center">
                        <input type="checkbox" id="checkall" data-oncheck="chk-forums" data-checkbox="chk-forums">
                    </th>
                    <th width="50" class="text-center"><?php _e('ID', 'bxpress'); ?></th>
                    <th align="left"><?php _e('Name', 'bxpress'); ?></th>
                    <th width="50" class="text-center"><?php _e('Topics', 'bxpress'); ?></th>
                    <th width="50" class="text-center"><?php _e('Posts', 'bxpress'); ?></th>
                    <th class="text-center"><?php _e('Category', 'bxpress'); ?></th>
                    <th width="26" class="text-center"><?php _e('Active', 'bxpress'); ?></th>
                    <th width="26" class="text-center"><?php _e('Attachments', 'bxpress'); ?></th>
                    <th class="text-center"><?php _e('Order', 'bxpress'); ?></th>
                </tr>
                </tfoot>
                <tbody>
                <?php if (empty($forums)): ?>
                    <tr class="text-center"><td colspan="9">
                            <span class="label label-info"><?php _e('There are not forums created yet!', 'bxpress'); ?></span>
                        </td></tr>
                <?php endif; ?>
                <?php foreach ($forums as $forum): ?>
                    <tr class="text-center">
                        <td>
                            <input type="checkbox" name="ids[]" id="item-<?php echo $forum['id']; ?>" value="<?php echo $forum['id']; ?>" data-oncheck="chk-forums">
                        </td>
                        <td><strong><?php echo $forum['id']; ?></strong></td>
                        <td class="text-left">
                            <a href="../forum.php?id=<?php echo $forum['id']; ?>"><?php echo $forum['title']; ?></a>
                <span class="cu-item-options">
                    <a href="?action=edit&amp;id=<?php echo $forum['id']; ?>"><?php _e('Edit', 'bxpress'); ?></a> &bull;
                    <a href="#" onclick="select_option(<?php echo $forum['id']; ?>,'delete','frm-forums');"><?php _e('Delete', 'bxpress'); ?></a> &bull;
                    <a href="?action=moderators&amp;id=<?php echo $forum['id']; ?>"><?php _e('Moderators', 'bxpress'); ?></a>
                </span>
                        </td>
                        <td><?php echo $forum['topics']; ?></td>
                        <td><?php echo $forum['posts']; ?></td>
                        <td><?php echo $forum['catego']; ?></td>
                        <td><img src="../images/<?php echo $forum['active'] ? 'ok' : 'no'; ?>.png" border="0" alt=""></td>
                        <td><img src="../images/<?php echo  $forum['attach'] ? 'ok' : 'no'; ?>.png" border="0" alt=""></td>
                        <td><input type="text" name="orders[<?php echo $forum['id']; ?>]" value="<?php echo $forum['order']; ?>" size="5" style="text-align: center;"></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

</div>
<!-- Bulk operations -->
<div class="cu-bulk-actions">
    <select name="actionb" id="bulk-bottom" class="form-control">
        <option value=""><?php _e('Bulk actions...', 'bxpress'); ?></option>
        <option value="enable"><?php _e('Activate', 'bxpress'); ?></option>
        <option value="disable"><?php _e('Disable', 'bxpress'); ?></option>
        <option value="delete"><?php _e('Delete', 'bxpress'); ?></option>
    </select>
    <button type="button" id="the-op-bottom" class="btn btn-default" onclick="before_submit('frm-forums');"><?php _e('Apply', 'bxpress'); ?></button>
    <a href="forums.php?action=new" class="btn btn-success pull-right"><span class="fa fa-plus"></span> <?php _e('Create Forum', 'dtransport'); ?></a>
</div>
<!--//-->
<?php echo $xoopsSecurity->getTokenHTML(); ?>
<input type="hidden" name="op" value="">
</form>
