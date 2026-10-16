-- Test database schema for QueryFilter.
-- Tables are created here (SchemaLoader); fixtures are data-only.

CREATE TABLE query_filter_tests (
    id INTEGER PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    age INTEGER,
    score INTEGER,
    status VARCHAR(50),
    created DATETIME,
    modified DATETIME
);
