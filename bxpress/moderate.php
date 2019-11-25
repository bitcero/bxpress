<?php
// $Id: moderate.php 857 2011-12-14 10:52:30Z mambax7@gmail.com $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require  dirname(dirname(__DIR__)) . '/mainfile.php';

$id = RMHttpRequest::request('id', 'integer', 0);

if ($id <= 0) {
    redirect_header('./', 2, __('Please, specify the forum you want to moderate!', 'bxpress'));
    die();
}

$forum = new bXForum($id);
if ($forum->isNew()) {
    redirect_header('./', 2, __('Specified forum doesn\'t exists!', 'bxpress'));
    die();
}

// Comprobamos los permisos de moderador
if (!$xoopsUser || (!$forum->isModerator($xoopsUser->uid()) && !$xoopsUser->isAdmin())) {
    redirect_header('forum.php?id=' . $id, 2, __('Sorry, you don\'t have permission to do this action!', 'bxpress'));
    die();
}

/**
 * @desc Muestra todas las opciones configurables
 */
function showItemsAndOptions()
{
    global $xoopsUser, $db, $xoopsOption, $tpl, $xoopsModule, $xoopsConfig, $xoopsSecurity;
    global $xoopsModuleConfig, $forum;

    $GLOBALS['xoopsOption']['template_main'] = 'bxpress-moderate.tpl';
    $xoopsOption['module_subpage'] = 'moderate';
    require __DIR__ . '/header.php';

    /**
     * Cargamos los temas
     */
    $tbl1 = $db->prefix('mod_bxpress_topics');
    $tbl2 = $db->prefix('mod_bxpress_forumtopics');

    $sql = "SELECT COUNT(*) FROM $tbl1 WHERE id_forum='" . $forum->id() . "' ";
    list($num) = $db->fetchRow($db->queryF($sql));

    $page = isset($_REQUEST['pag']) ? $_REQUEST['pag'] : '';
    $limit = $xoopsModuleConfig['topicperpage'] > 0 ? $xoopsModuleConfig['topicperpage'] : 15;
    if ($page > 0) {
        $page -= 1;
    }

    $start = $page * $limit;
    $tpages = (int)($num / $limit);
    if ($num % $limit > 0) {
        $tpages++;
    }

    $pactual = $page + 1;
    if ($pactual > $tpages) {
        $rest = $pactual - $tpages;
        $pactual = $pactual - $rest + 1;
        $start = ($pactual - 1) * $limit;
    }

    if ($tpages > 0) {
        $nav = new RMPageNav($num, $limit, $pactual);
        $nav->target_url('moderate.php?id=' . $forum->id() . '&amp;pag={PAGE_NUM}');
        $tpl->assign('itemsNavPage', $nav->render(false));
    }

    $sql = str_replace('COUNT(*)', '*', $sql);
    $sql .= " ORDER BY sticky DESC, date DESC LIMIT $start,$limit";
    $result = $db->query($sql);

    while (false !== ($row = $db->fetchArray($result))) {
        $topic = new bXTopic();
        $topic->assignVars($row);
        $last = new bXPost($topic->lastPost());
        $lastpost = [];
        if (!$last->isNew()) {
            $lastpost['date'] = bXFunctions::formatDate($last->date());
            $lastpost['by'] = sprintf(__('By: %s', 'bxpress'), $last->uname());
            $lastpost['id'] = $last->id();
            if ($xoopsUser) {
                $lastpost['new'] = $last->date() > $xoopsUser->getVar('last_login') && (time() - $last->date()) < $xoopsModuleConfig['time_new'];
            } else {
                $lastpost['new'] = (time() - $last->date()) <= $xoopsModuleConfig['time_new'];
            }
        }
        $tpages = ceil($topic->replies() / $xoopsModuleConfig['perpage']);
        if ($tpages > 1) {
            $pages = bXFunctions::paginateIndex($tpages);
        } else {
            $pages = null;
        }
        $tpl->append('topics', [
            'id' => $topic->id(),
            'title' => $topic->title(),
            'replies' => $topic->replies(),
            'views' => $topic->views(),
            'by' => sprintf(__('By: %s', 'bxpress'), $topic->posterName()),
            'last' => $lastpost,
            'popular' => ($topic->replies() >= $forum->hotThreshold()),
            'sticky' => $topic->sticky(),
            'pages' => $pages,
            'tpages' => $tpages,
            'approved' => $topic->approved(),
            'closed' => $topic->status(),
        ]);
    }

    $tpl->assign('forum', ['id' => $forum->id(), 'title' => $forum->name()]);
    $tpl->assign('lang_topic', __('Topic', 'bxpress'));
    $tpl->assign('lang_replies', __('Replies', 'bxpress'));
    $tpl->assign('lang_views', __('Views', 'bxpress'));
    $tpl->assign('lang_lastpost', __('Last Post', 'bxpress'));
    $tpl->assign('lang_sticky', __('Sticky', 'bxpress'));
    $tpl->assign('lang_moderating', __('Moderating Forum', 'bxpress'));
    $tpl->assign('lang_pages', __('Pages', 'bxpress'));
    $tpl->assign('lang_move', __('Move', 'bxpress'));
    $tpl->assign('lang_open', __('Unlock', 'bxpress'));
    $tpl->assign('lang_close', __('Lock', 'bxpress'));
    $tpl->assign('lang_dosticky', __('Sticky', 'bxpress'));
    $tpl->assign('lang_dounsticky', __('Unsticky', 'bxpress'));
    $tpl->assign('lang_approved', __('Approved', 'bxpress'));
    $tpl->assign('lang_app', __('Approve', 'bxpress'));
    $tpl->assign('lang_noapp', __('Unapprove', 'bxpress'));
    $tpl->assign('lang_owner', __('Owner', 'bxpress'));
    $tpl->assign('lang_delete', __('Delete', 'bxpress'));
    $tpl->assign('lang_confirm', __('Do you really want to delete selected topics?', 'bxpress'));
    $tpl->assign('token_input', $xoopsSecurity->getTokenHTML());

    bXFunctions::makeHeader();

    RMTemplate::getInstance()->add_style('style.css', 'bxpress');

    require __DIR__ . '/footer.php';
}

/**
 * @desc Mover temas de un foro a otro
 */
function moveTopics()
{
    global $db, $xoopsModuleConfig, $xoopsSecurity, $forum, $xoopsUser, $xoopsOption, $xoopsConfig;

    $topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;
    $ok = isset($_POST['ok']) ? $_POST['ok'] : 0;
    $moveforum = rmc_server_var($_POST, 'moveforum', 0);

    if (empty($topics) || (is_array($topics) && empty($topics))) {
        redirect_header('moderate.php?id=' . $moveforum, 2, __('Select at least a topic to moderate!', 'bxpress'));
        die();
    }

    $topics = !is_array($topics) ? [$topics] : $topics;

    if ($ok) {
        if (!$xoopsSecurity->check()) {
            redirect_header('moderate.php?id=' . $moveforum, 2, __('Session token expired!', 'bxpress'));
            die();
        }

        if ($moveforum <= 0) {
            redirect_header('moderate.php?id=' . $forum->id(), 2, __('Please select the target forum', 'bxpress'));
            die();
        }

        $mf = new bXForum($moveforum);
        if ($mf->isNew()) {
            redirect_header('moderate.php?id=' . $forum->id(), 2, __('Specified forum does not exists!', 'bxpress'));
            die();
        }

        $lastpost = false;
        foreach ($topics as $k) {
            $topic = new bXTopic($k);
            if ($topic->forum() != $forum->id()) {
                continue;
            }

            //Verificamos si el tema contiene el último mensaje del foro
            if (!$lastpost && array_key_exists($forum->lastPostId(), $topic->getPosts(0))) {
                $lastpost = true;
            }

            $topic->setForum($moveforum);
            if ($topic->save()) {
                //Decrementa el número de temas
                $forum->setTopics(($forum->topics() - 1 > 0) ? $forum->topics() - 1 : 0);
                $forum->setPosts(($forum->posts() - ($topic->replies() + 1) > 0) ? $forum->posts() - ($topic->replies() + 1) : 0);
                $forum->save();

                $mf->setPosts($mf->posts() + ($topic->replies() + 1));
                $mf->addTopic();
                $mf->save();

                //Cambiamos el foro de los mensajes del tema
                if ($topic->getPosts()) {
                    foreach ($topic->getPosts() as $k => $v) {
                        $v->setForum($moveforum);
                        $v->save();
                    }
                }
            }
        }

        //Actualizamos el último mensaje del foro
        if ($lastpost) {
            $post = $forum->getLastPost();
            $forum->setPostId($post);
            $forum->save();
        }

        //Actualizamos el último mensaje del foro al que fue movido el tema
        $post = $mf->getLastPost();
        $post ? $mf->setPostId($post) : '';
        $mf->save();

        redirect_header('moderate.php?id=' . $forum->id(), 1, __('Topics has been relocated!', 'bxpress'));
        die();
    }
    global $xoopsTpl;
    $tpl = $xoopsTpl;
    $GLOBALS['xoopsOption']['template_main'] = 'bxpress-moderate-forms.tpl';
    $xoopsOption['module_subpage'] = 'moderate';
    require __DIR__ . '/header.php';

    bXFunctions::makeHeader();
    $form = new RMForm(__('Move Topics', 'bxpress'), 'frmMove', 'moderate.php');
    $form->addElement(new RMFormHidden('id', $forum->id()));
    $form->addElement(new RMFormHidden('op', 'move'));
    $form->addElement(new RMFormHidden('ok', '1'));
    $i = 0;
    foreach ($topics as $k) {
        $form->addElement(new RMFormHidden('topics[' . $i . ']', $k));
        ++$i;
    }
    $form->addElement(new RMFormSubTitle('&nbsp', 1, ''));
    $form->addElement(new RMFormSubTitle(__('Select the forum where you wish to move selected topics', 'bxpress'), 1, 'even'));
    $ele = new RMFormSelect(__('Forum', 'bxpress'), 'moveforum');
    $ele->addOption(0, '', 1);

    $tbl1 = $db->prefix('mod_bxpress_categories');
    $tbl2 = $db->prefix('mod_bxpress_forums');
    $sql = "SELECT b.*, a.title FROM $tbl1 a, $tbl2 b WHERE b.cat=a.id_cat AND b.active='1' AND id_forum<>" . $forum->id() . ' ORDER BY a.order, b.order';
    $result = $db->query($sql);
    $categories = [];
    while (false !== ($row = $db->fetchArray($result))) {
        $cforum = ['id' => $row['id_forum'], 'name' => $row['name']];
        if (isset($categores[$row['cat']])) {
            $categories[$row['cat']]['forums'][] = $cforum;
        } else {
            $categories[$row['cat']]['title'] = $row['title'];
            $categories[$row['cat']]['forums'][] = $cforum;
        }
    }

    foreach ($categories as $cat) {
        $ele->addOption(0, $cat['title'], 0, true, 'color: #000; font-weight: bold; font-style: italic; border-bottom: 1px solid #c8c8c8;');
        foreach ($cat['forums'] as $cforum) {
            $ele->addOption($cforum['id'], $cforum['name'], 0, false, 'padding-left: 10px;');
        }
    }
    $form->addElement($ele, true, 'noselect:0');
    $ele = new RMFormButtonGroup();
    $ele->addButton('sbt', __('Move Topics Now!', 'bxpress'), 'submit');
    $ele->addButton('cancel', __('Cancel', 'bxpress'), 'button', 'onclick="history.go(-1);"');
    $form->addElement($ele);
    $tpl->assign('moderate_form', $form->render());

    require __DIR__ . '/footer.php';
}

/**
 * Change the owner for a list of topics
 */
function changeOwner()
{
    global $xoopsSecurity, $db, $xoopsUser, $xoopsTpl, $forum, $xoopsConfig, $xoopsOption;

    if (!$xoopsSecurity->check()) {
        RMUris::redirect_with_message(
            __('Session token is not valid!', 'bxpress'),
            'moderate.php?id=' . $forum->id(),
            RMMSG_ERROR
        );
    }

    $topics = RMHttpRequest::post('topics', 'array', []);

    if (empty($topics)) {
        RMUris::redirect_with_message(
            __('Select one topic at least!', 'bxpress'),
            'moderate.php?id=' . $forum->id(),
            RMMSG_WARN
        );
    }

    $tpl = $xoopsTpl;
    $GLOBALS['xoopsOption']['template_main'] = 'bxpress-moderate-forms.tpl';
    $xoopsOption['module_subpage'] = 'moderate';
    require __DIR__ . '/header.php';

    bXFunctions::makeHeader();
    $form = new RMForm(__('Change topic owner', 'bxpress'), 'frmOwner', 'moderate.php');
    $form->fieldClass = '';
    $form->addElement(new RMFormHidden('id', $forum->id()));
    $form->addElement(new RMFormHidden('op', 'change-owner'));

    $i = 0;
    foreach ($topics as $k) {
        $form->addElement(new RMFormHidden('topics[' . $i . ']', $k));
        ++$i;
    }

    $form->addElement(new RMFormSubTitle([
        'caption' => __('Select the user that will be assigned as owner for all selected topics', 'bxpress'),
        'level' => 4,
    ]));
    $form->addElement(new RMFormUser(__('User', 'bxpress'), 'owner'));

    $form->addElement(new RMFormYesNo([
        'caption' => __('Change first post owner too?', 'bxpress'),
        'value' => 'yes',
        'name' => 'first',
    ]));

    $ele = new RMFormButtonGroup();
    $ele->addButton('sbt', __('Change Owner!', 'bxpress'), 'submit');
    $ele->addButton('cancel', __('Cancel', 'bxpress'), 'button', 'onclick="history.go(-1);"');
    $form->addElement($ele);
    $tpl->assign('moderate_form', $form->render());

    require __DIR__ . '/footer.php';
}

function changeOwnerNow()
{
    global $xoopsSecurity, $forum, $xoopsDB;

    if (!$xoopsSecurity->check()) {
        RMUris::redirect_with_message(
            __('Session token is not valid!', 'bxpress'),
            'moderate.php?id=' . $forum->id(),
            RMMSG_ERROR
        );
    }

    $topics = RMHttpRequest::post('topics', 'array', []);

    if (empty($topics)) {
        RMUris::redirect_with_message(
            __('Select one topic at least!', 'bxpress'),
            'moderate.php?id=' . $forum->id(),
            RMMSG_ERROR
        );
    }

    $owner = RMHttpRequest::post('owner', 'integer', 0);

    if ($owner <= 0) {
        RMUris::redirect_with_message(
            __('You must select a user to set as owner of these topics!', 'bxpress'),
            'moderate.php?id=' . $forum->id(),
            RMMSG_ERROR
        );
    }

    $newOwner = new RMUser($owner);
    if ($newOwner->isNew()) {
        RMUris::redirect_with_message(
            __('Selected user does not exists!', 'bxpress'),
            'moderate.php?id=' . $forum->id(),
            RMMSG_ERROR
        );
    }

    $first = RMHttpRequest::post('first', 'integer', 1);

    $errors = [];

    // Modify all topics
    foreach ($topics as $topicId) {
        $topic = new bXTopic($topicId);

        if ($topic->isNew()) {
            $errors[] = sprintf(__('Topic width ID %u does not exists', 'bxpress'), $topicId);
            continue;
        }

        $topic->setPoster($owner);
        $topic->setPosterName($newOwner->uname);

        if (!$topic->save()) {
            $errors[] = sprintf(__('Errors occurs while trying to save topic %s: %s', 'bxpress'), $topic->title(), $topic->getErrors());
        }

        if (!$first) {
            continue;
        }

        $result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('mod_bxpress_posts') . " WHERE id_topic='" . $topic->id() . "' ORDER BY id_post ASC LIMIT 0, 1");
        if ($xoopsDB->getRowsNum($result) <= 0) {
            continue;
        }

        $row = $xoopsDB->fetchArray($result);
        $firstPost = new bXPost();
        $firstPost->assignVars($row);
        $firstPost->uid = $newOwner->id();
        $firstPost->poster_name = $newOwner->uname;

        if (!$firstPost->save()) {
            $errors[] = sprintf(__('First post from topic %s could not be modified', 'bxpress'), $topic->title());
        }
    }

    if (empty($errors)) {
        RMUris::redirect_with_message(
            __('Topics modified successfully!', 'bxpress'),
            'moderate.php?id=' . $forum->id(),
            RMMSG_SUCCESS
        );
    } else {
        RMUris::redirect_with_message(
            sprintf(__('Some errors occurs while trying to modified topics: %s', 'bxpress'), implode('<br>', $errors)),
            'moderate.php?id=' . $forum->id(),
            RMMSG_WARN
        );
    }
}

/**
 * @desc Cerrar o abrir un tema
 * @param mixed $close
 */
function closeTopic($close)
{
    global $xoopsSecurity, $forum, $xoopsUser;

    $topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;

    if (empty($topics) || (is_array($topics) && empty($topics))) {
        redirect_header('moderate.php?id=' . $forum->id(), 2, __('Select at least one topic to moderate!', 'bxpress'));
        die();
    }

    $topics = !is_array($topics) ? [$topics] : $topics;

    foreach ($topics as $k) {
        $topic = new bXTopic($k);
        if ($topic->isNew()) {
            continue;
        }

        $topic->setStatus($close);
        $topic->save();
    }

    redirect_header('moderate.php?id=' . $forum->id(), 1, __('Action completed!', 'bxpress'));
}

/**
 * @desc Cerrar o abrir un tema
 * @param mixed $sticky
 */
function stickyTopic($sticky)
{
    global $forum, $xoopsSecurity;

    if (!$xoopsSecurity->check()) {
        redirect_header('moderate.php?id=' . $forum->id(), 2, __('Session token expired!', 'bxpress'));
        die();
    }

    $topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;

    if (empty($topics) || (is_array($topics) && empty($topics))) {
        redirect_header('moderate.php?id=' . $forum->id(), 2, __('Select at least one topic to set as sticky!', 'bxpress'));
        die();
    }

    $topics = !is_array($topics) ? [$topics] : $topics;

    foreach ($topics as $k) {
        $topic = new bXTopic($k);
        if ($topic->isNew()) {
            continue;
        }

        $topic->setSticky($sticky);
        $topic->save();
    }

    redirect_header('moderate.php?id=' . $forum->id(), 1, __('Action completed!', 'bxpress'));
}

/**
 * @desc Eliminar temas
 */
function deleteTopics()
{
    global $db, $xoopsModuleConfig, $bxpress, $forum, $xoopsUser, $xoopsSecurity;

    $ok = isset($_POST['ok']) ? $_POST['ok'] : 0;
    $topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;

    if (empty($topics) || (is_array($topics) && empty($topics))) {
        redirect_header('moderate.php?id=' . $forum->id(), 2, __('Select at least one topic to delete!', 'bxpress'));
        die();
    }

    $topics = !is_array($topics) ? [$topics] : $topics;

    $lastpost = false;

    if (!$xoopsSecurity->check()) {
        redirect_header('moderate.php?id=' . $forum->id(), 2, __('Session token expired!', 'bxpress'));
        die();
    }

    foreach ($topics as $k) {
        $topic = new bXTopic($k);
        if ($topic->isNew()) {
            continue;
        }
        if ($topic->forum() != $forum->id()) {
            continue;
        }

        //Verificamos si el tema contiene el último mensaje del foro
        if (!$lastpost && array_key_exists($forum->lastPostId(), $topic->getPosts(0))) {
            $lastpost = true;
        }

        $topic->delete();
    }

    //Actualizamos el último mensaje del foro
    if ($lastpost) {
        $forum = new bXForum($forum->id());

        $post = $forum->getLastPost();
        $forum->setPostId($post);
        $forum->save();
    }

    redirect_header('moderate.php?id=' . $forum->id(), 1, __('Action completed!', 'bxpress'));
}

/**
 * @desc Aprueba o no un tema
 * @param mixed $app
 **/
function approvedTopics($app = 0)
{
    global $forum, $xoopsSecurity;

    $topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;

    if (empty($topics) || (is_array($topics) && empty($topics))) {
        redirect_header('moderate.php?id=' . $forum->id(), 2, __('Select at least one topic to moderate', 'bxpress'));
        die();
    }

    $topics = !is_array($topics) ? [$topics] : $topics;

    if (!$xoopsSecurity->check()) {
        redirect_header('moderate.php?id=' . $forum->id(), 2, __('Session token expired!', 'bxpress'));
        die();
    }

    $lastpost = false;
    foreach ($topics as $k) {
        $topic = new bXTopic($k);
        if ($topic->isNew()) {
            continue;
        }

        $lastapp = $topic->approved();

        $topic->setApproved($app);
        $topic->save();
    }

    //Actualizamos el último mensaje del foro
    $post = $forum->getLastPost();
    $forum->setPostId($post);
    $forum->save();

    redirect_header('moderate.php?id=' . $forum->id(), 1, __('Action completed!', 'bxpress'));
}

/**
 * @desc Aprueba o no un mensaje editado
 * @param mixed $app
 **/
function approvedPosts($app = 0)
{
    global $xoopsUser, $xoopsSecurity;

    $posts = isset($_REQUEST['posts']) ? intval($_REQUEST['posts']) : 0;

    //Verifica que el mensaje sea válido
    if ($posts <= 0) {
        redirect_header('./topic.php?id=' . $posts, 1, __('Topic not valid!', 'bxpress'));
        die();
    }

    //Comprueba que el mensaje exista
    $post = new bXPost($posts);
    if ($post->isNew()) {
        redirect_header('./topic.php?id=' . $posts, 1, __('Post doesn\'t exists!', 'bxpress'));
        die();
    }

    //Comprueba si usuario es moderador del foro
    $forum = new bXForum($post->forum());
    if (!$forum->isModerator($xoopsUser->uid()) || !$xoopsUser->isAdmin()) {
        redirect_header('./topic.php?id=' . $posts, 1, __('You don\'t have permission to do this action!', 'bxpress'));
        die();
    }

    if (!$xoopsSecurity->check()) {
        redirect_header('./topic.php?id=' . $posts, 2, __('Session token expired!', 'bxpress'));
        die();
    }

    $post->setApproved($app);
    if ($post->editText()) {
        $post->setText($post->editText());
    }
    $post->setEditText('');
    $post->save();

    redirect_header('./topic.php?id=' . $post->topic(), 1, __('Operation completed!', 'bxpress'));
}

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';

switch ($op) {
    case 'move':
        moveTopics();
        break;
    case 'close':
        closeTopic(1);
        break;
    case 'open':
        closeTopic(0);
        break;
    case 'sticky':
        stickyTopic(1);
        break;
    case 'unsticky':
        stickyTopic(0);
        break;
    case 'delete':
        deleteTopics();
        break;
    case 'approved':
        approvedTopics(1);
        break;
    case 'noapproved':
        approvedTopics();
        break;
    case 'approvedpost':
        approvedPosts(1);
        break;
    case 'noapprovedpost':
        approvedPosts();
        break;
    case 'owner':
        changeOwner();
        break;
    case 'change-owner':
        changeOwnerNow();
        break;
    default:
        showItemsAndOptions();
        break;
}
