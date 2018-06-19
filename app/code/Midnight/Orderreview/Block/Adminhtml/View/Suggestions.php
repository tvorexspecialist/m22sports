<?php

namespace Midnight\Orderreview\Block\Adminhtml\View;

use Magento\Framework\View\Element\Template;
use Midnight\Orderreview\Model\Suggestion;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\App\Request\Http;
use \Midnight\Orderreview\Helper\ListHelper;
use \Magento\Framework\Message\ManagerInterface;

class Suggestions extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    private $suggestion;
    private $listHelper;
    private $request;
    private $manager;

    public function __construct(Template\Context $context,
                                Suggestion $suggestion,
                                Http $request,
                                ListHelper $listHelper,
                                ManagerInterface $manager,
                                array $data = [])
    {
        parent::__construct($context, $data);
        $this->suggestion = $suggestion;
        $this->request = $request;
        $this->listHelper = $listHelper;
        $this->manager = $manager;
    }


    public function getSuggestions()
    {
        return $this->suggestion->getSuggestions($this->getOrder());
    }

    public function getOrder()
    {
        $orderId = $this->request->getParam('id');
        if(!empty($orderId)) {
            $order = $this->listHelper->getOrderById($orderId);

            if($order){
                return $order;
            }
            throw new LocalizedException(new \Magento\Framework\Phrase(__('Order with id %s does not exist!', $orderId)));
        }
        return false;
    }

        public function getCustomerJson(\Midnight\Winline\Model\Customer $customer)
        {
            $data = array(
                'account_number' => $customer->getAccountNumber(),
                'prefix'         => $customer->getPrefix(),
                'firstname'      => $customer->getFirstname(),
                'lastname'       => $customer->getLastname(),
                'street'         => $customer->getStreet(),
                'postcode'       => $customer->getPostcode(),
                'city'           => $customer->getCity(),
    //            'region' => $customer->getRegion(),
                'country'        => $customer->getCountry(),
                'telephone'      => $customer->getPhone(),
            );

            return json_encode($data);
        }

}
