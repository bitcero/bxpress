<?php
/**
 * bXpress Forums
 * A lightweight forum module for XOOPS and Common Utilities
 * 
 * Copyright © 2015 Eduardo Cortés
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
 * @package      bxpress
 * @author       Eduardo Cortés
 * @copyright    Eduardo Cortés
 * @license      GPL 2
 * @link         http://eduardocortes.mx
 * @link         http://rmcommon.com
 */

class Bxpress_Notifications extends Rmcommon_ANotifications
{
    use RMSingleton;
    public function __construct(){

        // Forum notifications
        $this->events['newtopic'] = array(
            'caption'       => __('Notify me when new topic is created in this forum', 'bxpress'),
            'event'         => 'newtopic',
            'element'        => 'bxpress',
            'type'        => 'module',
            'params'            => '',
            'permissions'   => array()
        );

        $this->events['forum-newpost'] = array(
            'caption'       => __('Notify me when new message is posted in this forum', 'bxpress'),
            'event'         => 'forum-newpost',
            'element'        => 'bxpress',
            'type'        => 'module',
            'params'            => '',
            'permissions'   => array()
        );

        // Topic notifications
        $this->events['reply'] = array(
            'caption'       => __('Notify me when new reply is sent in this topic', 'bxpress'),
            'event'         => 'reply',
            'element'        => 'bxpress',
            'type'        => 'module',
            'params'            => '',
            'permissions'   => array()
        );

    }

    public function is_valid( $event ){
        return array_key_exists( $event, $this->events );
    }

    public function from_name(){
        global $xoopsConfig;
        $mc = RMSettings::module_settings('bxpress');
        $ret = $xoopsConfig['sitename'] . ': ' . $mc->forum_title;
        return $ret;
    }

    public function subject( $name, $params ){

        $ret = '';

        switch( $name ){
            case 'newtopic':
                $ret = sprintf( __('New topic created in forum "%s"', 'bxpress'), $params['forum']->name() );
                break;
            case 'forum-newpost':
                $ret = sprintf( __('New post sent in forum "%s"', 'bxpress'), $params['forum']->name() );
                break;
            case 'reply':
                $ret = sprintf( __('New reply in topic "%s" from forum "%s"', 'bxpress'), $params['topic']->title(), $params['forum']->name() );
                break;
        }

        return $ret;

    }

    public function use_html(){
        return true;
    }

    public function body($event, $params){
        global $xoopsConfig;

        extract( $params );
        $account_link = XOOPS_URL . '/user.php';
        $unsubscribe_link = XOOPS_URL . '/notifications.php?page=cu-notification-list';

        if ( $event->event == 'reply' ){

            ob_start();
            include RMTemplate::get()->get_template( 'email/bxpress-notify-reply.php', 'module', 'bxpress' );
            $body = ob_get_clean();
            return $body;

        }

        if ( $event->event == 'newtopic' ){

            ob_start();
            include RMTemplate::get()->get_template( 'email/bxpress-notify-forum-topic.php', 'module', 'bxpress' );
            $body = ob_get_clean();
            return $body;
        }

        if ( $event->event == 'forum-newpost' ){

            ob_start();
            include RMTemplate::get()->get_template( 'email/bxpress-notify-forum-post.php', 'module', 'bxpress' );
            $body = ob_get_clean();
            return $body;
        }


    }

    public function element_data(){

        $module = RMModules::load_module( 'bxpress' );

        include_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxfunctions.class.php';

        $ret = array(
            'name'  => $module->getVar('name'),
            'link'  => bXFunctions::url(),
        );

        return $ret;

    }

    public function object_data( $event ){

        include_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxforum.class.php';
        include_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxtopic.class.php';

        switch( $event->event ){
            case 'reply':
                // Get topic
                $topic = new bXTopic( $event->params );
                if ( $topic->isNew() )
                    return null;
                $ret = array(
                    'name'  => $topic->title(),
                    'link'  => $topic->permalink()
                );
                break;
            case 'newtopic':
            case 'forum-newpost':
                // Get forum
                $forum = new bXForum( $event->params );
                if ( $forum->isNew() )
                    return null;
                $ret = array(
                    'name'  => $forum->name(),
                    'link'  => $forum->permalink()
                );
                break;
        }

        return $ret;

    }
}