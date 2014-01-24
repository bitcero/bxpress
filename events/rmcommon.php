<?php
// $Id: files.php 819 2011-12-08 23:43:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class BxpressRmcommonPreload
{
    public function eventRmcommonGetFeedsList($feeds){
        
        load_mod_locale('bxpress');
        include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxfunctions.class.php';
        include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxforum.class.php';
        
        $module = RMFunctions::load_module('bxpress');
        $config = RMSettings::module_settings('bxpress');
        $url = XOOPS_URL.'/'.($config['bxpress'] ? $config['htbase'] : 'modules/bxpress').'/';
        $bxFunc = new bXFunctions();

        $data = array(
            'title'    => $module->name(),
            'url'    => $url,
            'module' => 'bxpress'
        );
        
        $options[] = array(
            'title'    => __('All Recent Messages', 'dtransport'),
            'params' => 'show=all',
            'description' => __('Show all recent downloads','dtransport')
        );
        
        $forums = $bxFunc->forumList('',false);
        
        $table = '<table cellpadding="2" cellspacing="2" width="100%"><tr class="even">';
        $count = 0;
        foreach($forums as $forum){
            if ($count>=3){
                $count = 0;
                $table .= '</tr><tr class="'.tpl_cycle("odd,even").'">';
            }
            $table .= '<td width="33%"><a href="'.XOOPS_URL.'/backend.php?action=showfeed&amp;mod=bxpress&amp;show=forum&amp;forum='.$forum['id'].'">'.$forum['title'].'</a></td>';
            $count++;
        }
        $table .= '</tr></table>';
        
        $options[] = array(
            'title' => __('Posts by forum','dtransport'),
            'description' => __('Select a forum to see the messages posted recently.','dtransport').' <a href="javascript:;" onclick="$(\'#bxforums-feed\').slideToggle(\'slow\');">Show Forums</a>
                            <div id="bxforums-feed" style="padding: 10px; display: none;">'.$table.'</div>'
        );
        
        unset($forums);
        
        $feed = array('data'=>$data,'options'=>$options);
        $feeds[] = $feed;
        return $feeds;
        
    }
}
