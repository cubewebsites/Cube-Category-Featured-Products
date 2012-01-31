<?php

/* Updated to make it easier to show which store a category belongs to (for multiple stores with same category names)
 * Thanks to Eric
 * http://www.cubewebsites.com/blog/magento/freebie-magento-featured-products-widget/comment-page-1/#comment-452
 */

class Cube_CategoryFeatured_Model_Categories {

	public function toOptionArray() {
		
		return $this->getChildCategories(0);
		
	}
	
	private function getChildCategories($category,$parentname='') {
		
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