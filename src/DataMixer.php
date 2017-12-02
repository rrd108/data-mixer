<?php

namespace DataMixer;

use InvalidArgumentException;
use PDO;
use PDOException;

/**
 * Class DataMixer
 *
 * @package DataMixer
 */
class DataMixer
{
    /**
     * DataMixer constructor.
     *
     * Connects to a database server
     *
     * examples:
     *      new DataMixer('mysql:dbname=testdb;host=127.0.0.1', 'user', 'superSecret');
     *      new DataMixer('sqlite3::memory:', null, null, [PDO::ATTR_PERSISTENT => true]);
     *
     * @param string      $dsn
     * @param string|null $username
     * @param string|null $passwd
     * @param array       $options
     */
    public function __construct(string $dsn, string $username = null, string $passwd = null, array $options = [])
    {
        $this->pdo = new PDO($dsn, $username, $passwd, $options);
    }

    /**
     * Get mixed rows
     *
     * @param string $table
     * @param array  $fields
     *  simple array like ['first_name', 'last_name']
     *  or dependent array like ['first_name'=> 'sex', 'last_name']
     * @return mixed
     */
    public function getMixed(string $table, array $fields)
    {
        $rows = $this->getRows($table);
        $ids = array_keys($rows);

        foreach ($ids as $id) {
            foreach ($fields as $key => $field) {
                if (is_numeric($key)) {
                    $row = $rows[array_rand($rows)];
                    if ($row[$field]) {
                        $mixed[$table][$id][$field] = $row[$field];
                    }
                }
                //if $field is an array, we should random select a row
                //where the dependent field has the same value than the current
                if (!is_numeric($key)) {
                    $filtered_rows = [];
                    //collect rows with same key
                    foreach ($rows as $row) {
                        if ($row[$field] == $rows[$id][$field]) {
                            $filtered_rows[] = $row;
                        }
                    }
                    $row = $filtered_rows[array_rand($filtered_rows)];
                    if ($row[$key]) {
                        $mixed[$table][$id][$key] = $row[$key];
                    }
                }
            }
        }
        return $mixed;
    }

    /**
     * update database rows
     *
     * @param array $mixed
     * @return int
     */
    public function updateRows(array $mixed)
    {
        $i = 0;
        foreach ($mixed as $table => $row) {
            foreach ($row as $id => $data) {
                $sql = 'UPDATE ' . $table . ' SET ';
                foreach ($data as $key => $value) {
                    //TODO do some filtering
                    $sql .= $key . ' = "' . $value . '", ';
                }
                $sql = rtrim($sql, ', ');
                $sql .= ' WHERE id = ' . $id .';';
                $this->pdo->query($sql);
                $i++;
            }
        }
        return $i;
    }

    public function getRows(string $table)
    {
        $statement = $this->pdo->query('SELECT * FROM ' . $table);
        $values = $statement->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
        return $values;
    }
}
