<?php
/**
 * Simple product data view
 *
 */
class NovaWorks_ProductVideo_Block_Product_View_Media extends Mage_Catalog_Block_Product_View_Media
{
    protected function _toHtml()
	{
		$html = parent::_toHtml();
		$html .= $this->getChildHtml('media_video');
		return $html;
	}
}