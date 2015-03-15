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

function bxpress_block_users_show( $options ){

    // Add css styles
    RMTemplate::get()->add_style( 'bxpress-blocks.min.css', 'bxpress' );

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $tbu = $db->prefix("users");
    $tbp = $db->prefix("mod_bxpress_posts");
    $tbl = $db->prefix("mod_bxpress_likes");

    if ( 'active' == $options['type'] ){
        $sql = "SELECT DISTINCT posts.uid, users.uid, users.uname, users.email, users.name,
                (SELECT COUNT(*) FROM $tbp WHERE uid = posts.uid) as total,
                (SELECT SUM(likes) FROM $tbp WHERE uid = posts.uid ) as likes
                FROM $tbu as users, $tbp as posts WHERE users.uid = posts.uid ORDER BY total DESC LIMIT 0, $options[limit]";
    }

    $result = $db->query( $sql );
    $users = array();
    $user = new RMUser();

    while( $row = $db->fetchArray( $result ) ){

        $user->assignVars( $row );

        $users[] = array(
            'id'        => $user->id(),
            'name'      => '' != $user->name ? $user->name : $user->uname,
            'uname'     => $user->uname,
            'avatar'    => RMEvents::get()->run_event("rmcommon.get.avatar", $user->email, 0),
            'posts'     => $row['total'],
            'likes'     => $row['likes']
        );

    }

    $block['users'] = $users;
    return $block;

}

function bxpress_block_users_edit( $options ){

    ob_start();
    ?>

    <div class="row form-group">
        <div class="col-sm-4 col-lg-3">
            <label><?php _e('List type:','bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <select class="form-control" name="options[type]">
                <option value="active"<?php echo 'active' == $options['type'] ? ' selected' : ''; ?>><?php _e('Most active users', 'bxpress'); ?></option>
                <option value="recent"<?php echo 'recent' == $options['type'] ? ' selected' : ''; ?>><?php _e('Recent users', 'bxpress'); ?></option>
            </select>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Number of users:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <input type="text" class="form-control" name="options[limit]" value="<?php echo $options['limit']; ?>">
        </div>
    </div>

    <?php
    $form = ob_get_clean();

    return $form;

}