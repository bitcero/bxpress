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
 * @package       bXpress
 * @subpackage    Blocks
 * @author        Eduardo Cortés <i.bitcero@gmail.com>
 * @since         1.2
 * @license       GPL v2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link          https://github.com/bitcero/bxpress
 */

load_mod_locale('bxpress');

function bxpress_block_topics_show($options){

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $mc = RMSettings::module_settings('bxpress');

    $tbl1 = $db->prefix('mod_bxpress_posts');
    $tbl2 = $db->prefix('mod_bxpress_topics');
    $tbl3 = $db->prefix('mod_bxpress_likes');
    $tbl4 = $db->prefix('mod_bxpress_forums');

    // Calculate period of time
    if ( 0 < $options['days'] )
        $period = time() - ( $options['days'] * 86400 );

    $order = 'DESC' == $options['order'] ? 'DESC' : 'ASC';

    $sql = "SELECT topics.*, forums.name,
                (SELECT SUM(likes) FROM $tbl1 WHERE id_topic=topics.id_topic) as likes,
                (SELECT post_time FROm $tbl1 WHERE id_topic=topics.id_topic ORDER BY post_time DESC LIMIT 0, 1) as updated
                FROM $tbl2 as topics, $tbl4 as forums WHERE ";
    if ( 0 < $options['days'] )
        $sql .= " topics.date > $period AND ";

    $sql .= "forums.id_forum=topics.id_forum";

    if ( 'recent' == $options['type'] ){

        $sql .= " ORDER BY topics.id_topic $order LIMIT 0, $options[limit]";

    } elseif( 'hot' == $options['type'] ){

        $sql .= " ORDER BY topics.replies $order LIMIT 0, $options[limit]";

    } elseif( 'hits' == $options['type'] ){

        $sql .= " ORDER BY topics.views $order LIMIT 0, $options[limit]";

    }

    $result=$db->queryF($sql);

    $topics = array();
    $block = array();

    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxforum.class.php';
    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxpost.class.php';
    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxtopic.class.php';
    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxfunctions.class.php';

    $topic = new bXTopic();
    $forum = new bXForum();
    $tf = new RMTimeFormatter(0, '%T% %d%, %Y%');

    while ($row=$db->fetchArray($result)){

        $topic->assignVars( $row );
        $forum->assignVars( array('id_forum'=>$topic->forum()) );

        $ret = array(
            'id'        => $topic->id(),
            'title'     => $topic->title,
            'link'      => $topic->permalink(),
            'likes'     => $topic->likes,
            'replies'   => $topic->replies,
            'hits'      => $topic->views,
            'forum'     => array(
                'name'  => $row['name'],
                'id'    => $row['id_forum'],
                'link'  => $forum->permalink()
            ),
            'time'      => $topic->date,
            'date'      => $tf->ago( $topic->date ),
            'likes'     => $row['likes'],
            'updated'   => 'full' == $options['format'] ? sprintf( __('Updated on %s', 'bxpress'), $tf->format( $row['updated'] ) ) : $tf->ago( $row['updated'] )

        );
        $topics[] = $ret;
    }

    $block['topics'] = $topics;
    $block['format'] = $options['format'];

    // Add css styles
    RMTemplate::get()->add_style( 'bxpress-blocks.min.css', 'bxpress' );

    return $block;

}

function bxpress_block_topics_edit( $options ){

    ob_start();
    ?>

    <div class="form-group row">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('List type:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <select class="form-control" name="options[type]">
                <option value="recent"<?php echo 'recent'==$options['type']?' selected': ''; ?>><?php _e('Recent topics', 'bxpress'); ?></option>
                <option value="hot"<?php echo 'hot'==$options['type']?' selected': ''; ?>><?php _e('Hot topics', 'bxpress'); ?></option>
                <option value="hits"<?php echo 'hits'==$options['type']?' selected': ''; ?>><?php _e('Most viewed topics', 'bxpress'); ?></option>
                <option value="likes"<?php echo 'likes'==$options['type']?' selected': ''; ?>><?php _e('Most likely topics', 'bxpress'); ?></option>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Number of items:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <input type="text" class="form-control" name="options[limit]" value="<?php echo $options['limit']; ?>">
        </div>

    </div>

    <div class="form-group row">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Limit period in days', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <input type="text" class="form-control" name="options[days]" value="<?php echo $options['days']; ?>">
            <small class="help-block">
                <?php _e('Leave this value in 0 for no limit.', 'bxpress'); ?>
            </small>
        </div>

    </div>

    <div class="form-group row">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Display order:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <label class="radio-inline">
                <input type="radio" name="options[order]" value="DESC"<?php echo 'DESC'==$options['order']?' checked':''; ?>>
                <?php _e('Descending', 'bxpress'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[order]" value="ASC"<?php echo 'ASC'==$options['order']?' checked':''; ?>>
                <?php _e('Ascending', 'bxpress'); ?>
            </label>
        </div>
    </div>

    <div class="form-group row">

        <div class="col-sm-4 col-lg-3">
            <label><?php _e('Display format:', 'bxpress'); ?></label>
        </div>
        <div class="col-sm-8 col-lg-9">
            <label class="radio-inline">
                <input type="radio" name="options[format]" value="full"<?php echo 'full'==$options['format']?' checked':''; ?>>
                <?php _e('Full', 'bxpress'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[format]" value="medium"<?php echo 'medium'==$options['format']?' checked':''; ?>>
                <?php _e('Medium', 'bxpress'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="options[format]" value="compact"<?php echo 'compact'==$options['format']?' checked':''; ?>>
                <?php _e('Compact', 'bxpress'); ?>
            </label>
            <small class="help-block">
                <?php _e('Determines the way in that the block will be displayed. Full format shows all information about topics.', 'bxpress'); ?>
            </small>
        </div>

    </div>

    <?php
    $form = ob_get_clean();

    return $form;
}
