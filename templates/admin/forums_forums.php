<h1 class="cu-section-title"><?php _e('Forums Management','bxpress'); ?></h1>

<form name="frmForums" id="frm-forums" method="post" action="forums.php">
<!-- Bulk operations -->
<div class="bxpress_options">
    <select name="action" id="bulk-top">
        <option value=""><?php _e('Bulk actions...','bxpress'); ?></option>
        <option value="enable"><?php _e('Activate','bxpress'); ?></option>
        <option value="disable"><?php _e('Disable','bxpress'); ?></option>
        <option value="delete"><?php _e('Delete','bxpress'); ?></option>
    </select>
    <input type="button" id="the-op-top" value="<?php _e('Apply','bxpress'); ?>" onclick="before_submit('frm-forums');" />
    &nbsp; &nbsp;
    <a href="forums.php?action=new"><?php _e('New Forum','dtransport'); ?></a>    
</div>
<!--//-->
<table class="outer" cellspacing="1" cellpadding="0" width="100%">
    <thead>
    <tr align="center">
        <th width="20"><input type="checkbox" id="checkall" onchange="$('#frm-forums').toggleCheckboxes(':not(#checkall)');"></th>
        <th width="50"><?php _e('ID','bxpress'); ?></th>
        <th align="left"><?php _e('Name','bxpress'); ?></th>
        <th width="50"><?php _e('Topics','bxpress'); ?></th>
        <th width="50"><?php _e('Posts','bxpress'); ?></th>
        <th><?php _e('Category','bxpress'); ?></th>
        <th width="26"><?php _e('Active','bxpress'); ?></th>
        <th width="26"><?php _e('Attachments','bxpress'); ?></th>
        <th><?php _e('Order','bxpress'); ?></th>
    </tr>
    </thead>
    <tfoot>
    <tr align="center">
        <th width="20"><input type="checkbox" id="checkall2" onchange="$('#frm-forums').toggleCheckboxes(':not(#checkall2)');"></th>
        <th width="50"><?php _e('ID','bxpress'); ?></th>
        <th align="left"><?php _e('Name','bxpress'); ?></th>
        <th width="50"><?php _e('Topics','bxpress'); ?></th>
        <th width="50"><?php _e('Posts','bxpress'); ?></th>
        <th><?php _e('Category','bxpress'); ?></th>
        <th width="26"><?php _e('Active','bxpress'); ?></th>
        <th width="26"><?php _e('Attachments','bxpress'); ?></th>
        <th><?php _e('Order','bxpress'); ?></th>
    </tr>
    </tfoot>
    <tbody>
    <?php if(empty($forums)): ?>
    <tr class="even"><td colspan="9" align="center"><?php _e('There are not forums created yet!','bxpress'); ?></td></tr>
    <?php endif; ?>
    <?php foreach($forums as $forum): ?>
        <tr class="<?php echo tpl_cycle("even,odd"); ?>" align="center" valign="top">
            <td><input type="checkbox" name="ids[]" id="item-<?php echo $forum['id']; ?>" value="<?php echo $forum['id']; ?>" /></td>
            <td><strong><?php echo $forum['id']; ?></strong></td>
            <td align="left">
                <a href="../forum.php?id=<?php echo $forum['id']; ?>"><?php echo $forum['title']; ?></a>
                <span class="cu-item-options">
                    <a href="?action=edit&amp;id=<?php echo $forum['id']; ?>"><?php _e('Edit','bxpress'); ?></a> &bull;
                    <a href="#" onclick="select_option(<?php echo $forum['id']; ?>,'delete','frm-forums');"><?php _e('Delete','bxpress'); ?></a> &bull;
                    <a href="?action=moderators&amp;id=<?php echo $forum['id']; ?>"><?php _e('Moderators','bxpress'); ?></a>
                </span>
            </td>
            <td><?php echo $forum['topics']; ?></td>
            <td><?php echo $forum['posts']; ?></td>
            <td><?php echo $forum['catego']; ?></td>
            <td><img src="../images/<?php echo $forum['active'] ? 'ok' : 'no'; ?>.png" border="0" alt="" /></td>
            <td><img src="../images/<?php echo  $forum['attach'] ? 'ok' : 'no'; ?>.png" border="0" alt="" /></td>
            <td><input type="text" name="orders[<?php echo $forum['id']; ?>]" value="<?php echo $forum['order']; ?>" size="5" style="text-align: center;" /></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<!-- Bulk operations -->
<div class="bxpress_options">
    <select name="actionb" id="bulk-bottom">
        <option value=""><?php _e('Bulk actions...','bxpress'); ?></option>
        <option value="enable"><?php _e('Activate','bxpress'); ?></option>
        <option value="disable"><?php _e('Disable','bxpress'); ?></option>
        <option value="delete"><?php _e('Delete','bxpress'); ?></option>
    </select>
    <input type="button" id="the-op-bottom" value="<?php _e('Apply','bxpress'); ?>" onclick="before_submit('frm-forums');" />
    &nbsp; &nbsp;
    <a href="forums.php?action=new"><?php _e('New Forum','dtransport'); ?></a>   
</div>
<!--//-->
<?php echo $xoopsSecurity->getTokenHTML(); ?>
<input type="hidden" name="op" value="" />
</form>
