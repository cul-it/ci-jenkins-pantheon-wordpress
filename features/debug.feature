@debugging
Feature: Test anything
  Background: After I show the running environment
    Given I show the running environment
    And I am testing the correct domain

  @debugging_hidden_links
  Scenario: The web site has links hidden to poltergiest
    When I visit page "ares/search"
      And I search ares for "PSYCH"
    Then the ares results should contain "Mann Library Reserve"

