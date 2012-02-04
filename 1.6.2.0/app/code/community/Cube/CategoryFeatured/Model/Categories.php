<?php

/**
 * Allows Categories to be selected via the admin
 *
 * @author 	    Cube Websites
 * @copyright	Cube Websites 2012
 * @version	    2.0
 * @package	    Cube_CategoryFeatured
 * @link         https://github.com/cubewebsites/Cube-Category-Featured-Products
 */
class Cube_CategoryFeatured_Model_Categories {

	public function toOptionArray() {

		//get a list of all active top level categories
		$activeCategories = array();
		foreach ($this->getStoreCategories() as $child) {
			if ($child->getIsActive()) {
				$activeCategories[] = $child;
			}
		}
		$activeCategoriesCount = count($activeCategories);
		$hasActiveCategoriesCount = ($activeCategoriesCount > 0);

		//no active categories - exit
		if (!$hasActiveCategoriesCount) {
			return array();
		}

		//build up an array of active categories as value / label array
		$ret = array();
		foreach ($activeCategories as $category) {
			$children = $this->getChildCategories($category);
			foreach ($children as $k => $v)
				$ret[] = array(
					'value' => $k,
					'label' => $v
				);
		}
		return $ret;
	}

	/**
	 * Recursively returns a value / label array of all active categories
	 * @param Mage_Catalog_Model_Category $category
	 * @param String $parentname
	 * @return array
	 */
	private function getChildCategories($category, $parentname='') {

		//category not active - skip it
		if (!$category->getIsActive()) {
			return '';
		}

		//array containing all the categories to return
		$ret = array();

		/* Add the current category to return array
		 * Root categories shouldn't be selected
		 */
		if ($category->getLevel() > 1)
			$ret[$category->getID()] = $category->getName() . $parentname;

		// get all children
		if (Mage::helper('catalog/category_flat')->isEnabled()) {
			$children = (array) $category->getChildrenNodes();
			$childrenCount = count($children);
		} else {
			$children = $category->getChildrenCategories();
			$childrenCount = $children->count();
		}
		$hasChildren = ($children && $childrenCount);

		// select active children
		$activeChildren = array();
		foreach ($children as $child) {
			if ($child->getIsActive()) {
				$activeChildren[] = $child;
			}
		}
		$activeChildrenCount = count($activeChildren);
		$hasActiveChildren = ($activeChildrenCount > 0);

		/**
		 * Use recursion to include all children categories too
		 */
		foreach ($activeChildren as $child) {
			$childarray = $this->getChildCategories($child, " / " . $category->getName() . $parentname);
			foreach ($childarray as $k => $v)
				$ret[$k] = $v;
		}

		return $ret;
	}

	/**
	 * Gets the full path of parents for a given category
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	private function __getParentName($category) {

		$parent_name = "";
		if ($category->getParentID() && $category->getParentID() > 1) {
			$parent = Mage::getModel('catalog/category')->load($category->getParentID());
			$parent_name .= " / {$parent->getName()}" . $this->__getParentName($parent);
		}

		return $parent_name;
	}

	/**
	 * Get categories of current store
	 * @return Varien_Data_Collection_Db
	 */
	public function getStoreCategories() {
		$collection = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToSelect('name')
				->addAttributeToSelect('url_path')
				->addAttributeToSelect('is_active')
				->addAttributeToSort('parent_id', 'ASC')
				->addFieldToFilter('parent_id', 1)
				->addAttributeToSort('name', 'ASC');
		return $collection;
	}

}