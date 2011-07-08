<?php
namespace PHSA;

/**
 * Class used to handle the version of PHSA
 */
class Version {
    /**
     * The current version
     *
     * @var string
     */
    static private $id = '@package_version@';

    /**
     * Get the version number only
     *
     * @return string
     */
    static public function getVersionNumber() {
        if (strpos(self::$id, '@package_version') === 0) {
            return 'dev';
        }

        return self::$id;
    }

    /**
     * Get the version string
     *
     * @return string
     */
    static public function getVersionString() {
        return 'PHSA-' . self::getVersionNumber() . ' by Christer Edvartsen';
    }
}
