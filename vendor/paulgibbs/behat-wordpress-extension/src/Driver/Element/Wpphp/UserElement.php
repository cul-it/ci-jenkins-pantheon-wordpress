<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpphp;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;
use UnexpectedValueException;

/**
 * WP-API driver element for managing user accounts.
 */
class UserElement extends BaseElement
{
    /**
     * Create an item for this element.
     *
     * @param array $args Data used to create an object.
     *
     * @throws \UnexpectedValueException
     *
     * @return \WP_User The new item.
     */
    public function create($args)
    {
        $args = wp_slash($args);
        $user = wp_insert_user($args);

        if (is_wp_error($user)) {
            throw new UnexpectedValueException(sprintf('Failed creating new user: %s', $user->get_error_message()));
        }

        return $this->get($user->ID);
    }

    /**
     * Retrieve an item for this element.
     *
     * @param int|string $id Object ID.
     * @param array $args Optional data used to fetch an object.
     *
     * @throws \UnexpectedValueException
     *
     * @return \WP_User The item.
     */
    public function get($id, $args = [])
    {
        if (is_numeric($id) || ! isset($args['by'])) {
            $type = 'ID';
        } else {
            $type = $args['by'];
        }

        $user = get_user_by($type, $id);

        if (! $user) {
            throw new UnexpectedValueException(sprintf('Could not find user with ID %d', $id));
        }

        return $user;
    }

    /**
     * Delete an item for this element.
     *
     * @param int $id Object ID.
     * @param array $args Optional data used to delete an object.
     *
     * @throws \UnexpectedValueException
     */
    public function delete($id, $args = [])
    {
        $result = wp_delete_user($id, $args);

        if (! $result) {
            throw new UnexpectedValueException('Failed deleting user.');
        }
    }
}
