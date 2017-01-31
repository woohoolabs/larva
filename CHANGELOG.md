## 0.2.0 - unreleased

ADDED:

- Possibility to retrieve the connection from query builders
- Support for locking in `SELECT` statement
- `selectExpression()` and `selectColumn()` to `SelectQueryBuilder` to make it easier to build `SELECT` expressions

CHANGED:

- Renamed `SelectQueryBuilder::fields()` to `SelectQueryBuilder::select()`

FIXED:

- Parentheses are added for the ON clause
- Backtick `JOIN` aliases

## 0.1.0 - 2017-01-29

- Initial release
