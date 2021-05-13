<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

/**
 * Provides step definitions for managing plugins and themes.
 */
class SiteContext extends RawWordpressContext
{
    /**
     * Clear object cache.
     *
     * Example: When the cache is cleared
     * Example: Given the cache has been cleared
     *
     * @When the cache is cleared
     * @Given the cache has been cleared
     */
    public function cacheIsCleared()
    {
        $this->getDriver()->clearCache();
    }
}
