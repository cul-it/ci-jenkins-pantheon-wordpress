@site_basics
Feature: Test for basic site function
  Background: After I show the running environment
    Given I show the running environment
    And I am testing the correct domain

  @site_up
  Scenario: The web site is up and representing
    Given I go to the home page
    Then the page should show content "About"

  @https_only
  Scenario: http requests are redirected to https
    Given I go to the home page
    Then the page should show content "About"
    And the protocol should be https

  @https_only
  Scenario: redirect to https on another page
    Given I go to the home page
    Then I use http to go to "/about"
    And the protocol should be https

  @simplesaml
  Scenario: Be sure simplesaml configuration link works
    Given I go to the home page
    When I go to /simplesaml
    Then the page should not show content "Configuration error"
    And the page should show content "SimpleSAMLphp authentication software"

  @simplesaml
  @simplesaml-authentication
  Scenario: Test authentication page in simplesaml
    When I go to /simplesaml/admin/
    Then the page should show content "Enter your username and password"

  # @simplesaml
  # @simplesaml-federation
  # Scenario: Test Federation link in simplesaml installation page
  #   Given PENDING
  #   Given I go to /simplesaml
  #   And I click on the "Federation" link
  #   Then the page should not show content "Configuration error"

  # @simplesaml
  # @simplesaml-metadata
  # Scenario: Simplesaml should have metadata availabla
  #   Given I go to /simplesaml/module.php/saml/sp/metadata/default-sp
  #   Then the page should show content "urn:oasis:names:tc:SAML:2.0:metadata"
  #   And the page should show content "mailto:CUL-LIBSYS-L@list.cornell.edu"

  # @simplesaml
  # @simplesaml-two-step
  # Scenario: Test simplesaml installation page hits two-step login
  #   When I go to /simplesaml
  #   And I click on the "Authentication" link
  #   And I click on the "Test configured authentication sources" link
  #   And I click on the "default-sp" link
  #   Then I should see the CUWebLogin dialog
