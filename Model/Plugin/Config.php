<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/20/15
 * Time: 2:34 PM
 * File: Config.php
 * Module: magento2-autoresponder
 */

namespace Ebizmarts\AutoResponder\Model\Plugin;

class Config
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Module\ModuleList\Loader
     */
    protected $_loader;
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $_writer;


    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\ModuleList\Loader $loader
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\ModuleList\Loader $loader,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    )
    {
        $this->_objectManager = $objectManager;
        $this->_loader = $loader;
        $this->_writer = $configWriter;
    }

    public function aroundSave(\Magento\Config\Model\config $config, \Closure $proceed)
    {
        $ret = $proceed();
        $sectionId = $config->getSection();
        if ($sectionId == 'autoresponder' && !$this->_objectManager->create('\Ebizmarts\Mandrill\Helper\Data')->isActive($config->getStore())) {
            $this->_writer->save(\Ebizmarts\AutoResponder\Model\Config::ACTIVE, 0, $config->getScope(), $config->getScopeId());
        }
        return $ret;
    }
}