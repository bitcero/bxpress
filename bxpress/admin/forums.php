<?php
// $Id: forums.php 927 2012-01-15 06:56:26Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION', 'forums');
require __DIR__ . '/header.php';

/**
 * @desc Muestra la lista de foros existentes
 */
function bx_show_forums()
{
    global $xoopsModule, $xoopsSecurity;

    $catid = RMHttpRequest::request('catid', 'integer', 0);

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = 'SELECT * FROM ' . $db->prefix('mod_bxpress_forums');
    if ($catid > 0) {
        $sql .= " WHERE cat='$catid'";
    }
    $sql .= ' ORDER BY cat,`order`';

    $result = $db->query($sql);
    $categos = [];
    $forums = [];

    while (false !== ($row = $db->fetchArray($result))) {
        $forum = new bXForum();
        $forum->assignVars($row);
        // Cargamos la categoría
        if (isset($categos[$forum->category()])) {
            $catego = $categos[$forum->category()];
        } else {
            $categos[$forum->category()] = new bXCategory($forum->category());
            $catego = $categos[$forum->category()];
        }
        // Asignamos los valores
        $forums[] = [
            'id' => $forum->id(),
            'title' => $forum->name(),
            'topics' => $forum->topics(),
            'posts' => $forum->posts(),
            'catego' => $catego->title(),
            'active' => $forum->active(),
            'attach' => $forum->attachments(),
            'order' => $forum->order(),
        ];
    }

    $bc = RMBreadCrumb::get();
    $bc->add_crumb(__('Forums', 'bxpress'));
    xoops_cp_header();

    RMTemplate::get()->add_help(__('Forums Help', 'bxpress'), 'http://www.redmexico.com.mx/docs/bxpress-forums/foros/standalone/1/');
    RMTemplate::get()->add_script('admin.js', 'bxpress');
    RMTemplate::get()->add_head_script('var bx_select_message = "' . __('You must select one forum at least in order to run this action!', 'bxpress') . '";
        var bx_message = "' . __('Do you really want to delete selected forums?\n\nAll posts sent in this forum will be deleted also!', 'bxpress') . '";');

    include RMTemplate::get()->get_template('admin/forums-forums.php', 'module', 'bxpress');

    xoops_cp_footer();
}

/**
 * @desc Muestra el formulario para creación de Foros
 * @param int $edit Determina si se esta editando un foro existente
 */
function bx_show_form($edit = 0)
{
    global $xoopsModule, $xoopsConfig;

    $installed = \Common\Core\Helpers\Plugins::isInstalled('advform') || \Common\Core\Helpers\Plugins::isInstalled('advform-pro');

    if (!$installed) {
        showMessage(
            sprintf(
                __('BXpress recommends to use the plugin <a href=\"%s\">AdvancedForms</a>.', 'bxpress'),
                'https://github.com/bitcero/advform'
            ),
            RMMSG_WARN
        );
    }

    if ($edit) {
        $id = RMHttpRequest::request('id', 'integer', 0);
        if ($id <= 0) {
            RMUris::redirect_with_message(__('Provided ID is not valid!', 'bxpress'), 'forums.php', RMMSG_WARN);
            die();
        }

        $forum = new bXForum($id);
        if ($forum->isNew()) {
            RMUris::redirect_with_message(__('Specified forum does not exists!', 'bxpress'), 'forums.php', RMMSG_ERROR);
            die();
        }
    }

    RMTemplate::get()->add_style('admin.css', 'bxpress');
    xoops_cp_location("<a href='./'>" . $xoopsModule->name() . '</a> &raquo; ' . ($edit ? __('Edit Forum', 'bxpress') : __('New Forum', 'bxpress')));
    xoops_cp_header();

    $bcHand = new bXCategoryHandler();
    $bfHand = new bXForumHandler();

    $form = new RMForm($edit ? __('Edit Forum', 'bxpress') : __('New Forum', 'bxpress'), 'frmForum', 'forums.php');
    // Categorias
    $ele = new RMFormSelect([
        'caption' => __('Category', 'bxpress'),
        'name' => 'cat',
        'selected' => $edit ? [$forum->category()] : null,
        'class' => 'form-control',
        ]);
    $ele->addOption(0, __('Select category...', 'bxpress'), $edit ? 0 : 1);
    $ele->addOptionsArray($bcHand->getForSelect());
    $form->addElement($ele, true, 'noselect:0');
    // NOmbre
    $form->addElement(new RMFormText(__('Forum name', 'bxpress'), 'name', 50, 150, $edit ? $forum->name() : ''), true);
    // Descripcion
    $form->addElement(new RMFormEditor(__('Forum description', 'bxpress'), 'desc', '100%', '300px', $edit ? $forum->getVar('desc', 'e') : ''));

    if ($installed) {
        $form->addElement(new RMFormImageUrl(__('Forum image', 'bxpress'), 'image', $edit ? $forum->image : ''));
    } else {
        $form->addElement(new RMFormText(__('Forum image', 'bxpress'), 'image', $edit ? $forum->image : ''));
    }

    // Activo
    $form->addElement(new RMFormYesNo(__('Activate forum', 'bxpress'), 'active', $edit ? $forum->active() : 1));
    // Firmas
    $form->addElement(new RMFormYesNo(__('Allow signatures in the posts', 'bxpress'), 'sig', $edit ? $forum->signature() : 1));
    // Temas Populares
    $form->addElement(new RMFormText(__('Answers to match a topic as popular', 'bxpress'), 'hot_threshold', 10, 5, $edit ? $forum->hotThreshold() : 10), true, 'bigger:1');
    // Orden en la lista
    $form->addElement(new RMFormText(__('Order in the list', 'bxpress'), 'order', 10, 5, $edit ? $forum->order() : 0), false, 'bigger:-1');
    // Adjuntos
    $form->addElement(new RMFormYesNo(__('Allow attachments', 'bxpress'), 'attachments', $edit ? $forum->attachments() : 1));
    $ele = new RMFormText(__('Maximum attachments file size', 'bxpress'), 'attach_maxkb', 10, 20, $edit ? $forum->maxSize() : 50);
    $ele->setDescription(__('Specify this value in Kilobytes', 'bxpress'));
    $form->addElement($ele, false, 'bigger:0');
    $ele = new RMFormText(__('Allowed file types', 'bxpress'), 'attach_ext', 50, 0, $edit ? implode('|', $forum->extensions()) : 'zip|tar|jpg|gif|png|gz');
    $ele->setDescription(__('Specified the extensions of allowed file types separating each one with "|" and without the dot.', 'bxpress'));
    $form->addElement($ele);
    // Grupos con permiso
    if ($edit) {
        $grupos = $forum->permissions();
    }
    $form->addElement(new RMFormGroups(__('Can view the forum', 'bxpress'), 'perm_view', 1, 1, 5, $edit ? $grupos['view'] : [0]));
    $form->addElement(new RMFormGroups(__('Can start new topics', 'bxpress'), 'perm_topic', 1, 1, 5, $edit ? $grupos['topic'] : [1, 2]));
    $form->addElement(new RMFormGroups(__('Can answer', 'bxpress'), 'perm_reply', 1, 1, 5, $edit ? $grupos['reply'] : [1, 2]));
    $form->addElement(new RMFormGroups(__('Can edit their posts', 'bxpress'), 'perm_edit', 1, 1, 5, $edit ? $grupos['edit'] : [1, 2]));
    $form->addElement(new RMFormGroups(__('Can delete', 'bxpress'), 'perm_delete', 1, 1, 5, $edit ? $grupos['delete'] : [1]));
    $form->addElement(new RMFormGroups(__('Can vote', 'bxpress'), 'perm_vote', 1, 1, 5, $edit ? $grupos['vote'] : [1, 2]));
    $form->addElement(new RMFormGroups(__('Can attach', 'bxpress'), 'perm_attach', 1, 1, 5, $edit ? $grupos['attach'] : [1, 2]));
    $form->addElement(new RMFormGroups(__('Can send without approval', 'bxpress'), 'perm_approve', 1, 1, 5, $edit ? $grupos['approve'] : [1, 2]));

    $ele = new RMFormButtonGroup();
    $ele->addButton('sbt', $edit ? __('Save Changes', 'bxpress') : __('Create Forum', 'bxpress'), 'submit', '', 1);
    $ele->addButton('cancel', __('Cancel', 'bxpress'), 'button', 'onclick="window.location=\'forums.php\';"');
    $form->addElement($ele);
    $form->addElement(new RMFormHidden('action', $edit ? 'saveedit' : 'save'));
    if ($edit) {
        $form->addElement(new RMFormHidden('id', $forum->id()));
    }
    $form->display();

    xoops_cp_footer();
}

/**
 * @desc Almacena los datos de un foro
 * @param mixed $edit
 */
function bx_save_forum($edit = 0)
{
    global $xoopsSecurity, $xoopsModuleConfig, $xoopsConfig;

    $q = $edit ? 'action=edit' : 'action=new';

    $prefix = 1;
    foreach ($_POST as $k => $v) {
        if ('perm_' == mb_substr($k, 0, 5)) {
            $permissions[mb_substr($k, 5)] = $v;
        } else {
            $$k = $v;
        }

        if ('XOOPS_TOKEN_REQUEST' == $k || 'action' == $k) {
            continue;
        }
        $q .= '&' . $k . '=' . $v;
    }

    if (!$xoopsSecurity->check()) {
        RMUris::redirect_with_message(
            __('Session token expired', 'bxpress'),
            'forums.php?' . $q,
            RMMSG_ERROR
        );
    }

    if ($edit) {
        $id = RMHttpRequest::request('id', 'integer', 0);

        if ($id <= 0) {
            RMUris::redirect_with_message(
                __('Specified id is not valid!', 'bxpress'),
                'forums.php',
                RMMSG_ERROR
            );
        }

        $forum = new bXForum($id);
        if ($forum->isNew()) {
            RMUris::redirect_with_message(
                __('Specified forum does not exists!', 'bxpress'),
                'forums.php',
                RMMSG_ERROR
            );
        }
    } else {
        $forum = new bXForum();
    }

    $forum->setVar('name', $name);
    $forum->setVar('desc', $desc);
    $forum->setVar('image', $image);

    if (!$edit) {
        $forum->setVar('topics', 0);
        $forum->setVar('posts', 0);
        $forum->setVar('last_post_id', 0);
        $forum->setVar('subforums', 0);
    }

    $forum->setVar('cat', $cat);
    $forum->setActive($active);
    $forum->setSignature($sig);
    $forum->setPrefix($prefix);
    $forum->setHotThreshold($hot_threshold);
    $forum->setOrder($order);
    $forum->setAttachments($attachments);
    $forum->setMaxSize($attach_maxkb);
    $forum->setExtensions(explode('|', $attach_ext));
    $forum->setPermissions($permissions);

    // Check if forum exists
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_forums') . " WHERE name='$name' AND cat=$cat";
    if ($edit) {
        $sql .= ' AND id_forum != ' . $forum->id();
    }

    list($exists) = $db->fetchRow($db->query($sql));
    if ($exists) {
        RMUris::redirect_with_message(
            sprintf(__('Another forum with name "%s" already exists in this category.', 'bxpress'), $name),
            'forums.php?' . $q,
            RMMSG_ERROR
        );
    }

    if ($forum->save()) {
        if ($parent > 0) {
            $pf = new bXForum($parent);
            if (!$pf->isNew()) {
                $pf->setSubforums($pf->subforums() + 1);
                $pf->save();
            }
        }
        if (!$edit) {
            //Redireccionamos a ventana de selección de moderadores
            redirectMsg('forums.php?action=moderators&id=' . $forum->id(), __('Forum saved successfully! Redirecting to moderators assignment...', 'bxpress'), 0);
        } else {
            redirectMsg('forums.php', __('Changes saved successfully!', 'bxpress'), 0);
        }
    } else {
        redirectMsg('forums.php?' . $q, __('Forum could not be saved!', 'bxpress') . $forum->errors(), 1);
    }
}

/**
 * @desc Almacena los cambios realizados en la lista de foros
 */
function bx_save_changes()
{
    global $db,$util;

    if (!$util->validateToken()) {
        redirectMsg('forums.php', _AS_BB_ERRTOKEN, 1);
        die();
    }

    foreach ($_POST as $k => $v) {
        $$k = $v;
    }

    /**
     * Comprobamos que se haya proporcionado al menos un foro
     */
    if (!is_array($orders) || empty($orders)) {
        redirectMsg('forums.php', _AS_BB_NOSELECTFORUM, 1);
        die();
    }

    foreach ($orders as $k => $v) {
        $sql = 'UPDATE ' . $db->prefix('mod_bxpress_forums') . " SET `order`='" . $v . "' WHERE id_forum='$k'";
        $db->queryF($sql);
    }

    redirectMsg('forums.php', _AS_BB_DBOK, 0);
}

/**
 * @desc Activa o desactiva un foro
 * @param mixed $status
 */
function bx_activate_forums($status = 1)
{
    global $xoopsDB, $xoopsSecurity;

    if (!$xoopsSecurity->check()) {
        RMUris::redirect_with_message(__('Session token expired! Try again.', 'bxpress'), 'forums.php', RMMSG_ERROR);
    }

    $forums = RMHttpRequest::post('ids', 'array', null);

    if (!is_array($forums) || empty($forums)) {
        RMUris::redirect_with_message(__('No forum has been selected.', 'bxpress'), 'forums.php', RMMSG_ERROR);
    }

    $sql = 'UPDATE ' . $xoopsDB->prefix('mod_bxpress_forums') . " SET active='$status' WHERE ";
    $sql1 = '';
    foreach ($forums as $k => $v) {
        $sql1 .= '' == $sql1 ? "id_forum='$v' " : "OR id_forum='$v' ";
    }

    $xoopsDB->queryF($sql . $sql1);
    RMUris::redirect_with_message(__('Database updated successfully!', 'bxpress'), 'forums.php', RMMSG_INFO);
}

/**
 * @desc Eliminar un foro
 */
function bx_delete_forums()
{
    global $tpl, $xoopsModule, $xoopsConfig, $xoopsSecurity;

    $ids = rmc_server_var($_REQUEST, 'ids', 0);

    if (!$xoopsSecurity->check()) {
        redirectMsg('forums.php', __('Session token expired!', 'bxpress'), 1);
        die();
    }

    $errors = '';
    foreach ($ids as $id) {
        $forum = new bXForum($id);
        if ($forum->isNew()) {
            $errors .= sprintf(__('Forum with id "%u" does not exists!', 'bxpress'), $id);
            die();
        }

        if (!$forum->delete()) {
            $errors = sprintf(__('Forum "%s" could not be deleted!', 'bxpress'), $forum->name()) . '<br>' . $forum->errors();
        }
    }

    if ('' != $errors) {
        redirectMsg('forums.php', __('Errors ocurred while trying to delete forums:', 'bxpress') . '<br>' . $errors, 1);
    } else {
        redirectMsg('forums.php', __('Forums deleted without errors', 'bxpress'), 0);
    }
}

/**
 * @desc Visualiza lista de usuarios para determinar moderadores
 **/
function bx_moderators()
{
    global $xoopsModule;

    $id = rmc_server_var($_REQUEST, 'id', 0);

    if ($id <= 0) {
        redirectMsg('forums.php', __('No forum ID has been provided!', 'bxpress'), 1);
        die();
    }

    $forum = new bXForum($id);
    if ($forum->isNew()) {
        redirectMsg('forums.php', __('Specified forum does not exists!', 'bxpress'), 1);
    }

    RMTemplate::get()->set_help('http://www.redmexico.com.mx/docs/bxpress-forums/foros/standalone/1/#moderadores');
    xoops_cp_header();

    //Lista de usuarios
    $form = new RMForm(sprintf(__('Forum "%s" Moderators', 'bxpress'), $forum->name()), 'formmdt', 'forums.php');

    $form->addElement(new RMFormUser(__('Moderators', 'bxpress'), 'users', 1, $forum->moderators(), 30), true, 'checked');
    $form->element('users')->setDescription(__('Choose from the list the moderators users', 'bxpress'));

    $buttons = new RMFormButtonGroup();
    $buttons->addButton('sbt', __('Save Moderators', 'bxpress'), 'submit');
    $buttons->addButton('cancel', __('Cancel', 'bxpress'), 'button', 'onclick="window.location.href=\'forums.php\';"');

    $form->addElement($buttons);

    $form->addElement(new RMFormHidden('action', 'savemoderat'));
    $form->addElement(new RMFormHidden('id', $id));

    $form->display();

    xoops_cp_location("<a href='./'>" . $xoopsModule->name() . '</a> &raquo; ' . __('forum Moderators', 'bxpress'));
    xoops_cp_footer();
}

/**
 * @desc Almacena los usuarios moderadores
 **/
function bx_save_moderators()
{
    global $xoopsSecurity;

    if (!$xoopsSecurity->check()) {
        redirectMsg('forums.php', __('Session token expired!', 'bxpress'), 1);
        die();
    }

    foreach ($_POST as $k => $v) {
        $$k = $v;
    }

    //Verificamos si el foro es válido
    if ($id <= 0) {
        redirectMsg('forums.php', __('A forum ID has not been provided!', 'bxpress'), 1);
        die();
    }

    //Comprobamos que el foro exista
    $forum = new bXForum($id);
    if ($forum->isNew()) {
        redirectMsg('forums.php', __('Sepecified forum does not exists!', 'bxpress'), 1);
        die();
    }

    $forum->setModerators($users);
    if ($forum->save()) {
        redirectMsg('forums.php', __('Moderator saved successfully!', 'bxpress'), 0);
    } else {
        redirectMsg('forums.php', __('Moderators could not be saved!', 'bxpress') . '<br>' . $forum->errors(), 1);
    }
}

$action = RMHttpRequest::request('action', 'string', '');

switch ($action) {
    case 'new':
        bx_show_form();
        break;
    case 'edit':
        bx_show_form(1);
        break;
    case 'save':
        bx_save_forum();
        break;
    case 'saveedit':
        bx_save_forum(1);
        break;
    case 'savechanges':
        saveChanges();
        break;
    case 'enable':
        bx_activate_forums(1);
        break;
    case 'disable':
        bx_activate_forums(0);
        break;
    case 'delete':
        bx_delete_forums();
        break;
    case 'moderators':
        bx_moderators();
        break;
    case 'savemoderat':
        bx_save_moderators();
        break;
    default:
        bx_show_forums();
        break;
}
