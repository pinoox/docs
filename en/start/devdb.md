# DevDB For Local Development

[Back to index](../README.md)

Pinoox DevDB lets you build a Pinx app without installing MySQL, PostgreSQL, or SQLite manually.

The default Pinx `.env` is:

```dotenv
APP_ENV=development
DB_CONNECTION=devdb
```

You still write normal migrations, normal models, and normal query builder code.

---

## What DevDB Does

DevDB provides a development-only database connection.

It stores local data under:

```text
storage/devdb/
|-- schema.json
|-- devdb.sqlite
|-- data/
|   `-- {table}.json
`-- meta/
    |-- migrations.json
    |-- sequences.json
    `-- indexes.json
```

When `pdo_sqlite` is available, DevDB can use a local SQLite-backed engine for better SQL compatibility. When SQLite is not available, DevDB falls back to a JSON engine with file locking.

---

## Run Migrations

```bash
pinx migrate
```

Or force DevDB:

```bash
pinx migrate --devdb
```

Migration files remain the source of truth. DevDB records schema metadata and creates local development storage.

---

## Use Models Normally

```php
use App\Model\Post;

Post::create([
    'title' => 'Hello DevDB',
    'status' => 'published',
]);

$posts = Post::where('status', 'published')->get();
$post = Post::find(1);
$post->update(['status' => 'draft']);
$post->delete();
```

Use query builder normally:

```php
DB::app()->table('posts')
    ->where('status', 'published')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();
```

---

## Commands

```bash
pinx devdb:status
pinx devdb:inspect posts
pinx devdb:export storage/devdb.json
pinx devdb:clear --force
```

Open Pinx Inspector for a graphical database view:

```text
http://127.0.0.1:8000/~inspector
```

---

## When To Use DevDB

Use DevDB for:

- starting a new app quickly
- local UI/API development
- migrations while prototyping
- tests and sample data
- demos where installing a database is friction

Do not use DevDB for:

- production workloads
- high-concurrency writes
- exact MySQL/PostgreSQL behavior
- database performance testing

Production should use MySQL, PostgreSQL, or a production SQLite database.

---

## Switch To A Real Database

Update `.env`:

```dotenv
APP_ENV=production
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_shop
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
pinx doctor
pinx migrate
```

Pinoox never silently falls back to DevDB in production.
