<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Block\Adminhtml\Fee\Edit;

/**
 * Class GenericButton
 *
 * @author Rbo Developer
 */

use Magento\Backend\Block\Widget\Context;
use Rbofee\Extrafee\Api\FeeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var FeeRepositoryInterface
     */
    protected $feeRepository;

    /**
     * @param Context $context
     * @param FeeRepositoryInterface $feeRepository
     */
    public function __construct(
        Context $context,
        FeeRepositoryInterface $feeRepository
    ) {
        $this->context = $context;
        $this->feeRepository = $feeRepository;
    }

    /**
     * @return int|null
     */
    public function getFeeId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}