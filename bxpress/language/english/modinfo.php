<?php
// $Id: modinfo.php 1034 2012-09-06 02:30:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress
// A simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!function_exists("__")){
    function __($text, $d){
        return $text;
    }
}

if(function_exists("load_mod_locale")) load_mod_locale("bxpress");

# Opciones de configuración del módulo
define('_MI_BX_CNFTITLE',__('Forum Title','bxpress'));
define('_MI_BX_CNFTITLE_DESC', __('This title will be show in the Home page and in special sections.','bxpress'));
define('_MI_BX_URLMODE', __('Enable URLs rewriting','bxpress'));
define('_MI_BX_URLMODED', __('By enabling this option, bXpress will be capable to use friendly URLs.','bxpress'));
define('_MI_BX_BASEPATH', __('Base path for URL rewriting','bxpress'));

define('_MI_RMF_CNFSTOREFILES', __('Directory for the file storage','bxpress'));
define('_MI_RMF_CNFSTOREFILES_DESC', __('In this folder will be stored the posts attachments.','bxpress'));

define('_MI_BX_CNFMAXFILESIZE','Maximum allowed size for the sent files (en KB)');
define('_MI_BX_CNFMAXFILESIZE_DESC','The files sent in the forums will be limited to this size, bigger file sizes will be ignored.');

define('_MI_BX_SHOWCATS', __('Show Categories in the Home Page','bxpress'));
define('_MI_BX_SHOWCATS_DESC', __('If this option is enable the forums will be ordered by categories in the Home Page.','bxpress'));

define('_MI_BX_TOPICLIMIT', __('Topics Number per Page','bxpress'));
define('_MI_BX_POSTLIMIT', __('Message Number per Page','bxpress'));

define('_MI_BX_SEARCHANON', __('Enable search for anonymous users','bxpress'));

define('_MI_BX_HTML', __('Allow HTML in the posts','bxpress'));
define('_MI_BX_FILESIZE', __('Maximum file size for the attachment','bxpress'));
define('_MI_BX_FILESIZE_DESC', __('Specify this value in Kilobytes','bxpress'));
define('_MI_BX_APREFIX', __('Anonymous Users Prefix','bxpress'));
define('_MI_BX_TIMENEW', __('Time to mark a post as new','bxpress'));
define('_MI_BX_TIMENEW_DESC', __('Specify this value in seconds','bxpress'));
define('_MI_BX_NUMPOST', __('Post Limit in Review','bxpress'));
define('_MI_BX_NUMPOST_DESC', __('Post Maximun Number that will be shown in the post form.','bxpress'));
define('_MI_BX_PERPAGE', __('Post Number per Page','bxpress'));
define('_MI_BX_PERPAGE_DESC', __('This value can be configured individually for every user.','bxpress'));
define('_MI_BX_TPERPAGE', __('Topics Number per Page','bxpress'));
define('_MI_BX_DATES', __('Date Format','bxpress'));
define('_MI_BX_ATTACHLIMIT', __('Limit post attachments','bxpress'));
define('_MI_BX_ATTACHDIR', __('Directory to storage the attachment','bxpress'));
define('_MI_BX_ATTACHDIR_DESC', __('This directory must exist in the server and must have read and writing permisions.','bxpress'));
define('_MI_BX_STICKY', __('Activate Sticky Posts','bxpress'));
define('_MI_BX_STICKY_DESC', __('By enabling this option, bXpress could create topics like "sticky". The sticky topics always will appear in the first positions. Even when this option is disabled with the administrators and moderators will create sticky posts.','bxpress'));
define('_MI_BX_STICKYPOSTS', __('Required post number for a user to publish sticky topics','bxpress'));
define('_MI_BX_ANNOUNCEMENTS', __('Activate announcements in the module','bxpress'));
define('_MI_BX_ANNOUNCEMENTSMAX',__('Maximum number of announcements to show','bxpress'));
define('_MI_BX_ANNOUNCEMENTSMODE',__('Mode to show announcements','bxpress'));
define('_MI_BX_ANNOUNCEMENTSMODE1',__('Recents','bxpress'));
define('_MI_BX_ANNOUNCEMENTSMODE2',__('Random','bxpress'));


//Tiempo de temas recientes
define('_MI_BX_TIMETOPICS','Recent topic time');
define('_MI_BX_DESCTIMETOPICS','Time to mark a topic as recent.'); 

define('_MI_BX_RSSDESC', __('Description of the Syndication option','bxpress'));

//Ordenar temas por mensajes recientes
define('_MI_BX_ORDERPOST', __('Order topics for recent post','bxpress'));
define('_MI_BX_DESCORDERPOST', __('Indicate if the forum topics will be ordered per recent topics','bxpress'));

// Bloques
define('_MI_BX_BKRECENT', __('Topics with new Posts','bxpress'));

//Páginas del módulo
define('_MI_BX_INDEX', __('Home Page','bxpress'));
define('_MI_BX_FORUM', __('Forum Page','bxpress'));
define('_MI_BX_TOPIC', __('Topic Page','bxpress'));
define('_MI_BX_POST', __('Post Page','bxpress'));
define('_MI_BX_EDIT', __('Post Editing','bxpress'));
define('_MI_BX_MODERATE', __('Forum Moderation','bxpress'));
define('_MI_BX_REPORT', __('Reports','bxpress'));
define('_MI_BX_SEARCH', __('Search','bxpress'));
