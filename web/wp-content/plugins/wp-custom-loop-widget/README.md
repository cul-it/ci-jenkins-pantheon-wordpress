# WP Custom Loop Widget

This is a plugin that creates a widget within Elementor for displaying Custom Post Loops for CUL unit library sites. These custom loops are used to display Custom Post Types for each unit, as these vary by unit and tend to have different fields and ways that they want them displayed.

Keeping all of the loops in this plugin instead of the Code Snippets plugin allows for editing of the pages these loops are embedded in with Elementor (previously throwing an error when trying to edit). 


### How to use: 

1. This plugin should be installed like any other WP plugin and then activated.

2. Once the plugin has been activated, navigate to a page that currently displays, or will display, a custom post loop for a given custom post type on that unit site.

3. In the Elementor widgets panel, there will be a widget that is called Custom Post Loops. Drag this onto the page where this content should display. This will show an area in the Elementor panel with a dropdown menu.

4. Using the dropdown menu, select the loop that is needed for this page. After the loop has been selected, update the page and view on the front end to see the loop in action.


### Plugin Structure: 
```
assets/
      css/
            /staff_profiles.css
widgets/
      partials/
            /africana_thesislist.php
            /africana_thesissingle.php
            /ilr_botm.php
            /ilr_wit.php
            /law_fellows.php
            /management_dblist.php
            /management_dbsingle.php
            /management_dbsinglesidebar.php
            /management_faqslist.php
            /management_faqssingle.php
            /math_collectedworks.php
            /psl_corebooks.php
            /psl_databases.php
            /psl_journals.php
            /rare_onlineexhibitions.php
      /custom-post-loops.php
      /index.php
      
index.php
wp-custom-post-loops.php
plugin.php
README.md
```

* `assets` directory - Holds all css and js for Plugin widgets
  * `css` directory - Holds all css
      * `/staff_profiles.css` - All Units - CSS for Staff Profiles loop
* `widgets` directory - Holds Plugin widgets
  * `/custom-post-loops.php` - Custom Post Loops Widget class
  * `/index.php` - Prevent direct access to directories
  * `partials` directory - Holds all code for individual custom loops
      * `/staff_profiles.php` - All Units - Display a list of all Staff members on any given unit
      * `/africana_thesislist.php` - Africana - Display a list of all Theses
      * `/africana_thesissingle.php` - Africana - Display a single Thesis
      * `/ilr_botm.php` - ILR - Display Book of the Month list
      * `/ilr_wit.php` - ILR - Display Workplace Issues Today list
      * `/law_fellows.php` - Law - Display a list of Fellows based on page (Diversity or Bitner)
      * `/management_dblist.php` - Management - Display a list of Databases
      * `/management_dbsingle.php` - Management - Display a single Database's main content
      * `/management_dbsinglesidebar.php` - Management - Display a single Database's sidebar content
      * `/management_faqslist.php` - Management - Display a list of FAQs
      * `/management_faqssingle.php` - Management - Display a single FAQ
      * `/math_collectedworks.php` - Mathematics - Display a table view list of all Collected Works
      * `/psl_corebooks.php` - PSL - Display a grid view of Core Books based on the page (Astronomy, Physics, Chemistry)
      * `/psl_databases.php` - PSL - Display a list of Databases based on the page (Astronomy, Physics, Chemistry)
      * `/psl_journals.php` - PSL - Display a list of Journals based on the page (Astronomy, Physics, Chemistry)
      * `/rare_onlineexhibitions.php` - Rare - Display a grid view of all Online Exhibitions
* `index.php`	- Prevent direct access to directories
* `wp-custom-post-loops.php`	- Main plugin file, used as a loader if plugin minimum requirements are met.
* `plugin.php` - The actual Plugin file/Class.


### Updating the Plugin: 

1. Any updates to the loops should be made in the respective partials for that particular post type, unit, and loop combination.
2. New custom loops that are needed for additional custom post types on unit sites should be added as a new partial in the `widgets/partials/` directory.
3. Any assets such as CSS or JS files should be added to the appropriate folder within the `assets` directory. Then these assets should be added to the corresponding loop using either `wp_enqueue_style` or `wp_enqueue_script`.
4. After a new partial has been added, edit the `register_controls` and `render` functions within the `widgets/custom-post-loops.php` file.
      * Editing `register_controls` will be adding this new partial to the dropdown select with a nice name.
      * Editing `render` will be adding an additional statement to render the partial when the related select option is chosen.


### Additional Resources:

For more documentation please see [Elementor Developers Resource](https://developers.elementor.com/creating-an-extension-for-elementor/).
