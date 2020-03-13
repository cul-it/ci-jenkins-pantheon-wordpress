Feature: Logging in, Logging out

  @javascript
  Scenario: I can log-in and out with javascript
    Given I am logged in as an admin
    And I am on the dashboard
    Then I should see "Howdy"
    When I log out
    Then I should not see "Howdy"

  Scenario: I can log-in and out without javascript
    Given I am logged in as an admin
    And I am on the dashboard
    Then I should see "Howdy"
    When I log out
    Then I should not see "Howdy"
