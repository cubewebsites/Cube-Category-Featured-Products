<?php
class Cube_CategoryFeatured_Model_Categories {
	public function toOptionArray() {
		$store	    = Mage::app()->getStore()->getRootCategoryId();
		$collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($store)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('url_path')
            ->addAttributeToSelect('is_active');
        $ret	=	array();
        foreach($collection as $category) {
        	$ret[]	=	array(
        		'value'	=>	$category->getId(),
        		'label'	=>	$category->getName()
        	);	
        } 
        return $ret;
	}
}