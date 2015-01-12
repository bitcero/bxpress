<script type="text/javascript">
    var bx_message = '<?php _e('Do you really wish to delete selected announcements?','bxpress'); ?>';
</script>
<h1 class="cu-section-title"><?php _e('Announcements Management','bxpress'); ?></h1>

<form name="frmAnnoun" id="frm-announ" method="post" action="announcements.php">
    <div class="bxpress_options">
        <select name="action" id="bulk-top">
            <option value=""><?php _e('Bulk actions...','bxpress'); ?></option>
            <option value="delete"><?php _e('Delete','bxpress'); ?></option>
        </select>
        <input type="button" id="the-op-top" value="<?php _e('Apply','bxpress'); ?>" onclick="before_submit('frm-announ');" />
        &nbsp; &nbsp;
        <a href="announcements.php"><?php _e('Show All','bxpress'); ?></a> &nbsp; | &nbsp;
        <a href="announcements.php?action=new"><?php _e('New Announcement','bxpress'); ?></a>
    </div>
<table class="outer" cellspacing="1" width="100%">
    <thead>
	<tr class="head" align="center">
		<th width="20"><input type="checkbox" id="checkall" onchange="$('#frm-announ').toggleCheckboxes(':not(#checkall)');" /></th>
		<th width="50"><?php _e('ID','bxpress'); ?></th>
		<th><?php _e('Announcement','bxpress'); ?></th>
		<th><?php _e('Expire','bxpress'); ?></th>
		<th><?php _e('Location','bxpress'); ?></th>
		<th><?php _e('By','bxpress'); ?></th>
	</tr>
    </thead>
    <tfoot>
    <tr class="head" align="center">
		<th width="20"><input type="checkbox" id="checkall2" onchange="$('#frm-announ').toggleCheckboxes(':not(#checkall2)');" /></th>
		<th width="50"><?php _e('ID','bxpress'); ?></th>
		<th><?php _e('Announcement','bxpress'); ?></th>
		<th><?php _e('Expire','bxpress'); ?></th>
		<th><?php _e('Location','bxpress'); ?></th>
		<th><?php _e('By','bxpress'); ?></th>
	</tr>
    </tfoot>
    <tbody>
    <?php if(empty($announcements)): ?>
        <tr class="even">
            <td align="center" colspan="6"><?php _e('There are not announcements created yet!','bxpress'); ?></td>
        </tr>
    <?php endif; ?>
	<?php foreach($announcements as $item): ?>
		<tr class="<?php echo tpl_cycle("even,odd"); ?>" align="center" valign="top">
			<td><input type="checkbox" name="ids[]" id="item-<?php echo $item['id']; ?>" value="<?php echo $item['id']; ?>" /></td>
			<td><strong><?php echo $item['id']; ?></strong></td>
			<td align="left">
                <?php echo $item['text']; ?>
                <span class="cu-item-options">
                    <a href="?action=edit&amp;id=<?php echo $item['id']; ?>"><?php _e('Edit','bxpress'); ?></a> |
                    <a href="#" onclick="select_option(<?php echo $item['id']; ?>,'delete','frm-announ');"><?php _e('Delete','bxpress'); ?></a>
                </span>
            </td>
			<td><?php echo $item['expire']; ?></td>
			<td><a href="<?php echo $item['wherelink']; ?>"><?php echo $item['where']; ?></a></td>
			<td><?php echo $item['by']; ?></td>
		</tr>
	<?php endforeach; ?>
    </tbody>
</table>
<div class="bxpress_options">
        <select name="actionb" id="bulk-bottom">
            <option value=""><?php _e('Bulk actions...','bxpress'); ?></option>
            <option value="delete"><?php _e('Delete','bxpress'); ?></option>
        </select>
        <input type="button" id="the-op-bottom" value="<?php _e('Apply','bxpress'); ?>" onclick="before_submit('frm-announ');" />
        &nbsp; &nbsp;
        <a href="resources.php"><?php _e('Show All','bxpress'); ?></a> &nbsp; | &nbsp;
        <a href="announcements.php?action=new"><?php _e('New Announcement','bxpress'); ?></a>
    </div>
<?php echo $xoopsSecurity->getTokenHTML(); ?>
</form>