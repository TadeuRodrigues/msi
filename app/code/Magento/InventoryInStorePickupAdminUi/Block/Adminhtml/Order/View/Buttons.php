<?php

declare(strict_types=1);

namespace Magento\InventoryInStorePickupAdminUi\Block\Adminhtml\Order\View;
use Magento\Sales\Block\Adminhtml\Order\View;

class Buttons extends View
{
    const pickup_method = "flatrate_flatrate";

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $salesConfig,
            $reorderHelper,
            $data
        );
    }

    public function getPickupUrl()
    {
        return $this->getUrl('shipping/pickup/ready');
    }

    protected function _isPickupShippingMethod()
    {
        return $this->getOrder()->getShippingMethod() == self::pickup_method;
    }

    protected function addReadyForPickupButton()
    {
        $this->getToolbar()->addChild(
            'order_pickup',
            \Magento\Backend\Block\Widget\Button::class,
            [
                'label' => __('Ready for Pickup'),
                'onclick' => 'setLocation(\'' . $this->getPickupUrl() . '\')'
            ]
        );
    }

    public function addButtons(){
        // DEVE TESTAR SE TEM AUTORIZACAO/ESTA FATORADO/METODO DE ENTREGA PICKUP/NAO ESTA NO ESTADO COMPLETO
//        $this->_authorization->isAllowed('Magento_SalesArchive::add')
//        var_dump($this->getOrder()->getInvoiceCollection());
//        if ($this->getOrder()->getShippingMethod() != self::pickup_method) {
//            return;
//        }

        $order = $this->getOrder();

        // remove o buttton de ship
//        $this->removeButton('order_ship');

        if (
            $this->_isAllowedAction('Magento_InventoryInStorePickup::ship')
            && $order->canShip()
            && $this->_isPickupShippingMethod()
        ) {
            $this->addReadyForPickupButton();
        }
    }
}