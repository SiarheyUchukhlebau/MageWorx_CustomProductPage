<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomProductPage\Controller\Product;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Api\CartRepositoryInterface;
use MageWorx\DeliveryDate\Api\QueueManagerInterface;
use MageWorx\DeliveryDate\Api\Data\QueueDataInterface;
use MageWorx\DeliveryDate\Api\Repository\QueueRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 */
class Index extends Action
{

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->productRepository = $productRepository;
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $productId = 2085;
        $this->_request->setParams(['id' => $productId]);
        try {
            $product = $this->productRepository->getById($productId);
            $this->registry->register('current_product', $product);
            $this->registry->register('product', $product);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to locate product %1', $productId));
        }

        $resultPage = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);

        return $resultPage;
    }
}