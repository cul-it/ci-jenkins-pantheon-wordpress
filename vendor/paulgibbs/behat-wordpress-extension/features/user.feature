Feature: Managing users

  Scenario: I can add a new user
    Given I am logged in as an admin
    And I am on the dashboard
    And I go to menu item "Users"
    When I click on the "Add New" link in the header
    Then I should be on the "Add New User" page
