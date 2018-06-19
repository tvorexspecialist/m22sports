<?php

namespace Midnight\Winline\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Customer
 * @package Midnight\Winline\Model
 */
class Customer extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'ExportWebKunden';
    /**
     * @var string
     */
    protected $_cacheTag = 'ExportWebKunden';
    /**
     * @var string
     */
    protected $_eventPrefix = 'ExportWebKunden';

    const PAYMENT_METHOD_INVOICE = 8;
    const PAYMENT_METHOD_UNKNOWN = -1;
    static $paymentMethods = [self::PAYMENT_METHOD_INVOICE];
    /**
     * @var array
     */
    public static $countrymap = [
        'A' => 'AT',
        'AU' => 'AU',
        'AUS' => 'AU',
        'B' => 'BE',
        'CAN' => 'CA',
        'CDN' => 'CA',
        'CH' => 'CH',
        'CZ' => 'CZ',
        'D' => 'DE',
        'DK' => 'DK',
        'E' => 'ES',
        'F' => 'FR',
        'F-' => 'FR',
        'FI' => 'FI',
        'FIN' => 'FI',
        'FL' => 'LI',
        'GB' => 'GB',
        'GR' => 'GR',
        'GUS' => 'RU',
        'H' => 'HU',
        'HR' => 'HR',
        'HU' => 'HU',
        'I' => 'IT',
        'IE' => 'IE',
        'IRL' => 'IE',
        'L' => 'LU',
        'LU' => 'LU',
        'N' => 'NO',
        'NL' => 'NL',
        'NL-' => 'NL',
        'NO' => 'NO',
        'NOM' => 'CA',
        'NZ' => 'NZ',
        'P' => 'PT',
        'PL' => 'PL',
        'RA' => 'AR',
        'RO' => 'RO',
        'S' => 'SE',
        'SCG' => 'RS',
        'SE' => 'SE',
        'SG' => 'SG',
        'SI' => 'SI',
        'SK' => 'SK',
        'SL' => 'SI',
        'SL0' => 'SI',
        'SLO' => 'SI',
        'TN' => 'US',
        'UK' => 'GB',
        'US' => 'US',
        'USA' => 'US',
        'WI' => 'US',
        'ZA' => 'ZA',
        'ZIP' => 'IT'
    ];

    const FIELD_EMAIL = 'EMailAdresse';
    const FIELD_ACCOUNT_NUMBER = 'Kontonummer';
    const FIELD_POSTCODE = 'Postleitzahl';
    const FIELD_NAME = 'Kontoname';
    const FIELD_STREET = 'Strasse';
    const FIELD_CITY = 'Ort';
    const FIELD_PREFIX = 'Anrede';
    const FIELD_REGION = 'Land';
    const FIELD_COUNTRY = 'Staat';
    const FIELD_PHONE = 'Telefon';
    const FIELD_INACTIVE = 'Inaktiv';
    const FIELD_PAYMENT_METHOD = 'ZahlungskonditionFAKT';
    const FIELD_UPDATED = 'DatumletzteAenderung';

    /**
     * Customer constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Midnight\Winline\Model\ResourceModel\Customer');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getData(self::FIELD_NAME);
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        $name = $this->getName();
        $name_parts = explode(' ', $name, 2);
        if(!empty($name_parts[1]))
        return $name_parts[1];
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        $name = $this->getName();
        $name_parts = explode(' ', $name, 2);
        return $name_parts[0];
    }

    /**
     * @return mixed
     */
    public function getEmail(){
        return $this->getData(self::FIELD_EMAIL);
    }

    /**
     * @return mixed
     */
    public function getAccountNumber(){
        return $this->getData(self::FIELD_ACCOUNT_NUMBER);
    }

    /**
     * @return mixed
     */
    public function getPostcode(){
        return $this->getData(self::FIELD_POSTCODE);
    }

    /**
     * @return mixed
     */
    public function getStreet(){
        return $this->getData(self::FIELD_STREET);
    }

    /**
     * @return mixed
     */
    public function getCity(){
        return $this->getData(self::FIELD_CITY);
    }

    /**
     * @return mixed
     */
    public function getPrefix(){
        return $this->getData(self::FIELD_PREFIX);
    }

    /**
     * @return mixed
     */
    public function getRegion(){
        return $this->getData(self::FIELD_REGION);
    }

    /**
     * @return bool|mixed
     */
    public function getCountry()
    {
        $winline_country_code = $this->getData(self::FIELD_COUNTRY);
        if(!empty(self::$countrymap[$winline_country_code])) {
            return self::$countrymap[$winline_country_code];
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getPhone(){
        return $this->getData(self::FIELD_PHONE);
    }

    /**
     * @return bool
     */
    public function isInactive()
    {
        return '0000-00-00 00:00:00' !== $this->getData(self::FIELD_INACTIVE);
    }

    /**
     * @return int
     */
    public function getPaymentMethod()
    {
        $paymentMethod = intval($this->getData(self::FIELD_PAYMENT_METHOD), 10);
        if (!in_array($paymentMethod, self::$paymentMethods)) {
            return self::PAYMENT_METHOD_UNKNOWN;
        }
        return $paymentMethod;
    }

    /**
     * @return mixed
     */
    public function getUpdated(){
        return $this->getData(self::FIELD_UPDATED);
    }
}
