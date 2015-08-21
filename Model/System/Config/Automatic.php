<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/21/15
 * Time: 1:43 PM
 * File: Automatic.php
 * Module: magento2-autoresponder
 */
namespace Ebizmarts\AutoResponder\Model\System\Config;

class Automatic
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('value' => 1, 'label' => __('Specific')),
            array('value' => 2, 'label' => __('Automatic'))
        );
        return $options;
    }
}