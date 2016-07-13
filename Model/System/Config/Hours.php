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