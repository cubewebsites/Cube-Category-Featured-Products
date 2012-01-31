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
            //return array();
        }
		
		$ret = array();
		foreach ($activeCategories as $category) {
			//$ret[$category->getID()] = $category->getName();
			$children = $this->getChildCategories($category);			
			foreach($children as $k=>$v)
				$ret[] = array(
					'value' => $k,
					'label' => $v
				);
			
		}
		return $ret;
		
	}
	
	private function getChildCategories($category,$parentname='') {
		
		if (!$category->getIsActive()) {
            return '';
        }
		
		//array containing all the categories to return
		$ret	=	array();
		
		//Makes sure root categories aren't selectable
		if($category->getLevel() > 1)
			$ret[$category->getID()] = $category->getName() . $parentname;
		
		// get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
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
		
		foreach($activeChildren as $child) {			
			$childarray = $this->getChildCategories($child, " / " . $category->getName() . $parentname);
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
	
	/**
     * Get catagories of current store
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getStoreCategories()
    {
        $collection = Mage::getModel('catalog/category')->getCollection()
			->setStoreId($store)
			->addAttributeToSelect('name')
			->addAttributeToSelect('url_path')
			->addAttributeToSelect('is_active')
			->addAttributeToSort('parent_id', 'ASC')
			->addFilter('parent_id',1)
			->addAttributeToSort('name', 'ASC');
		return $collection;
    }

}