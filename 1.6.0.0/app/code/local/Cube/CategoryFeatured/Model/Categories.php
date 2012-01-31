<?php

/* Updated to make it easier to show which store a category belongs to (for multiple stores with same category names)
 * Thanks to Eric
 * http://www.cubewebsites.com/blog/magento/freebie-magento-featured-products-widget/comment-page-1/#comment-452
 */

class Cube_CategoryFeatured_Model_Categories {

	public function toOptionArray() {
		$store = Mage::app()->getStore()->getRootCategoryId();
		$collection = Mage::getModel('catalog/category')
				->getCollection()
				->setStoreId($store)
				->addAttributeToSelect('name')
				->addAttributeToSelect('url_path')
				->addAttributeToSelect('is_active')
				->addAttributeToSort('parent_id', 'ASC')
				->addAttributeToSort('name', 'ASC');
		$ret = array();
		foreach ($collection as $category) {
					
			if($category->getLevel()==1 || !$category->getID())
				continue;
			
			$parent_name = $this->__getParentName($category);
			
//			$ret[] = array(
//				'value' => $category->getId(),
//				'label' => $category->getName() . $parent_name
//			);
		}
		return $ret;
	}
	
	private function __getParentName($category) {
		
		$parent_name = "";
		
		if($category->getParentID() && $category->getParentID() > 1) {			
			$parent = Mage::getModel('catalog/category')->load($category->getParentID());
			$parent_name .= " / {$parent->getName()}" . $this->__getParentName($parent);
		}
		
		return  $parent_name;
	}

}