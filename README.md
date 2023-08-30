# SQL Query Builder

SQL Query Builder is a PHP package designed to help developers construct SQL queries, from the simplest to the most complex, using the builder pattern.

## Features

- Simple and intuitive API.
- Supports various SQL operations including `SELECT`, `INSERT`, `UPDATE`, and `DELETE`.
- Extendable architecture allows for adding more custom query types.
- Strongly typed components like `Where`, `Join`, and `OrderBy` provide better IDE support and reduce runtime errors.

## Installation

Using Composer:

```bash
composer require didslm/sql-builder
```

## Usage

### Simple SELECT query

```php
<?php

require 'vendor/autoload.php';

use Didslm\QueryBuilder\SelectQueryBuilder;

$query = SelectQueryBuilder::from('users')
    ->select('*')
    ->where('age', '>', 18)
    ->toSql();

echo $query;  // Outputs: SELECT * FROM users WHERE age > 18
```

### Complex JOIN Query

```php
$query = SelectQueryBuilder::from('users')
    ->select('*')
    ->where('age', '>', 18)
    ->toSql();

echo $query;  // Outputs: SELECT * FROM users WHERE age > 18
```


## Testing

After cloning the repository and installing the dependencies, you can run the tests with:

```bash
vendor/bin/phpunit
```

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you'd like to change.

Please make sure to update the tests as appropriate.

## License

[MIT](https://choosealicense.com/licenses/mit/)

---

You'll need to replace placeholders such as `your-vendor-name` with appropriate values that fit your project. The provided examples are also basic and should be replaced or extended based on the actual functionality of your package.