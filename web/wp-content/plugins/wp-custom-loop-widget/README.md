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
      js/
widgets/
      partials/
            /africana_audio_list.php
            /africana_audio_single.php
            /africana_films_list.php
            /africana_films_single.php
            /africana_thesis_list.php
            /africana_thesis_single.php
            /engineering_db_list.php
            /engineering_db_sidebar.php
            /engineering_db_single.php
            /ilr_botm.php
            /ilr_botm_single.php
            /ilr_wit_list.php
            /ilr_wit_single.php
            /law_bitnerfellows.php
            /law_bitnerfellows_single.php
            /law_diversityfellows.php
            /law_diversityfellows_single.php
            /management_db_list.php
            /management_db_single.php
            /management_db_single_sidebar.php
            /management_faqs_list.php
            /management_faqs_single.php
            /management_search.php
            /math_collectedworks.php
            /psl_corebooks.php
            /psl_corebooks_single.php
            /psl_databases.php
            /psl_databases_single.php
            /psl_journals.php
            /psl_journals_single.php
            /rare_onlineexhibitions.php
            /rare_onlineexhibitions_single.php
            /staff_profiles.php
            /staff_profiles_single.php
      /custom-post-loops.php
      /index.php
      
index.php
wp-custom-post-loops.php
plugin.php
README.md
```

* `assets` directory - Holds all css and js for Plugin widgets
  * `css` directory - Holds all css
  * `js` directory - Holds all js
* `widgets` directory - Holds Plugin widgets
  * `/custom-post-loops.php` - Custom Post Loops Widget class
  * `/index.php` - Prevent direct access to directories
  * `partials` directory - Holds all code for individual custom loops
      * `/staff_profiles.php` - All Units - Display a list of all Staff members on any given unit
      * `/staff_profiles_single.php` - All Units - Display single Staff member on any given unit
      * `/africana_audio_list.php` - Africana - Display a list of all Audio
      * `/africana_audio_single.php` - Africana - Display a single Audio
      * `/africana_films_list.php` - Africana - Display a list of all Films
      * `/africana_films_single.php` - Africana - Display a single Film
      * `/africana_thesis_list.php` - Africana - Display a list of all Theses
      * `/africana_thesis_single.php` - Africana - Display a single Thesis
      * `/engineering_db_list.php` - Engineering - Display Databases list
      * `/engineering_db_sidebar.php` - Engineering - Display a single Database's sidebar content
      * `/engineering_db_single.php` - Engineering - Display a single Database's main content
      * `/ilr_botm.php` - ILR - Display Book of the Month list
      * `/ilr_botm_single.php` - ILR - Display a single Book of the Month
      * `/ilr_wit_list.php` - ILR - Display Workplace Issues Today list
      * `/ilr_wit_single.php` - ILR - Display Workplace Issues Today Single Item
      * `/law_bitnerfellows.php` - Law - Display a list of Bitner Fellows
      * `/law_bitnerfellows_single.php` - Law - Display a single Bitner Fellow
      * `/law_diversityfellows.php` - Law - Display a list of Diversity Fellows
      * `/law_diversityfellows_single.php` - Law - Display a single Diversity Fellow
      * `/management_db_list.php` - Management - Display a list of Databases
      * `/management_db_single.php` - Management - Display a single Database's main content
      * `/management_db_single_sidebar.php` - Management - Display a single Database's sidebar content
      * `/management_faqs_list.php` - Management - Display a list of FAQs
      * `/management_faqs_single.php` - Management - Display a single FAQ
      * `/management_faqs_single.php` - Management - Display Search results
      * `/math_collectedworks.php` - Mathematics - Display a table view list of all Collected Works
      * `/math_collectedworks_single.php` - Mathematics - Display a single Collected Work
      * `/psl_corebooks.php` - PSL - Display a grid view of Core Books based on the page (Astronomy, Physics, Chemistry)
      * `/psl_corebooks_single.php` - PSL - Display a single Core Book
      * `/psl_databases.php` - PSL - Display a list of Databases based on the page (Astronomy, Physics, Chemistry)
      * `/psl_databases_single.php` - PSL - Display a single Database
      * `/psl_journals.php` - PSL - Display a list of Journals based on the page (Astronomy, Physics, Chemistry)
      * `/psl_journals_single.php` - PSL - Display a single Journal
      * `/rare_onlineexhibitions.php` - Rare - Display a grid view of all Online Exhibitions
      * `/rare_onlineexhibitions_single.php` - Rare - Display a single Online Exhibition
* `index.php`	- Prevent direct access to directories
* `wp-custom-post-loops.php`	- Main plugin file, used as a loader if plugin minimum requirements are met.
* `plugin.php` - The actual Plugin file/Class.


### Updating the Plugin: 

1. Any updates to the loops should be made in the respective partials for that particular post type, unit, and loop combination.
2. New custom loops that are needed for additional custom post types on unit sites should be added as a new partial in the `widgets/partials/` directory.
3. Any assets such as CSS or JS files should be added to the appropriate folder within the `assets` directory. Then these assets should be added to the corresponding loop using either `wp_enqueue_style` or `wp_enqueue_script`.
4. After a new partial has been added, edit the `register_controls` and `render` functions within the `widgets/custom-post-loops.php` file.
      * Editing `register_controls`
        * If unit already exists, add this new partial to the dropdown select that matches the unit this loops belongs within.
        * If unit does not exist, create a unit variable with the necessary select options. Duplicate the statement indicating `domain`, add the correct sub-domain name and unit variable.
      * Editing `render` will be adding an additional statement to render the partial when the related select option is chosen.


### Additional Resources:

For more documentation please see [Elementor Developers Resource](https://developers.elementor.com/creating-an-extension-for-elementor/).
