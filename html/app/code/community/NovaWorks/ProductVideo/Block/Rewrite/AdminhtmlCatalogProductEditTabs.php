<?php
class NovaWorks_ProductVideo_Block_Rewrite_AdminhtmlCatalogProductEditTabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs 
{
	protected function _prepareLayout() 
    {
		$return = parent::_prepareLayout();
		 
		 $this->addTab('youtube_videos', array(
            'label'     => Mage::helper('productvideo')->__('Videos'),
            'url'       => $this->getUrl('productvideo_admin/adminhtml_videos', array('_current' => true)),
            'class'     => 'ajax',
            'after'     => 'inventory',
        ));
		
		return $return;
	}
}