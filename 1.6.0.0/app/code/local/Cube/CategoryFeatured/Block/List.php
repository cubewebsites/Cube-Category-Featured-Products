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
        $collection->addFieldToFilter('entity_id',array('in'=> explode(',',$this->getData('categories'))));
        //var_dump($collection);
        return $collection;
        explode(",",$config);    	
    }
    
    protected function _getProducts($category) {
    	$featured_code	=	$this->getData('featured_code')?$this->getData('featured_code'):$this->_featuredattribute;
    	$product_type	=	$this->getData('product_type');
    	
    	$collection		=	$category->getProductCollection();
    	$visibility = array(  
             Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,  
             Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG  
        );
        $collection->addAttributeToFilter('visibility', $visibility);
    	
		$collection->addAttributeToSelect('*');		
		$collection->addAttributeToFilter( 'status' , array('='=> 1) );
		
		switch(strtolower($product_type)) {
			case 'featured':
				$collection->addAttributeToFilter( $featured_code , array('='=> 1) );
				$collection->getSelect()->order('rand()');
				break;
			//case 'bestsellers':
				//$collection->addOrderedQty();
				//$collection->setOrder('ordered_qty', 'desc');
				//break;
			case 'all':
			default:
				$collection->getSelect()->order('rand()');
				break;				
		}
				
		$collection->setPage(1,(int)$this->getData('num_products'));
		return $collection;
    }
    
	protected function _getAllProducts() {
		
		$category 		=	Mage::getModel('catalog/category');
    	$featured_code	=	$this->getData('featured_code')?$this->getData('featured_code'):$this->_featuredattribute;
    	$product_type	=	$this->getData('product_type');
    	
    	$collection		=	$category->getProductCollection();    	
    	
    	$visibility = array(  
             Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,  
             Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG  
        );
        $collection->addAttributeToFilter('visibility', $visibility);
		
		$collection->addAttributeToSelect('*');		
		$collection->addAttributeToFilter( 'status' , array('='=> 1) );
		
		switch(strtolower($product_type)) {
			case 'featured':
				$collection->addAttributeToFilter( $featured_code , array('='=> 1) );
				$collection->getSelect()->order('rand()');
				break;
			//case 'bestsellers':
				//$collection->addAttributeToFilter( 'best_seller' , array('='=> 1) );
				//$collection->addOrderedQty();
				//$collection->setOrder('ordered_qty', 'desc');
				//break;
			case 'all':
			default:
				$collection->getSelect()->order('rand()');
				break;				
		}
		
		
		$collection->setPage(1,(int)$this->getData('num_products'));
		return $collection;
    }
    
    public function getProductsPerRow() {
    	return $this->_products_per_row;
    }
} 