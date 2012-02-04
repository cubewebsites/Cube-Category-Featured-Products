<?php
/**
* Logic to fetch products for displaying the Featured Products widget
*
* @author 	    Cube Websites
* @copyright	Cube Websites 2012
* @version	    2.0
* @package	    Cube_CategoryFeatured
* @link         https://github.com/cubewebsites/Cube-Category-Featured-Products
*/

class Cube_CategoryFeatured_Block_List extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface {

	/**
	 * A model to serialize attributes
	 * @var Varien_Object
	 */
	protected $_serializer = null;
	protected $_featuredattribute = 'cube_category_featured';
	protected $_products_per_row;
	protected $_productCollection;

	/**
	 * Initialization
	 */
	protected function _construct() {
		$this->_serializer = new Varien_Object();

		$this->addData(array(
			'cache_lifetime' => 86400,
			'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG),
		));

		parent::_construct();
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo() {
		return array(
			'CATALOG_PRODUCT_CATEGORY_FEATURED',
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			Mage::getSingleton('customer/session')->getCustomerGroupId(),
			'template' => $this->getTemplate(),
			md5(serialize($this->getData()))
		);
	}

	/**
	 * Produce links list rendered as html
	 * @return string
	 */
	protected function _toHtml() {
		return $this->renderView();
	}

	/**
	 * Returns a collection of all the selected categories
	 * @return Varien_Data_Collection_Db
	 */
	protected function _getCategories() {
		$category = Mage::getModel('catalog/category');
		$collection = $category->getCollection();
		$collection->addAttributeToSelect('name');
		$collection->addAttributeToSelect('url_path');
		$collection->addAttributeToSelect('id');
		$collection->getSelect()->order('path');
		$collection->addFieldToFilter('entity_id', array('in' => explode(',', $this->getData('categories'))));
		return $collection;
	}

	/**
	 * Fetches product for a specified category
	 * @return Varien_Data_Collection_Db
	 */
	protected function _getProducts($category) {
		$product_type = $this->getData('product_type');
		$collection = $this->_setProductCollection($category->getProductCollection());

		switch (strtolower($product_type)) {
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
		$collection->load();
		Mage::getModel('review/review')->appendSummary($collection);
		return $collection;
	}

	/**
	 * Fetches products for all categories
	 * @return Varien_Data_Collection_Db
	 */
	protected function _getAllProducts() {
		$rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
		$category = Mage::getModel('catalog/category')->load($rootCategoryId);
		$collection = $category->getProductCollection();

		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection = $this->_setProductCollection($collection);

		$product_type = $this->getData('product_type');
		switch (strtolower($product_type)) {
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
		$collection->load();
		Mage::getModel('review/review')->appendSummary($collection);
		return $collection;
	}

	/**
	 * How many products are displayed per row
	 * @return int
	 */
	public function getProductsPerRow() {
		if (is_null($this->_products_per_row))
			$this->_products_per_row = (int) $this->getData('products_per_row') ? (int) $this->getData('products_per_row') : 3;
		return $this->_products_per_row;
	}

	/**
	 * Run whenever a new product collection set to apply basic filters
	 * Shouldn't be run on it's own as the collection isn't actually stored
	 * @return Varien_Data_Collection_Db
	 */
	protected function _initProductCollection($collection) {
		$collection->addStoreFilter();
		$this->_addProductAttributesAndPrices($collection);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		return $collection;
	}

	/**
	 * Sets the product collection to use based on the selected category
	 * @return Varien_Data_Collection_Db
	 */
	protected function _setProductCollection($collection) {
		$this->_productCollection = $this->_initProductCollection($collection);
		return $this->_productCollection;
	}

	/**
	 * Fetches the product collection object
	 * @return Varien_Data_Collection_Db
	 */
	protected function _getProductCollection() {
		if (!is_null($this->_productCollection)) {
			return $this->_productCollection;
		}
		return $this->_productCollection;
	}

	/**
	 *
	 * Gets the featured attribute handle to use for filtering
	 * @return String
	 */
	protected function _getFeaturedCode() {
		return $this->getData('featured_code') ? $this->getData('featured_code') : $this->_featuredattribute;
	}

	/**
	 * Ensures only featured products are returned
	 * @return Varien_Data_Collection_Db
	 */
	protected function _applyFeaturedCode() {
		$featuredcode = $this->_getFeaturedCode();
		$collection = $this->_getProductCollection();
		$collection->addAttributeToFilter($featuredcode, array('=' => 1));
		$this->_randomizeCollection();
		return $collection;
	}

	/**
	 * Orders the results randomly
	 * @return Varien_Data_Collection_Db
	 */
	protected function _randomizeCollection() {
		$this->_getProductCollection()->getSelect()->order('rand()');
		return $this->_getProductCollection();
	}

	/**
	 * Sets limits based on the num_products widget option
	 * @return Varien_Data_Collection_Db
	 */
	protected function _applyLimits() {
		$this->_getProductCollection()
				->setPageSize((int) $this->getData('num_products'))
				->setCurPage(1);
		return $this->_getProductCollection();
	}

}