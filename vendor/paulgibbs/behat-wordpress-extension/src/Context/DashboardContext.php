<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use PaulGibbs\WordpressBehatExtension\PageObject\AdminPage;

/**
 * Provides step definitions that are specific to the WordPress dashboard (wp-admin).
 */
class DashboardContext extends RawWordpressContext
{
    /**
     * Non-specific admin page (wp-admin/) object.
     * @param AdminPage
     */
    protected $admin_page;

    /**
     * Constructor.
     *
     * @param AdminPage $admin_page AdminPage object.
     */
    public function __construct(AdminPage $admin_page)
    {
        parent::__construct();

        $this->admin_page = $admin_page;
    }

    /**
     * Click a link within the page header tag.
     *
     * Example: When I click on the "Add New" link in the header
     *
     * @When I click on the :link link in the header
     *
     * @param string $link
     */
    public function iClickOnHeaderLink($link)
    {
        $this->admin_page->clickLinkInHeader($link);
    }

    /**
     * Assert the text in the page header tag matches the given string.
     *
     * Example: Then I should be on the "Posts" page
     *
     * @Then I should be on the :admin_page page
     *
     * @param string $admin_page
     */
    public function iShouldBeOnThePage($admin_page)
    {
        $this->admin_page->assertHasHeader($admin_page);
    }

    /**
     * Go to a given page on the admin menu.
     *
     * In 1.0.0, the regex will simplify to 'I go to the menu "foobar"'.
     *
     * Example: Given I go to menu item "Posts > Add New"
     * Example: Given I go to the menu item "Users"
     * Example: Given I go to the menu "Settings > Reading"
     *
     * @Given I go to (the )menu (item ):item
     *
     * @param string $item
     */
    public function iGoToMenuItem($item)
    {
        $adminMenu = $this->admin_page->getMenu();
        $adminMenu->clickMenuItem($item);
    }

    /**
     * Check the specified notification is on-screen.
     *
     * Example: Then I should see a status message that says "Post published"
     *
     * @Then /^(?:I|they) should see an? (error|status) message that says "([^"]+)"$/
     *
     * @param string $type    Message type. Either "error" or "status".
     * @param string $message Text to search for.
     *
     * @throws \Behat\Mink\Exception\ElementTextException
     */
    public function iShouldSeeMessageThatSays($type, $message)
    {
        $selector = 'div.notice';

        if ($type === 'error') {
            $selector .= '.error';
        } else {
            $selector .= '.updated';
        }

        $this->assertSession()->elementTextContains('css', $selector, $message);
    }
}
