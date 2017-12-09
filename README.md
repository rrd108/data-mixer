# data-mixer
A simple PHP tool to create test data from existing database by mixing records.

Sometimes you want to test your application with the same amount of data what you
have in your production enviroment. This script reads your exisiting data and
mix its content.

# Installation
`composer --require-dev rrd108/data-mixer`

# Usage
1. Create a backup of your production data and copy it to your development enviroment. **NEVER** use this tool in production, as you can accidently mix your data. **NO RESTORE, NO UNDO, NO CANCEL** method, what is mixed once, can not be reverted without a backup.

2. Create a new object in your code. DataMixer accepts the same arguments as PDO.
`$dataMixer = new DataMixer('mysql:dbname=test;host=localhost', 'user', 'superSecret');`

3. Define your selected fields to be mixed in the `$fields` variable.

4. Get your mixed data and modify it if you want.
`$mixed = $dataMixer->getMixed($table, $fields);`

5. Update your records in tha database. This will actually update your database, so be careful, **NO RESTORE, NO UNDO, NO CANCEL** method.
`$dataMixer->updateRows($mixed);`

# `$fields` array
The fields defined in `$fields` array will be mixed.

## Simple mixing
```php
$fields = ['first_name', 'last_name'];
```
In this case in the `$table` database table all records' `first_name` and `last_name` values will be mixed randomly. All other table fields will be unchaned.

Let's say we have the following *starter* table.

| id | first_name | last_name | passport_number | sex |
|----|------------|-----------|-----------------|-----|
| 1  | John       | Doe       | 123456789AA     | M   |
| 2  | Jane       | Gauranga  | 987456789AA     | F   | 
| 3  | Trevor     | Davis     | 985631458ZZ     | M   |

The simple mixing *may* give us the following result

| id | first_name | last_name | passport_number | sex |
|----|------------|-----------|-----------------|-----|
| 1  | Jane       | Davis     | 123456789AA     | M   |
| 2  | John       | Gauranga  | 987456789AA     | F   |
| 3  | Trevor     | Doe       | 985631458ZZ     | M   |

You can see the names are mixed, but all other data (`passport_number` and `sex`) is unchanged.

## Dependent mixing
```php
$fields = [
    ['first_name' => 'sex'], 
    'last_name'
];
```
In this case in the `$table` database table all records' `first_name` and `last_name` values will be mixed randomly like in simple mixing, but `first_name` set to be dependent on the value of the `sex` field. It means on mixing the script will only select first names from other records where the value of the `sex` field is the same.

The dependent mixing *may* give us the following result from the same *starter* table

| id | first_name | last_name | passport_number | sex |
|----|------------|-----------|-----------------|-----|
| 1  | Trevor     | Gauranga  | 123456789AA     | M   |
| 2  | Jane       | Doe       | 987456789AA     | F   |
| 3  | John       | Davis     | 985631458ZZ     | M   |

As we had only one female in our table and `first_name` is dependent on `sex`, there was nobody to mix with the first name. That is why Jane kept her first name.

# CLI Usage
The package can be used as a shell script.
`$ vendor/bin/datamixer`

The script will ask the PDO dsn string, username and password for connecting to the database, than the table name and the `fileds` array.

The `fileds` array shoud be define like this:
`first_name => sex, last_name`
