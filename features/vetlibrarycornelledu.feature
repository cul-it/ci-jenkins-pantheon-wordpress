@vetlibrarycornelledu
Feature: Test for Vet Library site function
  Background: After I show the running environment
    Given I show the running environment
    And I am testing the correct domain

  @vet_site_up
  Scenario: The web site is up and representing
    Given I go to the home page
    Then the page should show content "Flower-Sprecher Veterinary Library"
