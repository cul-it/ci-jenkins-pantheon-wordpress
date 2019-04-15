<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use PaulGibbs\WordpressBehatExtension\PageObject\EditPostPage;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ExpectationException;

/**
 * Provides step definitions relating to editing content in wp-admin.
 */
class EditPostContext extends RawWordpressContext
{

    /**
     * Edit post/page/post-type page (/wp-admin/post.php?post=<id>&action=edit) object.
     *
     * @param EditPostPage
     */
    private $edit_post_page;

    /**
     * Constructor.
     *
     * @param EditPostPage $edit_post_page The page object representing the edit post page.
     */
    public function __construct(EditPostPage $edit_post_page)
    {
        parent::__construct();

        $this->edit_post_page = $edit_post_page;
    }

    /**
     * Go to the edit post admin page for the referenced post.
     *
     * This step allows you to specify the post type to consider.
     *
     * Example: Given I am on the edit post screen for "Hello World"
     * Example: Given I am on the edit event screen for "Some Event"
     *
     * @Given /^I am on the edit ([a-zA-z_-]+) screen for "([^"]*)"$/
     *
     * @param string $post_type The post type of the referenced 'post' being edited.
     * @param string $title The name of the 'post' being edited.
     */
    public function iGoToEditScreenForPostType($post_type, $title)
    {
        $post = $this->getDriver()->content->get($title, ['by' => 'title', 'post_type' => $post_type]);
        $this->edit_post_page->open(array(
            'id' => $post->ID,
        ));
    }

    /**
     * Go to the edit post admin page for the referenced post.
     *
     * This step works for all post types.
     *
     * Example: Given I am on the edit screen for "Hello World"
     * Example: Given I am on the edit screen for "Some Event"
     *
     * @Given /^I am on the edit screen for "(?P<title>[^"]*)"$/
     *
     * @param string $title The name of the 'post' being edited.
     */
    public function iGoToEditScreenFor($title)
    {
        $post = $this->getDriver()->content->get($title, ['by' => 'title']);
        $this->edit_post_page->open(array(
            'id' => $post->ID,
        ));
    }

    /**
     * Change the title on the edit post screen.
     *
     * Example: When I change the title to "Hello World"
     *
     * @When /^I change the title to "(?P<title>[^"]*)"$/
     *
     * @param string $title The title to enter in the title field on the edit post page.
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iChangeTitleTo($title)
    {
        $this->edit_post_page->setContentTitle($title);
    }

    /**
     * Switch the mode of the post content editor.
     *
     * Example: When I switch to the post content editor's Visual mode
     * Example: When I switch to the post content editor's Text mode
     *
     * @When /^I switch to the post content editor's (visual|text) mode$/i
     *
     * @param string $mode The mode (visual or text) to switch to in the editor.
     */
    public function iSelectPostContentEditorMode($mode)
    {
        $content_editor = $this->edit_post_page->getContentEditor();
        $content_editor->setMode(strtoupper($mode));
    }

    /**
     * Enter the content into the content editor.
     *
     * Example: When I enter the following content into the post content editor:
     *   """
     *   Welcome to WordPress. This is your first post. Edit or delete it, then start writing!
     *   """
     *
     * @When I enter the following content into the post content editor:
     *
     * @param PyStringNode $content The content to enter into the editor.
     */
    public function iEnterContentIntoPostContentEditor(PyStringNode $content)
    {
        $content_editor = $this->edit_post_page->getContentEditor();
        $content_editor->setContent($content);
    }

    /**
     * Assert the mode that the content editor is in.
     *
     * Example: Then The post content editor is in Visual mode
     * Example: Then The post content editor is in Text mode
     *
     * @Then /^the post content editor is in (visual|text) mode$/i
     *
     * @param string $mode Assert that the editor is the specified mode (visual or text).
     *
     * @throws ExpectationException
     */
    public function postContentEditorIsInMode($mode)
    {
        $content_editor = $this->edit_post_page->getContentEditor();
        if (strtoupper($mode) !== $content_editor->getMode()) {
            throw new ExpectationException(
                sprintf('Content editor is in "%1$s" mode. Expected "%2$s".', $content_editor->getMode(), $mode),
                $this->getSession()
            );
        }
    }

    /**
     * Press the update/publish button.
     *
     * Example: When I press the publish button
     * Example: WhenI press the update button
     *
     * @When /^I press the (publish|update) button$/
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iPressThePublishButton()
    {
        $this->edit_post_page->pressUpdate();
    }

    /**
     * Assert that the edit screen for the given post and post type is displayed
     *
     * Example: Then I should be on the edit event screen for "Some Event"
     * Example: Then I should be on the edit post screen for "Hello World"
     *
     * @Then /^I should be on the edit "([a-zA-z_-]+)" screen for "([^"]*)"$/
     *
     * @param string $post_type The post type of the referenced 'post' being edited.
     * @param string $title     The name of the 'post' being edited.
     */
    public function iAmOnEditScreenForPostType($post_type, $title)
    {
        $post = $this->getDriver()->getContentFromTitle($title, $post_type);
        $this->edit_post_page->isOpen(array(
            'id' => $post->ID,
        ));
    }


    /**
     * Assert that the edit screen for the given post is displayed
     *
     * Example: Then I should be on the edit screen for "Hello World"
     *
     * @Then /^I should be on the edit screen for "([^"]*)"$/
     *
     * @param string $title The name of the 'post' being edited.
     */
    public function iAmOnEditScreenFor($title)
    {
        $post = $this->getDriver()->getContentFromTitle($title, null);
        $this->edit_post_page->isOpen(array(
            'id' => $post->ID,
        ));
    }

    /**
     * Assert that the referenced metabox is visible.
     *
     * The metabox may still be collapsed.
     *
     * Example: Then I should see the "Comments" metabox
     *
     * @Then I should see the :title metabox
     *
     * @param string $title The title of the metabox being checked
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iShouldSeeTheMetabox($title)
    {
        $this->edit_post_page->getMetaBox($title);
    }

    /**
     * Assert that the referenced metabox is not visible.
     *
     * The metabox may still be collapsed.
     *
     * Example: Then I should not see the "Featured Image" metabox
     *
     * @Then I should not see the :title metabox
     *
     * @param string $title The title of the metabox being checked
     *
     * @throws ExpectationException
     */
    public function iShouldNotSeeTheMetabox($title)
    {
        try {
            $this->edit_post_page->getMetaBox($title);
        } catch (ExpectationException $e) {
            // Expectation fulfilled
            return;
        }

        throw new ExpectationException(
            sprintf(
                'Metabox "%s" was found on the page, but it should not be there.',
                $title
            ),
            $this->getSession()
        );
    }
}
