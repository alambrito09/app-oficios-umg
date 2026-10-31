# App Oficios — Contratación de Oficios Bajo Demanda

Proyecto Final del curso Análisis de Sistemas — Universidad Mariano Gálvez de Guatemala.
Entrega: 30 de octubre de 2026.

## Arquitectura

**Monolito** desplegado en **Apache + PHP + MySQL + CodeIgniter 3**.

- Patrón MVC (Modelo-Vista-Controlador).
- Base de datos relacional MySQL con integridad referencial.
- Autenticación por rol (cliente, prestador, administrador, soporte).
- Simulaciones claramente identificadas: pagos, notificaciones, mapas, verificación externa.

## Estructura del proyecto

```
proyecto-oficios/
├── public/
│   ├── index.php          # Front controller (CodeIgniter 3)
│   ├── .htaccess          # Reescritura de URLs
│   └── assets/
│       └── css/app.css     # Estilos compartidos y responsive
├── application/
│   ├── core/              # MY_Controller: valida cuenta activa en rutas protegidas
│   ├── config/            # Configuración (base de datos, rutas, autoload)
│   ├── controllers/       # Controladores MVC, incluido Payment para checkout simulado
│   ├── models/            # Modelos MVC, incluido Payment_model
│   └── views/             # Vistas (layouts, auth, client, provider, admin)
├── database/
│   └── schema.sql         # Esquema y datos iniciales (seed)
└── README.md
```

## Requisitos previos

### Opción A: Docker (recomendado)
1. **Docker** y **Docker Compose** instalados: https://www.docker.com/

### Opción B: XAMPP/WAMP local
1. **XAMPP** (Apache + PHP + MySQL) o similar: https://www.apachefriends.org/
2. PHP 8.2+.
3. MySQL/MariaDB.

## Instalación con Docker (recomendado)

1. Clonar o copiar este proyecto en una carpeta.
2. Copiar el archivo de variables de entorno:
   ```bash
   copy .env.example .env
   ```
   (en PowerShell: `Copy-Item .env.example .env`)
3. Ajustar los valores en `.env` si es necesario (puertos, contraseñas).
4. Levantar los contenedores:
   ```bash
   docker-compose up -d
   ```
5. Esperar a que MySQL esté listo (el esquema `database/schema.sql` se importa automáticamente al crear el volumen por primera vez).
6. Acceder en el navegador: `http://localhost:8080/`
7. phpMyAdmin (opcional): `http://localhost:8081/`

### Actualizar una instalación con volumen existente

MySQL no vuelve a importar automáticamente `database/schema.sql` cuando el volumen ya existe. Aplica la migración de seguridad una vez desde PowerShell:
```powershell
Get-Content -Raw database/migrations/20261029_security_baseline.sql |
  docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" app_oficios'
```
La migración crea el control de intentos fallidos y deshabilita las cuentas ficticias antiguas sin borrar sus historiales.

### Comandos útiles de Docker
```bash
docker-compose up -d          # Levantar todos los servicios
docker-compose down           # Detener y eliminar contenedores
docker-compose logs -f web    # Ver logs de la aplicación
docker-compose logs -f db     # Ver logs de la base de datos
docker-compose restart        # Reiniciar todos los servicios
```

## Instalación con XAMPP/WAMP (alternativa)

1. Descargar e instalar XAMPP: https://www.apachefriends.org/
2. Iniciar Apache y MySQL desde el panel de XAMPP.
3. Crear la base de datos:
   ```sql
   CREATE DATABASE app_oficios CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Importar el esquema:
   ```bash
   mysql -u root -p app_oficios < database/schema.sql
   ```
5. Colocar el proyecto en la carpeta `htdocs` de XAMPP, por ejemplo:
   ```
   C:\xampp\htdocs\oficios\
   ```
6. Acceder en el navegador: `http://localhost/oficios/`

## Preparar cuentas para la demostración

No se incluyen cuentas con contraseñas conocidas. Registra desde la aplicación un usuario cliente y otro prestador; luego configura su perfil desde el flujo de proveedor.

Para habilitar un administrador local:
1. Registra desde la aplicación la cuenta que administrarás.
2. En phpMyAdmin (`http://localhost:8081/`), selecciona la base `app_oficios` y ejecuta la siguiente consulta sustituyendo `USUARIO_ELEGIDO`:
   ```sql
   UPDATE users
   SET role_id = (SELECT id FROM roles WHERE name = 'administrador'),
       status = 'activo'
   WHERE username = 'USUARIO_ELEGIDO';
   ```
3. Inicia sesión con la contraseña que elegiste al registrarte. No reutilices esa contraseña fuera del entorno local de demostración.

La base de datos existente de este entorno puede conservar cuentas antiguas. Para deshabilitar únicamente las cuentas ficticias preexistentes:
```sql
UPDATE users
SET status = 'inactivo', password_hash = '!disabled!'
WHERE username IN ('admin', 'cliente1', 'proveedor1');
```

La consulta anterior no elimina solicitudes ni historiales relacionados.

### Recuperación de cuenta

La recuperación envía un enlace de un solo uso y vigencia de una hora. Configura `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASSWORD`, `SMTP_CRYPTO` y `SMTP_FROM_EMAIL` en `.env`; sin SMTP no se crea ni se revela ningún token. La respuesta no confirma si un correo está registrado. El flujo se verificó con Mailpit local; esa prueba no configura ni valida un proveedor SMTP externo o de producción.

El inicio de sesión bloquea temporalmente la IP durante 15 minutos después de cinco contraseñas incorrectas dentro de una ventana de 15 minutos.

## Simulaciones declaradas

Según el documento académico, las siguientes funciones están **simuladas** (no son integraciones reales):

- **Pagos**: No hay pasarela de pago real. Se registra en tabla `payments` con estado simulado.
- **Notificaciones**: Se registran en tabla `notifications` pero no se envían correos/SMS reales.
- **Mapas / ubicación en tiempo real**: No se usa API de mapas real. Las coordenadas son campos nulos en BD.
- **Verificación externa de antecedentes**: La verificación de proveedores se registra manualmente por el administrador.

## Funcionalidades implementadas (RF01–RF14)

- **RF01**: Registro, inicio de sesión, recuperación por enlace temporal enviado por SMTP cuando está configurado.
- **RF02**: Perfiles de proveedor con experiencia, oficios, cobertura, disponibilidad.
- **RF03**: Gestión de categorías y especialidades (admin).
- **RF04**: Búsqueda de prestadores por oficio, zona y disponibilidad.
- **RF05**: Consulta de perfiles, condiciones y calificaciones.
- **RF06**: Creación de solicitudes con descripción, ubicación, horario, fotos opcionales.
- **RF07**: Aceptar, rechazar o dejar vencer solicitudes (proveedor).
- **RF08**: Cotizaciones con mano de obra, materiales y cargos separados; aprobación por cliente.
- **RF09**: Registro de avance, cambios de estado y cierre del servicio.
- **RF10**: Comunicación vinculada a la solicitud (notas de avance, comentarios).
- **RF11**: Gestión de cancelaciones y reclamos.
- **RF12**: Calificación solo de servicios realizados (evita calificaciones falsas).
- **RF13**: Historial de solicitudes y servicios.
- **RF14**: Administración de cuentas, categorías y reportes.

## Pago del cliente (demostración)

Después de aprobar una cotización, el cliente puede abrir **Historial → Servicios contratados → Pagar**, o entrar al detalle del servicio y elegir **Ir a pagar**. El flujo permite seleccionar efectivo, transferencia o tarjeta de demostración y registra una referencia ficticia en `payments`. No se solicitan datos financieros ni se realiza un cobro real. Un servicio con un pago simulado completado no permite registrar un segundo pago.

## Administración de usuarios y roles

El administrador puede crear cuentas de cliente/prestador y cambiar su estado entre activo, inactivo o suspendido. Inactivar retira el acceso y conserva solicitudes, servicios, pagos y demás historial; el acceso puede reactivarse desde el mismo panel. No se eliminan físicamente usuarios relacionados con registros del sistema. La cuenta administradora actual no puede desactivarse desde su propia sesión y debe permanecer al menos una cuenta administradora activa.

Cada rol ve su propia navegación. Clientes acceden a búsqueda, historial, reclamos y pagos simulados; prestadores a solicitudes, servicios, perfil y reclamos; administración a usuarios, oficios, reclamos y reportes; soporte solo a reclamos. El servidor verifica también el rol y que la cuenta siga activa, independientemente de los enlaces visibles.

## Estados de solicitud

`solicitado → en_evaluacion → pendiente_aprobacion → confirmado → en_ejecucion → finalizado`
Alternativas: `rechazado`, `vencido`, `cancelado`.

## Regla de doble aceptación

Una solicitud solo puede ser aceptada por un proveedor. Una vez asignada, no puede ser tomada por otro.

## Notas académicas

- Datos ficticios claramente identificados.
- El expediente de documentación se prepara después del código (según instrucción del usuario).
- Todas las simulaciones están marcadas con una insignia "SIMULADO".
