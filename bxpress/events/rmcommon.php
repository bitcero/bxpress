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
    public function eventRmcommonSavedSettings($dirname, $save, $add, $delete)
    {
        if ('bxpress' != $dirname) {
            return $dirname;
        }

        // URL rewriting
        $rule = 'RewriteRule ^' . trim($save['htbase'], '/') . '/?(.*)$ modules/bxpress/$1 [L]';
        if (1 == $save['urlmode']) {
            $ht = new RMHtaccess('bxpress');
            $htResult = $ht->write($rule);
            if (true !== $htResult) {
                showMessage(__('An error ocurred while trying to write .htaccess file!', 'bxpress'), RMMSG_ERROR);
            }
        } else {
            $ht = new RMHtaccess('bxpress');
            $ht->removeRule();
            $ht->write();
        }

        return null;
    }

    public function eventRmcommonGetFeedsList($feeds)
    {
        load_mod_locale('bxpress');
        require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxfunctions.class.php';
        require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxforum.class.php';

        $module = RMModules::load_module('bxpress');
        $config = RMSettings::module_settings('bxpress');
        $url = XOOPS_URL . '/' . ($config->urlmode ? $config->htbase : 'modules/bxpress') . '/';
        $bxFunc = new bXFunctions();

        $data = [
            'title' => $module->name(),
            'url' => $url,
            'module' => 'bxpress',
        ];

        $options[] = [
            'title' => __('All Recent Messages', 'bxpress'),
            'params' => 'show=all',
            'description' => __('Show all recent messages', 'bxpress'),
        ];

        $forums = $bxFunc->forumList('', false);

        $table = '<table cellpadding="2" cellspacing="2" width="100%"><tr class="even">';
        $count = 0;
        foreach ($forums as $forum) {
            if ($count >= 3) {
                $count = 0;
                $table .= '</tr><tr class="' . tpl_cycle('odd,even') . '">';
            }
            $table .= '<td width="33%"><a href="' . XOOPS_URL . '/backend.php?action=showfeed&amp;mod=bxpress&amp;show=forum&amp;forum=' . $forum['id'] . '">' . $forum['title'] . '</a></td>';
            $count++;
        }
        $table .= '</tr></table>';

        $options[] = [
            'title' => __('Posts by forum', 'bxpress'),
            'description' => __('Select a forum to see the messages posted recently.', 'bxpress') . ' <a href="javascript:;" onclick="$(\'#bxforums-feed\').slideToggle(\'slow\');">Show Forums</a>
                            <div id="bxforums-feed" style="padding: 10px; display: none;">' . $table . '</div>',
        ];

        unset($forums);

        $feed = ['data' => $data, 'options' => $options];
        $feeds[] = $feed;

        return $feeds;
    }
}
