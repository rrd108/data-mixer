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
            $options['host'],
            $options['dbUser'],
            $options['dbPassword'],
            $options['database']
        );

        if ($this->mysqli->connect_errno) {
            throw new InvalidArgumentException('Connection failed ' . $this->mysqli->connect_error);
        }
    }

    public function mix()
    {
        $updated = 0;
        $values = $ids = [];
        if ($result = $this->mysqli->query('SELECT * FROM ' . $table)) {
            while ($row = $result->fetch_assoc()) {
                $ids[] = $row['id'];
                $values[$row['id']] = $row;
            }

            $result->close();
        }

        foreach ($ids as $id) {
            foreach ($fields as $field) {
                //if $field is an array, we should random select a row
                //where the dependent field has the same value than the current
                if (is_array($field)) {
                    $filtered_values = [];
                    $key = key($field);
                    foreach ($values as $val) {
                        if ($val[$field[$key]] == $values[$id][$field[$key]]) {
                            $filtered_values[] = $val;
                        }
                    }
                    $row = $filtered_values[array_rand($filtered_values)];
                    if ($row[$key]) {
                        $this->mysqli->query('UPDATE ' . $table
                            . ' set ' . $key . ' = "' . $row[$key] . '" WHERE id = ' . $id);
                        $updated++;
                    }
                } else {
                    $row = $values[array_rand($values)];
                    if ($row[$field]) {
                        $this->mysqli->query('UPDATE ' . $table
                            . ' set ' . $field . ' = "' . $row[$field]
                            . '" WHERE id = ' . $id);
                        $updated++;
                    }
                }
            }
        }

        $this->mysqli->close();
    }
}
