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

/**
 * This file handle the likes requests to add or remove likes from a specific post
 */

require '../../mainfile.php';

$xoopsLogger->activated = false;

function response_json( $error = 0, $message = '', $data = array(), $token = true ){
    global $xoopsSecurity;

    echo json_encode( array('message' => $message, 'data' => $data, 'error' => $error, 'token' => $token ? $xoopsSecurity->createToken( 0, 'BXTOKEN' ) : '' ) );
    exit();

}

if ( !$xoopsUser )
    exit();

/*
 * Get parameters
 */
$id = RMHttpRequest::post( 'id', 'integer', 0 );

if ( !$xoopsSecurity->check( true, false, 'BXTOKEN' ) ){
    response_json(
        1, __('Please refresh the page in order to register your likes.', 'bxpress' ),
        array(), false
    );
}

$post = new bXPost( $id );
if ( $post->isNew() )
    response_json(
        1, __('The specified post does not exists! Verify it!', 'bxpress'), array(), true
    );

$sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix("mod_bxpress_likes") . " WHERE uid=" . $xoopsUser->uid() . " AND post=" . $post->id();
list($exists) = $xoopsDB->fetchRow( $xoopsDB->query( $sql ) );

if ( $exists > 0 )
    $action = 'unlike';
else
    $action = 'like';

if ( 'like' == $action ){

    // Add to likes table
    $sql = "INSERT INTO " . $xoopsDB->prefix("mod_bxpress_likes") . " (post,uid,time) VALUES (" . $post->id() . "," . $xoopsUser->uid() . "," . time() . ")";

    if ( !$xoopsDB->queryF( $sql ) )
        response_json( 1, __('We could not register your like for this post. Please try again.', 'bxpress'), array(), true);

    $sql = "UPDATE " . $xoopsDB->prefix("mod_bxpress_posts") . " SET likes=likes+1 WHERE id_post = " . $post->id();
    $xoopsDB->queryF( $sql );

    $data = array(
        'likes'     => $post->likes + 1,
        'uname'     => $xoopsUser->uname(),
        'uid'       => $xoopsUser->uid(),
        'name'      => $xoopsUser->getVar('name') != '' ? $xoopsUser->getVar('name') : $xoopsUser->getVar('uname'),
        'avatar'    => RMEvents::get()->run_event("rmcommon.get.avatar", $xoopsUser->getVar('email'), 40),
        'post'      => $post->id(),
        'action'    => 'add'
    );

    response_json(
        0, __('Your like has been registered successfully!', 'bxpress'),
        $data, true
    );

} elseif ( 'unlike' == $action ){

    // Remove likes from table
    $sql = "DELETE FROM " . $xoopsDB->prefix("mod_bxpress_likes") . " WHERE uid=" . $xoopsUser->uid() . " AND post=" . $post->id();

    if ( !$xoopsDB->queryF( $sql ) )
        response_json( 1, __('We could not remove your like from this post. Please try again.', 'bxpress'), array(), true);

    $rest = $xoopsDB->getAffectedRows();

    $sql = "UPDATE " . $xoopsDB->prefix("mod_bxpress_posts") . " SET likes=likes-$rest WHERE id_post = " . $post->id();
    $xoopsDB->queryF( $sql );

    $data = array(
        'likes'     => $post->likes - $rest,
        'uname'     => $xoopsUser->uname(),
        'uid'       => $xoopsUser->uid(),
        'name'      => $xoopsUser->getVar('name') != '' ? $xoopsUser->getVar('name') : $xoopsUser->getVar('uname'),
        'info'      => XOOPS_URL . '/userinfo.php?uid=' . $xoopsUser->uid(),
        'avatar'    => RMEvents::get()->run_event("rmcommon.get.avatar", $xoopsUser->getVar('email'), 40),
        'post'      => $post->id(),
        'action'    => 'remove'
    );

    response_json(
        0, __('Your like has been removed successfully!', 'bxpress'),
        $data, true
    );

}