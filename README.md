# top-adjudicaciones
top adjudicacones del DNCP

Para esta guía de instalación se utilizó servidor web apache2 + PHP + MySQL.

-Realizar el clon del proyecto en el directorio web /var/www/html

```
  git clone https://github.com/datospy/top-adjudicaciones.git
  
```

-Crear una base de datos en mysql

```
  CREATE DATABASE my_bd;
  
```

-Importar la base de datos desde el archivo clonado db.sql

```
  mysql -u usuario -p my_bd < db.sql
  
```

-Configurar el archivo constantes.php para la conexión a base de datos

```
  define("BD", "my_db");
  define("HOST", "localhost");
  define("USUARIO", "usuario");
  define("PASS", "********");
  
```

