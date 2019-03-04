<?php
// $Id: files.php 819 2011-12-08 23:43:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * @desc Archivo para procesar la entrega de archivos adjuntos
 */
define('BB_LOCATION', 'files');
require  dirname(dirname(__DIR__)) . '/mainfile.php';

$id = rmc_server_var($_GET, 'id', 0);
$topic = rmc_server_var($_GET, 'topic', 0);

if ($id <= 0) {
    redirect_header('topic.php?id=' . $topic, 2, __('No topic has been specified!', 'bxpress'));
    die();
}

$attach = new bXAttachment($id);
if ($attach->isNew()) {
    redirect_header('topic.php?id=' . $topic, 2, __('Specified file does not exists!', 'bxpress'));
    die();
}

if (!file_exists(XOOPS_UPLOAD_PATH . '/bxpress/' . $attach->file())) {
    redirect_header('topics.php', 2, __('Specified file does not exists!', 'bxpress'));
    die();
}
$ext = mb_substr($attach->file(), mb_strrpos($attach->file(), '.'));
header('Content-type: ' . $attach->mime());
header('Cache-control: no-store');
header('Expires: 0');
header('Content-disposition: attachment; filename=' . urlencode($attach->name() . $ext));
header('Content-Transfer-Encoding: binary');
header('Content-Lenght: ' . filesize(XOOPS_UPLOAD_PATH . '/bxpress/' . $attach->file()));
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(XOOPS_UPLOAD_PATH . '/bxpress/' . $attach->file())) . 'GMT');
ob_clean();
flush();
readfile(XOOPS_UPLOAD_PATH . '/bxpress/' . $attach->file());
exit();
