<?php

/* Updated to make it easier to show which store a category belongs to (for multiple stores with same category names)
 * Thanks to Eric
 * http://www.cubewebsites.com/blog/magento/freebie-magento-featured-products-widget/comment-page-1/#comment-452
 */

class Cube_CategoryFeatured_Model_Categories {

	public function toOptionArray() {
		
		$activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return array();
        }
		
		$ret = array();
		foreach ($activeCategories as $category) {
			$children = $this->getChildCategories($category);
			foreach($children as $k=>$v)
				$ret[$k] = $v;
		}
		return $ret;
		
	}
	
	private function getChildCategories($category,$parentname='') {
		
		if (!$category->getIsActive()) {
            return '';
        }
		
		$ret	=	array();
		
		// get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
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
		
		$ret[$category->getID()] = $category->getName();
		foreach($activeChildren as $child) {
			$childarray = $this->getChildCategories($child->getID());
			foreach($childarray as $k => $v)
				$ret[$k]	=	$v;
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