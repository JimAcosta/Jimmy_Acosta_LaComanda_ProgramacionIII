# 🍽️ La Comanda - Sistema de Gestión Gastronómica (API REST)


API REST desarrollada en PHP utilizando Slim Framework v4 y PostgreSQL para la gestión integral de un restaurante.

El sistema permite administrar pedidos, productos, empleados, mesas, encuestas y reportes operativos en tiempo real, con autenticación basada en roles.


##  Tecnologías Utilizadas

- PHP 8
- Slim Framework v4
- PostgreSQL
- PDO
- JWT Authentication
- Composer
- Deploy en Railway
- Base de datos en Railway
- Postman (colección incluida)


## 👥 Roles del Sistema

El sistema implementa control de acceso basado en roles:

-  Cocineros
-  Cerveceros
-  Bartenders
-  Mozos
-  Socios 

Cada rol tiene permisos específicos y acceso a funcionalidades determinadas.


## Funcionalidades Principales

### Gestión de Pedidos
- Creación de pedidos por parte del mozo
- Asociación de pedido a una mesa con código único
- Generación de código alfanumérico para seguimiento
- Carga de imagen de la mesa

###  Gestión de Estados
- Estados de pedido:
  - Pendiente
  - En preparación
  - Listo para servir
  - Entregado
- Estados de mesa:
  - Esperando pedido
  - Con cliente comiendo
  - Con cliente pagando
  - Cerrada

### Gestión por Sector
Cada sector del restaurante visualiza únicamente los productos que le corresponden:
- Cocina
- Barra de tragos
- Barra de cervezas
- Candy Bar

### Panel de Socios
- Visualización de todos los pedidos y su tiempo estimado
- Listado de mesas y estados
- Pedidos fuera de tiempo estimado
- Productos demorados
- Mesa más utilizada
- Mejores comentarios de clientes

### Encuestas
El cliente puede calificar:
- Mesa
- Restaurante
- Mozo
- Cocinero

Incluye comentario opcional.


## Autenticación

La API utiliza autenticación basada en JWT.

El usuario debe iniciar sesión para obtener un token válido.
El token debe enviarse en el header:

Authorization: Bearer {token}


##  Deploy

API en producción:
https://jimmyacostalacomandaprogramacioniii-production.up.railway.app

Base de datos PostgreSQL alojada en Railway.

## Pruebas con Postman

En la raíz del repositorio se incluyen:

- Archivo llamado  `La_Comanda.postman_collection.json` con la colección completa de endpoints.
- Archivo de requerimientos del proyecto.
- Archivo de los criterios de evaluación del proyecto.

Puede importar la coleccion en postman y seguir los pasos que estan en el archivo "API - La comanda Criterios de Correccion"

Tambien hay endpoints para la creacion de Las Clases 'Usuario', 'Mesa' y 'Producto'

### Cómo probar la API:

1. Descargar el archivo de colección `.json`.
2. Importarlo en Postman.
3. Ejecutar la request de login para obtener el token JWT.
4. Utilizar el token en las requests protegidas.


## Base de Datos

Base de datos relacional en PostgreSQL con:

- Relaciones entre entidades
- Integridad referencial
- Manejo de estados


## Conceptos Aplicados

- Programación Orientada a Objetos
- Separación de responsabilidades
- Middleware de autenticación
- Validaciones centralizadas
- Manejo de errores HTTP
- Arquitectura modular
