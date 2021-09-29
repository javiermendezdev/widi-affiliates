# Prueba técnica

Es necesario crear una API para dar un servicio apropiado a nuestros afiliados, para ello la aplicación dispone de una conexión a MariaDB donde se almacenan los datos de usuario, previamente almacenados.

Es necesario crear los siguientes endpoints:

## Requisitos

### Login

<table>
  <tr>
    <td><b>URL<b/></td>
    <td><b>TIPO<b/></td>
    <td><b>BODY<b/></td>
  </tr>
  <tr>
    <td>/login</td>
    <td>POST</td>
    <td>user/password</td>
  </tr>
  <tr>
    <td colspan="3" align="center"><b>RESPUESTA</b></td>
  </tr>
  <tr>
    <td><b>TIPO<b/></td>
    <td><b>CÓDIGO<b/></td>
    <td><b>RESULTADO<b/></td>
  </tr>
  <tr>
    <td>OK</td>
    <td>200</td>
    <td>JWT para la autenticación.</td>
  </tr>
  <tr>
    <td>Usuario o password incorrecto</td>
    <td>204</td>
    <td>Información de que el usuario o password es incorrecto.</td>
  </tr>
</table>

### Listar afiliados

<table>
  <tr>
    <td><b>URL<b/></td>
    <td><b>TIPO<b/></td>
    <td><b>PARAMS<b/></td>
  </tr>
  <tr>
    <td>/affiliates</td>
    <td>GET</td>
    <td></td>
  </tr>
  <tr>
    <td colspan="3" align="center"><b>RESPUESTA</b></td>
  </tr>
  <tr>
    <td><b>TIPO<b/></td>
    <td><b>CÓDIGO<b/></td>
    <td><b>RESULTADO<b/></td>
  </tr>
  <tr>
    <td>OK</td>
    <td>200</td>
    <td>Listado de afiliados, uuid, nombre, apellidos y email.</td>
  </tr>
  <tr>
    <td>No existen afiliados.</td>
    <td>204</td>
    <td>Información de que no hay afiliados.</td>
  </tr>
  <tr>
    <td>Error.</td>
    <td>500</td>
    <td>Error genérico.</td>
  </tr>
</table>

### Obtener afiliado

<table>
  <tr>
    <td><b>URL<b/></td>
    <td><b>TIPO<b/></td>
    <td><b>PARAMS<b/></td>
  </tr>
  <tr>
    <td>/affiliates/{uuid}</td>
    <td>GET</td>
    <td>uuid del afiliado</td>
  </tr>
  <tr>
    <td colspan="3" align="center"><b>RESPUESTA</b></td>
  </tr>
  <tr>
    <td><b>TIPO<b/></td>
    <td><b>CÓDIGO<b/></td>
    <td><b>RESULTADO<b/></td>
  </tr>
  <tr>
    <td>OK</td>
    <td>200</td>
    <td>Datos del afiliado, uuid, nombre, apellidos y email.</td>
  </tr>
  <tr>
    <td>No existe el afiliado.</td>
    <td>204</td>
    <td>Información de que no existe el afiliado.</td>
  </tr>
  <tr>
    <td>Error.</td>
    <td>500</td>
    <td>Error genérico.</td>
  </tr>
</table>

### Crear afiliado

<table>
  <tr>
    <td><b>URL<b/></td>
    <td><b>TIPO<b/></td>
    <td><b>BODY<b/></td>
  </tr>
  <tr>
    <td>/affiliates</td>
    <td>POST</td>
    <td>Nombre, apellidos y email.</td>
  </tr>
  <tr>
    <td colspan="3" align="center"><b>RESPUESTA</b></td>
  </tr>
  <tr>
    <td><b>TIPO<b/></td>
    <td><b>CÓDIGO<b/></td>
    <td><b>RESULTADO<b/></td>
  </tr>
  <tr>
    <td>OK</td>
    <td>200</td>
    <td>Uuid del usuario creado.</td>
  </tr>
  <tr>
    <td>El afiliado ya existe.</td>
    <td>409</td>
    <td>Email ya existe.</td>
  </tr>
  <tr>
    <td>Error.</td>
    <td>500</td>
    <td>Error genérico.</td>
  </tr>
</table>

## Análisis

Se extrae la necesidad de:

* Es necesario conectar a una base de datos Mariadb (symfony/orm-pack).

* Es necesario exponer una api para realizar login y gestionar afiliados.

* Necesitamos identificadores con uuid (ramsey/uuid).

* Añadir registros de usuarios prestablecidos para poder realizar login y poder obtener un token jwt (lexik/jwt-authentication-bundle). Se podría realizar la ingesta de datos mediante: hautelook:fixtures:load (librería: hautelook/alice-bundle). Estos usuarios pueden tener uuid, username, password, role=admin, etc

* Los afiliados tendrán los siguientes campos: uuid, firstname, lastname y email.

### Creación usuarios

Se ha ejecutado la funcionalidad por defecto:

```bash
php bin/console make:user
```

Indicando que se usará como campo principal del usuario el "username", también se ha indicado que los passwords se encripten.

Para generar un password encryptado:

```bash
# Deprecated 5.3 => php bin/console security:encode-password
php bin/console security:hash-password
```

```bash
Type in your password to be encoded:
 > changeme
 ------------------ ---------------------------------------------------------------------------------------------------
  Key                Value
 ------------------ ---------------------------------------------------------------------------------------------------
  Encoder used       Symfony\Component\Security\Core\Encoder\MigratingPasswordEncoder
  Encoded password   $argon2id$v=19$m=65536,t=4,p=1$3+SFI3tfF1n1aCk6qA9dzg$8ru5Bnm4jOBo2HSK8qzFN77Cplxd/ObFp+d00N+w+gw
 ------------------ ---------------------------------------------------------------------------------------------------
```

## TODO

* fix warning: Cannot create cache directory /.composer/cache/repo/https---repo.packagist.org/, or directory is not writable. Proceeding without cache

* 000-default.conf se podría usar un template y hacer dinámico el APP_URL, en vez de meter localhost
