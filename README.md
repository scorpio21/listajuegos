# ListaJuegos

<p align="center">
  <img src="caratulas/captura.png" alt="Vista del buscador" width="500"/>
</p>

> <strong>Gestor avanzado de base de datos y caratulas para juegos de PlayStation (PSX y PS2)</strong>

<p align="center">
  <a href="#caracteristicas-principales">✨ Caracteristicas</a> •
  <a href="#instalacion-y-primeros-pasos">🚀 Instalacion</a> •
  <a href="#seguridad-y-buenas-practicas">🛡️ Seguridad</a> •
  <a href="#creditos-y-agradecimientos">🤝 Creditos</a>
</p>

---

## Tabla de Contenidos

- [📝 Descripcion general](#descripcion-general)
- [✨ Caracteristicas principales](#caracteristicas-principales)
- [🔎 Vista principal del buscador](#vista-principal-del-buscador)
- [📁 Estructura del proyecto](#estructura-del-proyecto)
- [⚙️ Proceso de funcionamiento detallado](#proceso-de-funcionamiento-detallado)
- [🛠️ Utilidades y scripts incluidos](#utilidades-y-scripts-incluidos)
- [💻 Requisitos del sistema](#requisitos-del-sistema)
- [🚀 Instalacion y primeros pasos](#instalacion-y-primeros-pasos)
- [🛡️ Seguridad y buenas practicas](#seguridad-y-buenas-practicas)
- [📄 Licencia](#licencia)
- [🤝 Creditos y agradecimientos](#creditos-y-agradecimientos)
- [🗄️ Importar la base de datos](#importar-la-base-de-datos)

---

## Descripcion general

ListaJuegos es una aplicacion web desarrollada en <img src="https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white" height="20"/> y <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black" height="20"/> que permite gestionar, consultar y enriquecer una base de datos de juegos de PlayStation (PSX y PS2).

El sistema esta disenado para ser modular, eficiente y facil de mantener, integrando importacion masiva, busqueda avanzada y gestion automatizada de caratulas.

---

## Caracteristicas principales

- 🔍 **Buscador inteligente y unificado**: busqueda por titulo o serial, con coincidencias exactas o sugerencias flexibles.
- 🖼️ **Gestion automatica de caratulas**: descarga y almacenamiento desde la web si no existen en la base de datos.
- 📦 **Importacion masiva y utilidades**: herramientas para importar listados de juegos y caratulas desde CSV, Excel o scraping.
- 📝 **Gestion y edicion de datos**: edicion rapida de campos incompletos y visualizacion clara de la informacion.
- 🛡️ **Seguridad reforzada**: proteccion de carpetas sensibles y buenas practicas mediante `.htaccess` y `.gitignore`.
- 📊 **Estadisticas y control de calidad**: identifica juegos con datos incompletos y facilita su correccion.
- 🧩 **Codigo modular y limpio**: utilidades agrupadas en la carpeta `utilidad/`.
- 🔗 **API RESTful**: endpoint para integraciones externas y automatizacion.
- 📚 **Documentacion clara** y estructura profesional.

---

## Vista principal del buscador

A continuacion se muestra la interfaz principal del sistema.

---

## Estructura del proyecto

```text
listajuegos/
├── banderas/                  # Imagenes de banderas de regiones
│   ├── europa.png
│   ├── ntsc.png
│   ├── ntsc_c.png
│   ├── ntsc_j.png
│   ├── ntsc_u.png
│   └── pal.png
├── buscar_api.php             # API REST para busqueda (GET)
├── buscar_datos.php           # Backend de busqueda (GET/JSON)
├── caratulas/                 # Imagenes de caratulas y capturas
│   ├── default.jpg
│   ├── psx.png
│   └── captura.png
├── composer.json              # Dependencias de Composer
├── .env.example               # Variables de entorno (ejemplo)
├── .gitignore                 # Exclusiones para Git
├── .htaccess                  # Reglas de seguridad para Apache
├── index.php                  # Interfaz principal (frontend)
├── juegos_incompletos.php     # Listado de juegos con datos faltantes
├── ver_imagen.php             # Sirve carátulas desde la BD (BLOB)
├── Log/                       # Carpeta de logs
├── README.md                  # Documentacion principal
├── sql/                      # Copia de la base de datos (exportacion)
│   └── listajuegos.sql       # Archivo SQL para importar
├── utilidad/                  # Funciones auxiliares y utilidades
│   ├── guardar_imagen.php     # Descarga y guarda caratulas
│   └── utilidades.php         # Funciones comunes (conexion BD, logs...)
└── vendor/                    # Dependencias externas (Composer)
```

---

## Proceso de funcionamiento detallado

1. **Búsqueda de juegos:** El usuario introduce un título o serial y selecciona la plataforma. El frontend envía la petición a `buscar_datos.php` (GET), que consulta la base de datos y devuelve los datos en JSON (coincidencia exacta o lista de sugerencias).
2. **Carga y gestión de carátulas:** Si el juego tiene carátula en la base de datos, se sirve desde `ver_imagen.php?serial=...`. Si no, el frontend muestra una imagen por defecto según plataforma. Se puede guardar una carátula haciendo clic sobre la imagen.
3. **Edicion y actualizacion:** El usuario puede editar campos incompletos y guardar los cambios, que se actualizan en la base de datos.
4. **Importacion masiva:** Utilidades para importar listados de juegos y caratulas desde archivos externos.
5. **Control de calidad:** El sistema identifica juegos con datos incompletos y facilita su correccion.

---

## Utilidades y scripts incluidos

- **utilidad/utilidades.php**: Funciones comunes para limpieza, conexion a la base de datos, logs y utilidades generales.
- **utilidad/guardar_imagen.php**: Descarga y guarda caratulas en la base de datos de forma automatica.
- **juegos_incompletos.php**: Listado y control de juegos con informacion faltante.
- **buscar_api.php**: API REST para busquedas rapidas (GET).
- **buscar_datos.php**: Backend de busqueda y gestion de datos (POST).

---

## Requisitos del sistema

- <img src="https://img.shields.io/badge/PHP-7.4%2B-blue?logo=php&logoColor=white" height="20"/> PHP 7.4 o superior
- <img src="https://img.shields.io/badge/MySQL%2FMariaDB-5.7%2B-blue?logo=mysql&logoColor=white" height="20"/> MySQL/MariaDB
- <img src="https://img.shields.io/badge/Apache-2.4%2B-orange?logo=apache&logoColor=white" height="20"/> Servidor Apache recomendado
- <img src="https://img.shields.io/badge/Composer-2.x-blueviolet?logo=composer&logoColor=white" height="20"/> Composer para dependencias

---

## Instalacion y primeros pasos

1. Clona el repositorio en tu servidor local (por ejemplo, XAMPP `htdocs`).
2. Configura la base de datos MySQL y ajusta los datos de conexion en los scripts si es necesario.
3. Ejecuta `composer install` para instalar dependencias.
4. Accede a `index.php` desde tu navegador para comenzar a utilizar el sistema.

---

## Seguridad y buenas practicas

- Validacion estricta de entradas y manejo de errores para evitar inyecciones SQL y vulnerabilidades comunes.
- El acceso a scripts de importacion y utilidades debe restringirse a usuarios autorizados.
- `.htaccess` y `.gitignore` protegen carpetas internas y archivos sensibles.
- No se almacena ni expone informacion privada en el repositorio.

---

## Importar la base de datos

> Por motivos de tamaño y privacidad, **el archivo completo de base de datos (`sql/listajuegos.sql`) no se incluye en el repositorio**. Puedes solicitarlo o descargarlo desde un enlace externo si esta disponible.

Para que puedas utilizar la base de datos del proyecto, sigue estos pasos:

### Usando phpMyAdmin

1. Abre **phpMyAdmin** en tu navegador (por ejemplo, http://localhost/phpmyadmin).
2. Crea una nueva base de datos llamada, por ejemplo, `listajuegos`.
3. Selecciona la base de datos recien creada en el panel izquierdo.
4. Haz clic en la pestaña **Importar**.
5. Pulsa en **Seleccionar archivo** y elige el archivo `sql/listajuegos.sql` incluido en el repositorio.
6. Haz clic en **Continuar** para importar la estructura y los datos.

### Usando la terminal (PowerShell/CMD en XAMPP, recomendado para archivos grandes)

Para evitar límites de tamaño de phpMyAdmin, usa `mysql.exe` de XAMPP en Windows.

1. Asegúrate de que MySQL esté "Running" en XAMPP.
2. Abre PowerShell y ejecuta (root sin contraseña):

```powershell
& "d:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS listajuegos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
cmd /c ""d:\xampp\mysql\bin\mysql.exe" -u root listajuegos < "d:\xampp\htdocs\listajuegos\sql\listajuegos.sql""
```

Si root tiene contraseña, añade `-p` y escribe la clave cuando se solicite.

Notas:
- Si PowerShell muestra error con `<`, usa `cmd /c` como en el ejemplo.
- Si aparece "packet too large", edita `d:\xampp\mysql\bin\my.ini` y en `[mysqld]` añade `max_allowed_packet=256M`, reinicia MySQL.

### Endpoints disponibles

- `buscar_datos.php?titulo=...&plataforma=psx|ps2` → JSON de búsqueda (exacta/parcial/flexible).
- `obtener_juego.php?serial=SERIAL` → JSON con un juego.
- `juegos_incompletos.php?plataforma=psx|ps2` → JSON con seriales incompletos.
- `juegos_incompletos.php?estadisticas_vacios=1` → JSON con conteos por campo/plataforma.
- `utilidad/guardar_imagen.php` (POST JSON `{serial, url}`) → Guarda carátula (BLOB) en BD.
- `ver_imagen.php?serial=SERIAL[&tipo=caratula]` → Sirve la carátula desde la BD.

### Carátulas: guardar y visualizar

- La interfaz muestra una imagen por defecto si no existe carátula en BD.
- Para guardar una carátula del juego actual, haz clic sobre la imagen. El sistema guardará el binario de la URL actual (admite URL http/https o ruta local como `caratulas/psx.png`).
- Después de guardar, `ver_imagen.php?serial=SERIAL` servirá la imagen desde la tabla `imagenes`.

### .htaccess en Apache 2.4

Se incluyen reglas compatibles con Apache 2.4:

- `RewriteRule` para bloquear acceso web a carpetas sensibles (`vendor/`, `Log/`).
- `Options -Indexes` para evitar listados de directorios.
- Bloqueo de ejecución de scripts dentro de `caratulas/` y `banderas/` (se sirven solo imágenes).
- `DirectoryIndex index.php` como documento principal.

Si tu entorno cambia, revisa `/.htaccess` y ajusta las reglas según sea necesario.

---

## Licencia

Este proyecto se distribuye bajo la licencia MIT. Consulta el archivo [LICENSE](LICENSE) para mas detalles.

---

## Creditos y agradecimientos

- Inspirado en la comunidad de preservacion de videojuegos y coleccionismo.
- Fuentes externas para caratulas: [psxdatacenter.com](https://psxdatacenter.com), entre otras.
- Agradecimientos a colaboradores y usuarios que han aportado ideas y mejoras.

---

<p align="center">
  <b>¿Tienes dudas, sugerencias o quieres contribuir?</b> <br>
  ¡Abre un <a href="https://github.com/">issue</a> o <a href="https://github.com/">pull request</a> en GitHub!
</p>
