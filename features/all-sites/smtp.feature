@smtp
Feature: Test for basic site function
  Background: After I show the running environment
    Given I show the running environment
    And I am testing the correct domain

  @smtp_site_up
  Scenario: The web site is up and representing for all-sites
    Given PENDING : not working 
    And I am testing the correct domain
    And I go to the home page
    Then the page should show content "Cornell University"
    And I log in with SAML
    Then show me the page
    And user "James G. Reidy" is logged in
    And I visit page "admin/config/system/smtp"
    And I do not see complaints about javascript
    And I enter "xxx" for field "edit-smtp-username"
    
 @smtp_after
 Scenario: after login tests
    Given PENDING : not working 
    When I am testing the correct domain
    And I visit page "admin/config/system/smtp"
    And I do not see complaints about javascript
    And I change the SMTP user
    