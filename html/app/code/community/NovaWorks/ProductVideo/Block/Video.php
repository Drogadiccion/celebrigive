<?php
/**
 * Simple product video view
 *
 */
class NovaWorks_ProductVideo_Block_Video extends Mage_Catalog_Block_Product_View_Abstract
{
    protected $_videosCollection = null;
	
	
    public function getCode($video)
	{
	   return $video->getVideoCode();
	}
    
    public function getTitle($video)
	{
		if ($video->getVideoTitle())
		{
			return $video->getVideoTitle();
		}
		else
		{
			return $this->getProduct()->getName();
		}
	}
    
    public function getThumbWidth($video)
	{
		if ($video->getVideoThumbWidth())
		{
			return $video->getVideoThumbWidth();
		}
		else
		{
			return $this->helper('productvideo/data')->getDefaultThumbWidth();
		}
	}
	
	public function getThumbHeight($video)
	{
		if ($video->getVideoThumbHeight())
		{
			return $video->getVideoThumbHeight();
		}
		else
		{
			return $this->helper('productvideo/data')->getDefaultThumbHeight();
		}
		return $height;
	}
	
	public function getWidth($video)
	{
		if ($video->getVideoWidth())
		{
			return $video->getVideoWidth();
		}
		else
		{
			return $this->helper('productvideo/data')->getDefaultWidth();
		}
	}
	
	public function getHeight($video)
	{
		if ($video->getVideoHeight())
		{
			return $video->getVideoHeight();
		}
		else
		{
			return $this->helper('productvideo/data')->getDefaultHeight();
		}
	}

    
    protected function _getProductVideos()
    {
        $storeId = Mage::app()->getStore()->getId();
        
        if (is_null($this->_videosCollection))
    	{
            $this->_videosCollection = $this->_getVideosCollection($storeId)->getSize() ? $this->_getVideosCollection($storeId) : $this->_getVideosCollection();
    	}
        
        return $this->_videosCollection;
    }
    
    
    protected function _getVideosCollection($storeId = 0)
    {
        return Mage::getModel('productvideo/videos')
            ->getCollection()
 			->addFieldToFilter('product_id', $this->getProduct()->getId())
            ->addFieldToFilter('store_id', $storeId);
    }
}