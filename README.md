# Laravel API

## Prueba de conocimiento

Mas información en [Prueba-de-conocimiento.pdf](/docs/Prueba-de-conocimiento.pdf)

## Documentación de los endpoints

En la carpeta `docs` se encuentra toda la documentación de los endpoints.

La documentación puede visualizarse en vivo levantando
un servidor local al archivo [Documentación en vivo](/docs/InsomniaDocumenter/index.html)

Se puede visualizar abriendo el archivo pdf [Insomnia-Documenter-Laravel-API.pdf](/docs/Insomnia-Documenter-Laravel-API.pdf)

Para trabajar directamente con los endpoints puede ser importado directamente a la herramienta [Insomnia](https://insomnia.rest/download), la cual es un cliente para el consumo de APIs, usando el archivo exportado de JSON [Insomnia_2021-06-17.json](/docs/Insomnia_2021-06-17.json).

Dentro de insomnia setear las variables de entorno (ENV) TOKEN y HOME, donde HOME es el dominio del servidor (Ejemplo: `http://127.0.0.1:8000`) y TOKEN es el Bearer Token generado despues de logearse correctamente en la plataforma.

## Instalación y levantamiento del servidor

Para prueba local, asegurate de tener instalado [Composer](https://getcomposer.org/download/).

Correr los siguientes comandos en una terminal,
situandose en la raiz de este proyecto
```
// Para instalar las dependencias
composer install

// Establecer y configurar el archivo .env
cp .env.example .env

// Para correr las migraciones seteando las tablas
php artisan migrate

// Para correr los Seeders, cargando la data inicial
php artisan db:seed

// Para levantar el servidor
php artisan serve
```

## Sistema de Roles y Permisos (RBAC)

El sistema de roles se maneja usando 2 entidades

* Roles (Tabla `roles`)
* Permisos (tabla `permissions`)

En una relación de muchos-a-muchos, es decir, muchos
roles pueden tener muchos permisos.

Para hacer esto se hace uso de 1 tabla puente o pivot (permission_role).

A su vez se relacionan los roles con el `Usuario` a través
de 1 tabla puente (role_user).

### Permisos

Despues de haber corrido los seeders `php artisan db:seed`,
en la plataforma se cargan automaticamente 7 permisios iniciales

* **create_user** - Crear usuario
* **list_users** - Listar usuarios
* **edit_user** - Editar usuario
* **delete_user** - Eliminar usuario
* **show_user** - Ver detalle de usuario
* **manage_roles** - Manejar roles
* **manage_permissions** - Manejar permisos

Estos permisos son usados en la plataforma para autorizar,
el uso de los endpoints para cada usuario.

### Super Admin

Por defecto la plataforma posee un usuario con el rol
de `superadmin`, este rol y usuario son unicos y poseen todos los permisos en la plataforma excepto por la 
capacidad de eliminarse a si mismo y a su rol.

El uso de este usuario permite un mayor control en la
plataforma y evita que la plataforma pueda quedarse
sin la capacidad de administrarse a si misma debido
a la naturaleza flexible de los roles de usuario.

El usuario por defecto `superadmin` tiene las credenciales

```
Email: admin@mail.com
Contraseña: 12345678
```

Para configurar el usuario superadmin inicial solo debe setearse
los valores de entorno `SUPERADMIN_EMAIL` y `SUPERADMIN_PASSWORD`
en el archivo `.env` de la plataforma con los valores correspondientes

### Autorización y validación

La autorización se lleva a cabo usando una combinación de
`Policies` y `Requests`.

Existen 2 Policies

* `App\Policies\UserPolicy`
* `App\Policies\RolePolicy`

Existen 10 Request

* `App\Http\Requests\StoreUserRequest`
* `App\Http\Requests\UpdateUserRequest`
* `App\Http\Requests\DeleteUserRequest`
* `App\Http\Requests\GrantRoleToUserRequest`
* `App\Http\Requests\RevokeRoleToUserRequest`
* `App\Http\Requests\StoreRoleRequest`
* `App\Http\Requests\UpdateRoleRequest`
* `App\Http\Requests\DeleteRoleRequest`
* `App\Http\Requests\GrantPermissionToRoleRequest`
* `App\Http\Requests\RevokePermissionToRoleRequest`

Internamente se determina si se esta autorizado utilizando
el sistema de roles y permisos anteriormente definido.
