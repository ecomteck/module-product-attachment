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

namespace Ecomteck\ProductAttachment\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FileIconRepositoryInterface
{


    /**
     * Save Fileicon
     * @param \Ecomteck\ProductAttachment\Api\Data\FileIconInterface $fileIcon
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Ecomteck\ProductAttachment\Api\Data\FileIconInterface $fileIcon
    );

    /**
     * Retrieve Fileicon
     * @param string $fileIconId
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($fileIconId);

    /**
     * Retrieve Fileicon matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ecomteck\ProductAttachment\Api\Data\FileIconSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Fileicon
     * @param \Ecomteck\ProductAttachment\Api\Data\FileIconInterface $fileIcon
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Ecomteck\ProductAttachment\Api\Data\FileIconInterface $fileIcon
    );

    /**
     * Delete Fileicon by ID
     * @param string $fileIconId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fileIconId);
}
