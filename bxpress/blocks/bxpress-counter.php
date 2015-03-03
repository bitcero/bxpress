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
 * @package       bxpress
 * @subpackage    blocks
 * @author        Eduardo Cortés (i.bitcero@gmail.com)
 * @since         1.2
 * @license       GPL v2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link          https://github.com/bitcero/bxpress
 */

function bxpress_block_counter_show( $options ){

    // Load css styles
    RMTemplate::get()->add_style( 'bxpress-blocks.min.css', 'bxpress' );

    $counters = array();
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    /*
     * Load members stats
     */
    if ( $options['members'] ){

        $sql = "SELECT COUNT(*) FROM " . $db->prefix("mod_bxpress_posts") . ' GROUP BY uid';
        $count = $db->getRowsNum( ( $db->query( $sql ) ) );

        $counters[] = array(
            'count'     => $count,
            'caption'   => $options['members_caption']
        );

    }

    /*
     * Load topics stats
     */
    if ( $options['topics'] ){

        $sql = "SELECT COUNT(*) FROM " . $db->prefix("mod_bxpress_topics");
        list($count) = $db->fetchRow( $db->query( $sql ) );

        $counters[] = array(
            'count'     => $count,
            'caption'   => $options['topics_caption']
        );

    }

    /*
     * Load replies stats
     */
    if ( $options['replies'] ){

        $sql = "SELECT COUNT(*) FROM " . $db->prefix("mod_bxpress_posts");
        list($count) = $db->fetchRow( $db->query( $sql ) );

        $counters[] = array(
            'count'     => $count,
            'caption'   => $options['replies_caption']
        );

    }

    return array('counters' => $counters);

}

function bxpress_block_counter_edit( $options ){

    ob_start();
    ?>

    <div class="row form-group">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Show members counter:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <label class="radio-inline">
                <input type="radio" name="options[members]" value="1"<?php echo $options['members']==1?' checked':''; ?>>
                <?php _e('Yes', 'bxpress'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[members]" value="0"<?php echo $options['members']==0?' checked':''; ?>>
                <?php _e('No', 'bxpress'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Members counter caption:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <input type="text" name="options[members_caption]" value="<?php echo $options['members_caption']; ?>" class="form-control">
        </div>

    </div>

    <div class="row form-group">
        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Show topics counter:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <label class="radio-inline">
                <input type="radio" name="options[topics]" value="1"<?php echo $options['topics']==1?' checked':''; ?>>
                <?php _e('Yes', 'bxpress'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[topics]" value="0"<?php echo $options['topics']==0?' checked':''; ?>>
                <?php _e('No', 'bxpress'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Topics counter caption:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <input type="text" name="options[topics_caption]" value="<?php echo $options['topics_caption']; ?>" class="form-control">
        </div>

    </div>

    <div class="row form-group">
        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Show replies counter:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <label class="radio-inline">
                <input type="radio" name="options[replies]" value="1"<?php echo $options['replies']==1?' checked':''; ?>>
                <?php _e('Yes', 'bxpress'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[replies]" value="0"<?php echo $options['replies']==0?' checked':''; ?>>
                <?php _e('No', 'bxpress'); ?>
            </label>
        </div>
    </div>

    <div class="row form-group">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Replies counter caption:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <input type="text" name="options[replies_caption]" value="<?php echo $options['replies_caption']; ?>" class="form-control">
        </div>

    </div>

    <?php
    $form = ob_get_clean();
    return $form;

}