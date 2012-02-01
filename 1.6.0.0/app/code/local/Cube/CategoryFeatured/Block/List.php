<?php
class Cube_CategoryFeatured_Block_List
    extends Mage_Catalog_Block_Product_Abstract
    implements Mage_Widget_Block_Interface
{
    /**
     * A model to serialize attributes
     * @var Varien_Object
     */
    protected $_serializer = null;
    protected $_featuredattribute	=	'cube_category_featured';
    protected $_products_per_row;
	protected $_productCollection;

    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->_serializer = new Varien_Object();
        $this->_products_per_row	=	(int)$this->getData('products_per_row')?(int)$this->getData('products_per_row'):3;	
        parent::_construct();        
    }

    /**
     * Produce links list rendered as html
     *
     * @return string
     */
    protected function _toHtml()
    {        
        return $this->renderView();
    }

    protected function _getCategories() {
    	$category 	= Mage::getModel('catalog/category');
        $collection = $category->getCollection();
        $collection->addAttributeToSelect('name');      
        $collection->addAttributeToSelect('url_path');
        $collection->addAttributeToSelect('id');
		$collection->getSelect()->order('path');
        $collection->addFieldToFilter('entity_id',array('in'=> explode(',',$this->getData('categories'))));        
        return $collection;	
    }
    
    protected function _getProducts($category) {
    	
    	$product_type	=	$this->getData('product_type');    	
		$this->_setProductCollection($category);
		$collection		=	$this->_getProductCollection();
		
		switch(strtolower($product_type)) {
			case 'featured':
				$this->_applyFeaturedCode();
				break;
			//case 'bestsellers':
				//$collection->addOrderedQty();
				//$collection->setOrder('ordered_qty', 'desc');
				//break;
			case 'all':
			default:
				$this->_randomizeCollection();
				break;				
		}
		$this->_applyLimits();
		return $collection->load();
    }
    
	protected function _getAllProducts() {
		
		$category 		=	Mage::getModel('catalog/category');    	
    	$product_type	=	$this->getData('product_type');
		$this->_setProductCollection($category);
		$collection		=	$this->_getProductCollection();
		
		switch(strtolower($product_type)) {
			case 'featured':
				$this->_applyFeaturedCode();
				break;
			//case 'bestsellers':
				//$collection->addAttributeToFilter( 'best_seller' , array('='=> 1) );
				//$collection->addOrderedQty();
				//$collection->setOrder('ordered_qty', 'desc');
				//break;
			case 'all':
			default:
				$this->_randomizeCollection();
				break;				
		}
		
		$this->_applyLimits();
		return $collection->load();
    }
    
    public function getProductsPerRow() {
    	return $this->_products_per_row;
    }
	
	protected function _initProductCollection($category) {
		$collection		=	$category->getProductCollection()->addStoreFilter(); 	        
		$collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());		
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		return $collection;
	}
	
	protected function _setProductCollection($category) {
		$this->_productCollection = $this->_initProductCollection($category);
		return $this->_productCollection;
	}
	
	protected function _getProductCollection() {
		if(!is_null($this->_productCollection)) {
			return $this->_productCollection;
		}
		return $this->_setProductCollection($collection);	
	}
	
	protected function _getFeaturedCode() {
		return $this->getData('featured_code')?$this->getData('featured_code'):$this->_featuredattribute;
	}
	
	protected function _applyFeaturedCode() {
		$collection = $this->_getProductCollection();
		$collection->addAttributeToFilter( $featured_code , array('='=> 1) );
		$this->_randomizeCollection();
		return $collection;
	}
	
	protected function _randomizeCollection() {
		$this->_getProductCollection()->getSelect()->order('rand()');
		return $this->_getProductCollection();
	}
	
	protected function _applyLimits() {
		$this->_getProductCollection()->setPage(1,(int)$this->getData('num_products'));
		return $this->_getProductCollection();
	}
} 