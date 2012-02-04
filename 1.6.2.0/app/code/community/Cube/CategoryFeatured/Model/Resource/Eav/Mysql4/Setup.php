<?php
/**
* Installs the default featured attribute to the Default Attribute Set
*
* @author 	    Cube Websites
* @copyright	Cube Websites 2012
* @version	    2.0
* @package	    Cube_CategoryFeatured
* @link         https://github.com/cubewebsites/Cube-Category-Featured-Products
*/

class Cube_CategoryFeatured_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {
	
	public function getDefaultEntities() {
		return array(
			'catalog_product'	=>	array(
				'entity_model'					=>	'catalog/product',
				'attribute_model'				=>	'catalog/resource_eav_attribute',
				'table'							=>	'catalog/product',
				'additional_attribute_table'	=> 'catalog/eav_attribute',
                'entity_attribute_collection'	=> 'catalog/product_attribute_collection',
				'attributes'					=>	array(
					'cube_category_featured'	=>	array(
						'group'							=>	'General',
						'label'							=>	'Is Featured?',
						'type'							=>	'int',
						'input'							=>	'boolean',
						'default'						=>	'0',
						'class'							=>	'',
						'backend'						=>	'',
						'frontend'						=>	'',
						'source'						=>	'',
						'global'						=>	Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
						'visible'						=>	true,
						'required'						=>	false,
						'user_defined'					=>	true,
						'searchable'					=>	false,
						'filterable'					=>	false,
						'comparable'					=>	false,
						'visible_on_front'				=>	false,
						'visible_in_advanced_search'	=>	false,
						'unique'						=>	false
					)
				)
			)
		);
	}
	
}