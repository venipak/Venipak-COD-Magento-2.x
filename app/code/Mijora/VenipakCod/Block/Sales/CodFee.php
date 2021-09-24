<?php
namespace Mijora\VenipakCod\Block\Sales;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CodFee extends Template {
    public function displayFullSummary(){
        return true;
    }

    public function initTotals(){
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        $payment = $this->getPayment($source);
        $order = $this->getOrder($source);
        if ($payment && ($payment->getMethod() == 'venipak_cod')) {
            $fee = new DataObject(
                [
                    'code' => 'venipak_cod_fee',
                    'strong' => false,
                    'value' => $order->getVenipakCodFee(),
                    'label' => __('Cash on delivery fee'),
                ]
            );

            $parent->addTotalBefore($fee, 'grand_total');
        }

        return $this;
    }

    protected function getPayment($source){
        if ($source instanceof InvoiceInterface) {
            return $source->getOrder()->getPayment();
        }

        if ($source instanceof OrderInterface) {
            return $source->getPayment();
        }

        if ($source instanceof CreditMemoInterface) {
            return $source->getOrder()->getPayment();
        }

        return null;
    }
    
    protected function getOrder($source){
        if ($source instanceof InvoiceInterface) {
            return $source->getOrder();
        }

        if ($source instanceof OrderInterface) {
            return $source;
        }

        if ($source instanceof CreditMemoInterface) {
            return $source->getOrder();
        }

        return null;
    }
}