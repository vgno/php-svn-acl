<?php
namespace PHSA\Subversion\Driver;

use PHSA\Subversion\DriverInterface;

/**
 * svn extension driver (http://pecl.php.net/package/svn)
 */
class SvnExtension implements DriverInterface  {
    /**
     * Class constructor
     *
     * @throws \RuntimeException The constroctor will throw an exception if the svn extension is
     *                           not available.
     */
    public function __construct() {
        if (!extension_loaded('svn')) {
            throw new \RuntimeException(
                'This driver requires the svn extension to be installed and loaded'
            );
        }
    }

    /**
     * @see PHSA\Subversion\DriverInterface::validRepository()
     */
    public function validRepository($path) {
        // Yeah, I know, @ sucks, but svn_repos_open throws warnings when specifying an invalid
        // repos
        $res = @svn_repos_open($path);

        if ($res) {
            unset($res);
            return true;
        }

        return false;
    }
}
