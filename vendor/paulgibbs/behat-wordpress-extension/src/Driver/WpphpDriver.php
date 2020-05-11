<?php
namespace PaulGibbs\WordpressBehatExtension\Driver;

use RuntimeException;

/**
 * Connect Behat to WordPress by loading WordPress directly into the global scope.
 */
class WpphpDriver extends BaseDriver
{
    /**
     * Path to WordPress' files.
     *
     * @var string
     */
    protected $path = '';

    /**
     * WordPres database object.
     *
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Constructor.
     *
     * @param string $path Path to WordPress site's files.
     */
    public function __construct($path)
    {
        $this->path = realpath($path);
    }

    /**
     * Set up anything required for the driver.
     *
     * Called when the driver is used for the first time.
     *
     * @throws \RuntimeException
     */
    public function bootstrap()
    {
        if (! defined('ABSPATH')) {
            define('ABSPATH', "{$this->path}/");
        }

        $_SERVER['DOCUMENT_ROOT']   = $this->path;
        $_SERVER['HTTP_HOST']       = '';
        $_SERVER['REQUEST_METHOD']  = 'GET';
        $_SERVER['REQUEST_URI']     = '/';
        $_SERVER['SERVER_NAME']     = '';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        if (! file_exists("{$this->path}/index.php")) {
            throw new RuntimeException(sprintf('WordPress PHP driver cannot find WordPress at %s.', $this->path));
        }

        // "Cry 'Havoc!' and let slip the dogs of war".
        require_once "{$this->path}/wp-blog-header.php";

        if (! function_exists('activate_plugin')) {
            require_once "{$this->path}/wp-admin/includes/plugin.php";
            require_once "{$this->path}/wp-admin/includes/plugin-install.php";
        }

        $this->wpdb            = $GLOBALS['wpdb'];
        $this->is_bootstrapped = true;
    }


    /*
     * Internal helpers.
     */

    /**
     * Get information about a plugin.
     *
     * @param string $name
     * @return string Plugin filename and path.
     */
    public function getPlugin($name)
    {
        foreach (get_plugins() as $file => $_) {
            // Logic taken from WP-CLI.
            if ($file === "{$name}.php" || ($name && $file === $name) || (dirname($file) === $name && $name !== '.')) {
                return $file;
            }
        }

        return '';
    }


    /*
     * Backwards compatibility.
     */

    /**
     * Clear object cache.
     *
     * This method will be removed in release 1.0.0.
     */
    public function clearCache()
    {
        $this->cache->clear();
    }

    /**
     * Activate a plugin.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $plugin
     */
    public function activatePlugin($plugin)
    {
        $this->plugin->activate($plugin);
    }

    /**
     * Deactivate a plugin.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $plugin
     */
    public function deactivatePlugin($plugin)
    {
        $this->plugin->deactivate($plugin);
    }

    /**
     * Switch active theme.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $theme
     */
    public function switchTheme($theme)
    {
        $this->theme->change($theme);
    }

    /**
     * Create a term in a taxonomy.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $term
     * @param string $taxonomy
     * @param array  $args     Optional. Set the values of the new term.
     *
     * @return array {
     *     @type int    $id   Term ID.
     *     @type string $slug Term slug.
     * }
     */
    public function createTerm($term, $taxonomy, $args = [])
    {
        $args['taxonomy'] = $taxonomy;
        $args['term']     = $term;

        $term = $this->term->create($args);

        return array(
            'id'   => $term->term_id,
            'slug' => $term->slug,
        );
    }

    /**
     * Delete a term from a taxonomy.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int    $term_id
     * @param string $taxonomy
     */
    public function deleteTerm($term_id, $taxonomy)
    {
        $this->term->delete($term_id, ['taxonomy' => $taxonomy]);
    }

    /**
     * Create content.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param array $args Set the values of the new content item.
     *
     * @return array {
     *     @type int    $id   Content ID.
     *     @type string $slug Content slug.
     *     @type string $url  Content permalink.
     * }
     */
    public function createContent($args)
    {
        $post = $this->content->create($args);

        return array(
            'id'   => (int) $post->ID,
            'slug' => $post->post_name,
            'url'  => get_permalink($post),
        );
    }

    /**
     * Delete specified content.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int   $id   ID of content to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteContent($id, $args = [])
    {
        $this->content->delete($id, $args);
    }

    /**
     * Get content from its title.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $title     The title of the content to get.
     * @param string $post_type Post type(s) to consider when searching for the content.
     *
     * @throws \UnexpectedValueException
     *
     * @return array {
     *     @type int    $id   Content ID.
     *     @type string $slug Content slug.
     *     @type string $url Content url.
     * }
     */
    public function getContentFromTitle($title, $post_type = '')
    {
        $post = $this->content->get($title, ['by' => 'title', 'post_type' => $post_type]);

        return array(
            'id'   => $post->ID,
            'slug' => $post->post_name,
            'url'  => get_permalink($post),
        );
    }

    /**
     * Create a comment.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param array $args Set the values of the new comment.
     *
     * @return array {
     *     @type int $id Content ID.
     * }
     */
    public function createComment($args)
    {
        $comment = $this->comment->create($args);

        return array(
            'id' => $comment->comment_ID,
        );
    }

    /**
     * Delete specified comment.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int   $id   ID of comment to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteComment($id, $args = [])
    {
        $this->comment->delete($id, $args);
    }

    /**
     * Create a user.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $user_login User login name.
     * @param string $user_email User email address.
     * @param array  $args       Optional. Extra parameters to pass to WordPress.
     *
     * @return array {
     *     @type int    $id   User ID.
     *     @type string $slug User slug (nicename).
     * }
     */
    public function createUser($user_login, $user_email, $args = [])
    {
        $args['user_login'] = $user_login;
        $args['user_email'] = $user_email;

        $user = $this->user->create($args);

        return array(
            'id'   => $user->ID,
            'slug' => $user->user_nicename,
        );
    }

    /**
     * Delete a user.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int   $id   ID of user to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteUser($id, $args = [])
    {
        $this->user->delete($id, $args);
    }

    /**
     * Get a User's ID from their username.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $username The username of the user to get the ID of.
     *
     * @throws \UnexpectedValueException If provided data is invalid
     *
     * @return int ID of the user.
     */
    public function getUserIdFromLogin($username)
    {
        return $this->user->get($username, ['by' => 'login'])->ID;
    }

    /**
     * Start a database transaction.
     *
     * This method will be removed in release 1.0.0.
     */
    public function startTransaction()
    {
        $this->database->startTransaction();
    }

    /**
     * End (rollback) a database transaction.
     *
     * This method will be removed in release 1.0.0.
     */
    public function endTransaction()
    {
        $this->database->endTransaction();
    }
}
