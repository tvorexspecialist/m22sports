<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Model\Rule\Condition;

class PaymentMethod extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @var \Magento\Payment\Model\Config\Source\Allmethods
     */
    private $allMethods;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Payment\Model\Config\Source\Allmethods $allMethods,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->allMethods = $allMethods;
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
            . __(sprintf(__('Payment Method') . ' %s %s', $this->getOperatorElementHtml(), $value))
            . $this->getRemoveLinkHtml();
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $this->setAttribute('payment_method');

        return parent::validate($model);
    }

    /**
     * @return mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData('value_select_options', $this->allMethods->toOptionArray());
        }

        return $this->getData('value_select_options');
    }
}
