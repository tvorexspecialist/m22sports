<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Model\Rule\Condition;

use Magento\Framework\Model\AbstractModel;

class ShippingAddressLine extends \Magento\Rule\Model\Condition\AbstractCondition
{

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'string';
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
            . __(sprintf(__('Shipping Address Line') . ' %s %s', $this->getOperatorElementHtml(), $value))
            . $this->getRemoveLinkHtml();
    }

    /**
     * @param AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $model->setData('dest_street', $model->getStreetFull());
        $this->setAttribute('dest_street');

        return parent::validate($model);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOperatorSelectOptions()
    {
        $operators = [
            '{}' => __('contains'),
            '!{}' => __('does not contain'),
        ];
        $type = $this->getInputType();
        $result = [];
        $operatorByType = $this->getOperatorByInputType();
        foreach ($operators as $operatorKey => $operatorValue) {
            if (!$operatorByType || in_array($operatorKey, $operatorByType[$type])) {
                $result[] = ['value' => $operatorKey, 'label' => $operatorValue];
            }
        }

        return $result;
    }
}
