<?php

class NovaWorks_OneClickInstaller_Adminhtml_InstallerFormController extends Mage_Adminhtml_Controller_Action
{
    protected $_storeId = null;

    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
		protected function deleteBlock($id){
	        $block = Mage::getModel('cms/block')
	                ->setStoreId($this->_storeId)
	                ->load($id);
	
			$block->delete();
		}
    
    public function uninstallAction()
    {
		$post = $this->getRequest()->getPost();
		$message = "";
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
			$storeId = $post['design']['store_id'];		
			$this->_storeId = $storeId;	
			
			$this->deleteBlock('home_3_banner');
			$this->deleteBlock('detail_product_right');
			$this->deleteBlock('block-top-left');
			$this->deleteBlock('block_custom_menu');
			$this->deleteBlock('block_custom_slidebar_2');
			$this->deleteBlock('block_custom_slidebar_3');
			$this->deleteBlock('block_detail_product_page_1');
			$this->deleteBlock('block_detail_product_page_2');
			$this->deleteBlock('aditional_footer_right');
			$this->deleteBlock('aditional_footer_left');
			$this->deleteBlock('block_top_cart');
			$this->deleteBlock('block_contact_top');
			$this->deleteBlock('block_contact_bottom');
			$this->deleteBlock('block_header_right');
			$this->deleteBlock('block_bottom_info');
			$this->deleteBlock('block_bottom_right');
			$this->deleteBlock('block_about_box');
			$this->deleteBlock('block_home_who');
			$this->deleteBlock('block_contact_box');
			$this->deleteBlock('block-easy-slider-1');
			$this->deleteBlock('block-easy-slider-2');
			$this->deleteBlock('block_brand_list');
			if($storeId == 0) {
				$scope = 'default';
			}else{
				$scope = 'stores';
			}
			Mage::getConfig()->saveConfig('design/package/name','default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/template', 'default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/skin', 'default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/layout', 'default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/default', 'default', $scope, $storeId);
			
			$message = $this->__('BearStore theme was uninstalled successfully. ');
			Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
		$this->_redirect('*/*');		
    }
    public function installAction()
    {
      $post = $this->getRequest()->getPost();
      $message = "";
      try {
      	if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
        }
				$storeId 			= $post['design']['store_id'];
				$InstallBlock 	= $post['design']['install_block'];
				$InstallSlideshow 	= $post['design']['install_slideshow'];
				$stores = array($storeId); 	//Used at all blocks
				$RootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();			
				$novaworks_uploaded = false;
				$design = Mage::getModel('core/design_package')->getPackageList();
				foreach ($design as $package){
					if($package == "novaworks") {
						$novaworks_uploaded = true;
						break;
					}
				}
				if (!$novaworks_uploaded){
					Mage::throwException($this->__('BearStore Theme was not found. Please upload the theme first.'));				
				}					
				if($storeId == 0) {
					$scope = 'default';
				}else{
					$scope = 'stores';
				}
				//Configuration 
				//Design
				Mage::getConfig()->saveConfig('design/package/name', "novaworks", $scope, $storeId);
				Mage::getConfig()->saveConfig('design/theme/template', "bearstore", $scope, $storeId);
				Mage::getConfig()->saveConfig('design/theme/skin', "bearstore", $scope, $storeId);		
				Mage::getConfig()->saveConfig('design/theme/layout', "bearstore", $scope, $storeId);
				Mage::getConfig()->saveConfig('design/theme/default', "bearstore", $scope, $storeId);
				//Coppyright
				Mage::getConfig()->saveConfig('design/footer/copyright', "&copy; 2013 BearStore Theme. All Rights Reserved. Designed by <a href=\"http://novaworks.net/\" title=\"Novaworks\">Novaworks</a>",$scope, $storeId);
				//Header
				Mage::getConfig()->saveConfig('design/header/logo_src', "images/logo.png", $scope, $storeId);
				//Setup Static Block
				if($InstallBlock == 1) {
				//SETUP 404 NOT FOUND PAGE
				$html = '<div class="mail-center-404">
<div class="row-fluid">
<div class="span4">&nbsp;</div>
<div class="span3">
<div class="mail-center">
<h1>404</h1>
<h2>Oops!</h2>
<p>The page you are looking for does not exist.</p>
<p>Return to the <a href="{{config path="web/unsecure/base_url"}}">home page!</a></p>
</div>
</div>
<div class="span4">&nbsp;</div>
</div>
</div>';
				
				//load the current 404 page
				$page = Mage::getModel('cms/page');//->getCollection();
				$pageId = $page->checkIdentifier('no-route', $storeId);
				if ($pageId){
					//Update this one
					$page->load($pageId);
					$data = $page->getData();
					$data['is_active'] = 1;//Enable it
					$data['title'] = '404 Not Found'; //page title
					$data['root_template'] = 'one_column';
					$data['custom_theme'] = null;
					$data['content'] = $html;
					$page->setData($data);
				} else {
					//Create a new one
					$data = array();
					$data['form_key'] = '';
					$data['title'] = '404 Not Found'; //page title
					$data['identifier'] = 'no-route'; //URL
					$data['stores'][0] = $storeId; //stores array, store number
					$data['is_active'] = 1;
					$data['content'] = $html;
					$data['custom_theme'] = null;
					$data['custom_theme_from'] = '';
					$data['custom_theme_to'] = '';
					$data['root_template'] = 'one_column';
					$data['layout_update_xml'] = '';
					$data['meta_keywords'] = '';
					$data['meta_description'] = '';			
					 
					$page->setData($data);
				}
				
				// try to save it
				try {
					// save the data
					$page->save();
					$message .= $this->__(' 404 Not Found page saved.');
				} catch (Mage_Core_Exception $e) {
					$this->_getSession()->addError($e->getMessage());
					$this->_redirect('*/*');
					return;
				}
				//SETUP ABOUT US PAGE
				$html = '<div class="about-content">
<div class="row-fluid">
<div class="span6"><img style="width: 480px; height: 376px;" src="{{media url="wysiwyg/img-welcome.jpg"}}" alt="" /></div>
<div class="span6">
<div class="wel-come">
<h2>Welcome to BearStore Template, a wonderful and premium product for multipurpose websites</h2>
<p><span> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incidi-dunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercita-tion ullamco laboris nisi ut aliquip ex ea commodo consequat. </span></p>
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi archi-tecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
<p><span> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor </span> incidi-dunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercita-tion ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi archi-tecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores.</p>
</div>
</div>
</div>
<div class="row-fluid center-title">
<h1 class="title">Meet your team</h1>
</div>
<div class="row-fluid">
<div class="span3"><img src="{{media url="wysiwyg/view-project.png"}}" alt="" /></div>
<div class="span9">
<div class="row-fluid">
<h2>Ippolito Etro</h2>
<a href="#">Magento Developer</a>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa</p>
</div>
<div class="row-fluid"><img src="{{media url="wysiwyg/meet-your-team-1.png"}}" alt="" /> <img src="{{media url="wysiwyg/meet-your-team-2.png"}}" alt="" /> <img src="{{media url="wysiwyg/meet-your-team-3.png"}}" alt="" /></div>
</div>
</div>
<div class="afew-container">
<div class="a-few-features">
<div class="row-fluid center-title">
<h1 class="title">A Few Features</h1>
</div>
<div class="row-fluid afew-top">
<div class="span6">
<div class="row-fluid">
<div class="span4">&nbsp;</div>
<div class="span8">
<div class="retina-ready">
<h3>Retina ready</h3>
<p>anny pack godard YOLO VHS Austin irony bespoke, you probably haven&rsquo;t heard of them fashion axe church-key wes anderson kale chips four loko. Gentrify stumptown chillwave, pop-up tofu DIY cardigan jean shorts blog wayfarers before they sold out authentic bushwick</p>
<a href="#">FIND OUT MORE</a></div>
</div>
</div>
</div>
<div class="span6">
<div class="row-fluid">
<div class="span4">&nbsp;</div>
<div class="span8">
<div class="mobile-friendly">
<h3>Super Mobile-Friendly</h3>
<p>anny pack godard YOLO VHS Austin irony bespoke, you probably haven&rsquo;t heard of them fashion axe church-key wes anderson kale chips four loko. Gentrify stumptown chillwave, pop-up tofu DIY cardigan jean shorts blog wayfarers before they sold out authentic bushwick</p>
<a href="#">FIND OUT MORE</a></div>
</div>
</div>
</div>
</div>
<div class="row-fluid afew-bottom">
<div class="span6">
<div class="row-fluid">
<div class="span4">&nbsp;</div>
<div class="span8">
<div class="wide-or">
<h3>Wide or Boxed Layout</h3>
<p>anny pack godard YOLO VHS Austin irony bespoke, you probably haven&rsquo;t heard of them fashion axe church-key wes anderson kale chips four loko. Gentrify stumptown chillwave, pop-up tofu DIY cardigan jean shorts blog wayfarers before they sold out authentic bushwick</p>
<a href="#">FIND OUT MORE</a></div>
</div>
</div>
</div>
<div class="span6">
<div class="row-fluid">
<div class="span4">&nbsp;</div>
<div class="span8">
<div class="responsive-layout">
<h3>Responsive Layout</h3>
<p>anny pack godard YOLO VHS Austin irony bespoke, you probably haven&rsquo;t heard of them fashion axe church-key wes anderson kale chips four loko. Gentrify stumptown chillwave, pop-up tofu DIY cardigan jean shorts blog wayfarers before they sold out authentic bushwick</p>
<a href="#">FIND OUT MORE</a></div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>';
				
				$page = Mage::getModel('cms/page');//->getCollection();
				$pageId = $page->checkIdentifier('about-us', $storeId);
				if ($pageId){
					//Update this one
					$page->load($pageId);
					$data = $page->getData();
					$data['is_active'] = 1;//Enable it
					$data['title'] = 'About  Us'; //page title
					$data['root_template'] = 'one_column';
					$data['custom_theme'] = null;
					$data['content'] = $html;
					$page->setData($data);
				} else {
					//Create a new one
					$data = array();
					$data['form_key'] = '';
					$data['title'] = 'About  Us'; //page title
					$data['identifier'] = 'about-us'; //URL
					$data['stores'][0] = $storeId; //stores array, store number
					$data['is_active'] = 1;
					$data['content'] = $html;
					$data['custom_theme'] = null;
					$data['custom_theme_from'] = '';
					$data['custom_theme_to'] = '';
					$data['root_template'] = 'one_column';
					$data['layout_update_xml'] = '';
					$data['meta_keywords'] = '';
					$data['meta_description'] = '';			
					 
					$page->setData($data);
				}
				
				// try to save it
				try {
					// save the data
					$page->save();
					$message .= $this->__(' About Page page saved.');
				} catch (Mage_Core_Exception $e) {
					$this->_getSession()->addError($e->getMessage());
					$this->_redirect('*/*');
					return;
				}
				//SETUP ABOUT US PAGE
				$html = '<div class="faqs-content">
<div class="row-fluid">
<div id="accordion" class="accordion in">
<div class="accordion-group">
<h3>PRE-SALE QUESTIONS</h3>
<div class="row-fluid">
<div class="accordion-heading"><a class="accordion-toggle" href="#collapseOne" data-toggle="collapse" data-parent="#accordion"> Can I Preview the Documentation Before I Purchase the Theme? </a></div>
<div id="collapseOne" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
<div class="row-fluid">
<div class="accordion-group">
<div class="accordion-heading"><a class="accordion-toggle" href="#collapseTwo" data-parent="#accordion" data-toggle="collapse"> How Do I Get Support For My copy of the Theme, Beyond the Help of Documentation? </a></div>
<div id="collapseTwo" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
</div>
<div class="row-fluid">
<div class="accordion-group">
<div class="accordion-heading"><a class="accordion-toggle collapsed" href="#collapseThree" data-parent="#accordion" data-toggle="collapse"> Where Can I Find My Item Purchase Code? </a></div>
<div id="collapseThree" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
</div>
</div>
<div class="accordion-group">
<h3>A 2ND BLOCK OF FAQS</h3>
<div class="row-fluid">
<div class="accordion-heading"><a class="accordion-toggle" href="#collapsefour" data-toggle="collapse" data-parent="#accordion"> Can I Preview the Documentation Before I Purchase the Theme? </a></div>
<div id="collapsefour" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
<div class="row-fluid">
<div class="accordion-group">
<div class="accordion-heading"><a class="accordion-toggle" href="#collapsefive" data-parent="#accordion" data-toggle="collapse"> How Do I Get Support For My copy of the Theme, Beyond the Help of Documentation? </a></div>
<div id="collapsefive" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
</div>
<div class="row-fluid">
<div class="accordion-group">
<div class="accordion-heading"><a class="accordion-toggle collapsed" href="#collapsesix" data-parent="#accordion" data-toggle="collapse"> Where Can I Find My Item Purchase Code? </a></div>
<div id="collapsesix" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
</div>
</div>
<div class="accordion-group">
<h3>SHIPPING &amp; RETURNS</h3>
<div class="row-fluid">
<div class="accordion-heading"><a class="accordion-toggle" href="#collapseseven" data-toggle="collapse" data-parent="#accordion"> Can I Preview the Documentation Before I Purchase the Theme? </a></div>
<div id="collapseseven" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
<div class="row-fluid">
<div class="accordion-group">
<div class="accordion-heading"><a class="accordion-toggle" href="#collapseeight" data-parent="#accordion" data-toggle="collapse"> How Do I Get Support For My copy of the Theme, Beyond the Help of Documentation? </a></div>
<div id="collapseeight" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
</div>
<div class="row-fluid">
<div class="accordion-group">
<div class="accordion-heading"><a class="accordion-toggle collapsed" href="#collapsenine" data-parent="#accordion" data-toggle="collapse"> Where Can I Find My Item Purchase Code? </a></div>
<div id="collapsenine" class="accordion-body collapse">
<div class="accordion-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo.</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>';
				
				$page = Mage::getModel('cms/page');//->getCollection();
				$pageId = $page->checkIdentifier('faq', $storeId);
				if ($pageId){
					//Update this one
					$page->load($pageId);
					$data = $page->getData();
					$data['is_active'] = 1;//Enable it
					$data['title'] = 'FAQ'; //page title
					$data['root_template'] = 'one_column';
					$data['custom_theme'] = null;
					$data['content'] = $html;
					$page->setData($data);
				} else {
					//Create a new one
					$data = array();
					$data['form_key'] = '';
					$data['title'] = 'FAQ'; //page title
					$data['identifier'] = 'faq'; //URL
					$data['stores'][0] = $storeId; //stores array, store number
					$data['is_active'] = 1;
					$data['content'] = $html;
					$data['custom_theme'] = null;
					$data['custom_theme_from'] = '';
					$data['custom_theme_to'] = '';
					$data['root_template'] = 'one_column';
					$data['layout_update_xml'] = '';
					$data['meta_keywords'] = '';
					$data['meta_description'] = '';			
					 
					$page->setData($data);
				}
				
				// try to save it
				try {
					// save the data
					$page->save();
					$message .= $this->__(' FAQ page saved.');
				} catch (Mage_Core_Exception $e) {
					$this->_getSession()->addError($e->getMessage());
					$this->_redirect('*/*');
					return;
				}
				//Home page content
				Mage::getConfig()->saveConfig('themeoptions_homepage_content/homepagecontent/first_status', 1, $scope, $storeId);
				Mage::getConfig()->saveConfig('themeoptions_homepage_content/homepagecontent/homerowamount', 1, $scope, $storeId);
				Mage::getConfig()->saveConfig('themeoptions_homepage_content/homepagecontent/homeinfostype_1_1', 2, $scope, $storeId);
				Mage::getConfig()->saveConfig('themeoptions_homepage_content/homepagecontent/homecoltitle_1_1', '', $scope, $storeId);
				Mage::getConfig()->saveConfig('themeoptions_homepage_content/homepagecontent/homecustomblock_1_1', 'home_3_banner', $scope, $storeId);
				
				Mage::getConfig()->saveConfig('themeoptions_homepage/homepage_gallery/homegalleryimagetitle_4', 'Sample Slide Title 4', $scope, $storeId);
				Mage::getConfig()->saveConfig('themeoptions_homepage/homepage_gallery/homegalleryimage_4', 'sample/slideshow.png', $scope, $storeId);
				Mage::getConfig()->saveConfig('themeoptions_homepage/homepage_gallery/homegalleryimagetext_4', '<div class="home-gallery-info">
<h5 class="home-gallery-title">
<a href="#">Sample Slide Title 4</a></h5>
<div class="home-gallery-desc">
Lorem ipsum dolor sit amet consectetuer tempus felis dui nibh semper. Morbi eget eleifend In id sagittis sit libero vel mus eu. Pede cursus mauris commodo justo metus Maecenas feugiat Fusce commodo Aenean.
</div>						
</div>
<div class="home-gallery-button">
<a class="infintus-button0" href="#">Join Now</a>
</div>', $scope, $storeId);
					// Home page 3 banner
					$content = '<div class="col3-set">
<div class="col-1"><img src="{{media url="wysiwyg/01.jpg"}}" alt="" /></div>
<div class="col-2"><img src="{{media url="wysiwyg/01.jpg"}}" alt="" /></div>
<div class="col-3"><img src="{{media url="wysiwyg/01.jpg"}}" alt="" /></div>
</div>';
					$data = array("title" => "Home page 3 banner", 
								  "identifier" => "home_3_banner",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__('Home page 3 banner block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}
					
					// Details product right
					$content = '<p style="text-align: center;"><img src="{{media url="wysiwyg/detail-sample1_1.jpg"}}" alt="" /></p>';
					$data = array("title" => "Details product right", 
								  "identifier" => "detail_product_right",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Details product right block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}						
					// Block Top Left
					$content = '<ul>
<li><a href="{{config path="web/unsecure/base_url"}}about-us">About Us</a></li>
<li><a href="{{config path="web/unsecure/base_url"}}faq">FAQs</a></li>
<li><a href="{{config path="web/unsecure/base_url"}}contacts">Contact</a></li>
<li class="last"><a href="#">Buy Theme!</a></li>
</ul>';
					$data = array("title" => "Block Top Left", 
								  "identifier" => "block-top-left",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block Top Left block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
					// Block Custom Menu
					$content = '<div class="col4-set">
<div class="col-1">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
<div class="col-2">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
<div class="col-3">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
<div class="col-4">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
</div>';
					$data = array("title" => "Block Custom", 
								  "identifier" => "block_custom_menu",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__('Custom Menu block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}				
					// Custom Slidebar 2
					$content = '<div class="block custom-html">
<div id="custom-html"><strong> <span>Custom HTML</span> </strong></div>
<div class="block-content">
<p>Lorem ipsum dolor sit amet, con-sectetuer adipiscing elit sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.d tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim vemodo consequat.</p>
<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat</p>
</div>
</div>';
					$data = array("title" => "Custom Slidebar 2", 
								  "identifier" => "block_custom_slidebar_2",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Custom Slidebar 2 block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
					// Custom Slidebar 3
					$content = '<p><img src="{{media url="wysiwyg/sidebar-3.jpg"}}" alt="" /></p>
<p><img src="{{media url="wysiwyg/mango.jpg"}}" alt="" /></p>';
					$data = array("title" => "Custom Right Slidebar", 
								  "identifier" => "block_custom_slidebar_3",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Custom Slidebar 2 block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
					// Block Detail product page 1
					$content = '<div style="background-color: #f5f5f5; padding: 10px; margin-bottom: 10px; margin-top: 10px;">This product <strong><span style="color: #ca3400; text-transform: uppercase;">Shiping Free</span></strong></div>';
					$data = array("title" => "Block Detail product page 1", 
								  "identifier" => "block_detail_product_page_1",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block Detail product page 1 created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}						
					// Block Detail product page 2
					$content = '<div style="border-top: #f5f5f5 solid 1px; border-bottom: #f5f5f5 solid 1px; padding: 10px; margin-bottom: 10px; margin-top: 10px;"><img style="float: left; padding-right: 10px; margin-top: -20px;" src="http://nunakidz.com/demo/bigstore/media/wysiwyg/sample-seal.png" alt="" /> We guarantee that We is authorized to sell this product and that every brand we sell is authentic.</div>';
					$data = array("title" => "Block Detail product page 2", 
								  "identifier" => "block_detail_product_page_2",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block Detail product page 2 created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}
					// Block Who We Are
					$content = '<div class="who-we-are">
<div class="row-fluid who-center">
<h1 class="title">Who we are ?</h1>
</div>
<div class="row-fluid who-top">
<div class="span3"><img src="{{media url="wysiwyg/Ippolito-Etro.png"}}" alt="" />
<h2>Ippolito Etro</h2>
<p>Il mio &egrave; un lavoro emotivo. Osservo tutto e accumulo sensazioni e ispirazioni che condivido con il mio team creativo. Ho un quaderno di appunti dove annoto ci&ograve; che mi colpisce.</p>
<a href="#">READ MORE</a></div>
<div class="span3"><img src="{{media url="wysiwyg/Kean-Etro.png"}}" alt="" />
<h2>Kean Etro</h2>
<p>Il mio &egrave; un lavoro emotivo. Osservo tutto e accumulo sensazioni e ispirazioni che condivido con il mio team creativo. Ho un quaderno di appunti dove annoto ci&ograve; che mi colpisce.</p>
<a href="#">READ MORE</a></div>
<div class="span3"><img src="{{media url="wysiwyg/Jacopo-Etro.png"}}" alt="" />
<h2>Jacopo Etro</h2>
<p>Il mio &egrave; un lavoro emotivo. Osservo tutto e accumulo sensazioni e ispirazioni che condivido con il mio team creativo. Ho un quaderno di appunti dove annoto ci&ograve; che mi colpisce.</p>
<a href="#">READ MORE</a></div>
<div class="span3"><img src="{{media url="wysiwyg/Veronica--Etro.png"}}" alt="" />
<h2>Veronica Etro</h2>
<p>Il mio &egrave; un lavoro emotivo. Osservo tutto e accumulo sensazioni e ispirazioni che condivido con il mio team creativo. Ho un quaderno di appunti dove annoto ci&ograve; che mi colpisce.</p>
<a href="#">READ MORE</a></div>
</div>
</div>';
					$data = array("title" => "Who We Are", 
								  "identifier" => "block_home_who",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}		
					// Block Bottom Info
					$content = '<ul>
			<li><i class="icon-truck"></i> Free Shipping</li>
			<li><i class="icon-gift"></i> Continuous Promotion</li>
			<li><i class="icon-gauge"></i> 30-Day Returns</li>
			<li><i class="icon-award"></i> Top seller</li>
		</ul>';
					$data = array("title" => "Block Bottom Info", 
								  "identifier" => "block_bottom_info",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}																									
					//Bottom menu
					$content = '<div class="bottom-menu-column">
<div class="shippingpolicy" style="width: 140px; float: left;">
<h4>Our Offers</h4>
<ul class="bottom-menu">
<li><a href="#">New products</a></li>
<li><a href="#">Top sellers</a></li>
<li><a href="#">Specials</a></li>
<li><a href="#">Manufacturers</a></li>
<li><a href="#">Suppliers</a></li>
<li><a href="#">Specials</a></li>
<li><a href="#">Service</a></li>
</ul>
</div>
<div class="shippingpolicy" style="width: 150px; float: left;">
<h4>Shipping Info</h4>
<ul class="bottom-menu">
<li><a href="#">Returns</a></li>
<li><a href="#">Delivery</a></li>
<li><a href="#">Service</a></li>
<li><a href="#">Gift Cards</a></li>
<li><a href="#">Mobile</a></li>
<li><a href="#">Gift Cards</a></li>
<li><a href="#">Manufacturers</a></li>
</ul>
</div>
<div class="shippingpolicy" style="width: 150px; float: left;">
<h4>Our Account</h4>
<ul class="bottom-menu">
<li><a href="#">Your Account</a></li>
<li><a href="#">information</a></li>
<li><a href="#">Addresses</a></li>
<li><a href="#">Discount</a></li>
<li><a href="#">Orders history</a></li>
<li><a href="#">Addresses</a></li>
<li><a href="#">Search Terms</a></li>
</ul>
</div>
</div>';
					$data = array("title" => "Bottom menu", 
								  "identifier" => "aditional_footer_left",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Bottom menu block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}							
	// Block contact top
					$content = '<div class="col2-set">
<div class="col-1">
<h3>Address &amp; Directions:</h3>
We are moving to a new location and will update the contact information very soon.</div>
<div class="col-1">
<h3>Media Contact</h3>
<p>If you are interested in working with us and want to hold the beauty of our work in your own hands, simply request a FO Promo Box. We&rsquo;ll send you a copy of it, directly to your doorstep.</p>
</div>
</div>';
					$data = array("title" => "Block Contact Top", 
								  "identifier" => "block_contact_top",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block Contact Top created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}		
	// Block contact bottom
					$content = '<h3>Maps</h3>
<p><img src="{{media url="wysiwyg/map.jpg"}}" alt="" /></p>';
					$data = array("title" => "Block Contact Bottom", 
								  "identifier" => "block_contact_bottom",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block Contact Bottom created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
// Block bottom right
					$content = '<p><img src="{{media url="wysiwyg/connect-payment.png"}}" alt="" /></p>';
					$data = array("title" => "Block Bottom Right", 
								  "identifier" => "block_bottom_right",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block header right created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}		
	// Block About Box
					$content = '<div class="span4">
<p><a href="#"><img src="{{media url="wysiwyg/img-about.jpg"}}" alt="" /></a></p>
</div>
<div class="span8">
<h4>About Responsive Theme</h4>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo, quis pretium turpis neque eget nulla. Curabitur dictum consectetur metus nec dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In congue, justo non cursus adipiscing, dui nibh scelerisque justo,</p>
<p><a href="#"><span>BUY THIS THEME</span></a></p>
</div>';
					$data = array("title" => "Block About Box", 
								  "identifier" => "block_about_box",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}		
	// Block Contact Box
					$content = '
											<div class="span5">
<p class="phone"><em class="icon-mobile"></em> 8(802)234-5678</p>
<p class="fax"><em class="icon-print"></em> 8(800)234-5678</p>
<p class="support"><em class="icon-mail"></em> <span>support@shop.com</span></p>
</div>
<div class="span7">
<ul class="social-icons">
<li class="twitter"><a href="#"><i class="icon-twitter-rect"></i></a></li>
<li class="facebook"><a href="#"><i class="icon-facebook-rect"></i></a></li>
<li class="googleplus"><a href="#"><i class="icon-gplus"></i></a></li>
<li class="linkedin"><a href="#"><i class="icon-linkedin-squared"></i></a></li>
<li class="youtube"><a href="#"><i class="icon-vimeo"></i></a></li>
</ul>
</div>										
				';
					$data = array("title" => "Block Contact Box", 
								  "identifier" => "block_contact_box",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
	// Block Brand list
					$content = '<ul class="slides">
<li><img src="{{media url="wysiwyg/logo1.png"}}" alt="" /></li>
<li><img src="{{media url="wysiwyg/logo2.png"}}" alt="" /></li>
<li><img src="{{media url="wysiwyg/logo3.png"}}" alt="" /></li>
<li><img src="{{media url="wysiwyg/logo04.png"}}" alt="" /></li>
<li><img src="{{media url="wysiwyg/logo05.png"}}" alt="" /></li>
<li><img src="{{media url="wysiwyg/logo06.png"}}" alt="" /></li>
</ul>';
					$data = array("title" => "Block Brand list", 
								  "identifier" => "block_brand_list",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}			
																				
         //End Setup Static Block					
				}
				
							
				
				$model = Mage::getModel('core/store');
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();
				$storeCode = Mage::getModel('core/store')->load($storeId)->getCode();
				$store = Mage::app()->getStore($storeId);
			
				$message = $this->__('BearStore Theme was successfully installed on <i>'.$storeName.'</i>!');
        Mage::getSingleton('adminhtml/session')->addSuccess($message);
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
      $this->_redirect('*/*');
		}    
}