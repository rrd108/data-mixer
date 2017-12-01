<?php

namespace DataMixer;

use InvalidArgumentException;
use mysqli;

/**
 * Class DataMixer
 *
 * @package DataMixer
 */
class DataMixer
{

    private $mysqli;

    public function __construct(array $options)
    {
        $this->mysqli = new mysqli(
            $options['dbHost'],
            $options['dbUser'],
            $options['dbPassword'],
            $options['dbName']
        );

        if ($this->mysqli->connect_errno) {
            throw new InvalidArgumentException('Connection failed ' . $this->mysqli->connect_error);
        }
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

    public function updateRows(array $mixed)
    {
        //TODO use prepared statements
        foreach ($mixed as $table => $row) {
            foreach ($row as $id => $data) {
                $sql = 'UPDATE ' . $table . ' SET ';
                foreach ($data as $key => $value) {
                    $sql .= $key . ' = "' . $value . '", ';
                }
                $sql = rtrim($sql, ', ');
                $sql .= ' WHERE id = ' . $id .';';
                //$this->mysqli->query($sql);
                print $sql . "\n";
            }
        }
    }

    public function getRows(string $table)
    {
        $result = $this->mysqli->query('SELECT * FROM ' . $table);
        $values = [];

        while ($row = $result->fetch_assoc()) {
            $values[$row['id']] = $row;
        }
        $result->close();

        return $values;
    }

    protected function close()
    {
        $this->mysqli->close();
    }
}
