<h1 class="cu-section-title"><?php _e('Categories Management', 'bxpress'); ?></h1>

<div class="row">

    <div class="col-md-4">
        <div class="cu-box">
            <div class="box-header">
                <h3 class="box-title"><?php _e('Add Category', 'bxpress'); ?></h3>
            </div>
            <div class="box-content">
                <form name="frmNewCat" id="frm-new-categos" method="post" action="categories.php">
                    <div class="form-group">
                        <label for="cat-title"><?php _e('Name', 'bxpress'); ?></label>
                        <input type="text" name="title" id="cat-title" value="" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="cat-desc"><?php _e('Description', 'bxpress'); ?></label>
                        <textarea name="desc" id="cat-desc" cols="30" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <label><?php _e('Groups with access', 'bxpress'); ?></label>
                        <?php echo $groups->render(); ?>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="showdesc" value="1" checked="checked" />
                            <?php _e('Show description', 'bxpress'); ?>
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="status" value="1" checked="checked" />
                            <?php _e('Activate category', 'bxpress'); ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg"><?php _e('Create Category', 'bxpress'); ?></button>
                    </div>
                    <input type="hidden" name="action" value="save" />
                    <?php echo $xoopsSecurity->getTokenHTML(); ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">

        <form name="frmCats" id="frm-categories" action="categories.php" method="post">
            <div class="cu-bulk-actions">
                <select name="action" id="bulk-top" class="form-control">
                    <option value=""><?php _e('Bulk actions...', 'bxpress'); ?></option>
                    <option value="enable"><?php _e('Activate', 'bxpress'); ?></option>
                    <option value="disable"><?php _e('Disable', 'bxpress'); ?></option>
                    <option value="delete"><?php _e('Delete', 'bxpress'); ?></option>
                </select>
                <button type="button" id="the-op-top" onclick="before_submit('frm-categories');" class="btn btn-default"><?php _e('Apply', 'bxpress'); ?></button>
            </div>

            <div class="panel">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr class="head" align="center">
                            <th width="20" class="text-center">
                                <input type="checkbox" id="checkall" data-checkbox="chk-categories">
                            </th>
                            <th width="20" class="text-center"><?php _e('ID', 'bxpress'); ?></th>
                            <th align="left"><?php _e('Name', 'bxpress'); ?></th>
                            <th class="text-center"><?php _e('Active', 'bxpress'); ?></th>
                            <th align="left"><?php _e('Descripcion', 'bxpress'); ?></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr class="head" align="center">
                            <th width="20" class="text-center">
                                <input type="checkbox" id="checkall" data-checkbox="chk-categories">
                            </th>
                            <th width="20" class="text-center"><?php _e('ID', 'bxpress'); ?></th>
                            <th align="left"><?php _e('Name', 'bxpress'); ?></th>
                            <th class="text-center"><?php _e('Active', 'bxpress'); ?></th>
                            <th align="left"><?php _e('Descripcion', 'bxpress'); ?></th>
                        </tr>
                        </tfoot>

                        <tbody>
                        <?php if (empty($categos)): ?>
                            <tr class="text-center">
                                <td colspan="5"><span class="label label-info"><?php _e('There are not categories created yet!', 'bxpress'); ?></span></td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($categos as $cat): ?>
                            <tr class="<?php echo tpl_cycle("even,odd"); ?>" align="left" valign="top">
                                <td align="center"><input type="checkbox" name="ids[]" id="item-<?php echo $cat['id']; ?>" value="<?php echo $cat['id']; ?>" data-oncheck="chk-categories"></td>
                                <td align="center"><?php echo $cat['id']; ?></td>
                                <td align="left">
                                    <strong><a href="forums.php?catid=<?php echo $cat['id']; ?>"><?php echo $cat['title']; ?></a></strong>
                                <span class="cu-item-options">
                                    <a href="categories.php?action=edit&amp;id=<?php echo $cat['id']; ?>"><?php _e('Edit', 'bxpress'); ?></a> |
                                    <a href="#" class="delete_cat" id="cat-<?php echo $cat['id']; ?>" onclick="select_option(<?php echo $cat['id']; ?>,'delete','frm-categories');"><?php _e('Delete', 'bxpress'); ?></a>
                                </span>
                                </td>
                                <td align="center">
                                    <span class="fa <?php if ($cat['status']): ?>fa-check text-success<?php else: ?>fa-ban text-danger<?php endif; ?>" border="0" alt="" />
                                </td>
                                <td><?php echo $cat['desc']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cu-bulk-actions">
                <select name="actionb" id="bulk-bottom" class="form-control">
                    <option value=""><?php _e('Bulk actions...', 'bxpress'); ?></option>
                    <option value="enable"><?php _e('Activate', 'bxpress'); ?></option>
                    <option value="disable"><?php _e('Disable', 'bxpress'); ?></option>
                    <option value="delete"><?php _e('Delete', 'bxpress'); ?></option>
                </select>
                <button type="button" id="the-op-bottom" class="btn btn-default" onclick="before_submit('frm-categories');"><?php _e('Apply', 'bxpress'); ?></button>
            </div>
            <?php echo $xoopsSecurity->getTokenHTML(); ?>
        </form>

    </div>

</div>

        
