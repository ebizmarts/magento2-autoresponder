<?php
/**
 * Ebizmarts_Autoresponder Magento JS component
 *
 * @category    Ebizmarts
 * @package     Ebizmarts_Autoresponder
 * @author      Ebizmarts Team <info@ebizmarts.com>
 * @copyright   Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Ebizmarts\AutoResponder\Model;

use Ebizmarts\AutoResponder\Model\Config as Config;

class Cron
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected $_customerCollection;
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Ebizmarts\AutoResponder\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;


    protected $_couponAmount;
    protected $_couponExpireDays;
    protected $_couponType;
    protected $_couponLength;
    protected $_couponLabel;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Ebizmarts\AutoResponder\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Ebizmarts\AutoResponder\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_objectManager       = $objectManager;
        $this->_customerCollection  = $customerCollection;
        $this->_storeManager        = $storeManager;
        $this->_transportBuilder    = $transportBuilder;
        $this->_helper              = $helper;
        $this->_logger              = $logger;
    }

    /**
     *
     */
    public function process()
    {
        foreach($this->_storeManager->getStores() as $storeId => $val)
        {
            $this->_storeManager->setCurrentStore($storeId);
            if($this->_helper->getConfig(Config::ACTIVE,$storeId)) {
                $this->_processStore($storeId);
            }
        }
    }

    /**
     * @param $storeId
     */
    protected function _processStore($storeId)
    {
        if($this->_helper->getConfig(Config::NEWORDER_ACTIVE,$storeId)&&$this->_helper->isSetTime(Config::NEWORDER_CRON_TIME,$storeId))
        {
            $this->_processNewOrder($storeId);
        }
        if($this->_helper->getConfig(Config::BIRTHDAY_ACTIVE, $storeId) && $this->_helper->isSetTime($this->_helper->getConfig(Config::BIRTHDAY_CRON_TIME, $storeId))) {
            $this->_processBirthday($storeId);
        }
    }

    /**
     * @param $storeId
     */
    protected function _processNewOrder($storeId)
    {
        $customerGroups = explode(",", $this->_helper->getConfig(Config::NEWORDER_CUSTOMER_GROUPS, $storeId));
        $days = $this->_helper->getConfig(Config::NEWORDER_DAYS, $storeId);
        $tags = $this->_helper->getConfig(Config::NEWORDER_MANDRILL_TAG, $storeId) . "_$storeId";
//        $adapter = Mage::getSingleton('core/resource')->getConnection('sales_read');
        $mailSubject = $this->_helper->getConfig(Config::NEWORDER_SUBJECT, $storeId);
        $senderId = $this->_helper->getConfig(Config::GENERAL_SENDER, $storeId);
        $sender = array('name' => $this->_helper->getConfig("trans_email/ident_$senderId/name", $storeId), 'email' => $this->_helper->getConfig("trans_email/ident_$senderId/email", $storeId));
        $templateId = $this->_helper->getConfig(Config::NEWORDER_TEMPLATE, $storeId);

        $expr = sprintf('DATE_SUB(now(), %s)', $this->_getIntervalUnitSql($days, 'DAY'));
        $from = new \Zend_Db_Expr($expr);
        $expr = sprintf('DATE_SUB(now(), %s)', $this->_getIntervalUnitSql($days - 1, 'DAY'));
        $to = new \Zend_Db_Expr($expr);

        $collection = $this->_objectManager->create('\Magento\Sales\Model\Resource\Order\Collection');
        $collection->addFieldToFilter('main_table.store_id', array('eq' => $storeId))
            ->addFieldToFilter('main_table.created_at', array('from' => $from, 'to' => $to));
        if ($this->_helper->getConfig(Config::NEWORDER_TRIGGER, $storeId) == 2) {
            $collection->addFieldToFilter('main_table.status', array('eq' => strtolower($this->_helper->getConfig(Config::NEWORDER_ORDER_STATUS, $storeId))));
        }
        if (count($customerGroups)) {
            $collection->addFieldToFilter('main_table.customer_group_id', array('in' => $customerGroups));
        }
        $mandrillHelper = $this->_objectManager->get('\Ebizmarts\Mandrill\Helper\Data');
        foreach ($collection as $order) {
            //$translate = Mage::getSingleton('core/translate');
            $email = $order->getCustomerEmail();
            if ($mandrillHelper->isSubscribed($email, 'neworder', $storeId)) {
                $name = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
                $url = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK) . 'mandrill/autoresponder/unsubscribe?list=neworder&email=' . $email . '&store=' . $storeId;
                $vars = array('tags' => array($tags), 'url' => $url, 'subject' => $mailSubject);

                $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => $storeId])
                    ->setTemplateVars($vars)
                    ->setFrom($sender)
                    ->addTo($email, $name)
                    ->getTransport();
                $transport->sendMessage();
                $this->_objectManager->create('\Ebizmarts\Mandrill\Helper\Data')->saveMail('new order', $email, $name, "", $storeId);
            }
        }
    }

    /**
     * @param $storeId
     */
    public function _processBirthday($storeId)
    {
        $days = $this->_helper->getConfig(Config::BIRTHDAY_DAYS, $storeId);
        $customerGroups = explode(",", $this->_helper->getConfig(Config::BIRTHDAY_CUSTOMER_GROUPS, $storeId));
        $senderId = $this->_helper->getConfig(Config::GENERAL_SENDER, $storeId);
        $sender = array('name' => $this->_helper->getConfig("trans_email/ident_$senderId/name", $storeId), 'email' => $this->_helper->getConfig("trans_email/ident_$senderId/email", $storeId));
        $templateId = $this->_helper->getConfig(Config::BIRTHDAY_TEMPLATE, $storeId);
        $mailSubject = $this->_helper->getConfig(Config::BIRTHDAY_SUBJECT, $storeId);
        $tags = $this->_helper->getConfig(Config::BIRTHDAY_MANDRILL_TAG, $storeId) . "_$storeId";
        $sendCoupon = $this->_helper->getConfig(Config::BIRTHDAY_COUPON, $storeId);
        $customerGroupsCoupon = explode(",", $this->_helper->getConfig(Config::BIRTHDAY_CUSTOMER_COUPON, $storeId));

        $collection = $this->_customerCollection;
        $date2 = date("Y-m-d H:i:s", strtotime(" + $days days"));
        $month = date("m", strtotime($date2));
        $day = date("d", strtotime($date2));
        $moreSelect = "MONTH(at_dob.value) = $month AND DAY(at_dob.value) = $day";
        $collection->addAttributeToFilter('dob', array('neq' => 'null'))
            ->addFieldToFilter('store_id', array('eq' => $storeId));
        if (count($customerGroups)) {
            $collection->addFieldToFilter('group_id', array('in' => $customerGroups));
        }
        $collection->getSelect()->where($moreSelect);

        $mandrillHelper = $this->_objectManager->get('\Ebizmarts\Mandrill\Helper\Data');

        foreach ($collection as $customer) {
            $cust = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customer->getEntityId());
            $email = $cust->getEmail();
            $name = $cust->getFirstname() . ' ' . $cust->getLastname();
            if ($mandrillHelper->isSubscribed($email, 'birthday', $storeId)) {
                $vars = array();
                $url = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK) . 'mandrill/autoresponder/unsubscribe?list=birthday&email=' . $email . '&store=' . $storeId;
                $couponCode = '';
                if ($sendCoupon && in_array($customer->getGroupId(), $customerGroupsCoupon)) {
                    if ($this->_helper->getConfig(Config::BIRTHDAY_AUTOMATIC, $storeId) == Config::COUPON_AUTOMATIC) {
                        $this->_couponAmount = $this->_helper->getConfig(Config::BIRTHDAY_DISCOUNT, $storeId);
                        $this->_couponExpireDays = $this->_helper->getConfig(Config::BIRTHDAY_EXPIRE, $storeId);
                        $this->_couponType = $this->_helper->getConfig(Config::BIRTHDAY_DISCOUNT_TYPE, $storeId);
                        $this->_couponLength = $this->_helper->getConfig(Config::BIRTHDAY_LENGTH, $storeId);
                        $this->_couponLabel = $this->_helper->getConfig(Config::BIRTHDAY_COUPON_LABEL, $storeId);
                        list($couponCode, $discount, $toDate) = $this->_createNewCoupon($storeId, $email, 'Birthday coupon');
                        $vars = array('couponcode' => $couponCode, 'discount' => $discount, 'todate' => $toDate, 'name' => $name, 'tags' => array($tags), 'url' => $url);
                    } else {
                        $couponCode = $this->_helper->getConfig(Config::BIRTHDAY_COUPON_CODE);
                        $vars = array('couponcode' => $couponCode, 'name' => $name, 'tags' => array($tags), 'url' => $url);
                    }
                }
                $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => $storeId])
                    ->setTemplateVars($vars)
                    ->setFrom($sender)
                    ->addTo($email, $name)
                    ->getTransport();
                $transport->sendMessage();
                $mandrillHelper->saveMail('happy birthday', $email, $name, $couponCode, $storeId);
            }
        }

    }

    /**
     * @param $interval
     * @param $unit
     * @return string
     */
    function _getIntervalUnitSql($interval, $unit)
    {
        return sprintf('INTERVAL %d %s', $interval, $unit);
    }

    protected function _createNewCoupon($store, $email, $string)
    {
        $collection = $this->_objectManager->create('Magento\SalesRule\Model\Rule')->getCollection()
            ->addFieldToFilter('name', array('like' => $string . $email));

        if (!count($collection)) {
            $websiteid = $this->_storeManager->getStore()->getWebsiteId();
            $fromDate = date("Y-m-d");
            $toDate = date('Y-m-d', strtotime($fromDate . " + $this->_couponExpireDays day"));
            if ($this->_couponType == 1) {
                $action = 'cart_fixed';
                $discount = $this->_storeManager->getStore()->getCurrentCurrencyCode() . "$this->_couponAmount";
            } elseif ($this->_couponType == 2) {
                $action = 'by_percent';
                $discount = "$this->_couponAmount%";
            }
            $customer_group = $this->_objectManager->create('Magento\Customer\Model\Group');
            $allGroups = $customer_group->getCollection()->toOptionHash();
            $groups = array();
            foreach ($allGroups as $groupid => $name) {
                $groups[] = $groupid;
            }
            $coupon_rule = $this->_objectManager->create('Magento\SalesRule\Model\Rule');
            $coupon_rule->setName($string . ' ' . $email)
                ->setDescription($string . ' ' . $email)
                ->setFromDate($fromDate)
                ->setToDate($toDate)
                ->setIsActive(1)
                ->setCouponType(2)
                ->setUsesPerCoupon(1)
                ->setUsesPerCustomer(1)
                ->setCustomerGroupIds($groups)
                ->setProductIds('')
                ->setLengthMin($this->_couponLength)
                ->setLengthMax($this->_couponLength)
                ->setSortOrder(0)
                ->setStoreLabels(array($this->_couponLabel))
                ->setSimpleAction($action)
                ->setDiscountAmount($this->_couponAmount)
                ->setDiscountQty(0)
                ->setDiscountStep('0')
                ->setSimpleFreeShipping('0')
                ->setApplyToShipping('0')
                ->setIsRss(0)
                ->setWebsiteIds($websiteid);
            $uniqueId = $this->_objectManager->create('Magento\SalesRule\Model\Group\Codegenerator')->setLengthMin($this->_couponLength)->setLengthMax($this->_couponLength)->generateCode();
            $coupon_rule->setCouponCode($uniqueId);
            $coupon_rule->save();
            return array($uniqueId, $discount, $toDate);
        } else {
            $coupon = $collection->getFirstItem();
            if ($coupon->getSimpleAction() == 'cart_fixed') {
                $discount = $this->_storeManager->getStore()->getCurrentCurrencyCode() . ($coupon->getDiscountAmount() + 0);
            } else {
                $discount = $coupon->getDiscountAmount() + 0;
            }
            return array($coupon->getCode(), $discount, $coupon->getToDate());
        }
    }
}