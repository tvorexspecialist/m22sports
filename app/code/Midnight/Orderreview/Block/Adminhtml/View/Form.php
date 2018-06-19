<?php

namespace Midnight\Orderreview\Block\Adminhtml\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use \Magento\Framework\App\Request\Http;
use \Magento\Customer\Model\CustomerFactory;
use \Midnight\Orderreview\Helper\ListHelper;
use Magento\Framework\Data\Form\FormKey;
use \Magento\Directory\Model\CountryFactory;
use \Magento\Framework\App\Response\RedirectInterface;
use \Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Backend\Helper\Data;
use \Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Form extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected $order;
    /**
     * @var Http
     */
    protected $request;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var ListHelper
     */
    protected $listHelper;
    /**
     * @var FormKey
     */
    protected $formKey;
    /**
     * @var CountryFactory
     */
    protected $countryFactory;
    /**
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     * @var HttpResponse
     */
    protected $httpResponse;
    /**
     * @var Data
     */
    protected $backendHelper;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Form constructor.
     * @param Template\Context $context
     * @param Http $request
     * @param CustomerFactory $customerFactory
     * @param ListHelper $listHelper
     * @param FormKey $formKey
     * @param CountryFactory $countryFactory
     * @param RedirectInterface $redirect
     * @param HttpResponse $httpResponse
     * @param Data $backendHelper
     * @param ManagerInterface $messageManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(Template\Context $context,
                                Http $request,
                                CustomerFactory $customerFactory,
                                ListHelper $listHelper,
                                FormKey $formKey,
                                CountryFactory $countryFactory,
                                RedirectInterface $redirect,
                                HttpResponse $httpResponse,
                                Data $backendHelper,
                                ManagerInterface $messageManager,
                                CustomerRepositoryInterface $customerRepository,
                                array $data = [])
    {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->customerFactory = $customerFactory;
        $this->listHelper = $listHelper;
        $this->formKey = $formKey;
        $this->countryFactory = $countryFactory;
        $this->redirect = $redirect;
        $this->httpResponse = $httpResponse;
        $this->backendHelper = $backendHelper;
        $this->messageManager = $messageManager;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return bool|\Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $orderId = $this->request->getParam('id');
        if(!empty($orderId)) {
            $order = $this->listHelper->getOrderById($orderId);
            if($order){
                return $order;
            }
        }
        $this->messageManager->addErrorMessage(__('Order with ID %1 not found', $orderId));
        $this->httpResponse->setRedirect($this->redirect->getRefererUrl())->sendResponse();
    }

    /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer($customerId){
        if(!empty($customerId)) {
            try {
                return $this->customerRepository->getById($customerId);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('Winline customer with id %1 not found', $customerId));
            }
        }else{
            $this->messageManager->addErrorMessage(__('Order does not contain customer ID.'));
        }
        $this->httpResponse->setRedirect($this->redirect->getRefererUrl())->sendResponse();
    }

    /**
     * @param $url
     * @param array $params
     * @return string
     */
    public function geFormUrl($url, array $params = []){
        return $this->_urlBuilder->getUrl($url, $params);
    }

    /**
     * @return string
     */
    public function getFormKey(){
        return $this->formKey->getFormKey();
    }

    /**
     * @param $name
     * @param $value
     * @param $label
     * @param null $input_callback
     * @return string
     */
    public function formRow($name, $value, $label, $input_callback = null)
    {
        $id = $this->nameToId($name);
        if (is_string($input_callback)) {
            $input = $input_callback;
        } else {
            if (is_null($input_callback)) {
                $input_callback = array($this, 'input');
            }
            $input = call_user_func($input_callback, $name, $value);
        }
        return '<div class="form-row">
                    <label for="' . $id . '">' . $label . '</label>
                    <div class="inputs">' . $input . '</div>
                </div>';
    }

    /**
     * @param $name
     * @param $value
     * @param string $type
     * @return string
     */

    public function input($name, $value, $type = 'text')
    {
        $id = $this->nameToId($name);
        return '<input type="' . $type . '" name="' . $name . '" value="' . $value . '" id="' . $id . '" />';
    }

    /**
     * @param $name
     * @return string
     */
    public function nameToId($name)
    {
        return trim(preg_replace('/\W/i', '_', $name), '_');
    }

    /**
     * @param $countryCode
     * @return string
     */
    public function getCountryName($countryCode){
        return $this->countryFactory->create()->loadByCode($countryCode)->getName();
    }

}
