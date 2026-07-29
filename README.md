# TCG Premium

Aplicación web tipo e-commerce para administrar y vender cartas coleccionables Pokémon TCG.

El proyecto corresponde a un MVP funcional construido con Laravel, Livewire y MySQL. Incluye catálogo público, carrito, checkout, gestión de inventario y administración de pedidos.

## Funcionalidades

### Tienda

- Catálogo público de productos disponibles.
- Búsqueda de cartas por nombre.
- Filtros por set, idioma, condición y variante.
- Vista rápida del producto.
- Control de stock disponible.
- Carrito persistente para visitantes y usuarios autenticados.
- Incremento, disminución y eliminación de productos.
- Checkout con validación de datos del cliente y dirección.
- Confirmación de compra.
- Cálculo de subtotal, IVA incluido y total.
- Registro, inicio y cierre de sesión.
- Recuperación de contraseña.

### Administración

- Acceso restringido a usuarios administradores.
- Publicación y edición de productos.
- Gestión de precio, stock, idioma, condición y variante.
- Búsqueda y filtrado del inventario.
- Listado administrativo de pedidos.
- Búsqueda de pedidos por número, cliente o correo.
- Filtro de pedidos por estado.
- Vista detallada de cada pedido.
- Cancelación de pedidos pendientes.
- Restauración automática del inventario al cancelar.
- Fechas administrativas mostradas en la zona horaria configurada.

### Seguridad y calidad

- Protección CSRF proporcionada por Laravel.
- Regeneración de sesión durante la autenticación.
- Limitación de intentos para autenticación, recuperación de contraseña y checkout.
- Escape de contenido en las vistas Blade.
- Cabeceras HTTP de seguridad.
- Pruebas automatizadas con Pest.
- Formato de código mediante Laravel Pint.
- Diseño adaptable para escritorio y dispositivos móviles.

## Tecnologías

- PHP 8.3 o superior.
- Laravel 13.
- Livewire 4.
- Blade.
- Tailwind CSS.
- Alpine.js.
- MySQL 8.4.
- Redis.
- Vite.
- Pest.
- Laravel Sail.
- Docker.

## Requisitos

Para ejecutar el proyecto localmente se necesita:

- Git.
- Docker Desktop con soporte para WSL 2 o Docker Engine en Linux.
- PHP 8.3 o superior.
- Composer 2.

Node.js y MySQL no necesitan estar instalados directamente en el equipo, porque se ejecutan mediante Laravel Sail.

## Instalación local

### 1. Clonar el repositorio

```bash
git clone https://github.com/Joradi/tcg-premium.git
cd tcg-premium
```

### 2. Instalar las dependencias de PHP

```bash
composer install
```

### 3. Crear el archivo de entorno

```bash
cp .env.example .env
```

El archivo de ejemplo ya está configurado para utilizar MySQL mediante Laravel Sail.

### 4. Levantar los contenedores

```bash
./vendor/bin/sail up -d
```

### 5. Generar la clave de Laravel

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Instalar las dependencias frontend

```bash
./vendor/bin/sail npm install
```

### 7. Crear la base de datos y los datos iniciales

```bash
./vendor/bin/sail artisan migrate --seed
```

El seeder crea un administrador únicamente en los entornos `local` y `testing`.

Credenciales locales:

```text
Correo: admin@example.com
Contraseña: password
```

Estas credenciales son exclusivamente para desarrollo local y no deben utilizarse en producción.

### 8. Ejecutar Vite

En una segunda terminal:

```bash
./vendor/bin/sail npm run dev -- --port 5273
```

La aplicación estará disponible en:

```text
http://localhost
```

## Detener el proyecto

```bash
./vendor/bin/sail down
```

Para detenerlo y eliminar también los volúmenes de datos:

```bash
./vendor/bin/sail down -v
```

## Reiniciar la base de datos

Para eliminar los datos locales, ejecutar nuevamente las migraciones y crear el administrador:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

## Importación de cartas

El proyecto incluye comandos para importar información desde la API de Pokémon TCG.

### Importar un set

El argumento corresponde al identificador oficial del set:

```bash
./vendor/bin/sail artisan pokemon:import swsh10
```

Cuando no se indica un identificador, se utiliza `swsh10`:

```bash
./vendor/bin/sail artisan pokemon:import
```

### Importar todos los sets

```bash
./vendor/bin/sail artisan pokemon:import-all-sets
```

La importación completa realiza múltiples solicitudes externas y puede tardar varios minutos.

Las cartas importadas no aparecen automáticamente en el catálogo público. Un administrador debe crear o actualizar su registro de inventario, definir precio y stock, y publicarlas.

## Pruebas y controles de calidad

### Ejecutar toda la suite

```bash
./vendor/bin/sail artisan test
```

### Ejecutar Pest directamente

```bash
./vendor/bin/sail exec laravel.test ./vendor/bin/pest
```

### Comprobar el formato del código

```bash
./vendor/bin/sail pint --test
```

### Corregir el formato automáticamente

```bash
./vendor/bin/sail pint
```

### Generar los assets de producción

```bash
./vendor/bin/sail npm run build
```

### Revisar dependencias de PHP

```bash
./vendor/bin/sail composer audit
```

### Revisar dependencias frontend

```bash
./vendor/bin/sail npm audit
```

## Configuración relevante

Variables principales de `.env`:

```dotenv
APP_NAME="TCG Premium"
APP_URL=http://localhost
APP_DISPLAY_TIMEZONE=America/Santiago

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

VITE_PORT=5273
```

Laravel conserva internamente las fechas en UTC. Las fechas visibles en el panel administrativo se convierten a `APP_DISPLAY_TIMEZONE`.

## Estructura principal

- `app/Livewire/Storefront`: catálogo, carrito y checkout.
- `app/Livewire/Admin`: inventario y pedidos.
- `app/Models`: entidades y relaciones del dominio.
- `app/Console/Commands`: importación de sets y cartas.
- `database/migrations`: estructura de la base de datos.
- `database/seeders`: datos iniciales para desarrollo.
- `resources/views`: componentes Blade y vistas Livewire.
- `routes`: rutas web y autenticación.
- `tests/Feature`: pruebas funcionales y de seguridad.

## Alcance del MVP

El MVP contempla un flujo de compra sin integración de pago electrónico. Los pedidos se registran con estado pendiente y pueden administrarse desde el panel.

No están incluidos en esta versión:

- Integración con Webpay u otra pasarela de pago.
- Cálculo logístico o integración con empresas de transporte.
- Emisión de documentos tributarios.
- Despliegue productivo.
- Gestión avanzada de correos transaccionales.
