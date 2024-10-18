# Ampliación del Límite de Caracteres en PrestaShop 1.7 con Docker

Este proyecto tiene como objetivo ampliar el límite de caracteres para los campos de nombre (`firstname`) y apellidos (`lastname`) en PrestaShop 1.7 utilizando Docker. Los nombres y apellidos se ampliarán para aceptar hasta 512 caracteres.

## Requisitos Previos

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Pasos para la Instalación

### 1. Crear el archivo `docker-compose.yml`

Crea una carpeta para el proyecto, por ejemplo `prestashop-docker`.

Dentro de esa carpeta, crea un archivo llamado `docker-compose.yml` con el siguiente contenido:

```yaml
version: '3.8'

services:
  mysql:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: prestashop
      MYSQL_USER: prestashop
      MYSQL_PASSWORD: prestashop
    volumes:
      - db_data:/var/lib/mysql
    networks:
      - prestashop-net

  prestashop:
    image: prestashop/prestashop:1.7
    environment:
      DB_SERVER: mysql
      DB_NAME: prestashop
      DB_USER: prestashop
      DB_PASSWORD: prestashop
    ports:
      - "8080:80"
    volumes:
      - ./modules:/var/www/html/modules
      - ./themes:/var/www/html/themes
      - ./override:/var/www/html/override
    depends_on:
      - mysql
    networks:
      - prestashop-net

volumes:
  db_data:

networks:
  prestashop-net:
```
## 2. Lanzar los Contenedores

En la misma carpeta donde está el archivo `docker-compose.yml`, abre una terminal y ejecuta el siguiente comando:

```bash
docker-compose up -d
```
Esto descargará las imágenes de Docker necesarias y lanzará los contenedores para PrestaShop y MySQL.

Verifica que los contenedores están corriendo con:

```bash
docker ps
```
## 3. Instalar PrestaShop

1. Abre tu navegador e ingresa a `http://localhost:8080`. Verás el instalador de PrestaShop.
2. Selecciona el idioma.
3. Acepta los términos y condiciones.
4. Introduce la información de tu tienda.
5. En la configuración de la base de datos, utiliza los siguientes valores:
   - **Servidor de base de datos**: `mysql`
   - **Usuario**: `prestashop`
   - **Contraseña**: `prestashop`
   - **Nombre de la base de datos**: `prestashop`
6. Completa la instalación.

## 4. Modificar el Límite de Caracteres en la Base de Datos

1. Abre una terminal y accede al contenedor de MySQL:

    ```bash
    docker exec -it prestashop-docker_mysql_1 bash
    ```

2. Inicia sesión en MySQL con:

    ```bash
    mysql -u prestashop -p
    ```

3. Introduce la contraseña `prestashop`. Luego, modifica los campos `firstname` y `lastname` para que acepten hasta 512 caracteres:

    ```sql
    USE prestashop;

    ALTER TABLE ps_customer MODIFY firstname VARCHAR(512);
    ALTER TABLE ps_customer MODIFY lastname VARCHAR(512);
    ```

## 5. Modificar el Código de PrestaShop

1. Ahora necesitamos modificar el código de PrestaShop para que el frontend también permita el registro de nombres y apellidos largos.
   
2. Crea el archivo `Customer.php` en la carpeta `override/classes/` de tu proyecto con el siguiente contenido:

    ```php
    <?php

    class Customer extends CustomerCore
    {
        public $firstname;
        public $lastname;
        public $email;
        public $passwd;
        public $active = 1; 

        public static $definition = array(
            'table' => 'customer',
            'primary' => 'id_customer',
            'fields' => array(
                'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 512),
                'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 512),
                'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255),
                'passwd' => array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 255),
                'active' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => 1),
            ),
        );        
    }

    ```

Este archivo sobrescribe los campos `firstname` y `lastname` para que ahora puedan aceptar hasta 512 caracteres.

## 6. Limpiar la Caché de PrestaShop

1. Accede al back office de PrestaShop en `http://localhost:8080/admin`.
2. Ve a `Parámetros avanzados` > `Rendimiento`.
3. Selecciona la opción `Borrar caché`.

## 7. Realizar la Prueba

Finalmente, verifica que los cambios han surtido efecto:

1. Ve al formulario de registro de clientes en la tienda de PrestaShop (frontend).
2. Intenta registrar un cliente con un nombre y apellidos de más de 255 caracteres.
3. Verifica que el formulario se envía correctamente y que los datos se guardan en la base de datos.