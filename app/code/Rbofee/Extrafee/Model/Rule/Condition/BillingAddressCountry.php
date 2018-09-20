<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Model\Rule\Condition;

use Magento\Framework\Model\AbstractModel;

class BillingAddressCountry extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $country;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $value = '';
        try {
            $value = $this->getValueElementHtml();
        } catch (\Exception $e) {
            /**
             * if exception catch, than skip element
             */
        }
        return $this->getTypeElementHtml()
            . __(sprintf(__('Billing Address Country') . ' %s %s', $this->getOperatorElementHtml(), $value))
            . $this->getRemoveLinkHtml();
    }

    /**
     * @param AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        if (!$model->getSameAsBilling()) {
            $model->setData('billing_country', $model->getQuote()->getBillingAddress()->getCountryId());
        } else {
            $model->setData('billing_country', $model->getCountryId());
        }
        $this->setAttribute('billing_country');

        return parent::validate($model);
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData('value_select_options', $this->country->toOptionArray());
        }

        return $this->getData('value_select_options');
    }
}
