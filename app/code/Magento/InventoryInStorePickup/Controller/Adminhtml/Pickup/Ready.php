<?php

declare(strict_types=1);

namespace Magento\InventoryInStorePickup\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterfaceFactory;
use Magento\Framework\Controller\ResultFactory;

use Magento\InventoryApi\Api\SourceItemRepositoryInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;

/** usando criteria */
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrderBuilder;


use Magento\InventoryShipping\Model\InventoryRequestFromOrderFactory;
use Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterfaceFactory;

class Ready extends Action
{
    const ADMIN_RESOURCE = 'Magento_InventoryInStorePickup::ship';

    protected $orderRepository;

    protected $sourceItemRepository;

    protected $searchCriteria;

//    criteria
    protected $searchCriteriaBuilder;

    protected $filterBuilder;

    protected $filterGroup;

    protected $sortOrderBuilder;


    protected $inventoryRequestFromOrder;
    protected $sourceSelectionService;

    public function __construct(
        Context $context,
        OrderRepositoryInterfaceFactory $orderRepository,
        SourceItemRepositoryInterfaceFactory $sourceItemRepository,
        SearchCriteriaInterfaceFactory $searchCriteria,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroup $filterGroup,
        SortOrderBuilder $sortOrderBuilder,

        InventoryRequestFromOrderFactory $inventoryRequestFromOrder,
        SourceSelectionServiceInterfaceFactory $sourceSelectionService
    ) {
        $this->orderRepository = $orderRepository;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->searchCriteria = $searchCriteria;

        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroup = $filterGroup;
        $this->sortOrderBuilder = $sortOrderBuilder;

        $this->inventoryRequestFromOrder = $inventoryRequestFromOrder;
        $this->sourceSelectionService = $sourceSelectionService;
        parent::__construct($context);
    }

    protected function checkSourceIsEnough($orderId)
    {
        $order = $this->orderRepository->create()->get($orderId);

        $filterName = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $filterName[] = $this->filterBuilder
                ->setField("sku")
                ->setConditionType("eq")
                ->setValue($item->getSku())
                ->create();

            $filterName[] = $this->filterBuilder
                ->setField("quantity")
                ->setConditionType("gteq")
                ->setValue($item->getQtyOrdered())
                ->create();
        }


        $filterGroupName = $this->filterGroup;

        $filterGroupName->setFilters($filterName);

//        $sortOrder = $this->sortOrderBuilder->setField("id")->setDirection("ASC")->create();
//        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        $criteria = $this->searchCriteriaBuilder->setFilterGroups([$filterGroupName]);

        $repository = $this->sourceItemRepository->create();
        $list = $repository->getList($criteria->create());

        foreach ($list->getItems() as $item) {
//            var_dump($item->getSku());
        }

        $test = $this->inventoryRequestFromOrder->create($order);
        $exemplo = $this->sourceSelectionService->create()->execute($test, "priority");

//        var_dump($order->getData());
//        var_dump($test);
//        var_dump($exemplo);

        foreach ($exemplo->getSourceSelectionItems() as $item) {
            var_dump($item->getSourceCode());
            var_dump($item->getSku());
            var_dump($item->getQtyToDeduct());
            var_dump($item->getQtyAvailable());
        }
        die;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($orderId) {
//            $this->_archiveModel->removeOrdersFromArchiveById($orderId);
            $this->checkSourceIsEnough($orderId);
            $this->messageManager->addSuccessMessage(__('Order can be pick up.'));
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
//            $resultRedirect->setPath('adminhtml/order_shipment/start', ['order_id' => $orderId]);
        } else {
            $this->messageManager->addErrorMessage(__('Please specify the order ID to be pick up.'));
            $resultRedirect->setPath('sales/order');
        }

        return $resultRedirect;
    }
}
