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

namespace Ecomteck\ProductAttachment\Block;

/**
 * Class Attachment
 * @package Ecomteck\ProductAttachment\Block
 */
class Attachment extends \Magento\Framework\View\Element\Template
{
    /**
     * ProductAttachment collection
     *
     * @var \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\Collection
     */
    private $productattachCollection = null;
    
    /**
     * ProductAttachment factory
     *
     * @var \Ecomteck\ProductAttachment\Model\ProductAttachmentFactory
     */
    private $productattachCollectionFactory;

    /**
     * FileIcon factory
     *
     * @var \Ecomteck\ProductAttachment\Model\FileIconFactory
     */
    private $fileIconCollectionFactory;
    
    /**
     * @var \Ecomteck\ProductAttachment\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    private $orderModel;

    /**
     * Attachment constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\CollectionFactory $productattachCollectionFactory
     * @param \Ecomteck\ProductAttachment\Model\ResourceModel\FileIcon\CollectionFactory $fileIconCollectionFactory
     * @param \Ecomteck\ProductAttachment\Helper\Data $dataHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order $orderModel,
        \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\CollectionFactory $productattachCollectionFactory,
        \Ecomteck\ProductAttachment\Model\ResourceModel\FileIcon\CollectionFactory $fileIconCollectionFactory,
        \Ecomteck\ProductAttachment\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->customerSession =$customerSession;
        $this->orderModel = $orderModel;
        $this->productattachCollectionFactory = $productattachCollectionFactory;
        $this->fileIconCollectionFactory = $fileIconCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->registry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Check module is enable or not
     */
    public function isEnable()
    {
        return $this->getConfig('productattachment/general/enable');
    }

    public function fileExists($attachment)
    {
        try {
            $this->getFileSize($attachment);
            return true;
        } catch (\Magento\Framework\Exception\FileSystemException $e) {

        }

        return false;
    }

    /**
     * Retrieve productattach collection
     *
     * @return \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\Collection
     */
    public function getCollection()
    {
        $collection = $this->productattachCollectionFactory->create();
        return $collection;
    }

    /**
     * Filter productattach collection by product Id
     *
     * @param $productId
     * @return \Ecomteck\ProductAttachment\Model\ResourceModel\ProductAttachment\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttachment($productId)
    {
        $isBought = false;
        $customerId = $this->customerSession->getCustomerId();
        $orders = $this->orderModel->getCollection()
            ->addFieldToFilter("customer_id", $customerId)
            ->addFieldToFilter("status", \Magento\Sales\Model\Order::STATE_COMPLETE);

        foreach ($orders as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                if($item->getProductId() == $productId){
                    $isBought = true;
                    break;
                }
            }
        }

        $collection = $this->getCollection();
        
        $collection->addFieldToFilter(
            'customer_group',
            [
                ['null' => true],
                ['finset' => $this->getCustomerId()]
            ]
        );

        $collection->addFieldToFilter(
            'store',
            [
                ['eq' => 0],
                ['finset' => $this->dataHelper->getStoreId()]
            ]
        );

        $collection->getSelect()->where(
            "FIND_IN_SET(".$productId.", replace(products , '&', ',')) > 0"
        );

        if(!$isBought){
            $collection->addFieldToFilter(
                'visible_scope',
                [
                    ['eq' => 1],
                ]
            );
        }

        return $collection;
    }

    /**
     * Retrive attachment url by attachment
     *
     * @param $attachment
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttachmentUrl($attachment)
    {
        $url = $this->dataHelper->getBaseUrl().$attachment;
        return $url;
    }

    /**
     * Retrive current product id
     *
     * @return number
     */
    public function getCurrentId()
    {
        $product = $this->registry->registry('current_product');
        return $product->getId();
    }

    /**
     * Retrive current customer id
     *
     * @return number
     */
    public function getCustomerId()
    {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if(!$isLoggedIn) {
            return 0;
        }

        $customerId = $this->customerSession->getCustomer()->getGroupId();
        return $customerId;
    }

    /**
     * Retrieve file icon image
     *
     * @param string $fileExt
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFileIcon($fileExt)
    {
        $fileExt = \strtolower($fileExt);

        if ($fileExt) {
            $iconExt = $this->getIconExt($fileExt);
            if ($iconExt) {
                $mediaUrl = $this->dataHelper->getMediaUrl();
                $iconImage = $mediaUrl.'fileicon/tmp/icon/'.$iconExt;
            } else {
                $iconImage = $this->getViewFileUrl('Ecomteck_ProductAttachment::images/'.$fileExt.'.png');
            }
        } else {
            $iconImage = $this->getViewFileUrl('Ecomteck_ProductAttachment::images/unknown.png');
        }
        return $iconImage;
    }

    /**
     * Retrive icon ext name
     *
     * @param $fileExt
     * @return string
     */
    public function getIconExt($fileExt)
    {
        $iconCollection = $this->fileIconCollectionFactory->create();
        $iconCollection->addFieldToFilter('icon_ext',$fileExt);
        $icon = $iconCollection->getFirstItem()->getIconImage();
        return $icon;
    }

    /**
     * Retrive link icon image
     *
     * @return string
     */
    public function getLinkIcon()
    {
        $iconImage = $this->getViewFileUrl('Ecomteck_ProductAttachment::images/link.png');
        return $iconImage;
    }

    /**
     * Retrive file size by attachment
     *
     * @param $attachment
     * @return number
     */
    public function getFileSize($attachment)
    {
        $attachmentPath = \Ecomteck\ProductAttachment\Helper\Data::MEDIA_PATH.$attachment;
        $fileSize = $this->dataHelper->getFileSize($attachmentPath);
        return $fileSize;
    }

    /**
     * Retrive config value
     * @param $config
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrive Tab Name
     */
    public function getTabName()
    {
        $tabName = __($this->getConfig('productattachment/general/tabname'));
        return $tabName;
    }
}
