<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;
use function PaulGibbs\WordpressBehatExtension\Util\buildCLIArgs;
use UnexpectedValueException;

/**
 * WP-CLI driver element for managing user accounts.
 */
class UserElement extends BaseElement
{
    /**
     * Create an item for this element.
     *
     * @param array $args Data used to create an object.
     *
     * @return mixed The new item.
     */
    public function create($args)
    {
        $wpcli_args = buildCLIArgs(
            array(
                'ID', 'user_pass', 'user_nicename', 'user_url', 'display_name', 'nickname', 'first_name', 'last_name',
                'description', 'rich_editing', 'comment_shortcuts', 'admin_color', 'use_ssl', 'user_registered',
                'show_admin_bar_front', 'role', 'locale',
            ),
            $args
        );

        array_unshift($wpcli_args, $args['user_login'], $args['user_email'], '--porcelain');
        $user_id = (int) $this->drivers->getDriver()->wpcli('user', 'create', $wpcli_args)['stdout'];

        return $this->get($user_id);
    }

    /**
     * Retrieve an item for this element.
     *
     * @param int|string $id Object ID.
     * @param array $args Optional data used to fetch an object.
     *
     * @throws \UnexpectedValueException
     *
     * @return mixed The item.
     */
    public function get($id, $args = [])
    {
        // Fetch all the user properties by default, for convenience.
        if (! isset($args['field']) && ! isset($args['fields'])) {
            $args['fields'] = implode(
                ',',
                array(
                    'ID',
                    'user_login',
                    'display_name',
                    'user_email',
                    'user_registered',
                    'roles',
                    'user_pass',
                    'user_nicename',
                    'user_url',
                    'user_activation_key',
                    'user_status',
                    'url'
                )
            );
        }

        $wpcli_args = buildCLIArgs(
            array(
                'field',
                'fields',
            ),
            $args
        );

        array_unshift($wpcli_args, $id, '--format=json');
        $user       = $this->drivers->getDriver()->wpcli('user', 'get', $wpcli_args)['stdout'];
        $user       = json_decode($user);

        if (! $user) {
            throw new UnexpectedValueException(sprintf('Could not find user with ID %d', $id));
        }

        return $user;
    }

    /**
     * Delete an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Optional data used to delete an object.
     */
    public function delete($id, $args = [])
    {
        $wpcli_args = buildCLIArgs(
            ['network', 'reassign'],
            $args
        );

        array_unshift($wpcli_args, $id, '--yes');

        $this->drivers->getDriver()->wpcli('user', 'delete', $wpcli_args);
    }
}
