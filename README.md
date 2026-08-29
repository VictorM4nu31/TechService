# TechService — Sistema de Gestión de Tickets

Sistema web para la gestión de mantenimiento preventivo y correctivo de equipos de cómputo, construido con Laravel 12, Livewire 4 y Flux UI.

---

## ✨ Características

- **Gestión de Tickets** — Apertura, seguimiento, asignación y cierre de solicitudes de soporte técnico
- **Inventario de Equipos** — Registro de computadoras, impresoras y dispositivos por usuario
- **Control de Roles** — Admin, Agente y Cliente con permisos diferenciados (Spatie Permission)
- **Historial de Actividad** — Registro de cada cambio y comentario en los tickets
- **Archivos Adjuntos** — Soporte de imágenes y PDFs via Spatie Media Library
- **2FA** — Autenticación de dos factores con Laravel Fortify
- **UI Premium** — Tema oscuro "Stellar Midnight" con animaciones Alpine.js

---

## 🛠️ Stack Tecnológico

| Capa      | Tecnología                       |
| --------- | -------------------------------- |
| Backend   | PHP 8.2+ / Laravel 12            |
| Frontend  | Livewire 4 + Flux UI + Alpine.js |
| Auth      | Laravel Fortify                  |
| RBAC      | Spatie Laravel Permission        |
| Media     | Spatie Media Library v11         |
| Testing   | Pest PHP v4                      |
| DB (dev)  | SQLite                           |
| DB (prod) | MySQL 8+ / PostgreSQL 15+        |

---

## 🚀 Instalación

```bash
# Clonar el repositorio
git clone <repo-url>
cd techservice

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos y seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor de desarrollo
composer run dev
```

---

## 👥 Credenciales de Prueba (Seeders)

| Rol     | Email                     | Password |
| ------- | ------------------------- | -------- |
| Admin   | admin@techservice.com     | password |
| Agente  | agent1@techservice.com    | password |
| Cliente | client1@techservice.com   | password |

---

## 🧪 Tests

```bash
# Correr todos los tests
php artisan test

# Solo tests de tickets y equipos
php artisan test --filter="TicketTest|EquipmentTest"
```

---

## 🗂️ Estructura de Módulos

```
app/
├── Http/Controllers/
│   ├── DashboardController.php    — Panel principal con estadísticas
│   ├── TicketController.php       — Gestión de tickets (vista/detalle/edición)
│   ├── EquipmentController.php    — CRUD completo de equipos
│   └── TeamController.php         — Equipos/grupos de trabajo
├── Livewire/
│   ├── TicketCreate.php           — Formulario reactivo de creación
│   ├── MaintenanceCalendar.php    — Calendario CMMS interactivo
│   └── Actions/                   — Acciones complementarias
├── Models/                        — Ticket, Equipment, User, Comment, Activity...
├── Services/
│   └── TicketService.php          — Lógica de negocio de tickets
└── Policies/
    └── TicketPolicy.php           — Control de acceso por recurso
```

### Convenciones de componentes Livewire

El proyecto usa un patrón deliberado para elegir entre Livewire de clase y Volt:

- **Livewire de clase** (`app/Livewire/*.php` + `resources/views/livewire/*.blade.php`) para
  componentes con lógica de negocio o interacciones complejas (formularios con subida de
  archivos, calendario con estado propio): `TicketCreate`, `MaintenanceCalendar`.
- **Volt / single-file** (`resources/views/livewire/**/**.blade.php`) para vistas delgadas y
  transaccionales que solo exponen estado y acciones simples: detalles y edición de tickets.

Esta separación mantiene testable la subida de archivos y deja el marcado denso en archivos
ligeros.

---

## 🔐 Roles y Permisos

| Acción                    | Admin | Agente | Cliente |
| ------------------------- | :---: | :----: | :-----: |
| Ver todos los tickets     |  ✅   |   ✅   |   ❌    |
| Ver propios tickets       |  ✅   |   ✅   |   ✅    |
| Crear tickets             |  ✅   |   ✅   |   ✅    |
| Asignar tickets           |  ✅   |   ✅   |   ❌    |
| Cambiar estado            |  ✅   |   ✅   |   ❌    |
| Eliminar tickets          |  ✅   |   ❌   |   ❌    |
| Gestionar equipos propios |  ✅   |   ✅   |   ✅    |
| Ver equipos de otros      |  ✅   |   ❌   |   ❌    |

---

## 📋 Lista de pendientes para producción

- [ ] Cambiar `APP_DEBUG=false`, `APP_ENV=production`
- [ ] Migrar a MySQL/PostgreSQL
- [ ] Configurar mailer real (SES, Mailgun, etc.)
- [ ] `SESSION_ENCRYPT=true`
- [ ] Configurar Redis para caché y colas
- [ ] `php artisan optimize`

---

## 📄 Licencia

MIT — Uso interno / académico.
