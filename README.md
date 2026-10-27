# INATEC Emprende

**Catálogo institucional de proyectos estudiantiles de los centros tecnológicos de INATEC.**

Plataforma web *paperless* que da visibilidad equitativa a los proyectos técnicos y emprendimientos de las y los estudiantes, y que conecta al visitante directamente con su autor por WhatsApp, sin intermediarios y sin algoritmos de promoción pagada.

<p>
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white">
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL%20%2F%20MariaDB-8.x-4479A1?logo=mysql&logoColor=white">
  <img alt="Tailwind CSS" src="https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?logo=tailwindcss&logoColor=white">
  <img alt="Vite" src="https://img.shields.io/badge/Vite-8.x-646CFF?logo=vite&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/license-MIT-22C55E">
</p>

---

## Tabla de contenido

- [Objetivo](#objetivo)
- [Principios de producto](#principios-de-producto)
- [Funcionalidades](#funcionalidades)
- [Stack tecnológico](#stack-tecnológico)
- [Arquitectura del proyecto](#arquitectura-del-proyecto)
- [Modelo de datos](#modelo-de-datos)
- [Roles y permisos](#roles-y-permisos)
- [Rutas principales](#rutas-principales)
- [Instalación](#instalación)
- [Credenciales de demostración](#credenciales-de-demostración)
- [Comandos útiles](#comandos-útiles)
- [Pruebas](#pruebas)
- [Estándares de código](#estándares-de-código)
- [Base de conocimiento para agentes (`skills/`)](#base-de-conocimiento-para-agentes-skills)
- [Estado del proyecto](#estado-del-proyecto)
- [Licencia](#licencia)

---

## Objetivo

INATEC Emprende centraliza en un solo catálogo público los proyectos desarrollados por estudiantes de los centros tecnológicos del país. El sistema resuelve tres problemas concretos:

1. **Visibilidad justa.** Cada proyecto compite por atención con base en su relevancia, fecha de publicación, especialidad y centro tecnológico. No existen *posts* promocionados ni ponderaciones artificiales.
2. **Conversión directa.** El contacto ocurre en `wa.me/{número}` con un mensaje precargado que identifica el proyecto. No hay formularios de intermediación ni bandejas de mensajes que revisar.
3. **Costo cero de infraestructura.** Arquitectura *local-first* para la etapa MVP: base de datos y almacenamiento de imágenes en el propio servidor, sin dependencias de nube pagadas.

---

## Principios de producto

| Principio | Implementación |
| --- | --- |
| **Escaparate equitativo** | El feed público ordena por `created_at` descendente y filtra por especialidad, centro, carrera y ubicación. No hay campo de "destacado" ni de pago. |
| **Contacto sin fricción** | El número vive una sola vez en `user_profiles.phone_whatsapp`; el enlace se construye como *accessor* en el modelo `Project`. |
| **Cero costo en la nube** | `Storage::disk('public')` con enlace simbólico local; MySQL/MariaDB sobre XAMPP. |
| **Seguridad por diseño** | Policies de Laravel en todos los recursos, *FormRequests* dedicados y saneamiento de entrada para neutralizar XSS almacenado. |
| **Rendimiento por defecto** | *Eager loading* explícito en catálogo, panel y administración para evitar consultas N+1. |

---

## Funcionalidades

### Catálogo público (visitantes)

- Feed paginado de proyectos **publicados** (12 por página), ordenado por fecha.
- Filtros combinables: departamento → municipio → centro tecnológico → carrera, más especialidad y búsqueda de texto en título y descripción.
- Ficha de proyecto con portada, descripción, datos del autor (centro, carrera, departamento) y galería.
- Proyectos relacionados por especialidad (máximo 3).
- Botón de WhatsApp con mensaje precargado: *«Hola, vi el proyecto "…" en INATEC Emprende y quiero más información.»*
- Páginas legales de términos y privacidad.

### Panel del estudiante (autenticado)

- Tablero con métricas por estado: total, publicados, borradores y suspendidos.
- Gestión del perfil propio: nombre, correo, WhatsApp, descripción personal y contraseña (con verificación de la contraseña actual).
- Crear, editar y eliminar proyectos propios.
- Cambio de estado entre `draft` y `published`.
- Generación automática de `slug` único (considera proyectos con *soft delete*).
- Carga de portada validada (JPG/PNG/WebP, máximo 2 MB) con reemplazo y borrado físico del archivo anterior.

### Administración (`role = admin`)

- Directorio de usuarios con búsqueda por nombre/correo y filtro por rol.
- Cambio de rol entre `student` y `admin` (sin autopromoción).
- Eliminación permanente de cuentas: purga transaccional de proyectos y de todos sus archivos físicos.
- Moderación de proyectos vía `status = suspended` (a nivel de modelo y policy).

---

## Stack tecnológico

| Capa | Tecnología |
| --- | --- |
| Framework | Laravel 13.x (PHP 8.4+, código con `declare(strict_types=1)`) |
| Base de datos | MySQL / MariaDB 8.x (XAMPP) |
| Vistas | Blade + componentes anónimos (`x-ui.*`, `x-catalog.*`) |
| Estilos | Tailwind CSS 4 (vía `@tailwindcss/vite`) |
| Interactividad | Alpine.js 3 |
| Build | Vite 8 + `laravel-vite-plugin` |
| Almacenamiento | Disco `public` local (`storage/app/public/projects`) |
| Pruebas | PHPUnit 12 + `RefreshDatabase` |
| Formato | Laravel Pint |

---

## Arquitectura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── CatalogController.php        # Catálogo público + ficha de proyecto
│   │   ├── ProjectController.php        # CRUD del estudiante autenticado
│   │   ├── Admin/UserController.php     # Directorio y moderación de usuarios
│   │   └── Auth/                        # Login, registro, recuperación de contraseña
│   ├── Policies/                        # ProjectPolicy, UserPolicy (anti-IDOR)
│   └── Requests/                        # Validación, saneamiento y normalización
├── Models/                              # User, UserProfile, Project, ProjectGallery,
│                                        # Center, Career, Department, Municipality
├── Observers/ProjectObserver.php        # Borrado físico de imágenes
└── Providers/AppServiceProvider.php     # Gate `admin`

database/
├── migrations/                          # Esquema territorial, institucional y de proyectos
└── seeders/                             # Departamentos, municipios, centros, carreras, usuarios

resources/views/
├── layouts/                             # public, auth
├── pages/                               # home, terms, privacy
├── catalog/                             # index, search, filtros y resultados parciales
├── projects/                            # index (dashboard), create, edit, show, _form
├── components/                          # ui/*, catalog/*, legal/*, brand/*
└── auth/                                # login, register, forgot/reset password

skills/                                  # Base de conocimiento para agentes de IA
```

**Convenciones clave**

- Controladores delgados: solo coordinan, delegan validación a `FormRequest` y autorización a `Policy`.
- Toda regla de negocio vive en modelos, *observers* o *FormRequests*.
- Autorización explícita con `$this->authorize(...)` en cada acción sensible.
- Consultas con `with()` / `loadMissing()` obligatorio en listados.

---

## Modelo de datos

```
departments ──< municipalities ──< centers ──< careers
                                      │          │
                                      └────┬─────┘
                                           │
users ──1:1── user_profiles ───────────────┘
  │                  │
  │                  └── phone_whatsapp  (fuente única del contacto)
  │
  └──1:N── projects ──1:N── project_gallery
```

| Tabla | Campos relevantes |
| --- | --- |
| `departments` | `name` |
| `municipalities` | `department_id`, `name` |
| `centers` | `municipality_id`, `name`, `code` (único), `location` |
| `careers` | `center_id`, `name` |
| `users` | `name`, `email`, `password`, `role` (`student` \| `admin`) |
| `user_profiles` | `user_id` (único), `center_id`, `career_id`, `phone_whatsapp`, `bio` |
| `projects` | `user_id`, `title`, `slug` (único), `description`, `category`, `cover_photo`, `status` (`draft` \| `published` \| `suspended`), `deleted_at` |
| `project_gallery` | `project_id`, `image_path` |

**Decisiones de integridad**

- `centers.municipality_id`, `user_profiles.center_id` y `user_profiles.career_id` usan `RESTRICT`: los catálogos institucionales nunca arrastran datos de estudiantes.
- `projects.user_id` y `project_gallery.project_id` usan `CASCADE`.
- Índices compuestos `(status, created_at)` y `(category, status)` para el feed público.
- El número de WhatsApp **no** se duplica en `projects`: se resuelve desde el perfil del autor.

**Especialidades disponibles:** Tecnología · Agropecuaria · Industria · Turismo · Salud · Servicios · Comercio · Artesanía · Medio Ambiente.

---

## Roles y permisos

| Acción | Invitado | Estudiante | Admin |
| --- | :---: | :---: | :---: |
| Ver catálogo y proyectos publicados | ✅ | ✅ | ✅ |
| Ver borradores / suspendidos propios | ❌ | ✅ | ✅ |
| Crear proyecto | ❌ | ✅ | ✅ |
| Editar / eliminar proyecto propio | ❌ | ✅ | ✅ |
| Editar proyecto ajeno | ❌ | ❌ | ✅ |
| Restaurar o borrar definitivamente | ❌ | ❌ | ✅ |
| Cambiar roles y eliminar cuentas | ❌ | ❌ | ✅ |

Las restricciones se aplican con `ProjectPolicy` y `UserPolicy` (prevención de IDOR) más el gate `admin` registrado en `AppServiceProvider`.

---

## Rutas principales

| Método | URI | Nombre | Acceso |
| --- | --- | --- | --- |
| GET | `/` | `home` | Público |
| GET | `/catalog` | `catalog.index` | Público |
| GET | `/search` | `catalog.search` | Público |
| GET | `/projects/{slug}` | `projects.show` | Público / autor / admin |
| GET | `/terminos`, `/privacidad` | `terms`, `privacy` | Público |
| GET/POST | `/login`, `/register`, `/forgot-password`, `/reset-password/{token}` | — | Invitado |
| POST | `/logout` | `logout` | Autenticado |
| GET | `/dashboard` | `dashboard` | Autenticado |
| GET | `/profile` | `profile.show` | Autenticado |
| GET | `/profile/edit` | `profile.edit` | Autenticado |
| PUT | `/profile` | `profile.update` | Autenticado |
| PUT | `/profile/password` | `profile.password.update` | Autenticado |
| GET/POST | `/dashboard/projects/create`, `/dashboard/projects` | `projects.create`, `projects.store` | Autenticado |
| GET/PUT/DELETE | `/dashboard/projects/{project}/edit`, `/dashboard/projects/{project}` | `projects.edit`, `projects.update`, `projects.destroy` | Autor / admin |
| GET | `/admin/users` | `admin.users.index` | Admin |
| PUT | `/admin/users/{user}/role` | `admin.users.role.update` | Admin |
| DELETE | `/admin/users/{user}` | `admin.users.destroy` | Admin |

---

## Instalación

### Requisitos

- PHP **8.4+** con extensiones `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`
- Composer 2
- Node.js 20+ y npm
- MySQL / MariaDB (XAMPP, Laragon o equivalente)

### Pasos

```bash
# 1. Dependencias
composer install
npm install

# 2. Entorno
cp .env.example .env      # Windows: copy .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env` (crea antes la base `inatec-emprende` en phpMyAdmin):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inatec-emprende
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 3. Esquema y datos iniciales
php artisan migrate --seed

# 4. Almacenamiento público de imágenes
php artisan storage:link

# 5. Frontend y servidor
npm run build
php artisan serve
```

La aplicación queda disponible en <http://localhost:8000>.

> Atajo: `composer setup` ejecuta instalación, copia de `.env`, `key:generate`, migraciones, `npm install` y build en un solo paso.

---

## Credenciales de demostración

Creadas por `UserSeeder` (`php artisan db:seed --class=UserSeeder`):

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@inatec.edu.ni` | `password` |
| Estudiante | `estudiante@inatec.edu.ni` | `password` |

> Cambia estas credenciales antes de cualquier despliegue real.

---

## Comandos útiles

```bash
php artisan migrate:fresh --seed    # Reiniciar esquema con datos base
php artisan db:seed                 # Volver a sembrar catálogos y usuarios
php artisan storage:link            # Recrear el enlace público de imágenes
php artisan test                    # Suite completa de pruebas
php artisan pail                    # Logs en vivo
vendor/bin/pint                     # Formateo de código
npm run dev                         # Vite en modo desarrollo
```

---

## Pruebas

| Suite | Cobertura |
| --- | --- |
| `tests/Feature/PublicPortalTest.php` | Catálogo, filtros, visibilidad por estado y acceso a ficha |
| `tests/Feature/DashboardTest.php` | Tablero del estudiante y métricas por estado |
| `tests/Feature/Auth/*` | Login, registro con perfil institucional y restablecimiento de contraseña |
| `tests/Feature/ProfileTest.php` | Perfil propio: datos, WhatsApp normalizado y cambio de contraseña |

```bash
php artisan test
php artisan test --filter=PublicPortalTest
```

---

## Estándares de código

- `declare(strict_types=1)` en todos los archivos PHP.
- Tipado estricto de retornos y parámetros; constantes tipadas (`public const string`).
- Atributos modernos de Eloquent: `#[Fillable]`, `#[Hidden]`, `#[UsePolicy]`, `#[ObservedBy]`, `#[Scope]`.
- Validación, saneamiento y normalización exclusivamente en `app/Http/Requests`.
- **WhatsApp:** el estudiante escribe 8 dígitos nacionales (`[578]\d{7}`); `App\Support\WhatsAppNumber` centraliza la normalización a `505XXXXXXXX` y el campo (`x-ui.phone-input`) bloquea cualquier carácter no numérico en tiempo real.
- Prohibido el acceso directo a archivos sin pasar por `Storage`.
- Mensajes de interfaz y validación en español (`lang/es`).
- Formato con Laravel Pint antes de cada commit.

---

## Base de conocimiento para agentes (`skills/`)

El repositorio incluye `skills/`, una colección de *Agent Skills* en Markdown que documentan criterios de diseño de interfaz, animación y rendimiento (filosofía de *design engineering* de Emil Kowalski), además de guías de Swift y de selección de librerías frontend.

| Skill | Uso |
| --- | --- |
| `emil-design-eng` | Filosofía base de pulido visual y decisiones de motion |
| `animate` / `animation-performance` | Construcción de animaciones y diagnóstico de *jank* |
| `apple-design` / `mobile-native` | Motion físico y sensación nativa en móvil |
| `review-animations` / `improve-animations` / `find-animation-opportunities` | Revisión, auditoría y detección de oportunidades de movimiento |
| `break-ui` | Pruebas de estrés con datos extremos |
| `prototype` / `pick-ui-library` | Prototipado de variantes y elección de librerías |
| `animation-vocabulary` | Glosario de efectos de movimiento |
| `ask-sonner` / `animate-expo` / `write-swift` | Referencias específicas de stack (no aplican al MVP actual) |

La ruta se declara en `.env` mediante `SKILLS_PATH` y se replica en `.env.example`. Laravel no la consume en tiempo de ejecución: es documentación local para el CLI de OpenCode. Para que el agente las cargue automáticamente, expón la carpeta en `.opencode/skills/`.

---

## Estado del proyecto

| Módulo | Estado |
| --- | --- |
| Catálogo público, filtros y ficha de proyecto | ✅ Implementado |
| Autenticación y registro con perfil institucional | ✅ Implementado |
| CRUD de proyectos del estudiante y métricas | ✅ Implementado |
| Borrado físico de imágenes (observer) | ✅ Implementado |
| Perfil del estudiante (datos, WhatsApp y contraseña) | ✅ Implementado |
| Panel de administración (usuarios y roles) | ⚠️ Controlador y rutas listos; vistas pendientes |
| Gestión de galería desde la interfaz | ⚠️ Modelo y relación listos; UI pendiente |
| Moderación de proyectos (`suspended`) | ⚠️ Soportado en modelo y policy; acción de admin pendiente |

> Nota de consistencia: `AGENTS.md` menciona Laravel 11 y PHP 8.4; el `composer.json` fijado resuelve **Laravel 13.38** sobre **PHP 8.5**. Este README refleja el estado real del código.

---

## Licencia

Distribuido bajo licencia **MIT**. Proyecto de carácter institucional desarrollado para el Tecnológico Nacional (INATEC), Nicaragua.
