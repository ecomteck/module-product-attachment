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

use Ecomteck\ProductAttachment\Api\Api;
use Ecomteck\ProductAttachment\Api\ProductAttachment;
use Ecomteck\ProductAttachment\Api\Data;
use Magento\Framework\Exception\NotFoundException;

class ProductAttachmentWebApi implements \Ecomteck\ProductAttachment\Api\ProductAttachmentInterface
{
    /**
     * @var \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment
     */
    protected $_productAttachment;

    /**
     * @var \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\CollectionFactory
     */
    protected $_productAttachmentCollectionFactory;

    /**
     * @var \Ecomteck\ProductAttachment\Api\Data\ProductAttachmentTableInterface
     */
    protected $_productAttachmentTableInterface;

    /**
     * @var Data\ProductAttachmentTableInterfaceFactory
     */
    protected $_productAttachmentTableInterfaceFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * @var \Ecomteck\ProductAttachment\Helper\Data
     */
    protected $_dataHelper;

    /**
     * ProductAttachmentWebApi constructor.
     * @param ResourceModel\ProductAttachment $productAttachment
     * @param ProductAttachmentTableFactory $productAttachmentCollectionFactory
     * @param Data\ProductAttachmentTableInterface $productAttachmentTableInterface
     * @param Data\ProductAttachmentTableInterfaceFactory $productAttachmentTableInterfaceFactory
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Ecomteck\ProductAttachment\Helper\Data $dataHelper
     */
    public  function __construct(
        \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment $productAttachment,
        \Ecomteck\ProductAttachment\Model\ProductAttachmentTableFactory $productAttachmentCollectionFactory,
        \Ecomteck\ProductAttachment\Api\Data\ProductAttachmentTableInterface $productAttachmentTableInterface,
        \Ecomteck\ProductAttachment\Api\Data\ProductAttachmentTableInterfaceFactory $productAttachmentTableInterfaceFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Ecomteck\ProductAttachment\Helper\Data $dataHelper
    ) {
        $this->_productAttachment = $productAttachment;
        $this->_productAttachmentCollectionFactory = $productAttachmentCollectionFactory;
        $this->_productAttachmentTableInterface = $productAttachmentTableInterface;
        $this->_productAttachmentTableInterfaceFactory = $productAttachmentTableInterfaceFactory;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @param Data\ProductAttachmentTableInterface $productAttachmentTableInterface
     * @param $fileName
     * @param $fileContent
     * @return mixed
     * @throws \Exception
     */
    public function UpdateInsertAttachment(
        \Ecomteck\ProductAttachment\Api\Data\ProductAttachmentTableInterface $productAttachmentTableInterface,
        $fileName,
        $fileContent
    ) {
        $objectArray = $productAttachmentTableInterface->getData();

        $id = $productAttachmentTableInterface->getId();
        if($id == 0)
            $objectArray["productattach_id"] = null;

        if(array_key_exists('products', $objectArray)) {
            $productIds = $objectArray['products'];
            $objectArray['products'] = str_replace(',', '&', $productIds);
        }

        $attachment = $this->_productAttachmentCollectionFactory->create();
        $attachment->setData($objectArray);

        $this->_productAttachment->load($attachment, $id);

        if($attachment->isObjectNew() == false)
        {
            //UPDATE ATTACHMENT RECORD
            if(array_key_exists('name', $objectArray))
                $attachment->setName($objectArray['name']);
            if(array_key_exists('description', $objectArray))
                $attachment->setDescription($objectArray['description']);
            if(array_key_exists('url', $objectArray))
                $attachment->setUrl($objectArray['url']);
            if(array_key_exists('products', $objectArray))
                $attachment->setProducts($objectArray['products']);
            if(array_key_exists('customer_group', $objectArray))
                $attachment->setCustomerGroup($objectArray['customer_group']);
            if(array_key_exists('store', $objectArray))
                $attachment->setStore($objectArray['store']);
            if(array_key_exists('active', $objectArray))
                $attachment->setActive($objectArray['active']);
        }

        //check if file already exists on the file system
        if($fileContent){
            //this is a new file or an updated version of it => check if file already exists on the system
            if($this->_dataHelper->checkIfFileExists($fileName)) {
                //delete file
                $this->_dataHelper->deleteFile($this->_dataHelper->getFileDispersionPath($fileName)."/".$fileName);
            }
            //create file
            if(!$this->_dataHelper->saveFile($fileName, $fileContent)){
                return -1;
            } else {
                //update file path
                $attachment->setFile( $this->_dataHelper->getFilePathForDB($fileName) );

                $fileExt = "";
                $slicedFileName = explode('.', $fileName);
                if(count($slicedFileName) > 1){
                    $fileExt = $slicedFileName[count($slicedFileName)-1];
                }
                $attachment->setFileExt($fileExt);
            }
        } else {
            $attachment->setFileExt('');
        }

        //save attachment record
        $this->_productAttachment->save($attachment);

        //return the id of the create/updated record
        return $attachment->getId();
    }

    /**
     * @param int $int
     * @return bool
     * @throws \Exception
     */
    public function DeleteAttachment(
        $int
    ) {
        //delete DB record
        $attachment = $this->_productAttachmentCollectionFactory->create();
        $this->_productAttachment->load($attachment, $int);
        if(!$attachment->getId())
            return false;
        $this->_productAttachment->delete($attachment);

        //check if this is the last record from the DB linked to this file => if it's true, than delete this file
        /** @var \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\Collection $collection */
        $collection = $this->_productAttachmentCollectionFactory->create()->getCollection();
        $collection->addFieldToFilter('file', $attachment->getData("file"));
        if($collection->count() == 0){
            //delete file on the file system
            $this->_dataHelper->deleteFile($attachment->getData("file"));
        }

        return true;
    }

    /**
     * @param int $int
     * @return ProductAttachmentTable
     * @throws NotFoundException
     */
    public function GetAttachment(
        $int
    ) {
        $attachment = $this->_productAttachmentCollectionFactory->create();
        $this->_productAttachment->load($attachment, $int);
        if(!$attachment->getId()) {
            throw new \Magento\Framework\Exception\NotFoundException(
                __('no attachment found')
            );
        }
        $attachResponse = $this->_productAttachmentCollectionFactory->create();
        if($attachment->getData()) {
            $attachResponse->setProductAttachId($attachment->getId());
            $attachResponse->setName($attachment->getName());
            $attachResponse->setDescription($attachment->getDescription());
            $attachResponse->setFile($attachment->getFile());
            $attachResponse->setUrl($attachment->getUrl());
            $attachResponse->setStore($attachment->getStore());
            $attachResponse->setCustomerGroup($attachment->getCustomerGroup());
            $attachResponse->setProducts($attachment->getProducts());
            $attachResponse->setActive($attachment->getActive());
        }

        return $attachResponse;
    }

    const CUSTOM_PATH = "custom/upload";
}
