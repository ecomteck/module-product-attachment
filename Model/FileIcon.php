<?php

/**
 * Ecomteck
 * Copyright (C) 2018 Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category Ecomteck
 * @package Ecomteck_ProductAttachment
 * @copyright Copyright (c) 2018 Ecomteck
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Ecomteck
 */

namespace Ecomteck\ProductAttachment\Model;

use Ecomteck\ProductAttachment\Api\Data\FileIconInterface;

class FileIcon extends \Magento\Framework\Model\AbstractModel implements FileIconInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ecomteck\ProductAttachment\Model\ResourceModel\FileIcon');
    }

    /**
     * Get fileicon_id
     * @return string
     */
    public function getFileIconId()
    {
        return $this->getData(self::FILE_ICON_ID);
    }

    /**
     * Set fileicon_id
     * @param string $fileIconId
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     */
    public function setFileIconId($fileIconId)
    {
        return $this->setData(self::FILE_ICON_ID, $fileIconId);
    }

    /**
     * Get icon_ext
     * @return string
     */
    public function getIconExt()
    {
        return $this->getData(self::ICON_EXT);
    }

    /**
     * Set icon_ext
     * @param string $icon_ext
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     */
    public function setIconExt($icon_ext)
    {
        return $this->setData(self::ICON_EXT, $icon_ext);
    }

    /**
     * Get icon_image
     * @return string
     */
    public function getIconImage()
    {
        return $this->getData(self::ICON_IMAGE);
    }

    /**
     * Set icon_image
     * @param string $icon_image
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     */
    public function setIconImage($icon_image)
    {
        return $this->setData(self::ICON_IMAGE, $icon_image);
    }
}
