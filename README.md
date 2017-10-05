RED SOCIAL SYMFONY 3
====================

1.Instalación y configuración inicial Proyecto Symfony
======================================================

* Instalamos composer en consola `composer create-project symfony/framework-standard-edition curso_social_network`.
* Subimos la configuración de nuestra base de datos importandola mediante **PhpMyAdmin**

-------------------------------------------------------------------------

*Nota*: Requerimos además de la instalación básica de [Composer](https://getcomposer.org/) los siguientes componentes, que añadirán mediante la terminal:
    * `composer require doctrine/doctrine-cache-bundle`.
    * `composer require incenteev/composer-parameter-handler`.
    * `composer require knplabs/knp-paginator-bundle`.

-------------------------------------------------------------------------

1.1.Base de Datos del Proyecto
------------------------------

* Seleccionamos **database_name (symfony): curso_social_network** durante la instalación.
* Creamos la base de datos lanzando el comando de consola `php bin/console doctrine:database:create`.

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

-------------------------------------------------------------------------

*Nota<sup>1</sup>*: Si queremos generar una nueva entidad usaremos `php bin/console doctrine:generate:entity`

*Nota<sup>2</sup>*: Si previamente ya subimos la base de datos al servidor (como en este caso) y queremos importar su configuración a nuestro bundle, **BackendBundle** usaremos `php bin/console doctrine:mapping:import BackendBundle yml` (Así mapearemos la base de datos y crearemos nuestra entidad en Symfony).

-------------------------------------------------------------------------

2.BackendBundle del Proyecto, lógicas y estrategias usadas
==========================================================

2.1.Creando el BackendBundle
----------------------------

Actualizamos composer con los paquetes nuevos usando `composer update`, para a continuación crear el *Bundle* **BackendBundle**, mediante el comando de consola: `php bin/console generate:bundle`.

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
|----------------|----------------------------------------------------------------------------------------------------------------|

-----------------------------------------------------------------------------------------------------

2.1.1.Modificando BackendBundle
-------------------------------

**IMPORTANTE** Se recomienda los nombres de las configuraciones de la base de datos de plural a singular, así:

* **Likes.orm.yml** -> **Like.orm.yml**
* **Notifications.orm.yml** -> **Notification.orm.yml**
* **PrivateMessages.orm.yml** -> **PrivateMessage.orm.yml**
* **Publications.orm.yml** -> **Publication.orm.yml**
* **Users.orm.yml** -> **User.orm.yml**

Además de dentro del archivo **Like.orm.yml** (y el resto de archivos, incluido **src\BackendBundle\Entity\Following.php**) cambiar el nombre de la entidad para que sea singular, y las relaciones existentes interiormente con las demás también.

* Lanzamos el comando `php bin/console doctrine:generate:entities BackendBundle` para generar / actualizar las entidades, y posteriormente `php bin/console doctrine:schema:update --force` para actualizar la Base de Datos según la entidades generada.

*Nota*: Si diera el fallo **[RuntimeException] Bundle "BackendBundle" does not contain any mapped entities.**, probamos a actualizar composer mediante `composer update` y repetimos `php bin/console doctrine:generate:entities BackendBundle`.

Así, se habrán generado:
* **src\BackendBundle\Entity\Like.php**
* **src\BackendBundle\Entity\Notification.php**
* **src\BackendBundle\Entity\PrivateMessage.php**
* **src\BackendBundle\Entity\Publication.php**
* **src\BackendBundle\Entity\User.php**
* **src\BackendBundle\Entity\Following.php**

2.2.Gestión de los Controladores de la App
-------------------------------------------

Crearemos los controladores **src\AppBundle\Controller\UserController.php** y **src\AppBundle\Controller\PublicationController.php** (dentro ambos de AppBundle), con el siguiente contenido base (extraido de **DefaultController.php**):

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

2.3.Gestión de los Sistemas de enrutado
---------------------------------------

**IMPORTANTE** Usaremos el sistema **yml** para así poder gestionar los enrutados de una manera más sencilla, manteniendo la posibilidad de escalar de manera sencilla la aplicación.

* **app\config\routing.yml** indicará dónde se encuentra el enrutador de nuestro proyecto `resource: '@AppBundle/Resources/config/routing.yml'` así como su prefijo `prefix:   /`.

```yml
# app\config\routing.yml
app:
    resource: '@AppBundle/Resources/config/routing.yml'
    prefix:   /
```

* **src\AppBunlde\Resources\config\routing.yml** a su vez indicará la ubicación del enrutador de cada una de las funcionalidades **app_user** (`resource: '@AppBundle/Resources/config/routing/user.yml'`) y **app_publication** (`resource: '@AppBundle/Resources/config/routing/publication.yml'`), además de su correspodiente prefijo `prefix:   /` (que en este caso será nulo).

```yml
# src\AppBunlde\Resources\config\routing.yml
app_homepage:
    path: /
    defaults: { _controller: AppBundle:Default:index }

app_user:
    resource: '@AppBundle/Resources/config/routing/user.yml'
    prefix:   /

app_publication:
    resource: '@AppBundle/Resources/config/routing/publication.yml'
    prefix:   /
```

* **src\AppBunlde\Resources\config\routing\user.yml** y **src\AppBunlde\Resources\config\routing\publication.yml**, son los enrutadores específicos.

```yml
# src\AppBunlde\Resources\config\routing\user.yml
login:
    path: /login
    defaults: { _controller: AppBundle:User:login }
´´´
´´ýml
# src\AppBunlde\Resources\config\routing\publication.yml
home_publication:
    path: /home
    defaults: { _controller: AppBundle:Publication:index }
```

2.4.Gestión del Sistema de Vistas
---------------------------------

Para referenciar la plantilla tomaremos como ejemplo el método `public function loginAction(Request $request)`, dentro del controlador **src\AppBundle\controller\UserController.php**.

```php
    public function loginAction(Request $request)
    {
        return $this->render('AppBundle:User:login.html.twig', array(
            "titulo"=>"Login"));
    }
```

Nuestra plantilla **src\AppBundle\Resources\views\login.html.twig**, tendrá que contener un `<body></body>` que incluya el contenido en su interior para que se muestre la barra de **debbug** de Symfony, tal que así:

```twig
{# src\AppBundle\Resources\views\login.html.twig #}
<body> <h1>Página de {{titulo}} </h1> </body>
```

2.4.1.Extender las Vistas (reutilizando código)
-----------------------------------------------

Para extender las vistas podemos usar tanto la base predefinida dentro de la instalación básica de symfony en **app\Resources\views\base.html.twig**, como creando nuestra propia plantilla base ( **src\AppBundle\Resources\views\Layouts\layout.html.twig** ) como es nuestro caso.

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
          {# LOGO #} 
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
          {# LOGO #}
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            {# MENÚ LOGIN + REGISTRO #} 
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
            {# MENÚ LOGIN + REGISTRO #} 
            {# MENÚ USUARIO #} 
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar">
                      {# Imagen de usuario si existe un registro o no de la imagen #}
                      {% if app.user.image == null %}
                        <img src="{{ asset ('assets/images/default.png') }}" />
                      {% else %}
                        <img src="{{ asset ('uploads/users/'~app.user.image) }}" />
                      {% endif %}
                    </div>
                    {{app.user.name}} {{app.user.surname}}
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="{{path("user_edit")}}">
                        <span class="glyphicon glyphicon-user" aria-hidden="true">
                        </span>&nbsp;Mi Perfil
                      </a>
                    </li>
                    <li>
                      <a href="{{path("user_edit")}}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>&nbsp;Mis datos</a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                      <a href="{{ path("logout") }}"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Salir</a>
                    </li>
                    <li>
                      <a href="{{ path("logout") }}"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>&nbsp;Ayuda</a>
                    </li>
                  </ul>
                </li>
              </ul>
            {# MENÚ USUARIO #} 
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

El siguiente paso consistirá en extender la plantilla **src\AppBundle\Resources\views\index.html.twig** a partir de **src\AppBundle\Resources\views\Layouts\layout.html.twig** mediante `{% extends "AppBundle:Layouts:layout.html.twig"%}`. 

```twig
{# src\AppBundle\Resources\views\index.html.twig #}
{% extends "AppBundle:Layouts:layout.html.twig"%}
{% block content %}
  <body><h1>Página de {{titulo}} </h1></body>
{% endblock %}
```

En el ejemplo anterior modificamos la plantilla que refleja el método `public function loginAction(Request $request)` dentro de **src\AppBundle\Controller\UserController.php**.

3.Seguridad
===========

3.1.Encoders
------------

Para el método de registro y logueo vamos a usar un sistema de encriptación bajo **bycrypt**, el cual definiremos dentro de **app\config\security.yml**, indicando además el número de veces que se va a encriptar la contraseña. 

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

4.Sistema de Registro
=====================

Definiremos el routing dentro de **src/AppBundle/Resources/config/routing/user.yml**:
```yml
# ROUTING PARA EL REGISTRO
register:
    path: /register
    defaults: { _controller: AppBundle:User:register }
```

4.1.Definiendo el Formulario de Registro (RegisterType)
-------------------------------------------------------

Generaremos un formulario mediante `php bin/console doctrine:generate:form BackendBundle:User`, creandose el siguiente archivo **\src\BackendBundle/Form/UserType.php**.

El siguiente paso consistirá en mover **\src\BackendBundle\Form\UserType.php** a **\src\AppBundle\Form\RegisterType.php**, para centralizar los formularios dentro del *Bundle* **AppBundle** (Además cambiamos el nombre de **UserType** a **RegisterType**). Posteriormente se realizará las siguientes modificaciones:

* Cambiar en **namespace** de  namespace **BackendBundle\Form;** a **namespace AppBundle\Form;**

```php
/* Cambiamos el namespace al cambiar el Bundle *********************/
// namespace BackendBundle\Form;
namespace AppBundle\Form;
```

* Incluiremos las librerías de *componentes* de *Symfony* que nos permitirán usar los tipos de datos de entrada del formulario.

```php
/* Añadimos los componentes que permitirán el uso de nuevas clases */
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/*******************************************************************/
```

* Eliminamos las entradas del fromulario que no queremos que aparezcan (`->add('role')->add('bio')->add('active')->add('image');`), modificamos cada entrada al formulario (que si queremos que aparezcan) indicando sus características y añadimos una nueva entrada **SubmitType**.

Como no hemos modifcado la ubicación de las entidades no habrá que modificar el método `public function configureOptions(OptionsResolver $resolver)`.

Finalmente el archivo **src\AppBundle\Form\RegisterType.php** quedará así:

```php
// src\AppBundle\Form\RegisterType.php
<?php
// namespace BackendBundle\Form;
/* Cambiamos el namespace al cambiar el Bundle                     */
namespace AppBundle\Form;
/*******************************************************************/
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/* Añadimos los componentes que permitirán el uso de nuevas clases */
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/*******************************************************************/
class RegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('name', TextType::class, array(
            'label'=>'Nombre',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-name form-control'
            )
          ))
          ->add('surname', TextType::class, array(
            'label'=>'Apellido',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-surname form-control'
            )
          ))
          ->add('nick', TextType::class, array(
            'label'=>'Nick',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-nick form-control nick-input'
            )
          ))
          ->add('email', EmailType::class, array(
            'label'=>'Correo electrónico',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-email form-control'
            )
          ))
          ->add('password', PasswordType::class, array(
            'label'=>'Contraseña',
            'required'=>'required',
            'attr'=>array(
              'class'=>'form-password form-control'
            )
          ))
          ->add('Registrarse',SubmitType::class, array(
            "attr"=>array(
              "class"=>"form-submit btn btn-success"
            )
          ));
/* No interesan que aparezcan estos 'input' dentro del formulario de registro */
// ->add('role')->add('bio')->add('active')->add('image');
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BackendBundle\Entity\User'
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'backendbundle_user';
    }
}
```

4.2.Controlador y Método de Registro
------------------------------------

* Creamos el controlador **src\AppBundle\Controller\UserController.php** con la estructura básica siguiente:
```php
<?php
namespace AppBundle\Controller;

/* Componentes necesarios iniciales *******************************************************/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/******************************************************************************************/
class UserController extends Controller
{
    public function indexAction(Request $request)
    {
        // indicamos la vista
        return $this->render('AppBundle:User:index.html.twig');
    }
}
```

* Añadimos los siguientes componentes, para **dar acceso** *a la entidad* **User** y al *formulario* **RegisterType**. :

```php
/* Añadimos los componentes que permitirán el uso de nuevas clases ************************/
use BackendBundle\Entity\User;                           // Da acceso a la Entidad Usuario
use AppBundle\Form\RegisterType;                         // Da acceso al Formulario RegisterType
/******************************************************************************************/
```
* Incluimos el método `public function registerAction(Request $request){}`.

```php
// src\AppBundle\Controller\UserController.php
/* EXTRACTO DE CÓDIGO EXISTENTE ***********************************************************/
class UserController extends Controller
{
/* FIN DE EXTRACTO ************************************************************************/
/* MÉTODO PARA EL REGISTRO DE USUARIO *****************************************************/
    public function registerAction(Request $request)
    {
        // Creamos un nuevo objeto User
        $user = new User();
        /*
         * Creamos el formulario a partir de la clase RegisterType,
         * le pasaremos la variable User
         * hay que declarar la clase RegisterType arriba
         */
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        // Si se envía y es válido el formulario
        if($form->isSubmitted()){
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                // $user_repo = $em->getTepository("BackendBundle:User");
                // hacemos la consulta
                $query = $em->createQuery('SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick')
                            ->setParameter('email', $form->get("email")->getData())
                            ->setParameter('nick', $form->get("nick")->getData());
                //extraemos el resultado de la $query
                $user_isset = $query->getResult();
                // Si no hay ningun usuario con ese email y nick
                if(count($user_isset==0)){
                    // si el usuario no existe
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword(
                        $form->get("password")->getData(),
                        $user->getSalt()
                      );
                    // subimos los datos usando los setters
                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImage(null);
                    // persistimos los datos dentro de Doctirne
                    $em->persist($user);
                    // guardamos los datos persistidos dentro de la BD
                    $flush = $em->flush();
                    // Si se guardan correctamente los datos en la BD
                    if($flush == null){
                        $status = "Te has registrado correctamente!";
                        // generamos los mensajes FLASH (necesario activar las sesiones)
                        $this->session->getFlashBag()->add("status", $status);
                        return $this->redirect("login");
                    }else{
                        $status = "No te has registrado correctamente";
                    }
                }else{
                  // si el usuario existe
                  $status = "El usuario ya existe!!";
                }
            }else{
                $status = "No te has registrado correctamente !!";
            }
             // generamos los mensajes FLASH (necesario activar las sesiones)
            $this->session->getFlashBag()->add("status", $status);
        }
        // enviamos la vista con el html del formulario ($form)
        return $this->render('AppBundle:User:register.html.twig', array(
            "form"=>$form->createView()
        ));
    }
```

4.3.Sistema de Validación de Registro
-------------------------------------

*Nota*: El formato **yml** suele ser problemático, para testear su escritura correcta usar "[yamllint.com](http://www.yamllint.com/)"

```yml
# src\AppBundle\Resources\config\validation.yml
BackendBundle\Entity\User:
    properties:
        name:
            - NotBlank: {message: "El nombre no puede estar vacío"}
            - Type:
                type: string
                message: "El nambre tiene que ser cadena"
            - Length:
                min: 3
                minMessage: "El titulo debe tener mas de dos caracteres"
        surname:
            - NotBlank: {message: "Los apellidos no pueden estar vacíos"}
        nick:
            - NotBlank: {message: "El nick no puede estar vacío"}
        email:
            - NotBlank: {message: "El email no puede estar vacío"}
            - Email:
                message: "El email {{ value }} no es válido"
                checkMX: true
```

4.4.Vista de Registro
---------------------

Para la Vista del sistema de Registro, usaremos la plantilla **src/AppBundle/Resources/views/User/register.html.twig**.

```twig
{# src/AppBundle/Resources/views/User/register.html.twig #}
{% extends "AppBundle:Layouts:layout.html.twig"%}
{% block content %}
  <div class="col-lg-8 box-form">
    <h2>Registrarse</h2>
    <hr/>
    {{form_start(form, {'action':'', 'method':'POST'})}}
    {{form_errors(form)}}
    {{form_end(form)}}
  </div>
{% endblock %}
```

4.5.Mensajes FLASH
------------------

* Crearemos un mensaje que mostrar según cada una de las distintas situaciones que podremos encontrar, para incluirlo dentro de la variable `$status`
* Incluiremos justo antes de cada `return` que nos redireccione o derive a una vista el siguiente código para generar el mensaje FLASH (notificación):
```php
// generamos los mensajes FLASH (necesario activar las sesiones)
$this->session->getFlashBag()->add("status", $status);
```

Quedaría nuestro código así:

```php
// src\AppBundle\Controller\UserController.php
/* EXTRACTO DE CÓDIGO EXISTENTE ***********************************************************/
                }else{
                  // si el usuario existe
                  $status = "El usuario ya existe!!";
                }
            }else{
                $status = "No te has registrado correctamente !!";
            }
/* FIN DE EXTRACTO ************************************************************************/
             // generamos los mensajes FLASH (necesario activar las sesiones)
            $this->session->getFlashBag()->add("status", $status);
/******************************************************************************************/
```
```php
// src\AppBundle\Controller\UserController.php
/* EXTRACTO DE CÓDIGO EXISTENTE ***********************************************************/
                    $flush = $em->flush();
                    // Si se guardan correctamente los datos en la BD
                    if($flush == null){
                        $status = "Te has registrado correctamente!";
/* FIN DE EXTRACTO ************************************************************************/
                        // generamos los mensajes FLASH (necesario activar las sesiones)
                        $this->session->getFlashBag()->add("status", $status);
/******************************************************************************************/
```

Para mostrarlo lo ubicaremos dentro de la plantilla base **src\AppBundle\Resources\views\Layouts\layout.html.twig**

```twig
{# src\AppBundle\Resources\views\Layouts\layout.html.twig #}
{# EXTRACTO DE CÓDIGO     #}
    <section id="content">
{# FIN DE EXTRACTO        #}
{# Mostramos los FLASHBAG #}
      <div class="container">
        <div class="col-lg-11">
          {% for message in app.session.flashbag().get('status') %}
            <div class="alert alert-success">{{message}}</div>
          {% endfor %}
        </div>
      </div>
      <div class="clearfix"></div>
{# Mostramos los FLASHBAG #}
{# EXTRACTO DE CÓDIGO     #}
      {% block content %}{% endblock%}
    </section>
{# FIN DE EXTRACTO        #}
```

4.6.Método AJAX
---------------

* Creamos el método `public function nickTestAction(Request $request){}`dentro de **src\AppBundle\Controller\UserController.php**.

```php
//  src\AppBundle\Controller\UserController.php
/* MÉTODO PARA LA CONSULTA AJAX (¿EXISTE EL NICK DE REGISTRO?) ****************************/
    public function nickTestAction(Request $request)
    {
        // Guardamos dentro de la variable $nick el dato que nos llega por POST
        $nick = $request->get("nick");
        // Busco dentro de la BD el dato
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:User");
        $user_isset = $user_repo->findOneBy(array("nick"=>$nick));
        $result = "used";
        if(count($user_isset) >= 1 && is_object($user_isset)){
            $result = "used";
        }else{
            $result = "unused";
        }
        // Para usar el método response es necesario cargar el componente
        return new Response ($result);
    }
```

* Añadimos dentro de **src\AppBundle\Controller\UserController.php** el componente `use Symfony\Component\HttpFoundation\Response;` para habilitar el uso del método **response**.

```php
/* Añadimos los componentes que permitirán el uso de nuevas clases ************************/
use Symfony\Component\HttpFoundation\Response; // Permite usar el método Response
/******************************************************************************************/
```

* El routing dentro de **src/AppBundle/Resources/config/routing/user.yml**:

```yml
# ROUTING PARA CONSULTA AJAX (¿EXISTE EL NICK DE REGISTRO?)
user_nick_test:
    path: /nick-test
    defaults: { _controller: AppBundle:User:nickTest }
    methods: [POST]
```

* Creamos el archivo JavaScript dentro de **web/assets/js/custom/nick-test.js** que lanzará la consulta a la BD.

```js
// Escuchamos el documento y activamos la función
$(document).ready(function(){
/* Identificamos el elemento con esa clase  ".nick-input"
y cuando salgamos de él (.blur) actuamos */
  $(".nick-input").blur(function(){
// Capturamos el valor
    var nick = this.value;
// Cargamos ajax
    $.ajax({
      url: URL+'/nick-test',
      data: {nick: nick},
      type: 'POST',
      success: function(response){
// Si 'response'=used
        if(response =="used"){
          $(".nick-input").css("border","1px solid red");
// Si 'response'!=used
        }else{
          $(".nick-input").css("border","1px solid green");
        }
      }
    });
  });
});
```

Para la Vista del sistema de Registro, usaremos la plantilla **src/AppBundle/Resources/views/User/register.html.twig**.

```twig
{# src/AppBundle/Resources/views/User/register.html.twig #}
{# EXTRACTO DE CÓDIGO     #}
{% extends "AppBundle:Layouts:layout.html.twig"%}
{# FIN DE EXTRACTO        #}
{# Ampliamos el bloque de JavaScript con el script 'nick-test.js'    #}
{% block javascripts %}
  {# la función parent() carga todo el contenido del bloque anterior #}
  {{ parent() }}
  <script src="{{ asset('assets/js/custom/nick-test.js') }}"></script>
{% endblock %}
```

5.Login
=======

Definiremos el routing dentro de **src/AppBundle/Resources/config/routing/user.yml**:
```yml
# ROUTING PARA EL LOGIN
login:
    path: /login
    defaults: { _controller: AppBundle:User:login }

# ROUTING PARA EL CHEQUEAR EL LOGIN
login_check:
    path: /login_check

# ROUTING PARA SALIR
logout:
    path: /logout
```

5.1.Controlador y Método de Login
---------------------------------

* Modificamos el controlador **src\AppBundle\Controller\UserController.php** para añadir el siguiente método `public function loginAction(Request $request){}`.

```php
// src\AppBundle\Controller\UserController.php
/* EXTRACTO DE CÓDIGO EXISTENTE ***********************************************************/
class UserController extends Controller
{
/* FIN DE EXTRACTO ************************************************************************/
/* MÉTODO PARA EL LOGIN DE USUARIO ********************************************************/
public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('AppBundle:User:login.html.twig', array(
            'lastUsername'=>$lastUsername,
            'error'=>$error ));
    }
```

5.2.Vista de Login
------------------

Para la Vista del **sistema de Login**, usaremos la plantilla **src/AppBundle/Resources/views/User/login.html.twig**.

```twig
{# src/AppBundle/Resources/views/User/login.html.twig #}
{% extends "AppBundle:Layouts:layout.html.twig"%}
{% block content %}
  <div class="col-lg-4 box-form">
    <h2>Identificarse</h2>
    <hr/>
    {% if is_granted ('ROLE_USER') %}
        {{ dump(app.user) }}
    {% endif %}
    {% if last_username is defined %}
        {{ last_username }}
    {% endif %}

    <form action="{{path('login_check')}}" method="POST">
        <label>Email</label>
        <input type="email" id="username" name="_username"
            value="
                {% if last_username is defined %}
                    {{ last_username }}
                {% endif %}
            " class="form-control" />
        <label>Contraseña</label>
        <input type="password" id="password" name="_password" class="form-control" />
        <input type="submit" value="Entrar" class="btn btn-success" />
        {# Mediante el input Type="hidden" definimos el nombre del "target_path" al que nos
        queremos dirigir después de enviar el formulario #}
        <input type="hidden" name="target_path" value="home" />
    </form>
  </div>
{% endblock %}
```

5.4.Redirección Logueo
----------------------

Para evitar el uso de la url **/register** y **/login**, colocaremos un redireccionamiento al principio de cada uno de estos métodos:

```php
// src\AppBundle\Controller\UserController.php
/* EXTRACTO DE CÓDIGO EXISTENTE ***********************************************************/
    public function loginAction(Request $request)
    {
/* FIN DE EXTRACTO ************************************************************************/    
        /* si existe el objeto User nos rediriges a home            */
        if( is_object($this->getUser()) ){
          return $this->redirect('home');
        }
        /************************************************************/
```

```php
// src\AppBundle\Controller\UserController.php
/* EXTRACTO DE CÓDIGO EXISTENTE ***********************************************************/
    public function registerAction(Request $request)
    {
/* FIN DE EXTRACTO ************************************************************************/    
        /* si existe el objeto User nos rediriges a home            */
        if( is_object($this->getUser()) ){
          return $this->redirect('home');
        }
        /************************************************************/
```

5.5.Ocultar Parte del Menú Según Logueo
---------------------------------------

Para evitar la visualización dentro de menú de la opción de **login** y **registro** cuando se está logueado, y **Mi Perfil**, **Mis Datos**, **Salir** y **Ayuda** cuando no se está logueado, incluiremos esa parte de plantilla dentro del condicional siguiente:
```twig
{# Cuando SI se está logueado #}
{% if app.user == null %}
{% endif %}
```

```twig
{# Cuando NO se está logueado #}
{% if app.user != null %}
{% endif %}
```

Quedando así:
```twig
{# src/AppBundle/Resources/views/User/login.html.twig #}
{# Evitamos que se vea esta parte del menú cuando NO estamos logueados #}
{% if app.user == null %}
   <li>
      <a href="{{path("login")}}"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>&nbsp;Entrar</a>
   </li>
   <li>
       <a href="{{path("register")}}"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Registro</a>
   </li>
{% endif %}
```
```twig
{# src/AppBundle/Resources/views/User/login.html.twig #}
{# Evitamos que se vea esta parte del menú cuando estamos logueados #}
{% if app.user != null %}
<ul class="nav navbar-nav navbar-right">
   <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      <div class="avatar">
         <img src="{{ asset ('assets/images/default.png') }}" />
      </div>
      {{app.user.name}} {{app.user.surname}}
         <span class="caret"></span>
         </a>
         <ul class="dropdown-menu">
            <li>
               <a href="{{path("user_edit")}}"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;Mi Perfil</a>
            </li>
            <li>
               <a href="{{path("user_edit")}}"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>&nbsp;Mis datos</a>
            </li>
            <li role="separator" class="divider"></li>
            <li>
               <a href="{{ path("logout") }}"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Salir</a>
            </li>
            <li>
               <a href="{{ path("logout") }}"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>&nbsp;Ayuda</a>
            </li>
        </ul>
     </li>
  </ul>
{% endif %}
```


6.Session
=========

Modifcamos **src/AppBundle/Controller/UserController.php** para añadir dentro el componente:
```php
// src\AppBundle\Controller\UserController.php
/* Añadimos los componentes que permitirán el uso de nuevas clases ************************/
use Symfony\Component\HttpFoundation\Session\Session; // Permite usar sesiones
/******************************************************************************************/
```

y la variable `private $session;` junto al método `public function __construct(){}`.

```php
// src\AppBundle\Controller\UserController.php
/* EXTRACTO DE CÓDIGO EXISTENTE ***********************************************************/
class UserController extends Controller
{
/* FIN DE EXTRACTO ************************************************************************/
/* MÉTODO SESSION *************************************************************************/
/*
 * OBJETO SESSIÓN
 * Para activar las sesiones inicializamos la variable e incluimos
 * en ella el objeto Session()
 * No olvidar dar acceso al componenete de Symfony
 * Session() permitirá usar los mensajes FLASHBAG
 */
    private $session;
    public function __construct(){
      $this->session = new Session();
    }
/*******************************************************************/
```

Sustituimos la imagen predefinida `<img src="{{ asset ('assets/images/default.png') }}" />`por el siguiente código:
```twig
      {# Imagen de usuario si existe un registro o no de la imagen #}
      {% if app.user.image == null %}
         <img src="{{ asset ('assets/images/default.png') }}" />
      {% else %}
         <img src="{{ asset ('uploads/users/'~app.user.image) }}" />
      {% endif %}
```

7.Editar Usuario
================

