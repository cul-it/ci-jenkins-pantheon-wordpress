<?php
namespace PaulGibbs\WordpressBehatExtension\PageObject;

/**
 * Page object representing the Dashboard page.
 */
class Dashboard extends AdminPage
{
    /**
     * @var string $path
     */
    protected $path = '/wp-admin/index.php';

    /**
     * Asserts the page header tag reads 'Dashboard'
     *
     * @throws \Exception
     */
    protected function verifyPage()
    {
        $this->assertHasHeader('Dashboard');
    }
}
