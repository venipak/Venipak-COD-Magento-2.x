<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mijora\VenipakCod\Model;

use Mijora\Venipak\Model\Carrier;

/**
 * Cash on delivery payment method model
 *
 * @method \Magento\Quote\Api\Data\PaymentMethodExtensionInterface getExtensionAttributes()
 *
 * @api
 * @since 100.0.2
 */
class Cod extends \Magento\Payment\Model\Method\AbstractMethod {

    const PAYMENT_METHOD_CASHONDELIVERY_CODE = 'venipak_cod';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_CASHONDELIVERY_CODE;

    /**
     * Cash On Delivery payment block paths
     *
     * @var string
     */
    //protected $_formBlockType = \Mijora\VenipakCod\Block\Form\Cod::class;

    /**
     * Info instructions block path
     *
     * @var string
     */
    // protected $_infoBlockType = \Magento\Payment\Block\Info\Instructions::class;;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
    protected $carrier = null;

    public function __construct(\Magento\Framework\Model\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
            \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
            \Magento\Payment\Helper\Data $paymentData,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            \Magento\Payment\Model\Method\Logger $logger,
            \Magento\Framework\Module\ModuleListInterface $moduleList,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
            Carrier $carrier,
            array $data = array()
    ) {
        parent::__construct(
                $context, $registry, $extensionFactory, $customAttributeFactory,
                $paymentData, $scopeConfig, $logger,  null,
                null, $data
        );
        $this->carrier = $carrier;
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null) {
        if (!parent::isAvailable($quote)) {
            return false;
        }

        if ($quote->getItemVirtualQty() > 0) {
            return false; //can't use this method if cart contains virtual products
        }
        //if pickpoint, check if it supports COD
        if (strtolower($quote->getShippingAddress()->getShippingMethod()) === "venipak_pickup_point") {
            $pickup_point = $this->carrier->getOrderPickup($quote);
            
            if ($pickup_point !== false && $pickup_point->cod_enabled){
                return true;
            }
            return false;
        }
        return true;
    }

}
