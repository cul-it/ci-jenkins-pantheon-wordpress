Feature: Widgets

  @db
  Scenario: Viewing a widget
    Given I have the "meta" widget in "Blog Sidebar"
      | Title     |
      | My widget |
    And I am on the homepage
    Then I should see "My widget"
