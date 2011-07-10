<?php
namespace PHSA\Subversion;

/**
 * Interface for subversion drivers
 */
interface DriverInterface {
    /**
     * See if a path is a valid repository
     *
     * @param string $path Path to a directory
     * @return boolean
     */
    function validRepository($path);

    /**
     * List all valid repositories in a path
     *
     * @param string $path Path to look for Subversion repositories
     * @return string[]
     */
    function listRepositories($path);
}
