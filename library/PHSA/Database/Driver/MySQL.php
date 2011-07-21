<?php
namespace PHSA\Database\Driver;

use PHSA\Database\Query;
use PHSA\Database\DriverInterface;
use PHSA\Acl;

/**
 * MySQL driver
 */
class MySQL implements DriverInterface {
    /**
     * PDO instance
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * Parameters for PDO
     *
     * @var array
     */
    private $params;

    /**
     * Class constructor
     *
     * @param array $params
     */
    public function __construct(array $params) {
        $this->params = $params;
    }

    /**
     * Fetch the database connection
     *
     * @return \PDO
     */
    public function getDb() {
        if ($this->pdo === null) {
            $options = array(
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES   => true,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            );

            try {
                $this->pdo = new \PDO(
                    sprintf('mysql:host=%s;dbname=%s',
                            $this->params['hostname'],
                            $this->params['database']),
                    $this->params['username'],
                    $this->params['password'],
                    $options
                );
            } catch (\PDOException $e) {
                throw new \RuntimeException('Could not connect to the database', $e->getCode(), $e);
            }

            $this->pdo->query("SET NAMES 'utf8'");
        }

        return $this->pdo;
    }

    /**
     * Get a rule based on a database row
     *
     * @param array $row
     *
     * @return PHSA\Acl\Rule
     */
    private function getRuleFromDatabaseRow(array $row) {
        $rule = new Acl\Rule();

        $rule->user  = $row['username'];
        $rule->group = $row['groupname'];
        $rule->repos = $row['repository'];
        $rule->path  = $row['path'];
        $rule->rule  = $row['rule'];

        return $rule;
    }

    /**
     * Get a ruleset based on several database rows
     *
     * @param array $rows
     *
     * @return PHSA\Acl\Ruleset
     */
    private function getRulesetFromDatabaseRows(array $rows) {
        $ruleset = new Acl\Ruleset();

        foreach ($rows as $row) {
            $rule = $this->getRuleFromDatabaseRow($row);
            $ruleset->addRule($rule);
        }

        return $ruleset;
    }

    /**
     * @see PHSA\Database\DriverInterface::getAllRules()
     */
    public function getAllRules() {
        return $this->getRules(new Query());
    }

    /**
     * Build a where clause based on a query object
     *
     * @param PHSA\Datbase\Query $query
     * @param array $params
     *
     * @return string
     */
    private function buildWhereClauseFromQuery(Query $query, array &$params) {
        $whereClause = array();

        if ($repositories = $query->getRepositories()) {
            $whereClause[] = "repository IN " . self::getPlaceHolderExpression(count($repositories));
            $params = array_merge($params, $repositories);
        }

        if ($users = $query->getUsers()) {
            $whereClause[] = "username IN " . self::getPlaceHolderExpression(count($users));
            $params = array_merge($params, $users);
        }

        if ($groups = $query->getGroups()) {
            $whereClause[] = "groupname IN " . self::getPlaceHolderExpression(count($groups));
            $params = array_merge($params, $groups);
        }

        if ($query->getRole() === Acl\Rule::USER) {
            $whereClause[] = "groupname IS NULL";
        } else if ($query->getRole() === Acl\Rule::GROUP) {
            $whereClause[] = "username IS NULL";
        }

        if ($query->getRule() === Acl\Rule::ALLOW || $query->getRule() === Acl\Rule::DENY) {
            $whereClause[] = "rule = ?";
            $params[] = $query->getRule();
        }

        if ($paths = $query->getPaths()) {
            $where = '(';

            for ($i = 0; $i < count($topLevels); $i++) {
                $where .= " (path = ? OR path LIKE ?) OR";

                $params[] = $path;
                $params[] = $path . '/%';
            }

            // Add IS NULL to fetch top level rules
            $where .= ' path IS NULL)';

            $whereClause[] = $where;
        }

        return implode(' AND ', $whereClause);
    }

    /**
     * @see PHSA\Database\DriverInterface::getRules()
     */
    public function getRules(Query $query) {
        $params = array();
        $whereClause = $this->buildWhereClauseFromQuery($query, $params);

        // Build query
        $sql = "SELECT * FROM rules";

        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }

        $sql .= " ORDER BY repository, username, groupname, path ASC";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll();

        return $this->getRulesetFromDatabaseRows($rows);
    }

    /**
     * @see PHSA\Database\DriverInterface::allowUser()
     */
    public function allowUser($user, $repository, $path = null) {
        return $this->addUserRule($user, $repository, $path, Acl\Rule::ALLOW);
    }

    /**
     * @see PHSA\Database\DriverInterface::allowUser()
     */
    public function denyUser($user, $repository, $path = null) {
        return $this->addUserRule($user, $repository, $path, Acl\Rule::DENY);
    }

    /**
     * Add a user specific rule
     *
     * @param string $user
     * @param string $repository
     * @param string $path
     * @param string $rule
     *
     * @return boolean
     */
    private function addUserRule($user, $repository, $path, $rule) {
        return $this->addRule($user, null, $repository, $path, $rule);
    }

    /**
     * @see PHSA\Database\DriverInterface::allowGroup()
     */
    public function allowGroup($group, $repository, $path = null) {
        return $this->addGroupRule($group, $repository, $path, Acl\Rule::ALLOW);
    }

    /**
     * @see PHSA\Database\DriverInterface::allowUser()
     */
    public function denyGroup($group, $repository, $path = null) {
        return $this->addGroupRule($group, $repository, $path, Acl\Rule::DENY);
    }

    /**
     * Add a group specific rule
     *
     * @param string $group
     * @param string $repository
     * @param string $path
     * @param string $rule
     *
     * @return boolean
     */
    private function addGroupRule($group, $repository, $path, $rule) {
        return $this->addRule(null, $group, $repository, $path, $rule);
    }

    /**
     * Add a rule
     *
     * @param string $user
     * @param string $group
     * @param string $repository
     * @param string $path
     * @param string $rule
     *
     * @return boolean
     */
    private function addRule($user, $group, $repository, $path, $rule) {
        $sql = "
            INSERT INTO rules (
                username,
                groupname,
                repository,
                path,
                rule
            ) VALUES (
                :username,
                :groupname,
                :repository,
                :path,
                :rule
            )
        ";
        $stmt = $this->getDb()->prepare($sql);

        return (boolean) $stmt->execute(array(
            ':username'   => $user,
            ':groupname'  => $group,
            ':repository' => $repository,
            ':path'       => $path,
            ':rule'       => $rule,
        ));
    }

    /**
     * @see PHSA\Database\DriverInterface::removeRules()
     */
    public function removeRules(Query $query) {
        $params = array();
        $whereClause = $this->buildWhereClauseFromQuery($query, $params);

        $sql = "DELETE FROM rules";

        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }

        $stmt = $this->getDb()->prepare($sql);
        $result = $stmt->execute($params);

        if (!$result) {
            // @codeCoverageIgnoreStart
            return false;
        }
        // @codeCoverageIgnoreEnd

        return $stmt->rowCount();
    }

    /**
     * @see PHSA\Database\DriverInterface::removeAllRules()
     */
    public function removeAllRules() {
        return $this->removeRules(new Query());
    }

    /**
     * Get a placeholder expression with as many placeholders as $columns
     *
     * @param int $columns
     * @return string
     */
    static public function getPlaceholderExpression($columns) {
        return '(' . substr(str_repeat('?, ', $columns), 0, -2) . ')';
    }
}
