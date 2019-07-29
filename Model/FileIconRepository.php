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

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Ecomteck\ProductAttachment\Model\ResourceModel\FileIcon as ResourceFileIcon;
use Magento\Framework\Reflection\DataObjectProcessor;
use Ecomteck\ProductAttachment\Api\Data\FileIconSearchResultsInterfaceFactory;
use Ecomteck\ProductAttachment\Model\ResourceModel\FileIcon\CollectionFactory as FileIconCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Ecomteck\ProductAttachment\Api\FileIconRepositoryInterface;
use Ecomteck\ProductAttachment\Api\Data\FileIconInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;

class FileIconRepository implements fileiconRepositoryInterface
{

    private $storeManager;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $searchResultsFactory;

    protected $resource;

    protected $fileIconCollectionFactory;

    protected $fileIconFactory;

    protected $dataFileIconFactory;


    /**
     * @param ResourceFileIcon $resource
     * @param FileIconFactory $fileIconFactory
     * @param FileIconInterfaceFactory $dataFileIconFactory
     * @param FileIconCollectionFactory $fileIconCollectionFactory
     * @param FileIconSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceFileIcon $resource,
        FileIconFactory $fileIconFactory,
        FileIconInterfaceFactory $dataFileIconFactory,
        FileIconCollectionFactory $fileIconCollectionFactory,
        FileIconSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->fileIconFactory = $fileIconFactory;
        $this->fileIconCollectionFactory = $fileIconCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFileIconFactory = $dataFileIconFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Ecomteck\ProductAttachment\Api\Data\FileIconInterface $fileIcon
    ) {
        /* if (empty($fileIcon->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $fileIcon->setStoreId($storeId);
        } */
        try {
            $fileIcon->getResource()->save($fileIcon);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the file icon: %1',
                $exception->getMessage()
            ));
        }
        return $fileIcon;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fileIconId)
    {
        $fileIcon = $this->fileIconFactory->create();
        $fileIcon->getResource()->load($fileIcon, $fileIconId);
        if (!$fileIcon->getId()) {
            throw new NoSuchEntityException(__('File icon with id "%1" does not exist.', $fileIconId));
        }
        return $fileIcon;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->fileIconCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Ecomteck\ProductAttachment\Api\Data\FileIconInterface $fileIcon
    ) {
        try {
            $fileIcon->getResource()->delete($fileIcon);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the File icon: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fileIconId)
    {
        return $this->delete($this->getById($fileIconId));
    }
}
