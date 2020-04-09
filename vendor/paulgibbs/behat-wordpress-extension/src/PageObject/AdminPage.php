<?php
namespace PaulGibbs\WordpressBehatExtension\PageObject;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Mink\Exception\ExpectationException;

/**
 * Page object representing a generic admin page.
 *
 * This class houses methods for navigating around the WordPress admin and
 * acts as a base for most page-specific admin page objects.
 */
class AdminPage extends Page
{
    /**
     * Returns the text in the header tag.
     *
     * The first h1 element is used, or first h2 element if it is not present.
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     *
     * @return string The text in the header tag.
     */
    public function getHeaderText()
    {
        $header      = $this->getHeaderElement();
        $header_text = $header->getText();
        $header_link = $header->find('css', 'a');

        // The page headers can often include an 'add new link'. Strip that out of the header text.
        if ($header_link) {
            $header_text = trim(str_replace($header_link->getText(), '', $header_text));
        }

        return $header_text;
    }

    /**
     * Asserts the text in the header tag matches the given text.
     *
     * @param string $expected
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function assertHasHeader($expected)
    {
        $actual = $this->getHeaderText();
        if ($expected === $actual) {
            return;
        }

        throw new ExpectationException(
            sprintf(
                'Expected page header "%1$s", found "%2$s".',
                $expected,
                $actual
            ),
            $this->getDriver()
        );
    }

    /**
     * Get the text in the header tag.
     *
     * The first h1 element is used, or first h2 element if it is not present.
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    protected function getHeaderElement()
    {
        // h2s were used prior to 4.3/4 and h1s after
        // @see https://make.wordpress.org/core/2015/10/28/headings-hierarchy-changes-in-the-admin-screens/
        $header2 = $this->find('css', '.wrap > h2');
        $header1 = $this->find('css', '.wrap > h1');

        if ($header1) {
            return $header1;
        } elseif ($header2) {
            return $header2;
        }

        throw new ExpectationException('Header could not be found', $this->getDriver());
    }

    /**
     * Click a link within the page header tag.
     *
     * @param string $link Link text.
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function clickLinkInHeader($link)
    {
        $header_link = $this->find('css', '.page-title-action');

        if ($header_link->getText() === $link) {
            $header_link->click();
        } else {
            $this->getHeaderElement()->clickLink($link);
        }
    }

    /**
     * Returns the AdminMenu element.
     *
     * @return \SensioLabs\Behat\PageObjectExtension\PageObject\Element
     */
    public function getMenu()
    {
        return $this->getElement('Admin menu');
    }

    /**
     * Modified Page::isOpen() function which throws an exception on failure
     *
     * @see https://github.com/sensiolabs/BehatPageObjectExtension/issues/57
     *
     * @param array $url_parameters
     *
     * @throws SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException
     *         If the current page does not match this one.
     *
     * @return boolean
     */
    public function isOpen(array $url_parameters = array())
    {
        $this->verify($url_parameters);
        return true;
    }

    /**
     * Get the URL based on WordHat's site_url and not Mink's base_url
     *
     * We override this method as we need to modify the private method Page::makeSurePathIsAbsolute()
     *
     * @param array $url_parameters
     *
     * @return string Absolute URL of this page
     */
    protected function getUrl(array $url_parameters = array())
    {
        return $this->makeSurePathIsAbsolute($this->unmaskUrl($url_parameters));
    }

    /**
     * We use WordHat's site_url property rather than Mink's base_url property
     * to get the correct URL to wp-admin, wp-login.php etc.
     *
     * @param string $path Relative path of this page
     *
     * @return string Absolute URL
     */
    protected function makeSurePathIsAbsolute($path)
    {
        $site_url = rtrim($this->getParameter('site_url'), '/') . '/';
        return 0 !== strpos($path, 'http') ? $site_url . ltrim($path, '/') : $path;
    }

    /**
     * Insert values for placeholders in the page's path.
     *
     * @param array $url_parameters
     *
     * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException
     *
     * @return string
     */
    protected function unmaskUrl(array $url_parameters)
    {
        $url = $this->getPath();

        foreach ($url_parameters as $parameter => $value) {
            $url = str_replace(sprintf('{%s}', $parameter), $value, $url);
        }

        return $url;
    }
}
