# TCG Premium

Aplicación web tipo e-commerce para la venta de cartas coleccionables Pokémon TCG.

El proyecto permite importar cartas desde una API externa, gestionar inventario, visualizar un catálogo de productos y agregar cartas a un carrito de compras.

## Objetivo del proyecto

Este proyecto nace como una tienda online para cartas Pokémon TCG, orientada a tiendas o vendedores que necesitan administrar su catálogo, controlar stock y publicar productos disponibles para la venta.

## Funcionalidades principales

* Catálogo de cartas Pokémon TCG.
* Importación de cartas desde una API externa.
* Gestión de inventario.
* Control de precio y stock.
* Carrito de compras.
* Panel administrativo.
* Búsqueda y filtrado de productos.
* Base de datos relacional.
* Interfaz construida con Laravel, Livewire y Blade.

## Tecnologías utilizadas

* PHP
* Laravel
* Laravel Livewire
* Blade
* MySQL
* Tailwind CSS
* Docker
* Git y GitHub

## Estructura general del proyecto

El proyecto está organizado siguiendo la estructura estándar de Laravel:

* `app/Models`: modelos principales del dominio.
* `app/Livewire`: componentes interactivos para catálogo, carrito e inventario.
* `database/migrations`: estructura de base de datos.
* `routes`: definición de rutas de la aplicación.
* `resources/views`: vistas de la interfaz.
* `app/Console/Commands`: comandos personalizados, incluyendo importación de cartas.

## Entidades principales

* `Card`: representa una carta Pokémon.
* `CardSet`: representa el set o colección al que pertenece una carta.
* `Inventory`: representa el stock y precio disponible para venta.
* `Cart`: representa el carrito de compras.
* `CartItem`: representa los productos agregados al carrito.

## Instalación local

Clonar el repositorio:

```bash
git clone https://github.com/Joradi/tcg-premium.git
cd tcg-premium
```

Instalar dependencias de PHP:

```bash
composer install
```

Instalar dependencias de Node:

```bash
npm install
```

Copiar archivo de entorno:

```bash
cp .env.example .env
```

Generar clave de aplicación:

```bash
php artisan key:generate
```

Ejecutar migraciones:

```bash
php artisan migrate
```

Levantar el servidor local:

```bash
php artisan serve
```

Compilar assets:

```bash
npm run dev
```

## Estado actual

El proyecto se encuentra en desarrollo. Actualmente cuenta con funcionalidades base de catálogo, inventario y carrito.

## Próximas mejoras

* Agregar capturas de pantalla al README.
* Mejorar validaciones del panel administrativo.
* Agregar pruebas automatizadas para el carrito.
* Separar parte de la lógica de negocio en servicios.
* Mejorar manejo de errores al importar cartas desde la API.
* Agregar documentación técnica del flujo de compra.
* Evaluar despliegue en ambiente demo.

## Aprendizajes aplicados

Este proyecto me ha permitido practicar:

* Modelado de entidades.
* Relaciones entre tablas.
* Uso de Laravel y Eloquent.
* Consumo de APIs externas.
* Gestión de inventario.
* Lógica de carrito de compras.
* Organización de un proyecto web.
* Uso de Git y GitHub.
