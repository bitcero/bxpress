<?php
// $Id: bxattachment.class.php 896 2012-01-02 18:43:23Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * @desc Clase para el manejo de archivos adjuntos de mensajes
 */
class bXAttachment extends RMObject
{
    private $dir = '';

    public function __construct($id = null, $dir = '')
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix('mod_bxpress_attachments');
        $this->setNew();
        $this->initVarsFromTable();

        if ('' == $dir || !file_exists($dir)) {
            global $xoopsModuleConfig;
            $dir = $xoopsModuleConfig['attachdir'];
        }

        $this->dir = $dir;

        if (!isset($id)) {
            return;
        }

        if (!$this->loadValues($id)) {
            return;
        }

        $this->unsetNew();
    }

    public function id()
    {
        return $this->getVar('attach_id');
    }

    public function post()
    {
        return $this->getVar('post_id');
    }

    public function setPost($value)
    {
        return $this->setVar('post_id', $value);
    }

    public function file()
    {
        return $this->getVar('file');
    }

    public function setFile($value)
    {
        return $this->setVar('file', $value);
    }

    public function name()
    {
        return $this->getVar('name');
    }

    public function setName($value)
    {
        return $this->setVar('name', $value);
    }

    public function mime()
    {
        return $this->getVar('mimetype');
    }

    public function setMime($value)
    {
        return $this->setVar('mimetype', $value);
    }

    public function date()
    {
        return $this->getVar('date');
    }

    public function setDate($value)
    {
        return $this->setVar('date', $value);
    }

    public function size()
    {
        return @filesize($this->dir . '/' . $this->file());
    }

    /**
     * @desc Obtiene la URL del icono correcto para el nombre de archivo
     */
    public function getIcon()
    {
        if (file_exists(XOOPS_ROOT_PATH . '/modules/bxpress/images/ftypes/' . mb_strtolower($this->extension()) . '.png')) {
            return XOOPS_URL . '/modules/bxpress/images/ftypes/' . mb_strtolower($this->extension()) . '.png';
        }

        return XOOPS_URL . '/modules/bxpress/images/ftypes/default.png';
    }

    public function downloads()
    {
        return $this->getVar('downloads');
    }

    public function setDownloads($value)
    {
        return $this->setVar('downloads', $value);
    }

    public function addDownload()
    {
        $this->setDownloads($this->downloads() + 1);
    }

    public function extension()
    {
        $pos = mb_strrpos(mb_strtolower($this->file()), '.');
        if ($pos <= 0) {
            return;
        }

        return mb_substr($this->file(), $pos + 1);
    }

    public function save()
    {
        if ($this->isNew()) {
            return $this->saveToTable();
        }

        return $this->updateTable();
    }

    public function delete()
    {
        @unlink($this->dir . '/' . $this->file());

        return $this->deleteFromTable();
    }
}
