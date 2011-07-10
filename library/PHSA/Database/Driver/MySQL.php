<?php
namespace PHSA\Database\Driver;

use PHSA\Database\Driver;
use PHSA\Database\DriverInterface;
use PHSA\Acl;

/**
 * MySQL driver
 */
class MySQL extends Driver implements DriverInterface {
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
     * @see PHSA\Database\DriverInterface::getAcls()
     */
    public function getAcls(array $repositories = array(), array $users = array(), array $groups = array(), $role = null, $rule = null) {
        $params = array();
        $whereClause = array();

        if (!empty($repositories)) {
            $whereClause[] = "repository IN " . self::getPlaceHolderExpression(count($repositories));
            $params = array_merge($params, $repositories);
        }

        if (!empty($users)) {
            $whereClause[] = "username IN " . self::getPlaceHolderExpression(count($users));
            $params = array_merge($params, $users);
        }

        if (!empty($groups)) {
            $whereClause[] = "groupname IN " . self::getPlaceHolderExpression(count($groups));
            $params = array_merge($params, $groups);
        }

        if ($role === DriverInterface::ROLE_USER) {
            $whereClause[] = "groupname IS NULL";
        } else if ($role === DriverInterface::ROLE_GROUP) {
            $whereClause[] = "username IS NULL";
        }

        if ($rule === DriverInterface::RULE_ALLOW || $rule === DriverInterface::RULE_DENY) {
            $whereClause[] = "rule = ?";
            $params[] = $rule;
        }

        // Build query
        $sql = "SELECT * FROM rules";

        if (!empty($whereClause)) {
            $sql .= " WHERE " . implode(' AND ' , $whereClause);
        }

        $sql .= " ORDER BY repository, username, groupname, path ASC";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll();
        $ruleset = new Acl\Ruleset();

        foreach ($rows as $row) {
            $rule = new Acl\Rule();

            $rule->user  = $row['username'] ?: ' - ';
            $rule->group = $row['groupname'] ?: ' - ';
            $rule->repos = $row['repository'];
            $rule->path  = $row['path'] ?: '<root>';
            $rule->rule  = $row['rule'];

            $ruleset->addRule($rule);
        }

        return $ruleset;
    }

    /**
     * @see PHSA\Database\DriverInterface::allowUser()
     */
    public function allowUser($user, $repository, $path = null) {
        return $this->addUserRule($user, $repository, $path, DriverInterface::RULE_ALLOW);
    }

    /**
     * @see PHSA\Database\DriverInterface::allowUser()
     */
    public function denyUser($user, $repository, $path = null) {
        return $this->addUserRule($user, $repository, $path, DriverInterface::RULE_DENY);
    }

    /**
     * Add a user rule
     *
     * @param string $user
     * @param string $repository
     * @param string $path
     * @param string $rule
     *
     * @return boolean
     */
    private function addUserRule($user, $repository, $path, $rule) {
        $sql = "
            INSERT INTO rules (
                username,
                repository,
                path,
                rule
            ) VALUES (
                :username,
                :repository,
                :path,
                :rule
            )
        ";
        $stmt = $this->getDb()->prepare($sql);

        return (boolean) $stmt->execute(array(
            ':username'   => $user,
            ':repository' => $repository,
            ':path'       => $path,
            ':rule'       => $rule,
        ));
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
