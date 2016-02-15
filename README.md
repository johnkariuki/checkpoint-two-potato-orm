#Checkpoint Two: Potato ORM

##Andela Checkpoint Two

A simple agnostic ORM that can perform the basic crud database operations.

#####TIA

##Install

Via Github

``` bash
$ git clone git@github.com:andela-jkariuki/checkpoint-two-potato-orm.git
```

Update packages

``` bash
$ composer install
```

Rename .env.example to .env

Update your .env to have your database credentials

```bash
DB_DRIVER = "sqlite"
DB_USERNAME = "homestead"
DB_PASSWORD = "secret"
DB_NAME = "potato.db"
DB_HOST = "localhost"
DB_PORT = 8000
```

##Usage

To use Potato ORM, extend the ```PotatoModel``` class. 

The default naming convention for a table name associated with a class is it's lowercase plural syntax.

The default naming convention for the unique table field is id.

To overwrite the default naming conventions, declare protected variables as explained below.

```php
/**
 * Default table name for Car class is cars.
 *
 * Default uniqueId field for table cars is id
 */
class Car extends PotatoModel
{
    //protected static $table = "your_table_name";
    //protected static $uniqueId = "your_unique_id";
}
```
Ensure the table exists before using the Potato ORM class to avoid exceptions.

Use the SQL statement template below

```php
CREATE TABLE `cars` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT,
	`model`	TEXT,
	`year`	INTEGER
)
```
## Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/andela-jkariuki/checkpoint-two-potato-orm).

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Security

If you discover any security related issues, please email [John Kariuki](john.kariuki@andela.com) or create an issue.

## Credits

[John kariuki](https://github.com/andela-jkariuki)

## License

### The MIT License (MIT)

Copyright (c) 2016 John kariuki <john.kariuki@andela.com>

> Permission is hereby granted, free of charge, to any person obtaining a copy
> of this software and associated documentation files (the "Software"), to deal
> in the Software without restriction, including without limitation the rights
> to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
> copies of the Software, and to permit persons to whom the Software is
> furnished to do so, subject to the following conditions:
>
> The above copyright notice and this permission notice shall be included in
> all copies or substantial portions of the Software.
>
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
> THE SOFTWARE.
