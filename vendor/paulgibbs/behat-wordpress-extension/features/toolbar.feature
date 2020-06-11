@javascript
Feature: Toolbar

  Background:
    Given I am logged in as an admin
    And I am on the homepage

  Scenario: I can go to the support forums
    When I follow the toolbar link "WordPress > Support Forums"
    Then I should be on "https://wordpress.org/support/"

  Scenario: I can add a new page
    When I follow the toolbar link "New > Page"
    Then I should be on the "Add New Page" page

  @db
  Scenario: I can search using the toolbar
     Given there are posts:
       | post_type | post_title | post_content | post_status |
       | page      | Test Post  | Hello World  | publish     |
    When I search for "Hello World" in the toolbar
    Then I should see "Search results"
