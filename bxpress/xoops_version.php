<?php
/**
 * bXpress Forums
 * A light weight and easy to use XOOPS module to create forums
 * 
 * Copyright © 2014 Eduardo Cortés https://eduardocortes.mx
 * -----------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * -----------------------------------------------------------------
 * @package    bXpress
 * @author     Eduardo Cortés <i.bitcero@gmail.com>
 * @since      1.2
 * @license    GPL v2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link       https://github.com/bitcero/bxpress
 */

$amod = xoops_getActiveModules();
if(!in_array("rmcommon",$amod)){
    $error = "<strong>WARNING:</strong> bXpress requires that %s is installed!<br />Please install %s before trying to use bXpress";
    $error = str_replace("%s", '<a href="http://rmcommon.com/" target="_blank">Common Utilities</a>', $error);
    xoops_error($error);
    $error = '%s is not installed! This might cause problems with functioning of bXpress and entire system. To solve, install %s or uninstall bXpress and then delete module folder.';
    $error = str_replace("%s", '<a href="http://rmcommon.com/" target="_blank">Common Utilities</a>', $error);
    trigger_error($error, E_USER_WARNING);
    echo "<br />";
}

if (!function_exists("__")){
    function __($text, $d){
        return $text;
    }
}

if(function_exists("load_mod_locale")) load_mod_locale('bxpress');

$modversion = array(

    // General info
    'name'          => __('bXpress','bxpress'),
    'description'   => __('A simple forums module for XOOPS and common utilities.','bxpress'),
    'version'       => 1.2,
    'license'       => 'GPL 2',
    'dirname'       => 'bxpress',
    'official'      => 0,

    // Install and update
    'onInstall'     => 'include/install.php',
    'onUpdate'      => 'include/install.php',

    // Common Utilities
    'rmnative'      => 1,
    'url'           => 'https://github.com/bitcero/bxpress',
    'rmversion'     => array(
        'major'     => 1,
        'minor'     => 2,
        'revision'  => 35,
        'stage'     => 0,
        'name'      => 'bXpress'
    ),
    'rewrite'       => 0,
    'updateurl'     => "https://www.xoopsmexico.net/modules/vcontrol/",
    'help'          => 'docs/readme.html',

    // Author information
    'author'        => "Eduardo Cortes",
    'authormail'    => "i.bitcero@gmail.com",
    'authorweb'     => "EduardoCortes.mx",
    'authorurl'     => "http://eduardocortes.mx",
    'credits'       => "Eduardo Cortes",

    // Logo and icons
    'image'         => "images/logo.png",
    'icon'          => "svg-rmcommon-bubbles text-danger",

    // Social
    'social'        => array(
        array(
            'title' => 'Twitter',
            'type'  => 'twitter-square',
            'url'   => 'http://www.twitter.com/bitcero/'
        ),
        array(
            'title' => 'Facebook',
            'type'  => 'facebook-square',
            'url'   => 'http://www.facebook.com/eduardo.cortes.hervis/'
        ),
        array(
            'title' => 'Instagram',
            'type'  => 'instagram',
            'url'   => 'http://www.instagram.com/eduardocortesh/'
        ),
        array(
            'title' => 'LinkedIn',
            'type'  => 'linkedin-square',
            'url'   => 'http://www.linkedin.com/in/bitcero/'
        ),
        array(
            'title' => 'GitHub',
            'type'  => 'github',
            'url'   => 'http://www.github.com/bitcero/'
        ),
        array(
            'title' => 'Google+',
            'type'  => 'google-plus-square',
            'url'   => 'https://plus.google.com/100655708852776329288'
        ),
        array(
            'title' => __('My Blog', 'works'),
            'type'  => 'quote-left',
            'url'   => 'http://eduardocortes.mx'
        ),
    ),

    // Backend
    'hasAdmin'      => 1,
    'adminindex'    => "admin/index.php",
    'adminmenu'     => "admin/menu.php",

    // Front End
    'hasMain'       => 1,

    // Search
    'hasSearch'     => 1,
    'search'        => array(
        'file'  => "include/search.php",
        'func'  => "bxpress_perform_search"
    ),

    // SQL file
    'sqlfile'       => array( 'mysql' => "sql/mysql.sql" ),

    // Database tables
    'tables'        => array(
        'mod_bxpress_announcements',
        'mod_bxpress_attachments',
        'mod_bxpress_categories',
        'mod_bxpress_forums',
        'mod_bxpress_posts',
        'mod_bxpress_posts_text',
        'mod_bxpress_report',
        'mod_bxpress_topics',
        'mod_bxpress_likes'
    ),

    // Smarty templates
    'templates'     => array(
        array(
            'file'          => 'bxpress-index-categories.tpl',
            'description'   => __('Categories index', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-index-forums.tpl',
            'description'   => __('Forums index', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-forum.tpl',
            'description'   => __('Show the forum topics', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-header.tpl',
            'description'   => __('Forum header', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-postform.tpl',
            'description'   => __('Post form template', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-topic.tpl',
            'description'   => __('Shows topic contents', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-powered.tpl',
            'description'   => __('Powered legend template', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-moderate.tpl',
            'description'   => __('Moderator control panel', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-moderate-forms.tpl',
            'description'   => __('Moderation forms', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-announcements.tpl',
            'description'   => __('Annpuncements templates', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-report.tpl',
            'description'   => __('Reporting form template', 'bxpress')
        ),
        array(
            'file'          => 'bxpress-search.tpl',
            'description'   => __('Standalone template for search', 'bxpress')
        ),

    )

);

/**
 * Settings
 */
$modversion['config'][] = array(
    'name' => 'forum_title',
    'title' => __('Title of the forum', 'bxpress'),
    'description' => __('This title will be used in header of module', 'bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => __('bXpress Forums','bxpress'),
);

// URL rewriting
$modversion['config'][] = array( 
    'name' => 'urlmode',
    'title' => __('Enable permalinks:', 'bxpress'),
    'description' => __('When active, this option allow to module to manage shorter URLs', 'bxpress'),
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 0,
);
$modversion['config'][] = array( 
    'name' => 'htbase',
    'title' => __('Relative path for module', 'bxpress'),
    'description' => __('The relative path where module will respond to queries.', 'bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => '/modules/bxpress',
);

$modversion['config'][] = array(
    'name' => 'show_inactive',
    'title' => __('Show inactive forums for webmasters and moderators', 'bxpress'),
    'description' => __('When this option is enabled the inactive forums will be shown to webmasters and moderators for administrative purposes. For all other users, the inactive forums remain hidden.', 'bxpress'),
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 1,
);

$modversion['config'][] = array(
    'name' => 'maxfilesize',
    'title' => __('Maximum allowed size for attached files (in KB)', 'bxpress'),
    'description' => __('The files sent in the forums will be limited to this size, bigger file sizes will be ignored.', 'bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => 500
);

$modversion['config'][] = array(
    'name' => 'showcats',
    'title' => __('Show categories in the Homepage', 'bxpress'),
    'description' => __('If this option is enable the forums will be ordered by categories in the homepage.', 'bxpress'),
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 0
);

// Búsqueda
$modversion['config'][] = array(
    'name' => 'search',
    'title' => __('Enable search for anonymous users', 'bxpress'),
    'description' => '',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 0
);

// HTML
$modversion['config'][] = array(
    'name' => 'html',
    'title' => __('Allow HTML in the posts', 'bxpress'),
    'description' => '',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 0
);

// Prefijo para usuarios Anónimos
$modversion['config'][] = array(
    'name' => 'anonymous_prefix',
    'title' => __('Anonymous Users Prefix', 'bxpress'),
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => 'guest_'
);

// Mensajes Nuevos
$modversion['config'][] = array(
    'name' => 'time_new',
    'title' => __('Time to mark a post as new','bxpress'),
    'description' => __('Specify this value in seconds','bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 600
);

// Numero de mensajes en el formulario de envio
$modversion['config'][] = array(
    'name' => 'numpost',
    'title' => __('Post Limit in Review','bxpress'),
    'description' => __('Post Maximun Number that will be shown in the post form.','bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 10
);

// Numero de mensajes en cada página
$modversion['config'][] = array(
    'name' => 'perpage',
    'title' => __('Post Number per Page','bxpress'),
    'description' => __('This value can be configured individually for every user.','bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 15
);

// Numero de temas en cada página
$modversion['config'][] = array(
    'name' => 'topicperpage',
    'title' => __('Topics Number per Page','bxpress'),
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 30
);

// formato de Fechas
$modversion['config'][] = array(
    'name' => 'dates',
    'title' => __('Date Format','bxpress'),
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => 'm/d/Y'
);

// Límite de archivos adjuntos por mensaje
$modversion['config'][] = array(
    'name' => 'attachlimit',
    'title' => __('Limit post attachments','bxpress'),
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 5
);

// Directorio para adjuntos
$modversion['config'][] = array(
    'name' => 'attachdir',
    'title' => __('Directory to storage the attachment','bxpress'),
    'description' => __('This directory must exist in the server and must have read and writing permisions.','bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => XOOPS_UPLOAD_PATH.'/bxpress'
);

// Mensajes Fijos
$modversion['config'][] = array(
    'name' => 'sticky',
    'title' => __('Activate Sticky Posts','bxpress'),
    'description' => __('By enabling this option, bXpress could create topics like "sticky". The sticky topics always will appear in the first positions. Even when this option is disabled with the administrators and moderators will create sticky posts.','bxpress'),
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 1
);

// Rangos para mensajes fijos
$modversion['config'][] = array(
    'name' => 'sticky_posts',
    'title' => __('Previous messages sent to can create sticky posts', 'bxpress'),
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 1000
);

// Anuncios en el módulo
$modversion['config'][] = array(
    'name' => 'announcements',
    'title' => __('Activate announcements in the module','bxpress'),
    'description' => '',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => 1
);

// Numero de Anuncios en el módulo
$modversion['config'][] = array(
    'name' => 'announcements_max',
    'title' => __('Maximum number of announcements to show','bxpress'),
    'description' => '',
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 1
);

// Modo para los anuncios
$modversion['config'][] = array(
    'name' => 'announcements_mode',
    'title' => __('Mode to show announcements','bxpress'),
    'description' => '',
    'formtype' => 'select',
    'valuetype' => 'int',
    'default' => 0,
    'options' => array(__('Recents','bxpress')=>0,__('Random','bxpress')=>1)
);

//Tiempo de temas recientes
$modversion['config'][] = array(
    'name' => 'time_topics',
    'title' => __('Recent topic time', 'bxpress'),
    'description' => __('Time to mark a topic as recent.', 'bxpress'),
    'formtype' => 'textbox',
    'valuetype' => 'int',
    'default' => 24
);

//Tiempo de temas recientes
$modversion['config'][] = array(
    'name' => 'rssdesc',
    'title' => __('Description of the Syndication option','bxpress'),
    'description' => '',
    'formtype' => 'textarea',
    'valuetype' => 'text',
    'default' => ''
);

//Ordenar por mensajes recientes
$modversion['config'][] = array(
    'name' => 'order_post',
    'title' => __('Order topics for recent post','bxpress'),
    'description' => __('Indicate if the forum topics will be ordered per recent topics','bxpress'),
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => '0'
);

// Bloque Recientes
$modversion['blocks'] = array(

    array(
        'file'          => 'bxpress-topics.php',
        'name'          => __('Forum topics list', 'bxpress'),
        'description'   => __('Show a list with topics in forum according to configured options', 'bxpress'),
        'show_func'     => 'bxpress_block_topics_show',
        'edit_func'     => 'bxpress_block_topics_edit',
        'template'      => 'block-bxpress-topics.tpl',
        'options'       => array(
            'type'  => 'recent',
            'limit' => 10,
            'days'  => 0,
            'format'=> 'full',
            'order' => 'DESC'
        )
    ),

    array(
        'file'          => 'bxpress-counter.php',
        'name'          => __('Status counter', 'bxpress'),
        'description'   => __('Shows the counters for forum status', 'bxpress'),
        'show_func'     => 'bxpress_block_counter_show',
        'edit_func'     => 'bxpress_block_counter_edit',
        'template'      => 'block-bxpress-counter.tpl',
        'options'       => array(
            'members'           => 1,
            'members_caption'   => __('members', 'bxpress'),
            'topics'            => 1,
            'topics_caption'    => __('topics', 'bxpress'),
            'replies'           => 1,
            'replies_caption'   => __('replies', 'bxpress'),
            'likes'             => 1,
            'likes_caption'     => __('likes', 'bxpress'),
            'files'             => 1,
            'files_caption'     => __('files', 'bxpress'),
        )
    ),

    array(
        'file'          => 'bxpress-users.php',
        'name'          => __('Users activity', 'bxpress'),
        'description'   => __('Show the activity from users', 'bxpress'),
        'show_func'     => 'bxpress_block_users_show',
        'edit_func'     => 'bxpress_block_users_edit',
        'template'      => 'block-bxpress-users.tpl',
        'options'       => array(
            'limit'     => 10,
            'type'      => 'active'
        )
    )


);


//Páginas del Módulo
$modversion['subpages']['index'] = __('Index','bxpress');
$modversion['subpages']['forums'] = __('Forums','bxpress');
$modversion['subpages']['topics'] = __('Topics','bxpress');
$modversion['subpages']['post'] = __('Post','bxpress');
$modversion['subpages']['edit'] = __('Edit Post','bxpress');
$modversion['subpages']['moderate'] = __('Moderate','bxpress');
$modversion['subpages']['report'] = __('Report post','bxpress');
$modversion['subpages']['search'] = __('Search','bxpress');
