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