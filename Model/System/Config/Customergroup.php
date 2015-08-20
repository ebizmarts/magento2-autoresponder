<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/20/15
 * Time: 12:59 PM
 * File: Customergroup.php
 * Module: magento2-autoresponder
 */
namespace Ebizmarts\AutoResponder\Model\System\Config;

class Customergroup
{
    protected $_options;
    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $groupFactory;

    /**
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     */
    public function __construct(
        \Magento\Customer\Model\GroupFactory $groupFactory
    ) {
        $this->groupFactory = $groupFactory;
    }


    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->groupFactory->create()->getCollection()
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }}