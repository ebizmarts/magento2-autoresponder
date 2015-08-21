<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/21/15
 * Time: 1:51 PM
 * File: Discounttype.php
 * Module: magento2-autoresponder
 */
namespace Ebizmarts\AutoResponder\Model\System\Config;

class Discounttype
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('value' => 1, 'label' => __('Fixed amount')),
            array('value' => 2, 'label' => __('Percentage'))
        );
        return $options;
    }
}