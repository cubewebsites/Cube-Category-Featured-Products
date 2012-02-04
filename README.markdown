#Cube Category Featured Products Widget
Cube Category Featured Products is a widget/block which lets you show featured products with the option to group by category

##Requirements
* Magento 1.4.0.0 - 1.6.2.0

## Installation
Copy the `app` and `skin` directories into your Magento installation  

##Usage

###CMS Pages
1. Login to your Magento admin
2. Go to CMS > Pages
3. Select the page you want to use
4. Click the `Content` tab
5. In the WYSIWYG Editor, click the _Insert Widget_ button (for me it's top row, second from the left)
6. As _Widget Type_ select `Cube Websites - Category Featured Products`
7. Configure your Widget using the available options

**Frontend Template**  
`Categorised` - Displays the products grouped by the category they belong in  
`Mixed` - Displays all the products in a single block with no grouping

**Categories** (only applies if _Categorised_ Frontend Template is selected)  
The categories to select products from  

**Number of Products**  
If `Categorised` - the number of products from each category  
If `Mixed` - the total number of products  

**Products Per Row**  
How many products will be displayed on each row in the frontend  

**Products to Display**  
`Featured` - Displays featured products only  
`All` - Displays all available products  

**Featured Attribute Code** (optional)  
By default this is **cube_category_featured** but if you've created your own featured attribute code then you can use that instead.

###Within your template
1. Open your favourite text editor
2. Within find the Layout XML you want to add the block to within the app/design/frontend directory
3. In your _.xml_ file, add the following in the relevant position  

		<block type="categoryfeatured/list" name="featured_products" as="featuredProducts" template="categoryfeatured/block.phtml">
			<action method="setData"><name>categories</name><value>1,2,3,4</value></action>
			<action method="setData"><name>num_products</name><value>4</value></action>
			<action method="setData"><name>products_per_row</name><value>4</value></action>
			<action method="setData"><name>product_type</name><value>featured</value></action>
			<action method="setData"><name>featured_code</name><value>cube_category_featured</value></action>
		</block>

**template**  
`categoryfeatured/block.phtml` - Displays the products grouped by the category they belong in  
`categoryfeatured/mixed.phtml` - Displays all the products in a single block with no grouping

**categories** (only applies if _categoryfeatured/block.phtml_ template is selected)  
The categories to select products from (comma separated)  

**num_products**  
If `categoryfeatured/block.phtml` - the number of products from each category  
If `categoryfeatured/mixed.phtml` - the total number of products  

**products_per_row**  
How many products will be displayed on each row in the frontend  

**product_type**  
`featured` - Displays featured products only  
`all` - Displays all available products  

**featured_code** (optional)  
By default this is **cube_category_featured** but if you've created your own featured attribute code then you can use that instead.

If you need to output the block in a certain location of your template, then make sure you include the following in your .phtml file
    `<?php echo $this->getChildHtml('featuredProducts') ?>`

##Changelog
**2.0 Major Upgrade**

*  Massive code rewrite
*  Made to work with Magento 1.6.0.0
*  Added caching
*  Updated mixed template
*  Removed styles which reduced flexibility for users (overriding default font colours)
*  Auto-creation of Featured Attribute
*  Allows Review display
*  Added Add to Cart button
*  Included minimal prices
*  Applied filters to only fetch products from current store
*  Display path to categories to make category selection easier on multi-store installation
*  Created documentation
*  Bug fixes

**1.0 Initial Release**