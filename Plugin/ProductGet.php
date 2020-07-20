<?php

namespace Ecomteck\ProductAttachment\Plugin;

/**
 * Class ProductGet
 * @package Ecomteck\ProductAttachment\Plugin
 */
class ProductGet
{
    /**
     * @var \Magento\Catalog\Api\Data\ProductExtensionFactory
     */
    protected $productExtensionFactory;

    /**
     * @var \Ecomteck\ProductAttachment\Block\Attachment
     */
    protected $productAttachment;

    /**
     * ProductGet constructor.
     * @param \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory
     * @param \Ecomteck\ProductAttachment\Block\Attachment $productAttachment
     */
    public function __construct(
        \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
        \Ecomteck\ProductAttachment\Block\Attachment $productAttachment
    ) {
        $this->productExtensionFactory = $productExtensionFactory;
        $this->productAttachment = $productAttachment;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ) {
        if(!$product->getExtensionAttributes()) {
            $productExtension = $this->productExtensionFactory->create();
            $product->setExtensionAttributes($productExtension);
        }
        $extensionAttributes = $product->getExtensionAttributes();

        $attachmentIds = [];
        $attachments = $this->productAttachment->getAttachment($product->getId());
        foreach ($attachments as $attachment) {
            $attachmentIds[] = $attachment->getId();
        }
        $extensionAttributes->setAttachments($attachmentIds);
        return $product;
    }
}
