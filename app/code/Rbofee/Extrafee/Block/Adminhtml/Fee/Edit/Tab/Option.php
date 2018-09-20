<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Block\Adminhtml\Fee\Edit\Tab;

/**
 * Class Option
 *
 * @author Rbo Developer
 */

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Rbofee\Extrafee\Controller\RegistryConstants;

class Option extends Generic implements TabInterface
{
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Options');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        /** @var \Rbofee\Extrafee\Model\Fee $model */
        $model = $this->_coreRegistry->registry(RegistryConstants::FEE);

        $fieldset = $form->addFieldset(
            'option_fieldset',
            ['legend' => __('Options'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'options',
            'text',
            [
                'name' => 'options',
                'label' => __('Options'),
                'title' => __('Options')
            ]
        );

        $form->getElement(
            'options'
        )->setRenderer(
            $this->getLayout()
                ->createBlock('Rbofee\Extrafee\Block\Adminhtml\Fee\Edit\Tab\Option\Field')
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}