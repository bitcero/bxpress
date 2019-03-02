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

ob_start();
?>

var bxpressLang = {
    'liked_by':     'Liked by',
    'likes_more':   'and %u more'
};

<?php
$lang = ob_get_clean();
return $lang;
