<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/19/15
 * Time: 5:42 PM
 * File: Newordertrigger.php
 * Module: magento2-autoresponder
 */

namespace Ebizmarts\AutoResponder\Model\System\Config;

class Newordertrigger
{
    protected $_options;

    public function toOptionArray()
    {
        $this->_options = array(
            array('value' => 0, 'label' => 'Days after order'),
            array('value' => 1, 'label' => 'Order status'),
            array('value' => 2, 'label' => 'Days after order status changed to')
        );
        return $this->_options;
    }
}