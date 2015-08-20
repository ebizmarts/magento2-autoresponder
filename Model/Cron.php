<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/20/15
 * Time: 1:17 PM
 * File: Cron.php
 * Module: magento2-autoresponder
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

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Ebizmarts\AutoResponder\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Ebizmarts\AutoResponder\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_objectManager   = $objectManager;
        $this->_storeManager    = $storeManager;
        $this->_transportBuilder= $transportBuilder;
        $this->_helper          = $helper;
        $this->_logger          = $logger;
    }
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
    protected function _process($storeId)
    {
        if($this->_helper->getConfig(Config::NEWORDER_ACTIVE,$storeId))
        {
            $this->_processNewOrder($storeId);
        }
    }
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

        $collection = $this->_objectManager->create('\Magento\Sales\Model\Order\Collection');
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
            $translate = Mage::getSingleton('core/translate');
            $email = $order->getCustomerEmail();
            if ($mandrillHelper->isSubscribed($email, 'neworder', $storeId)) {
                $name = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
                $url = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK) . 'ebizautoresponder/autoresponder/unsubscribe?list=neworder&email=' . $email . '&store=' . $storeId;
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
}