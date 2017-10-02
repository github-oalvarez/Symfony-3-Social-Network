RED SOCIAL SYMFONY 3
====================

Instalación previa
------------------

Instalamos composer en consola `composer create-project symfony/framework-standard-edition curso_social_network`.
Seleccionamos **database_name (symfony): curso_social_network** durante la instalación.
Creamos la base de datos lanzando el comando de consola `php bin/console doctrine:database:create`.

---------------------------------------------------------------------------------------------------

```sql
-- si la base de datos no existe la crearemos
CREATE DATABASE IF NOT EXISTS curso_social_network;
USE curso_social_network;
-- creamos la tabla 'users'

CREATE TABLE users(
id        int(255) auto_increment not null,
role      varchar(20),
email     varchar(255),
name      varchar(255),
surname   varchar(255),
password  varchar(255),
nick      varchar(50),
bio       varchar(255),
active    varchar(2),
image     varchar(255),
--los campos con valores únicos será 'email' y 'nick'
CONSTRAINT users_uniques_fields UNIQUE (email, nick),
--la clave primaria será'id'
CONSTRAINT pk_users PRIMARY KEY(id)
-- forzamos 'ENGINE=innoDb' para mantener la entidad relacional entre als tablas existentes.
)ENGINE = InnoDb;


CREATE TABLE publications(
id        int(255) auto_increment not null,
user_id   int(255),
text      mediumtext,
document  varchar(100),
image     varchar(255),
status    varchar(30),
created_at datetime,
--la clave primaria será'id'
CONSTRAINT pk_publications PRIMARY KEY (id),
-- relación entre tablas user_id referenciado a users(id)
CONSTRAINT fk_publications_users FOREIGN KEY (user_id) references users(id)
)ENGINE = InnoDb;


CREATE TABLE following(
id       int(255) auto_increment not null,
user     int(255),
followed int(255),
CONSTRAINT pk_following PRIMARY KEY(id),
CONSTRAINT fk_following_users FOREIGN KEY(user) references users(id),
CONSTRAINT fk_followed FOREIGN KEY(followed) references users(id)
)ENGINE = InnoDb;


CREATE TABLE private_messages(
id       int(255) auto_increment not null,
message  longtext,
emitter  int(255),
receiver int(255),
file     varchar(255),
image    varchar(255),
readed   varchar(3),
created_at datetime,
CONSTRAINT pk_private_messages PRIMARY KEY(id),
CONSTRAINT fk_emmiter_privates FOREIGN KEY(emitter) references users(id),
CONSTRAINT fk_receiver_privates FOREIGN KEY(receiver) references users(id)
)ENGINE = InnoDb;


CREATE TABLE likes(
id       int(255) auto_increment not null,
user        int(255),
publication int(255),
CONSTRAINT pk_likes PRIMARY KEY(id),
CONSTRAINT fk_likes_users FOREIGN KEY(user) references users(id),
CONSTRAINT fk_likes_publication FOREIGN KEY(publication) references publications(id)
)ENGINE = InnoDb;


CREATE TABLE notifications(
id        int(255) auto_increment not null,
user_id   int(255),
type      varchar(255),
type_id   int(255),
readed   varchar(3),
created_at datetime,
extra   varchar(100),
CONSTRAINT pk_notifications PRIMARY KEY(id),
CONSTRAINT fk_notifications_users FOREIGN KEY(user_id) references users(id)
)ENGINE = InnoDb;
```

---------------------------------------------------------------------------------------------------

Requerimos instalar además de composer básico los siguientes componentes mediante la terminal:
    * `composer require doctrine/doctrine-cache-bundle`.
    * `composer require incenteev/composer-parameter-handler`.
    * `composer require knplabs/knp-paginator-bundle`.

Actualizamos composer con los paquetes nuevos usando `composer update`.
Creamos el primer Bundle `php bin/console generate:bundle`.

| Are you planning on sharing this bundle across multiple applications?           | [no]: no       |
|:--------------------------------------------------------------------------------|----------------|
| Give your bundle a descriptive name, like BlogBundle. Bundle name [BlogBundle]: | BackendBundle  |
| Target Directory [src/]:                                                        | src/           |
| Configuration format (annotation, yml, xml, php) [annotation]:                  | yml            |

-----------------------------------------------------------------------------------------------------

| POSIBLE FALLO SYMFONY 3.3 |
|---------------------------|

Puede dar error al ejecutarlo, lanzando el siguiente mensaje Fatal error: Class 'PruebaBundle' not found in C:\wamp64\www\symfony.test\app\AppKernel.php on line 19 (Ver hilo con solución, aquí).

En este caso el error se encontraba dentro de **C:\wamp64\www\symfony.test\composer.json**,

Dentro de **C:\wamp64\www\symfony.test\composer.json**, buscamos:

```json
"autoload": {
        "psr-4": {
            "AppBundle\\": "src/AppBundle",
            "": "src/"
        },
        "classmap": [ "app/AppKernel.php", "app/AppCache.php" ]
    }
```

y lo sustituimos por:

```json
"autoload": {
        "psr-4": {
            "": "src/"
        },
        "classmap": [ "app/AppKernel.php", "app/AppCache.php" ]
    }
```

| MUY IMPORTANTE | ejecutar `composer dump-autoload` para ejecutar la función autoload. |
|----------------|--------------------------------------------------------------------|

-----------------------------------------------------------------------------------------------------

Si queremos generar una nueva entidad usaremos `php bin/console doctrine:generate:entity`

*Nota*: Si previamente ya subimos la base de datos al servidor (como en este caso) y queremos importar su configuración a nuestro bundle, **BackendBundle** usaremos `php bin/console doctrine:mapping:import BackendBundle yml`.

---------------------------------------------------------------------------------------------------

**IMPORTANTE** cambiamos los nombres de las configuraciones de la base de datos de plural a singular, así:

* **Likes.orm.yml** -> **Like.orm.yml**
* **Notifications.orm.yml** -> **Notification.orm.yml**
* **PrivateMessages.orm.yml** -> **PrivateMessage.orm.yml**
* **Publications.orm.yml** -> **Publication.orm.yml**
* **Users.orm.yml** -> **User.orm.yml**

Dentro del archivo **Like.orm.yml** (y el resto de archivos, incluido **src\BackendBundle\Entity\Following.php**) cambiamos el nombre de la entidad para que sea singular, y las relaciones existentes interiormente con las demás también.

Lanzaremos el comando `php bin/console doctrine:generate:entities BackendBundle` para generar / actualizar las entidades
Posteriormente `php bin/console doctrine:schema:update --force` para actualizar la Base de Datos según la entidades generada.

*Nota*: Si diera el fallo **[RuntimeException] Bundle "BackendBundle" does not contain any mapped entities.**, probamos a actualizar composer mediante `composer update` y repetimos `php bin/console doctrine:generate:entities BackendBundle`.

Se habrán generado:
* **src\BackendBundle\Entity\Like.php**
* **src\BackendBundle\Entity\Notification.php**
* **src\BackendBundle\Entity\PrivateMessage.php**
* **src\BackendBundle\Entity\Publication.php**
* **src\BackendBundle\Entity\User.php**
* **src\BackendBundle\Entity\Following.php**

¿Cómo gestionaremos los controladores?
--------------------------------------

Crearemos los controladores **src\AppBundle\Controller\UserController.php** y **src\AppBundle\Controller\PublicationController.php**, con el siguiente contenido base:

```php
<?php
// src\AppBundle\Controller\PublicationController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublicationController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        echo "Publication USer";
        die();
    }
}
```

```php
<?php
// src\AppBundle\Controller\UserController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        echo "Publication USer";
        die();
    }
}
```

¿Cómo gestionaremos el sistema de enrutado?
-------------------------------------------

Usaremos el sistema **yml** para así poder gestionar los enrutados de una manera más sencilla a gran escala.

* **app\config\routing.yml** indicará dónde se encuentra el enrutador de nuestro proyecto `resource: '@AppBundle/Resources/config/routing.yml'` así como su prefijo `prefix:   /`.

```yml
# app\config\routing.yml
app:
    resource: '@AppBundle/Resources/config/routing.yml'
    prefix:   /
```

* **src\AppBunlde\Resources\config\routing.yml** indicará dónde se encuentra el enrutador de cada una de las funcionalidades **app_user** (`resource: '@AppBundle/Resources/config/routing/user.yml'`)y **app_publication** (`resource: '@AppBundle/Resources/config/routing/publication.yml'`), además de su correspodiente prefijo `prefix:   /`.

```yml
# src\AppBunlde\Resources\config\routing.yml
app_user:
    resource: '@AppBundle/Resources/config/routing/user.yml'
    prefix:   /

app_publication:
    resource: '@AppBundle/Resources/config/routing/publication.yml'
    prefix:   /

home_page:
    path: /
    defaults: { _controller: AppBundle:User:login }
```

* **src\AppBunlde\Resources\config\routing\user.yml** y **src\AppBunlde\Resources\config\routing\publication.yml**, son los enrutadores específicos.

```yml
# src\AppBunlde\Resources\config\routing\user.yml
login:
    path: /login
    defaults: { _controller: AppBundle:User:login }

# src\AppBunlde\Resources\config\routing\publication.yml
home_publication:
    path: /home
    defaults: { _controller: AppBundle:Publication:index }
```

¿Cómo gestionaremos el sistema de vistas?
-----------------------------------------

Para referenciar la plantilla tomaremos como ejemplo el método `public function loginAction(Request $request)`, dentro del controlador **src\AppBundle\controller\UserController.php**.

```php
    public function loginAction(Request $request)
    {
        return $this->render('AppBundle:User:login.html.twig', array(
            "titulo"=>"Login"));
    }
```

Nuestra plantilla **src\AppBundle\Resources\views\login.html.twig** será:

```twig
{# src\AppBundle\Resources\views\login.html.twig #}
<body><h1>Página de {{titulo}} </h1></body>
```

Extender vistas
---------------
Para extender las vistas podemos usar tanto la base predefinida dentro de la instalación básica de symfony en **app\Resources\views\base.html.twig**, como creando nuestra propia plantilla base como en el siguiente ejemplo:

```twig
{# src\AppBundle\Resources\views\Layouts\layout.html.twig #}
<!DOCTYPE HTML>
<html lang="es">
  <head>
    <meta charset="utf-8"/>
    <title>{% block title %} SF3 NETWORK {% endblock %}</title>
    {% block stylesheets %}
      <link href="{{ asset ('assets/bootstrap/css/bootstrap.min.css')}}" type="text/css" rel="stylesheet">
      <link href="{{ asset ('assets/css/bootstrap.cosmo.min.css')}}" type="text/css" rel="stylesheet">
      <link href="{{ asset ('assets/css/styles.css')}}" type="text/css" rel="stylesheet">
    {% endblock%}
    {% block javascripts %}
      <script src="{{ asset ('assets/js/jquery.min.css')}}"></script>
      <script src="{{ asset ('assets/bootstrap/js/bootstrap.min.js')}}"></script>
    {% endblock%}
  </head>
  <body>
    <header>
      <nav class="navbar navbar-inverse">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapse" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
              <span class="sr-only">NAVEGACIÓN</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{path("app_homepage")}}">
            <span class="glyphicon glyphicon-cloud" aria-hidden="true"></span>
              NETWORK
            </a>
          </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
              <li>
                <a href="{{path("login")}}">
                  <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>
                  &nbsp;
                  Entrar
                </a>
              </li>
              <li>
                <a href="{{path("login")}}">
                  <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                  &nbsp;
                  Registro
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <section id="content">
    {% block content %}{% endblock%}
    </section>
    <footer class="col-lg-12">
      <hr/>
      <div class="container">
        <p class="text-muted">SF3 NETWORK </p>
      </div>
    </footer>
  </body>
</html>
```

En el siguiente ejemplo modificaremos la plantilla que refleja el método `public function loginAction(Request $request)` dentro de **src\AppBundle\Controller\UserController.php**.

```twig
{# src\AppBundle\Resources\views\login.html.twig #}
{% extends "AppBundle:Layouts:layout.html.twig"%}
{% block content %}
  <body><h1>Página de {{titulo}} </h1></body>
{% endblock %}
```

encoders
--------

```yml
# app\config\security.yml
security:
    # https://symfony.com/doc/current/security.html
    # http://symfony.com/doc/current/security/named_encoders.html
    encoders:
        BackendBundle\Entity\User:
            algorithm: bcrypt
            cost: 4 # Número de veces que se va a encriptar la contraseña
```

Generaremos un formulario mediante `php bin/console doctrine:generate:form BackendBundle:User`, el cual creará el siguiente archivo **\src\BackendBundle/Form/UserType.php**.

El siguiente paso consistirá en mover **\src\BackendBundle\Form\UserType.php** a **\src\AppBundle\Form\RegisterType.php** y realizar las siguientes modificaciones:
