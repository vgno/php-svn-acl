<?php
namespace PHSA\Database;

/**
 * Abstract database driver
 */
abstract class Driver {
    /**
     * Get a query builder for the current driver
     *
     * @return PHSA\Database\QueryBuilder
     */
    public function getAclsQueryBuilder() {
        return new QueryBuilder($this);
    }
}
