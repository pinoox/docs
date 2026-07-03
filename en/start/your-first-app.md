# Create Your First Pinx App

[Back to index](../README.md)

This guide builds a small single-app project from zero. You will create the project, run it locally, add a route, add a controller, create a table with a migration, use a model, and prepare the app for release.

---

## 1. Create The Project

```bash
pinx new blog
cd blog
```

Or with Composer only:

```bash
composer create-project pinoox/app blog
cd blog
```

Check the project:

```bash
pinx doctor
```

---

## 2. Run Migrations

The default `.env` uses DevDB:

```dotenv
APP_ENV=development
DB_CONNECTION=devdb
```

Run migrations:

```bash
pinx migrate
```

If no real database is configured, DevDB stores local development schema and data under `storage/devdb/`.

---

## 3. Start The Dev Server

```bash
pinx dev --open
```

Open Pinx Inspector:

```text
http://127.0.0.1:8000/~inspector
```

Use Inspector to check routes, database tables, logs, config, migrations, and project health while developing.

---

## Build The First Feature

The app root is the project root. You do not work inside `apps/{package}` in a Pinx project.

### Create A Controller

```bash
pinx make controller PostController
```

Example:

```php
<?php

namespace App\Controller;

use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class PostController extends Controller
{
    public function index()
    {
        return View::render('posts/index', [
            'title' => 'Posts',
        ]);
    }
}
```

### Add A Route

Edit `routes/web.php`:

```php
<?php

use App\Controller\PostController;
use function Pinoox\Router\get;

get('/posts', [PostController::class, 'index'])->name('posts.index');
```

Check routes:

```bash
pinx routes
```

### Add A View

Create `theme/default/posts/index.twig`:

```twig
<h1>{{ title }}</h1>
<p>Your first Pinx page is running.</p>
```

Open:

```text
http://127.0.0.1:8000/posts
```

---

## Add Data

Create a migration and model:

```bash
pinx make migration create_posts_table
pinx make model Post
```

Example migration:

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\Migration;
use Pinoox\Portal\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

Run:

```bash
pinx migrate
```

Example model:

```php
<?php

namespace App\Model;

use Pinoox\Component\Database\Model;

class Post extends Model
{
    protected $fillable = ['title', 'status'];
}
```

Use it:

```php
Post::create(['title' => 'Hello Pinoox', 'status' => 'published']);

$posts = Post::where('status', 'published')
    ->orderBy('id', 'desc')
    ->get();
```

---

## Test, Build, Release

```bash
pinx make test PostTest --feature
pinx test
pinx doctor
pinx build
pinx release --bump=patch
```

See [Build and release](./build-release.md) for signing and production packaging.

---

## Next

- [Project structure](./structure.md)
- [DevDB](./devdb.md)
- [Routes](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Migrations](../database/migrations.md)
- [Eloquent ORM](../eloquent-orm/getting-started.md)
