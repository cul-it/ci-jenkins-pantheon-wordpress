<?php
namespace PaulGibbs\WordpressBehatExtension\PageObject;

use Behat\Mink\Exception\ExpectationException;

/**
 * Page object representing the Dashboard page.
 */
class EditPostPage extends AdminPage
{
    protected $path = '/wp-admin/post.php?post={id}&action=edit';

    /**
     * Enter the title into the title field.
     *
     * @param string $title
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function setContentTitle($title)
    {
        $this->fillField('title', $title);
    }

    /**
     * Get the content editor node element.
     *
     * @return \SensioLabs\Behat\PageObjectExtension\PageObject\Element
     */
    public function getContentEditor()
    {
        return $this->getElement('Post Content editor');
    }

    /**
     * Press the update/publish button.
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function pressUpdate()
    {
        // TODO wait if the button is disabled during auto-save
        $this->pressButton('publish');
    }

    /**
     * Get the node element for the specified metabox.
     *
     * @param string $title The title of the metabox to get
     *
     * @return \Behat\Mink\Element\NodeElement
     *
     * @throws \Behat\Mink\Exception\ExpectationException If the metabox cannot be found
     */
    public function getMetaBox($title)
    {
        $metaboxes = $this->findAll('css', '.postbox');
        if ($metaboxes) {
            foreach ($metaboxes as $metabox) {
                if ($metabox->find('css', 'h2')->getText() === $title) {
                    return $metabox;
                }
            }
        }

        throw new ExpectationException(
            sprintf(
                'Metabox "%s" not found on the page.',
                $title
            ),
            $this->getDriver()
        );
    }
}
