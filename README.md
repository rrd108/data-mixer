# data-mixer
A simple PHP tool to create test data from existing database by mixing records.

Sometimes you want to test your application with the same amount of data what you
have in your production enviroment. This script reads your exisiting data and
mix its content.

# Installation
There is no installation process, however you have to have PHP installed on your system.

# Usage
1. Create a backup of your production data and copy it to your development enviroment. **NEVER** use this tool in production, as you can accidently mix your data. **NO RESTORE, NO UNDO, NO CANCEL** method, what is mixed once, can not be reverted without a backup.

2. Open `data_mixer.php` and change the values of `$host`, `$database_user`, `$database_password`, `$database` and `$table` variables.

3. Define your selected fields to be mixed in the `$fields` variable.

4. Run the script via command line or via your browser.

# `$fields` array
The fields defined in `$fields` array will be mixed.

## Simple mixing
```php
$fields = ['first_name', 'last_name'];
```
In this case in the `$table` database table all records' `first_name` and `last_name` values will be mixed randomly. All other table fields will be unchaned.

Let's say we have the following *starter* table.

| id | first_name | last_name | passport_number | sex |
| ---|------------| ----------|-----------------|-----|
| 1  | John       | Doe       | 123456789AA     | M   |
| 2  | Jane       | Gauranga  | 987456789AA     | F   | 
| 3  | Trevor     | Davis     | 985631458ZZ     | M   |

The simple mixing *may* give us the following result

| id | first_name | last_name | passport_number | sex |
| ---|------------| ----------|-----------------|-----|
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
In this case in the `$table` database table all records' `first_name` and `last_name` values will be mixed randomly like in simple mixing, but `first_name` set to be dependent on the value of the `sex` field. It means on mixing the script will only select first names from other records where the value of the sex filed is the same.

The dependent mixing *may* give us the following result from the same *starter* table

| id | first_name | last_name | passport_number | sex |
| ---|------------| ----------|-----------------|-----|
| 1  | Trevor     | Gauranga  | 123456789AA     | M   |
| 2  | Jane       | Doe       | 987456789AA     | F   |
| 3  | John       | Davis     | 985631458ZZ     | M   |

As we had only one female in our table and `first_name` is dependent on `sex`, there was nobody to mix with the first name. That is why Jane kept her first name.