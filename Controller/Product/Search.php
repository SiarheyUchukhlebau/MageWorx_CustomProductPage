<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomProductPage\Controller\Product;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Search
 */
class Search extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * Search constructor.
     *
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder
    ) {
        parent::__construct($context);
        $this->productRepository            = $productRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder                = $filterBuilder;
        $this->filterGroupBuilder           = $filterGroupBuilder;
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
        try {
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

            $filters = [
                $this->filterBuilder->setField('available_shipping_methods')
                                    ->setValue('%shipping_shipping%')
                                    ->setConditionType('like')
                                    ->create(),
                $this->filterBuilder->setField('available_shipping_methods')
                                    ->setValue('%flatrate_flatrate%')
                                    ->setConditionType('like')
                                    ->create(),
                $this->filterBuilder->setField('available_shipping_methods')
                                    ->setValue(null)
                                    ->setConditionType('null')
                                    ->create()
            ];

            $filterGroup = $this->filterGroupBuilder->setFilters($filters)
                                                    ->create();

            $searchCriteriaBuilder->setFilterGroups([$filterGroup]);
            $searchCriteria = $searchCriteriaBuilder->create();

            $productList = $this->productRepository->getList($searchCriteria);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultPage = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);

        return $resultPage;
    }
}
