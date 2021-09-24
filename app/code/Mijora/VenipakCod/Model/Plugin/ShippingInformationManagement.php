<?php
namespace Mijora\VenipakCod\Model\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    private $fee;
    private $feeFixed;
    private $feePercent;
    private $feeType;
    private $freeFrom;
    private $freeCod;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Sivajik34\CustomFee\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->feeType = (string)$scopeConfig->getValue('payment/venipak_cod/cod_fee_type', ScopeInterface::SCOPE_STORE);
        $this->feeFixed = (float)$scopeConfig->getValue('payment/venipak_cod/cod_amount_fixed', ScopeInterface::SCOPE_STORE);
        $this->feePercent = (float)$scopeConfig->getValue('payment/venipak_cod/cod_amount_percent', ScopeInterface::SCOPE_STORE);
        $this->freeFrom = (float)$scopeConfig->getValue('payment/venipak_cod/free_cod_amount', ScopeInterface::SCOPE_STORE);
        $this->freeCod = (float)$scopeConfig->getValue('payment/venipak_cod/free_cod', ScopeInterface::SCOPE_STORE);
        $this->fee = null;
        
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        
        $quote = $this->quoteRepository->getActive($cartId);
        $shipping_price = $quote->getShippingAddress()->getShippingAmount();
        $subtotal_price = $quote->getSubtotal();
        $total_price = $shipping_price + $subtotal_price;
        if ($this->feeType == "fixed"){
            $this->fee = (float)$this->feeFixed;
        }
        if ($this->feeType == "percent"){
            $this->fee = (float)($total_price*$this->feePercent/100);
        }
        if ($this->freeCod && $this->freeFrom && $this->freeFrom <= $total_price){
            $this->fee = 0;
        }
        $quote->setVenipakCodFee($this->fee);
    }
}