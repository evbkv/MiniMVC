# MiniMVC

A minimalistic MVC/CRUD PHP framework designed for small-load web applications that do *not* process sensitive or confidential data. MiniMVC focuses on clarity, simplicity, structural cleanliness, and rapid development using **PHP + SQLite**, with zero external dependencies and a fully transparent architecture.

## Description

MiniMVC is a lightweight educational and prototyping framework built around a clean MVC structure:

* **Minimalistic architecture** — compact codebase with no unnecessary abstractions  
* **CRUD-ready model system** using SQLite  
* **Simple routing** via config file  
* **Role-based Access Control (RBAC)**  
* **Secure sessions, CSRF protection, sanitization**  
* **Ideal for small tools, prototypes, admin dashboards, educational purposes**  
* *Not intended for handling sensitive or confidential information*

## Installation

### Requirements
- PHP **8.1+**
- SQLite (PDO SQLite enabled)
- Apache/Nginx (recommended: Apache with `.htaccess`)

### Steps

**1. Clone the repository:**
```bash
git clone https://github.com/your/repository.git
```

**2. Ensure the /database directory is writable:**
```bash
chmod 775 database
```

**3. Point your server’s document root to:**
```
/public
```

**4. Open in browser:**
```
http://yourhost/setup.php
```

**5. Complete the setup wizard:**
* Create the SQLite database
* Create the first admin user
* Auto-login will redirect to the home page

### Configuration

**1. Database Structure**
Database creation and migration logic lives in:
```bash
database/migration.php
```
Default schema:
```sql
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
To add your own tables:
* Add new SQL statements to migration.php
* Run migrations (allowed in development mode)
* Or manually execute SQL scripts

**2. Creating Models**
Models extend the base Model class:
```php
class Article extends Model {
    protected static $table = 'articles';
    protected static $fillable = ['title', 'content'];
    protected static $rules = [
        'title' => 'required|min:3',
        'content' => 'required'
    ];
}
```
Available model methods:
* Model::all()
* Model::find($id)
* Model::where([...])
* Model::create([...])
* Model::update($id, [...])
* Model::delete($id)

**3. Creating Controllers**
Controllers extend Controller:
```php
class ArticleController extends Controller {
    public function index() {
        $articles = Article::all();
        $this->render('articles/index', ['articles' => $articles]);
    }
}
```
Features:
* Rendering views
* Redirects
* Auth access ($this->auth())
* Permission checks ($this->checkAuth('permission'))
* Access to request params

**4. Creating Views**
Views are simple PHP files in:
```
app/Views/
```
Example:
```php
<h1><?php echo $article['title']; ?></h1>
<p><?php echo $article['content']; ?></p>
```
Auto-available view variables:
* $csrf_token
* $current_user
* $params
* Data passed from controller

**5. Adding Routes**
Routes are defined in:
```
config/routes.php
```
Example:
```php
return [
    'articles' => ['ArticleController', 'index'],
    'article/create' => ['ArticleController', 'create'],
];
```

**6. Roles & Permissions**
Roles are defined in config/app.php:
```php
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');
```
Permissions are mapped in:
```
app/Services/Auth.php
```
Example mapping:
```php
ROLE_ADMIN => ['manage_users', 'create_article', 'edit_article', 'delete_article'],
ROLE_USER  => ['view_article', 'edit_own_article'],
```
To protect an action:
```php
$this->checkAuth('create_article');
```

## Roadmap

* CLI generator for models, controllers, views
* Automatic CRUD scaffolding
* Pagination helpers
* Basic ORM-like relations
* Middleware system improvements
* Optional template engine (lightweight)
* Modular architecture / plugin system

## Author

[Evgenii Bykov](https://github.com/evbkv)

## License

GNU General Public License v3.0
