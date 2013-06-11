<?php
class NovaWorks_ProductVideo_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_DEFAULT_THUMB_WIDTH     = 'novaworks_productvideo/settings/default_thumb_width';
	const XML_PATH_DEFAULT_THUMB_HEIGHT    = 'novaworks_productvideo/settings/default_thumb_height';
	const XML_PATH_DEFAULT_WIDTH           = 'novaworks_productvideo/settings/default_width';
	const XML_PATH_DEFAULT_HEIGHT          = 'novaworks_productvideo/settings/default_height';	
	public function getDefaultThumbWidth()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_THUMB_WIDTH);
	}
	
	public function getDefaultThumbHeight()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_THUMB_HEIGHT);
	}
	
	public function getDefaultWidth()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_WIDTH);
	}
	
	public function getDefaultHeight()
	{
		return Mage::getStoreConfig(self::XML_PATH_DEFAULT_HEIGHT);
	}
}