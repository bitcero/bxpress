<?php
// $Id: install.php 861 2011-12-19 02:38:22Z i.bitcero $
// --------------------------------------------------------------
// bXpress
// Blogging System
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

function xoops_module_pre_install_bxpress(&$mod){
    
    xoops_setActiveModules();
    
    $mods = xoops_getActiveModules();
    
    if(!in_array("rmcommon", $mods)){
        $mod->setErrors('bXpress could not be instaled if <a href="http://www.redmexico.com.mx/w/common-utilities/" target="_blank">Common Utilities</a> has not be installed previously!<br />Please install <a href="http://www.redmexico.com.mx/w/common-utilities/" target="_blank">Common Utilities</a>.');
        return false;
    }
    
    return true;
    
}

function xoops_module_update_bxpress($mod, $pre){
    global $xoopsDB;

    $db = $xoopsDB;

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_announcements").'` TO  `'.$db->prefix("mod_bxpress_announcements").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_announcements").'` ENGINE = INNODB;');

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_attachments").'` TO  `'.$db->prefix("mod_bxpress_attachments").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_attachments").'`  ENGINE = INNODB;;');

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_categories").'` TO  `'.$db->prefix("mod_bxpress_categories").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_categories").'`  ENGINE = INNODB;;');

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_forums").'` TO  `'.$db->prefix("mod_bxpress_forums").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_forums").'`  ENGINE = INNODB;;');

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_posts").'` TO  `'.$db->prefix("mod_bxpress_posts").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_posts").'`  ENGINE = INNODB;;');

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_posts_text").'` TO  `'.$db->prefix("mod_bxpress_posts_text").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_posts_text").'` ENGINE = INNODB;;');

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_report").'` TO  `'.$db->prefix("mod_bxpress_report").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_report").'` ENGINE = INNODB;;');

    $db->queryF('RENAME TABLE `'.$db->prefix("mod_bxpress_topics").'` TO  `'.$db->prefix("mod_bxpress_topics").'` ;');
    $db->queryF('ALTER TABLE `'.$db->prefix("mod_bxpress_topics").'`  ENGINE = INNODB;;');

    $db->queryF("ALTER TABLE `" . $db->prefix("mod_bxpress_forums") . "` ADD `image` varchar(255) NOT NULL AFTER `desc`;");
    $db->queryF("ALTER TABLE `" . $db->prefix("mod_bxpress_posts") . "` ADD `parent` int(11) NOT NULL DEFAULT '0' AFTER `require_reply`;");
    $db->queryF("ALTER TABLE `" . $db->prefix("mod_bxpress_posts") . "` ADD `likes` int(11) NOT NULL DEFAULT '0' AFTER `parent`;");

    // Add likes table
    $db->queryF( "CREATE TABLE `". $db->prefix("mod_bxpress_likes") . "` (
  `id_like` int(11) NOT NULL AUTO_INCREMENT,
  `post` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id_like`),
  KEY `post` (`post`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    return true;

}
