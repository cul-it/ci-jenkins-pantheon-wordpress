<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use RuntimeException;
use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-CLI driver element for manipulating the database directly.
 */
class DatabaseElement extends BaseElement
{
    /**
     * Export site database.
     *
     * @param int   $id   Not used.
     * @param array $args
     *
     * @return string Path to the database dump.
     */
    public function get($id, $args = [])
    {
        $wpcli_args = ['--porcelain', '--add-drop-table'];

        if (! empty($args['path'])) {
            $file = tempnam($args['path'], 'wordhat');
            if ($file) {
                array_unshift($wpcli_args, $file);
            }
        };

        // Protect against WP-CLI changing the filename.
        $path = $this->drivers->getDriver()->wpcli('db', 'export', $wpcli_args)['stdout'];
        if (! $path) {
            throw new RuntimeException('Could not export database.');
        }

        return $path;
    }

    /**
     * Import site database.
     *
     * @param int   $id   Not used.
     * @param array $args
     */
    public function update($id, $args = [])
    {
        $this->drivers->getDriver()->wpcli('db', 'import', [$args['path']]);
    }


    /*
     * Convenience methods.
     */

    /**
     * Alias of get().
     *
     * @see get()
     *
     * @param int   $id   Not used.
     * @param array $args
     *
     * @return string Path to the export file.
     */
    public function export($id, $args = [])
    {
        return $this->get($id, $args);
    }

    /**
     * Alias of update().
     *
     * @see update()
     *
     * @param int   $id   Not used.
     * @param array $args
     */
    public function import($id, $args = [])
    {
        $this->update($id, $args);
    }
}
