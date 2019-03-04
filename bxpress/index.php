<?php
// $Id: index.php 819 2011-12-08 23:43:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require  dirname(dirname(__DIR__)) . '/mainfile.php';

function forums_data($data)
{
    global $xoopsUser;

    if (empty($data)) {
        return;
    }

    $forums = [];

    foreach ($data as $forum) {
        $isModerator = $xoopsUser && ($xoopsUser->isAdmin() || $forum->isModerator($xoopsUser->uid()));
        if (!$forum->active && !$isModerator) {
            continue;
        }

        $last = new bXPost($forum->lastPostId());
        $lastpost = [];
        if (!$last->isNew()) {
            if (!isset($posters[$last->uid])) {
                $posters[$last->uid] = new RMUser($last->uid);
            }

            $user = $posters[$last->uid];

            $lastpost['date'] = bXFunctions::formatDate($last->date());
            $lastpost['by'] = sprintf(__('by %s', 'bxpress'), $last->uname());
            $lastpost['id'] = $last->id();
            $lastpost['topic'] = $last->topic();
            $lastpost['user'] = [
                'uname' => $user->uname,
                'name' => '' != $user->name ? $user->name : $user->uname,
                'avatar' => $user ? RMEvents::get()->run_event('rmcommon.get.avatar', $user->getVar('email'), 50) : '',
            ];

            if ($xoopsUser) {
                $lastpost['new'] = $last->date() > $xoopsUser->getVar('last_login') && (time() - $last->date()) < $xoopsModuleConfig['time_new'];
            } else {
                $lastpost['new'] = (time() - $last->date()) <= $xoopsModuleConfig['time_new'];
            }
        }

        $category = new bXCategory($forum->cat);

        $forums[] = [
            'id' => $forum->id(),
            'name' => $forum->name(),
            'desc' => $forum->description(),
            'topics' => $forum->topics(),
            'posts' => $forum->posts(),
            'link' => $forum->makeLink(),
            'last' => $lastpost,
            'image' => $forum->image,
            'active' => $forum->active,
            'category' => [
                'title' => $category->title,
            ],
        ];
    }

    return $forums;
}

if ($xoopsModuleConfig['showcats']) {
    /**
     * Cargamos las categorías y los foros ordenados por categorías
     */
    $GLOBALS['xoopsOption']['template_main'] = 'bxpress-index-categories.tpl';
    $xoopsOption['module_subpage'] = 'index';
    require __DIR__ . '/header.php';

    $categos = bXCategoryHandler::getObjects(1);

    foreach ($categos as $catego) {
        if (!$catego->groupAllowed($xoopsUser ? $xoopsUser->getGroups() : [0, XOOPS_GROUP_ANONYMOUS])) {
            continue;
        }

        $forums = bXForumHandler::getForums($catego->id(), $xoopsModuleConfig['show_inactive'] ? -1 : 1, true);
        $tpl->append('categos', ['id' => $catego->id(), 'title' => $catego->title(), 'forums' => forums_data($forums)]);
    }
} else {
    /**
     * Cargamos solo los foros
     */
    $GLOBALS['xoopsOption']['template_main'] = 'bxpress-index-forums.tpl';
    $xoopsOption['module_subpage'] = 'index';
    require __DIR__ . '/header.php';

    $fHand = new bXForumHandler();
    $forums = $fHand->getForums(0, $xoopsModuleConfig['show_inactive'] ? -1 : 1, true);
    $posters = [];

    $tpl->assign('forums', forums_data($forums));
}

$user = bXFunctions::getLastUser();

if ($user) {
    $tpl->assign('user', ['id' => $user->uid(), 'uname' => $user->uname()]);
}

unset($user);

// Usuarios Conectados
$tpl->assign('register_num', bXFunctions::getOnlineCount(1));
$tpl->assign('anonymous_num', bXFunctions::getOnlineCount(0));
$tpl->assign('total_users', bXFunctions::totalUsers());
$tpl->assign('total_topics', bXFunctions::totalTopics());
$tpl->assign('total_posts', bXFunctions::totalPosts());

$tpl->assign('lang_forum', __('Forum', 'bxpress'));
$tpl->assign('lang_topics', __('Topics', 'bxpress'));
$tpl->assign('lang_posts', __('Posts', 'bxpress'));
$tpl->assign('lang_lastpost', __('Last Post', 'bxpress'));
$tpl->assign('lang_lastuser', __('Last registered user:', 'bxpress'));
$tpl->assign('lang_regnum', __('Registered users conected:', 'bxpress'));
$tpl->assign('lang_annum', __('Anonymous users conected:', 'bxpress'));
$tpl->assign('lang_totalusers', __('Registered users:', 'bxpress'));
$tpl->assign('lang_totaltopics', __('Total topics:', 'bxpress'));
$tpl->assign('lang_totalposts', __('Total posts:', 'bxpress'));
$tpl->assign('lang_ourforums', __('Our Forums', 'bxpress'));
$tpl->assign('lang_foot', __('%s posts in %s topics. %s. Last post by %s %s', 'bxpress'));

$tpl->assign('xoops_pagetitle', $xoopsModuleConfig['forum_title']);

bXFunctions::makeHeader();
bXFunctions::loadAnnouncements(0);

require __DIR__ . '/footer.php';
