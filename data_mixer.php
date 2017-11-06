<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$database_user = 'username';
$database_password = 'password';

$database = 'name_of_your_database';
$table = 'name_of_the_database_table';
$fields = [
    ['first_name' => 'sex'],
    ['last_name' => 'sex'],
    'passport_number'
];

$mysqli = new mysqli($host, $database_user, $database_password, $database);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$updated = 0;
$values = $ids = [];
if ($result = $mysqli->query('SELECT * FROM ' . $table)) {
    
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
                $mysqli->query('UPDATE ' . $table
                    . ' set ' . $key . ' = "' . $row[$key] . '" WHERE id = ' . $id);
                $updated++;
            }
        } else {
            $row = $values[array_rand($values)];
            if ($row[$field]) {
                $mysqli->query('UPDATE ' . $table
                    . ' set ' . $field . ' = "' . $row[$field]
                    . '" WHERE id = ' . $id);
                $updated++;
            }
        }
    }
}

$mysqli->close();
