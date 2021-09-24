<?php
namespace Mijora\VenipakCod\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;

class Totals extends AbstractTotal
{

    const TOTAL_CODE = 'venipak_cod_fee';
    
    const BASE_TOTAL_CODE = 'base_venipak_cod_fee';

    const LABEL = 'Cash On Delivery Fee';

    /**
     * @var float
     */
    private $fee;
    private $feeFixed;
    private $feePercent;
    private $feeType;
    private $baseCurrency;
    private $paymentMethodManagement;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        PaymentMethodManagementInterface $paymentMethodManagement
    )
    {
        $this->feeType = (float)$scopeConfig->getValue('payment/venipak_cod/cod_fee_type', ScopeInterface::SCOPE_STORE);
        $this->feeFixed = (float)$scopeConfig->getValue('payment/venipak_cod/cod_amount_fixed', ScopeInterface::SCOPE_STORE);
        $this->feePercent = (float)$scopeConfig->getValue('payment/venipak_cod/cod_amount_percent', ScopeInterface::SCOPE_STORE);
        $this->fee = (float)null;
        $currencyCode = $scopeConfig->getValue("currency/options/base", ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $this->baseCurrency =  $currencyFactory->create()->load($currencyCode);
        $this->paymentMethodManagement = $paymentMethodManagement;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface
        $shippingAssignment,
        Total $total
    ): Totals {
        parent::collect($quote, $shippingAssignment, $total);

        if (count($shippingAssignment->getItems()) == 0) {
            return $this;
        }

        $baseCashOnDeliveryFee = $this->getFee($quote);
        $currency = $quote->getStore()->getCurrentCurrency();
        $cashOnDeliveryFee = $this->baseCurrency->convert($baseCashOnDeliveryFee, $currency);
        
        if ($this->canAddFee($quote)) {
            $total->setData(static::TOTAL_CODE, $cashOnDeliveryFee);
            $total->setData(static::BASE_TOTAL_CODE, $baseCashOnDeliveryFee);

            $total->setTotalAmount(static::TOTAL_CODE, $cashOnDeliveryFee);
            $total->setBaseTotalAmount(static::TOTAL_CODE, $baseCashOnDeliveryFee);
        }
        return $this;
    }

    public function fetch(Quote $quote, Total $total){
        $base_value = $this->getFee($quote);
        
        if ($this->canAddFee($quote)) {
            $currency = $quote->getStore()->getCurrentCurrency();
            $value = $this->baseCurrency->convert($base_value, $currency);
        
            return [
                'code' => static::TOTAL_CODE,
                'title' => $this->getLabel(),
                'value' => $value
            ];
        } else {
            return [];
        }
    }
    
    public function canAddFee(Quote $quote){
        if (!$quote->getId()) {
            return false;
        }
        //return true;
        /*
        $paymentMethodsList = $this->paymentMethodManagement->getList($quote->getId());
        if ((count($paymentMethodsList) == 1) && (current($paymentMethodsList)->getCode() == 'venipak_cod')) {
            return true;
        }
         */
        return ($quote->getPayment()->getMethod() == 'venipak_cod');
    }

    public function getLabel() {
        return __(static::LABEL);
    }

    private function getFee(Quote $quote){
        $this->fee = $quote->getVenipakCodFee();

        return $this->fee;
    }
}