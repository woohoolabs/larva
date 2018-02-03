## 0.6.0 - unreleased

ADDED:

- `SelectQueryBuilderInterface::orderByAttribute` and `SelectQueryBuilderInterface::orderByExpression` methods

CHANGED:

- Require PHPUnit 7.0 minimally for running tests

REMOVED:

- `SelectQueryBuilderInterface::orderBy` method

FIXED:

- Fatal error when `AbstractPdoConnection::fetch()` returns an empty result set
- Fatal error on PHP 7.1 because of incompatible declarations

## 0.5.0 - 2017-09-12

ADDED:

- Support for `TRUNCATE`
- Support for `UNION` in `SELECT` queries

CHANGED:

- Increased minimum PHP version requirement to 7.1
- Added missing type declarations
- Translator objects are loaded lazily
- SQL statements aren't pretty printed
- Updated development docker images

FIXED:

- Subqueries in `FROM` clauses will be translated properly when using the MySQL driver
- Renamed `WoohooLabs\Larva\Driver\Mysql` namespace to `WoohooLabs\Larva\Driver\MySql` to match its filesystem path
- `MasterSlaveConnection::getLastInsertedId` didn't use the `$name` parameter

## 0.4.0 - 2017-03-05

ADDED:

- `SelectQueryBuilder::selectExpressions()` and `SelectQueryBuilder::selectCount()` methods

CHANGED:

- `SelectQueryBuilder::select()` was renamed to `SelectQueryBuilder::selectColumns()`
- Connection instantiation became more straightforward

FIXED:

- `Bool` values are now casted to `int` when using prepared statements
- Return type of `SelectQueryBuilder::fetchColumn()` became `mixed` so that it can now be used to also retrieve numbers
- Adding condition groups via `ConditionBuilder::addConditionGroup()` when originally there was no condition
- Subselect statements are now translated properly when using the MySQL driver
- Columns in the `SELECT` clause are now translated properly when using the MySQL driver

## 0.3.1 - 2017-02-24

FIXED:

- Transaction handling

## 0.3.0 - 2017-02-12

ADDED:

- `SelectQueryBuilder::lockForShare()` and `SelectQueryBuilder::lockForUpdate()`
- Support for exists/any/some/all conditions

CHANGED:

- Conditions can be specified via `ConditionBuilder` instances instead of `Closure`s
- Additional conditions can now be specified via `ConditionBuilder::addConditionGroup()`

REMOVED:

- `ConditionBuilder::subselect()` method
- `MySqlConditionsTranslator` class

FIXED:

- Many conditions didn't get translated by the MySQL driver

## 0.2.0 - 2017-02-03

ADDED:

- Support for using configuration file/array for connections via the `ConnectionFactory`
- Support for master/slave connections
- Possibility to retrieve the connection from query builders
- Support for locking in `SELECT` statement
- `selectExpression()` and `selectColumn()` to `SelectQueryBuilder` to make it easier to build `SELECT` expressions

CHANGED:

- Renamed `SelectQueryBuilder::fields()` to `SelectQueryBuilder::select()`

FIXED:

- `MySqlPdoConnection` doesn't expect the `$driver` constructor parameter anymore
- Parentheses are added when using the `ON` clause with the MySQL driver
- Backtick `JOIN` aliases when using the MySQL driver

## 0.1.0 - 2017-01-29

- Initial release
