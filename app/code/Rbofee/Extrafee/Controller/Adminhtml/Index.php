<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Controller\Adminhtml;

/**
 * Class Index
 *
 * @author Rbo Developer
 */

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Rbofee\Extrafee\Controller\RegistryConstants;
use Rbofee\Extrafee\Model\FeeRepository;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;
use Rbofee\Extrafee\Model\FeeFactory;
use Magento\Ui\Component\MassAction\Filter;
use Rbofee\Extrafee\Model\ResourceModel\Fee\CollectionFactory as FeeCollectionFactory;

abstract class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Rbofee_Extrafee::manage';

    /** @var ForwardFactory  */
    protected $_resultForwardFactory;

    /** @var PageFactory  */
    protected $_resultPageFactory;

    /** @var Registry  */
    protected $_coreRegistry;

    /** @var FeeRepository  */
    protected $_feeRepository;

    /** @var FeeFactory  */
    protected $_feeFactory;

    /** @var Filter  */
    protected $_filter;

    /** @var FeeCollectionFactory  */
    protected $_feeCollectionFactory;

    /**
     * @var \Rbofee\Base\Model\Serializer
     */
    protected $serializer;

    /**
     * @param Action\Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param FeeRepository $feeRepository
     * @param FeeFactory $feeFactory
     * @param Filter $filter
     * @param FeeCollectionFactory $feeCollectionFactory,
     * @param \Rbofee\Base\Model\Serializer $serializer
     */
    public function __construct(
        Action\Context $context,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        FeeRepository $feeRepository,
        FeeFactory $feeFactory,
        Filter $filter,
        FeeCollectionFactory $feeCollectionFactory,
        \Rbofee\Base\Model\Serializer $serializer
    ){
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_feeRepository = $feeRepository;
        $this->_feeFactory = $feeFactory;
        $this->_filter = $filter;
        $this->_feeCollectionFactory = $feeCollectionFactory;
        $this->serializer = $serializer;
        return parent::__construct($context);
    }

    /**
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface|\Rbofee\Extrafee\Model\Fee
     * @throws \Rbofee\Extrafee\Model\NoSuchEntityException
     */
    protected function initCurrentFee()
    {
        $feeId = $this->getRequest()->getParam('id');
        $fee = $this->_feeRepository->create();
        if ($feeId) {
            $fee = $this->_feeRepository->getById($feeId);
        }
        $this->_coreRegistry->register(RegistryConstants::FEE, $fee);
        return $fee;
    }

    /**
     * @param Page $resultPage
     */
    protected function prepareDefaultTitle(Page $resultPage)
    {
        $resultPage->getConfig()->getTitle()->prepend(__('Fees'));
    }
}