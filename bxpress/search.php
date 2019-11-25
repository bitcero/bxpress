<?php
// $Id: search.php 861 2011-12-19 02:38:22Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require dirname(dirname(__DIR__)) . '/mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'bxpress-search.tpl';
$xoopsOption['module_subpage']           = 'search';
require __DIR__ . '/header.php';

$myts = MyTextSanitizer::getInstance();
bXFunctions::makeHeader();

$search = TextCleaner::getInstance()->addslashes(rmc_server_var($_REQUEST, 'search', ''));
//$type 0 Todas las palabras
//$type 1 Cualquiera de las palabras
//$type 2 Frase exacta
$type = intval(rmc_server_var($_REQUEST, 'type', 0));
//$topics 1 Temas recientes
//$topics 2 Temas sin respuestas
$themes = intval(rmc_server_var($_REQUEST, 'themes', 0));

$tbl1 = $db->prefix('mod_bxpress_topics');
$tbl2 = $db->prefix('mod_bxpress_posts_text');
$tbl3 = $db->prefix('mod_bxpress_posts');
$tbl4 = $db->prefix('mod_bxpress_forums');

//Barra de navegación

$sql  = "SELECT COUNT(*) FROM $tbl1 a,$tbl2 b, $tbl3 c,$tbl4 d WHERE ";
$sql1 = '';
$sql2 = '';

if (0 == $themes && '' == $search) {
    $sql .= '  a.id_forum=b.id_forum';
} else {
    if ($search) {
        $sql1 = '(';
        $sql2 = ')';
        //Comprobamos el tipo de búsqueda a realizar
        if (0 == $type || 1 == $type) {
            if (0 == $type) {
                $query = 'AND';
            } else {
                $query = 'OR';
            }

            //Separamos la frase en palabras para realizar la búsqueda
            $words = explode(' ', $search);

            foreach ($words as $k) {
                //Verificamos el tamaño de la palabra
                if (mb_strlen($k) <= 2) {
                    continue;
                }

                $sql1 .= ('(' == $sql1 ? '' : $query) . " ( a.title LIKE '%$k%' OR b.post_text LIKE '%$k%') ";
            }
        } else {
            $sql1 .= " (a.title LIKE '%$search%' OR b.post_text LIKE '%$search%') ";
        }
    }

    $sql2 .= ($sql1 ? ' AND ' : '') . ' c.approved=1 AND a.id_topic=c.id_topic AND b.post_id=c.id_post AND d.id_forum=c.id_forum ';

    $sql2 .= ($themes ? (1 == $themes ? ' AND a.date>' . (time() - ($xoopsModuleConfig['time_topics'] * 3600)) : (2 == $themes ? ' AND a.replies=0' : '')) : '');
}

list($num) = $db->fetchRow($db->queryF($sql . $sql1 . $sql2));

$page  = intval(rmc_server_var($_REQUEST, 'pag', 1));
$limit = $xoopsModuleConfig['topicperpage'] > 0 ? $xoopsModuleConfig['topicperpage'] : 15;
if ($page > 0) {
    $page -= 1;
}

$start  = $page * $limit;
$tpages = (int)($num / $limit);
if ($num % $limit > 0) {
    $tpages++;
}

$pactual = $page + 1;
if ($pactual > $tpages) {
    $rest    = $pactual - $tpages;
    $pactual = $pactual - $rest + 1;
    $start   = ($pactual - 1) * $limit;
}

$nav = new RMPageNav($num, $limit, $pactual);
$nav->target_url("search.php?limit=$limit&amp;pag={PAGE_NUM}&amp;search=$search&amp;type=$type&amp;themes=$themes");
$tpl->assign('itemsNavPage', $nav->render(true));

//Fin barra de navegación

$sql1 = '';
$sql2 = '';

if (0 == $themes && '' == $search) {
    $sql  = " SELECT a.*,b.name FROM $tbl1 a,$tbl4 b WHERE a.id_forum=b.id_forum";
    $sql1 .= "  ORDER BY a.sticky DESC, a.date DESC LIMIT $start,$limit ";
} else {
    $sql = "SELECT a.id_topic,a.title,a.views,a.poster_name,a.date,a.replies,a.sticky,a.status,a.last_post,b.post_text,c.*,d.name FROM
    $tbl1 a,$tbl2 b, $tbl3 c,$tbl4 d WHERE ";

    if ($search) {
        $sql1 = '(';
        $sql2 = ')';
        //Comprobamos el tipo de búsqueda a realizar
        if (0 == $type || 1 == $type) {
            if (0 == $type) {
                $query = 'AND';
            } else {
                $query = 'OR';
            }

            //Separamos la frase en palabras para realizar la búsqueda
            $words = explode(' ', $search);

            foreach ($words as $k) {
                //Verificamos el tamaño de la palabra
                if (mb_strlen($k) <= 2) {
                    continue;
                }

                $sql1 .= ('(' == $sql1 ? '' : $query) . " ( a.title LIKE '%$k%' OR b.post_text LIKE '%$k%') ";
            }
        } else {
            $sql1 .= " (a.title LIKE '%$search%' OR b.post_text LIKE '%$search%') ";
        }
    }

    $sql2 .= ($sql1 ? ' AND ' : '') . ' c.approved=1 AND a.id_topic=c.id_topic AND b.post_id=c.id_post AND d.id_forum=c.id_forum ';

    $sql2 .= ($themes ? (1 == $themes ? ' AND a.date>' . (time() - ($xoopsModuleConfig['time_topics'] * 3600)) : (2 == $themes ? ' AND a.replies=0' : '')) : '');
    $sql2 .= "  ORDER BY a.sticky DESC, a.date DESC LIMIT $start,$limit";
}

$result = $db->queryF($sql . $sql1 . $sql2);
while (false !== ($rows = $db->fetchArray($result))) {
    $date      = bXFunctions::formatDate($rows['date']);
    $lastpost  = [];
    $firstpost = [];
    if (!$search && 0 == $themes) {
        $firstpost        = bXFunctions::getFirstId($rows['id_topic']);
        $last             = new bXPost($rows['last_post']);
        $lastpost['date'] = bXFunctions::formatDate($last->date());
        $lastpost['by']   = sprintf(__('By: %s', 'bxpress'), $last->uname());
        $lastpost['id']   = $last->id();
        if ($xoopsUser) {
            $lastpost['new'] = $last->date() > $xoopsUser->getVar('last_login') && (time() - $last->date()) < $xoopsModuleConfig['time_new'];
        } else {
            $lastpost['new'] = (time() - $last->date()) <= $xoopsModuleConfig['time_new'];
        }
    }

    $tpl->append('posts', [
        'id'        => $rows['id_topic'],
        'title'     => $rows['title'],
        'sticky'    => $rows['sticky'],
        'user'      => $rows['poster_name'],
        'replies'   => $rows['replies'],
        'views'     => $rows['views'],
        'closed'    => $rows['status'],
        'date'      => $date,
        'by'        => sprintf(__('By: %s', 'bxpress'), $rows['poster_name']),
        'forum'     => $rows['name'],
        'id_post'   => $rows['id_post'],
        'post_text' => TextCleaner::getInstance()->truncate($rows['post_text'], 100),
        'last'      => $lastpost,
        'firstpost' => $firstpost,
    ]);
}

$tpl->assign('lang_search', __('Search:', 'bxpress'));
$tpl->assign('lang_recenttopics', __('Recent topics', 'bxpress'));
$tpl->assign('lang_alltopics', __('All topics', 'bxpress'));
$tpl->assign('lang_anunswered', __('Unanswered topics', 'bxpress'));
$tpl->assign('themes', $themes);
$tpl->assign('search', $search);
$tpl->assign('type', $type);
$tpl->assign('lang_allwords', __('All words', 'bxpress'));
$tpl->assign('lang_anywords', __('Any word', 'bxpress'));
$tpl->assign('lang_exactphrase', __('Exact phrase', 'bxpress'));
$tpl->assign('lang_topic', __('Topic', 'bxpress'));
$tpl->assign('lang_replies', __('Replies', 'bxpress'));
$tpl->assign('lang_views', __('Views', 'bxpress'));
$tpl->assign('lang_sticky', __('Sticky', 'bxpress'));
$tpl->assign('lang_closed', __('Closed', 'bxpress'));
$tpl->assign('lang_forum', __('Forum', 'bxpress'));
$tpl->assign('lang_date', __('Date', 'bxpress'));
$tpl->assign('lang_last', __('Last post', 'bxpress'));
$tpl->assign('lang_newposts', __('New Posts', 'bxpress'));

include('footer.php');
