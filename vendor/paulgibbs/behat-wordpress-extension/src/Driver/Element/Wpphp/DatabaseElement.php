<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpphp;

use RuntimeException;
use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-API driver element for manipulating the database directly.
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
        if (empty($args['path'])) {
            $args['path'] = sys_get_temp_dir();
        }

        $bin          = '';
        $path         = tempnam($args['path'], 'wordhat');
        $command_args = sprintf(
            '--no-defaults %1$s --add-drop-table --result-file=%2$s --host=%3$s --user=%4$s',
            DB_NAME,
            $path,
            DB_HOST,
            DB_USER
        );

        $old_pwd = getenv('MYSQL_PWD');
        putenv('MYSQL_PWD=' . DB_PASSWORD);

        // Support Windows.
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $bin = '/usr/bin/env ';
        }

        // Export DB via mysqldump.
        $proc = proc_open(
            "{$bin}mysqldump {$command_args}",
            array(
                1 => ['pipe', 'w'],
            ),
            $pipes
        );

        $stdout = trim(stream_get_contents($pipes[1]));
        fclose($pipes[1]);
        $exit_code = proc_close($proc);
        putenv('MYSQL_PWD=' . $old_pwd);

        if ($exit_code || strpos($stdout, 'Warning: ') === 0 || strpos($stdout, 'Error: ') === 0) {
            throw new RuntimeException(
                sprintf(
                    "WP-PHP driver failure in database export for method %1\$s(): \n%2\$s\n(%3\$s)",
                    debug_backtrace()[1]['function'],
                    $stdout,
                    $exit_code
                )
            );
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
        $bin          = '';
        $command_args = sprintf(
            '--no-defaults --no-auto-rehash --host=%1$s --user=%2$s --database=%3$s --execute=%4$s',
            DB_HOST,
            DB_USER,
            DB_NAME,
            escapeshellarg(sprintf(
                'SET autocommit = 0; SET unique_checks = 0; SET foreign_key_checks = 0; SOURCE %1$s; COMMIT;',
                $args['path']
            ))
        );

        $old_pwd = getenv('MYSQL_PWD');
        putenv('MYSQL_PWD=' . DB_PASSWORD);

        // Support Windows.
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $bin = '/usr/bin/env ';
        }

        // Import DB via mysql-cli.
        $proc = proc_open(
            "{$bin}mysql {$command_args}",
            array(
                1 => ['pipe', 'w'],
            ),
            $pipes
        );

        $stdout = trim(stream_get_contents($pipes[1]));
        fclose($pipes[1]);
        $exit_code = proc_close($proc);
        putenv('MYSQL_PWD=' . $old_pwd);

        if ($exit_code || strpos($stdout, 'Warning: ') === 0 || strpos($stdout, 'Error: ') === 0) {
            throw new RuntimeException(
                sprintf(
                    "WP-PHP driver failure in database import for method %1\$s(): \n%2\$s\n(%3\$s)",
                    debug_backtrace()[1]['function'],
                    $stdout,
                    $exit_code
                )
            );
        }
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
