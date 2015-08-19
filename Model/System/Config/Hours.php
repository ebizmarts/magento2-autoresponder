<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/19/15
 * Time: 6:06 PM
 * File: Hours.php
 * Module: magento2-autoresponder
 */
namespace Ebizmarts\AutoResponder\Model\System\Config;

class Hours
{
    public function toOptionArray()
    {
        $options = array();
        for ($i = 0; $i < 24; $i++) {
            $options[] = array('value' => $i, 'label' => $i);
        }
        return $options;
    }
}