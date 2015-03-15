<?php
// $Id: forum.php 890 2011-12-30 08:41:00Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION','forum');
include '../../mainfile.php';
$xoopsOption['template_main'] = "bxpress-forum.tpl";
$xoopsOption['module_subpage'] = "forums";
include 'header.php';
$myts =& MyTextSanitizer::getInstance();

$id = isset($_GET['id']) ? $myts->addSlashes($_GET['id']) : '';
if ($id==''){
    redirect_header(BB_URL, 2, __('No forum ID has been specified','bxpress'));
    die();
}

$forum = new bXForum($id);
if ($forum->isNew()){
    redirect_header(BB_URL, 2, __('Specified forum does not exists!','bxpress'));
    die();
}

/**
 * Check if module is inactive
 */
$isModerator = $xoopsUser && ( $xoopsUser->isAdmin() || $forum->isModerator( $xoopsUser->uid() ) );
if ( !$forum->active && !$isModerator )
    RMUris::redirect_with_message(
        __('This forum is closed and you don\'t have permissions to view it', 'bxpress'),
        BX_URL, RMMSG_WARN
    );

/**
* Comprobamos que el usuario actual tenga permisos
* de acceso al foro
*/
if (!$forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : array(0, XOOPS_GROUP_ANONYMOUS), BXPRESS_PERM_VIEW) && !$xoopsUser->isAdmin()){
    redirect_header(BB_URL, 2, __('You are not allowed to view this forum!','bxpress'));
    die();
}

/**
* Cargamos los temas
*/
$tbl1 = $db->prefix("mod_bxpress_topics");
$tbl2 = $db->prefix("mod_bxpress_forumtopics");

$sql = "SELECT COUNT(*) FROM $tbl1 WHERE id_forum='".$forum->id()."' AND approved='1'";
list($num)=$db->fetchRow($db->queryF($sql));
    
$page = isset($_REQUEST['pag']) ? $_REQUEST['pag'] : '';
$limit = $xoopsModuleConfig['topicperpage'] > 0 ? $xoopsModuleConfig['topicperpage'] : 15;
if ($page > 0){ $page -= 1; }
        
$start = $page * $limit;
$tpages = (int)($num / $limit);
if($num % $limit > 0) $tpages++;
    
$pactual = $page + 1;
if ($pactual>$tpages){
    $rest = $pactual - $tpages;
    $pactual = $pactual - $rest + 1;
    $start = ($pactual - 1) * $limit;
}
    
if ($tpages > 0) {
    $nav = new RMPageNav($num, $limit, $pactual);
    $nav->target_url($forum->permalink().'&amp;pag={PAGE_NUM}');
    $tpl->assign('itemsNavPage', $nav->render(false));
}

$sql = str_replace("COUNT(*)", '*', $sql);
$sql .= " ORDER BY sticky DESC,";
$sql .=$xoopsModuleConfig['order_post'] ? " last_post " : " date ";
$sql .=" DESC LIMIT $start,$limit";
$result = $db->query($sql);

/**
 * Posters cache
 */
$posters = array();

while ($row = $db->fetchArray($result)){
    $topic = new bXTopic();
    $topic->assignVars($row);
    $last = new bXPost($topic->lastPost());

    if ( !isset( $posters[ $topic->poster ] ) )
        $posters[$topic->poster] = new RMUser( $topic->poster );

    if ( !isset( $posters[ $last->uid ] ) )
        $posters[$last->uid] = new RMUser( $last->uid );

    $poster = $posters[$topic->poster];
    $last_poster = $posters[$last->uid];

    $lastpost = array();
    if (!$last->isNew()){
    	$lastpost['date']   = formatTimeStamp( $last->date(), __('M d, Y') );
    	$lastpost['time']   = $last->date();
    	$lastpost['id']     = $last->id();
        $lastpost['poster'] = array(
            'uid'       => $last->uid,
            'uname'     => $last->poster_name,
            'name'      => $last_poster->name != '' ? $last_poster->name : $last_poster->uname,
            'email'     => $last_poster->email,
            'avatar'    => RMEvents::get()->run_event( 'rmcommon.get.avatar', $last_poster->getVar('email'), 50 ),
            'link'      => XOOPS_URL . '/userinfo.php?uid=' . $last_poster->uid
        );

    	if ($xoopsUser){
    		$lastpost['new'] = $last->date()>$xoopsUser->getVar('last_login') && (time()-$last->date()) < $xoopsModuleConfig['time_new'];
    	} else {
    		$lastpost['new'] = (time()-$last->date())<=$xoopsModuleConfig['time_new'];
		}
	}
	$tpages = ceil($topic->replies()/$xoopsModuleConfig['perpage']);
	if ($tpages>1){
		$pages = bXFunctions::paginateIndex($tpages);
	} else {
		$pages = null;
	}
    $tpl->append('topics', array(
        'id'        => $topic->id(),
        'title'     => $topic->title(),
        'replies'   => $topic->replies(),
        'views'     => $topic->views(),
        'by'        => sprintf(__('By: %s','bxpress'), $topic->posterName()),
        'last'      => $lastpost,
        'popular'   => ($topic->replies()>=$forum->hotThreshold()),
        'sticky'    => $topic->sticky(),
        'pages'     => $pages,
        'tpages'    => $tpages,
        'closed'    => $topic->status(),
        'poster'    => array(
            'uid'       => $topic->poster,
            'uname'     => $poster->uname,
            'name'      => $poster->name,
            'email'     => $poster->email,
            'avatar'    => RMEvents::get()->run_event( 'rmcommon.get.avatar', $poster->getVar('email'), 100 ),
            'type'      => $poster->isAdmin() ? 'admin' : ( $forum->isModerator( $topic->poster ) ? 'moderator' : 'user' )
        )
    ));
}

// Datos del Foro
$tpl->assign('forum', array('id'=>$forum->id(), 'title'=>$forum->name(),'moderator'=>$xoopsUser ? $forum->isModerator($xoopsUser->uid()) || $xoopsUser->isAdmin() : false));

$tpl->assign('lang_pages', __('Pages:','bxpress'));
$tpl->assign('lang_topic', __('Topics','bxpress'));
$tpl->assign('lang_replies', __('Replies','bxpress'));
$tpl->assign('lang_views', __('Views','bxpress'));
$tpl->assign('lang_lastpost', __('Last Post','bxpress'));
$tpl->assign('lang_nonew', __('No new posts','bxpress'));
$tpl->assign('lang_withnew', __('New posts','bxpress'));
$tpl->assign('lang_hotnonew', __('No hot topics','bxpress'));
$tpl->assign('lang_hotnew', __('New hot topics','bxpress'));
$tpl->assign('lang_sticky', __('Sticky','bxpress'));
$tpl->assign('lang_closed', __('Closed Topic','bxpress'));
if ($forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, 'topic')){
	$tpl->assign('lang_newtopic', __('New Topic'));
	$tpl->assign('can_topic', 1);
}
$tpl->assign('lang_newposts', __('New Posts','bxpress'));

bXFunctions::makeHeader();
$tpl->assign('xoops_pagetitle', $forum->name().' &raquo; '.$xoopsModuleConfig['forum_title']);
if ($xoopsUser){
	if ($forum->isModerator($xoopsUser->uid()) || $xoopsUser->isAdmin() ){
		$tpl->assign('lang_moderate', __('Moderate','bxpress'));
	}
}
$tpl->assign('lang_goto', __('Go to:','bxpress'));
$tpl->assign('lang_go', __('Go!','bxpress'));
$tpl->assign('lang_updated', __('Updated on %s.','bxpress'));
$tpl->assign('lang_lastreply', __('Last reply','bxpress'));
$tpl->assign('lang_lastreply_by', __('%s by %s','bxpress'));
$tpl->assign('lang_noreplies', __('No replies yet','bxpress'));
$tpl->assign('lang_admin', __('Admin','bxpress'));
$tpl->assign('lang_moderator', __('Mod','bxpress'));
$tpl->assign('lang_user', __('User','bxpress'));

bXFunctions::forumList();
bXFunctions::loadAnnouncements(1, $forum->id());

include 'footer.php';
