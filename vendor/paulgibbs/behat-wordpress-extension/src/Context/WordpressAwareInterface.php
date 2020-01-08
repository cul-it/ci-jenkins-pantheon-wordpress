<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use Behat\Behat\Context\Context;
use PaulGibbs\WordpressBehatExtension\WordpressDriverManager;

/**
 * Common interface for Behat contexts.
 */
interface WordpressAwareInterface extends Context
{
    /**
     * Set WordPress driver manager instance.
     *
     * @param WordpressDriverManager $wordpress
     */
    public function setWordpress(WordpressDriverManager $wordpress);

    /**
     * Get WordPress driver manager instance.
     *
     * @return WordpressDriverManager
     */
    public function getWordpress();

    /**
     * Sets parameters provided for WordPress.
     *
     * @param array $parameters
     */
    public function setWordpressParameters($parameters);

    /**
     * Get a specific WordPress parameter.
     *
     * @param string $name Parameter name.
     * @return mixed
     */
    public function getWordpressParameter($name);
}
