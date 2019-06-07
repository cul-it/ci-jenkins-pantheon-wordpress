@db
Feature: You can write in visual and text mode

  Background:
    Given I am logged in as an admin
    And there are posts:
      | post_title      | post_content              | post_status | post_author |
      | Just my article | The content of my article | publish     | admin       |
    And I am on the edit post screen for "Just my article"

  @javascript
  Scenario: I can update a post using the text view
    When I change the title to "Article written in text mode"
    And I switch to the post content editor's Text mode
    Then the post content editor is in Text mode

    When I enter the following content into the post content editor:
      """
      <strong>HTML</strong> tags should be interpreted, and not displayed in text mode.
      """
    And I press the update button
    Then I should see a status message that says "Post updated"

    When I follow "View Post"
    Then I should see "Article written in text mode"
    And I should see "HTML tags should be interpreted, and not displayed in text mode."
    And I should not see "<strong>HTML</strong>"

  @javascript
  Scenario: I can update a post using the text view
    When I change the title to "Article written in visual mode"
    And I switch to the post content editor's Visual mode
    Then the post content editor is in Visual mode

    When I enter the following content into the post content editor:
      """
      <strong>HTML</strong> tags should be displayed, and not parsed in visual mode.
      """
    And I press the update button
    Then I should see a status message that says "Post updated"

    When I follow "View Post"
    Then I should see "Article written in visual mode"
    And I should see "<strong>HTML</strong> tags should be displayed, and not parsed in visual mode."
    And I should not see "HTML tags"
