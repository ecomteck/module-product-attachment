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

namespace Ecomteck\ProductAttachment\Api\Data;

interface FileIconInterface
{

    const ICON_IMAGE = 'icon_image';
    const FILE_ICON_ID = 'fileicon_id';
    const ICON_EXT = 'icon_ext';


    /**
     * Get file icon id
     * @return string|null
     */
    public function getFileIconId();

    /**
     * Set file icon id
     * @param string $fileIconId
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     */
    public function setFileIconId($fileIconId);

    /**
     * Get icon_ext
     * @return string|null
     */
    public function getIconExt();

    /**
     * Set icon_ext
     * @param string $icon_ext
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     */
    public function setIconExt($icon_ext);

    /**
     * Get icon_image
     * @return string|null
     */
    public function getIconImage();

    /**
     * Set icon_image
     * @param string $icon_image
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     */
    public function setIconImage($icon_image);
}
