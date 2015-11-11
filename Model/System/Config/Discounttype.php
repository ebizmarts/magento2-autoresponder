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