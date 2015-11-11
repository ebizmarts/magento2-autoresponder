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


namespace Ebizmarts\AutoResponder\Block\Adminhtml\System\Config\Birthday\Fieldset;

class Hint extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'Ebizmarts_AutoResponder::system/config/birthday/fieldset/hint.phtml';
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_loader;
    /**
     * @var \Ebizmarts\AutoResponder\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Module\ModuleList\Loader $loader
     * @param \Ebizmarts\AutoResponder\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\ModuleList\Loader $loader,
        \Ebizmarts\AutoResponder\Helper\Data $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_loader = $loader;
        $this->_helper = $helper;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->toHtml();
    }

    public function dobActive()
    {
        return $this->_helper->getConfig('customer/address/dob_show');
    }
}