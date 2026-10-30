# PHP MVC Practice Skeleton

A minimal MVC base so you can focus on design (auth, repositories, services...) instead of plumbing.
Requires PHP 8.1+ with `pdo_mysql`. No Composer or autoloader: every class file is loaded with `require_once` in `app/requires.php`.

## Run it

```bash
cp .env.example .env            # then edit DB credentials
mysql -u root -p < database/schema.sql
php -S localhost:8000 -t public
```

Open http://localhost:8000 and http://localhost:8000/health (checks the DB connection).

## Structure

```
public/index.php      Front controller (only web-exposed PHP file)
bootstrap.php         Loads app/requires.php, env, config, container wiring
app/requires.php      List of require_once lines (add a line per new class)
config/               app.php, database.php, routes.php
app/Core/             Framework plumbing (don't mix feature code in here)
app/Controllers/      HTTP layer: read Request, return Response
app/Models/           Data/domain classes (extend Core\Model or write your own)
app/Middleware/       Classes implementing Core\Middleware
app/Views/            PHP templates + layouts
database/schema.sql   SQL for your tables
```

## Request flow

`public/index.php` -> `bootstrap.php` -> `Application::run()` -> `Router::dispatch()` -> middleware pipeline -> controller action -> `Response`.

## Core building blocks

| Piece | Purpose |
|---|---|
| `Container` | Dependency injection with constructor autowiring; bind interfaces to implementations in `bootstrap.php` |
| `Router` | `get/post/put/delete`, `{param}` paths, per-route middleware |
| `Request` / `Response` | HTTP in/out objects (`Response::json()`, `Response::redirect()`) |
| `View` | Templates with layouts; use `e()` to escape output |
| `Database` | Lazy PDO, prepared statements, `transaction()` |
| `Config` / `Env` | Dot-notation config (`$config->get('app.debug')`), `.env` support |
| `HttpException` | `throw new HttpException(403)` renders an error page |

## Using the database in repositories

Repositories receive the shared `Database` service, which wraps PDO and uses
prepared statements. Keep SQL and persistence in repositories; inject a
repository interface into a controller or service instead of putting database
access on the `User` model.

For example, `UserRepo` implements `UserRepoInterface` and can be resolved
through the container:

```php
$users = $container->get(\App\Interfaces\UserRepoInterface::class);
$user = $users->findByEmail('person@example.com');
```

Bindings for both repository interfaces are configured in `bootstrap.php`.
Edit `.env` for connection settings, then run `database/schema.sql` to create
the tables used by the repositories.

## Adding a feature (example: auth)

1. Table in `database/schema.sql`.
2. Interface + implementation (e.g. `UserRepository` / `PdoUserRepository`) in `app/Models`, bound in `bootstrap.php`.
3. Service class for the business logic (e.g. `AuthService`), depending on the interface.
4. Controller + views.
5. Routes in `config/routes.php`, guarded with a middleware in `app/Middleware`.

Sessions, CSRF protection, validation and a password hasher are intentionally left out; those are yours to design.
