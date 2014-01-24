<?php
// $Id: uninstall.php 819 2011-12-08 23:43:13Z i.bitcero $
// --------------------------------------------------------------
// XOOPS EXM
// Nueva Versión Mejorada de XOOPS
// CopyRight  2007 - 2008. Red México
// Autor: BitC3R0
// http://www.redmexico.com.mx
// http://www.xoopsmexico.net
// --------------------------------------------
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License as
// published by the Free Software Foundation; either version 2 of
// the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public
// License along with this program; if not, write to the Free
// Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,
// MA 02111-1307 USA
// --------------------------------------------------------------
// @copyright:  2007 - 2008. Red México
// @author: BitC3R0


/**
* @desc Comprueba si se eliminaron los archivos pertenecientes al foro a ser eliminado
**/
function xoops_module_uninstall_exmbb(&$mod){
	$path=XOOPS_ROOT_PATH."/uploads/exmbb/";
	xoops_delete_directory($path);
	if (!file_exists($path)){
		return true;	
	}
	else{
		return false;
	}
}


?>
