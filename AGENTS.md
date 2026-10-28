# AGENTS.md — Contexto técnico y reglas de arquitectura

> Documento de referencia para cualquier agente de IA (OpenCode, Claude, Codex, Cursor, etc.)
> que trabaje en este repositorio. Describe **lo que el código hace hoy**, no lo que se planea.
>
> Última verificación contra el código: **2026-10-27** (commit `71fb77d`).
> Si algo de este documento no coincide con el código, **el código manda**; actualiza este archivo
> en el mismo cambio en que corrijas la discrepancia.

---

## 0. Cómo usar este documento

1. Lee las secciones **2 (stack)**, **3 (reglas de oro)**, **5 (ciclo de petición)** y **14 (deuda técnica)**
   antes de tocar nada. Con eso ya puedes navegar el proyecto sin romper sus invariantes.
2. Antes de crear un archivo nuevo, busca si ya existe un patrón equivalente (controlador, FormRequest,
   policy, vista, componente Blade o test). **Este proyecto es deliberadamente consistente: imita el patrón.**
3. No instales paquetes nuevos, no cambies de framework y no introduzcas capas (services, repositories,
   DTOs) sin que el usuario lo pida explícitamente. La arquitectura actual es intencionalmente simple.
4. Los textos de interfaz, validación y documentación de producto están **en español**. El código
   (clases, métodos, variables) está en inglés.

---

## 1. Qué es INATEC Emprende

Catálogo institucional *paperless* de proyectos técnicos y emprendimientos de estudiantes de los
centros tecnológicos de INATEC (Nicaragua).

Tres principios de producto que condicionan la arquitectura:

| Principio | Consecuencia técnica |
| --- | --- |
| **Escaparate equitativo** | No existe campo de "destacado", pagos, promociones ni ranking. El catálogo ordena por `created_at` y filtra por territorio, centro, carrera, especialidad y texto. |
| **Conversión directa** | El contacto es un enlace `wa.me/{número}` con mensaje precargado. **No hay formularios de contacto, bandejas de mensajes ni notificaciones al autor.** |
| **Costo cero / local-first** | MySQL/MariaDB local (XAMPP) e imágenes en `storage/app/public`. Sin servicios cloud pagados. |

---

## 2. Stack y versiones reales (verificado)

| Capa | Tecnología |
| --- | --- |
| Framework | **Laravel 13.38.0** (`laravel/framework ^13.17`), no Laravel 11 |
| Lenguaje | PHP **8.5.12** en este equipo; `composer.json` exige `^8.3` |
| Base de datos | MySQL/MariaDB, base `inatec-emprende` (XAMPP). Tests: SQLite `:memory:` |
| Vistas | Blade + componentes anónimos (`x-ui.*`, `x-catalog.*`, `x-layout.*`, `x-legal.*`, `x-brand.*`) |
| Estilos | Tailwind CSS 4 vía `@tailwindcss/vite`; tokens de motion en `resources/css/app.css` |
| Interactividad | Alpine.js 3.17 (componentes registrados en `resources/js/app.js`) |
| Build | Vite 8 + `laravel-vite-plugin` 3.1 + `@laravel/multiplex` (opcional) |
| Pruebas | PHPUnit 12.5.40 + `RefreshDatabase` |
| Formato | Laravel Pint |
| Auth | Sesiones de Laravel (driver `database`), sin API tokens / Sanctum |

**Configuración del entorno actual** (`.env`, no commitear):

```env
APP_NAME="INATEC Emprende"
APP_ENV=local
APP_URL=http://localhost:8000
APP_LOCALE=es
DB_CONNECTION=mysql
DB_DATABASE=inatec-emprende
DB_USERNAME=root
SESSION_DRIVER=database
QUEUE_CONNECTION=database
MAIL_MAILER=log
FILESYSTEM_DISK=local
SKILLS_PATH=skills
```

Notas:

- `config/app.php` fija `timezone` en `UTC` (no en `America/Managua`). Si implementas lógica de fechas
  "de Nicaragua", hazlo explícito; no asumas que la zona horaria del servidor es local.
- `FILESYSTEM_DISK=local` **no** significa que las subidas usen el disco local por defecto: el código
  siempre pasa `'public'` explícitamente. Mantén esa costumbre.
- `SKILLS_PATH` no lo consume Laravel en runtime; es documentación para el CLI.
- Las imágenes requieren el enlace `php artisan storage:link` (`public/storage`, ignorado por git).
  **Si el enlace falta, las imágenes no dan 404: dan 403 `Forbidden`.** Laravel 13 registra una ruta
  `GET /storage/{path}` para el disco `local` (`'serve' => true`, URLs privadas firmadas) que captura
  cualquier `/storage/*` que no exista físicamente en `public/` y lo rechaza por falta de firma. Un 403
  en `/storage/...` casi siempre significa "falta el link", no un problema de permisos ni del código.

---

## 3. Reglas de oro (no negociables)

1. **Controladores delgados.** Solo coordinan: leen la petición, autorizan, llaman al modelo y devuelven
   vista/redirección. La validación va en `FormRequest`; la autorización en `Policy`; el ciclo de vida
   de archivos en el `Observer`.
2. **Toda entrada se valida y se sanea en un `FormRequest`.** Nunca valides con `$request->validate()`
   dentro del controlador.
3. **Toda acción sensible se autoriza explícitamente.** Usa `$this->authorize(...)` en el controlador
   y/o `authorize()` en el `FormRequest`. Las policies se descubren solas por el atributo
   `#[UsePolicy(...)]`, pero la comprobación debe existir en ambos puntos cuando aplique.
4. **Anti-IDOR.** Un estudiante nunca puede leer, editar ni borrar recursos de otro. La regla vive en
   `ProjectPolicy` / `UserPolicy`; no la reimplementes con `if` sueltos.
5. **Eager loading obligatorio en listados.** Carga con `with()` / `load()` / `loadMissing()` la cadena
   `user.profile.center.municipality.department`, `user.profile.career` y `gallery`. Evita N+1.
6. **Nunca accedas a archivos directamente.** Usa `Storage::disk('public')` para guardar y borrar.
  Al reemplazar una portada, avatar, banner o diploma, o hacer borrado definitivo, el archivo físico se elimina
   (`ProjectObserver` / `ProfileObserver`). La base de datos guarda **solo la ruta relativa**.
7. **No dupliques el WhatsApp.** La única fuente es `user_profiles.phone_whatsapp`. El enlace se
   construye en el accessor `Project::whatsappUrl`. No agregues una columna de teléfono a `projects`.
8. **Los catálogos institucionales son protegidos.** `centers.municipality_id` y
   `user_profiles.center_id/career_id` usan `RESTRICT`: no se borran en cascada datos de estudiantes.
9. **Las categorías tienen una sola fuente de verdad:** `ProjectStoreRequest::CATEGORIES`.
   El catálogo, las validaciones y las vistas leen de ahí.
10. **Idioma:** mensajes de UI/validación en español; nombres de código en inglés.
11. **No commitees `.env`, `public/build`, `public/storage` ni `vendor/`.** Ya están en `.gitignore`.
12. **Formatea con Pint** antes de dar por terminado un cambio PHP.

---

## 4. Mapa del repositorio

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php                    # Base (AuthorizesRequests, ValidatesRequests)
│   │   ├── CatalogController.php             # Home, catálogo, búsqueda y ficha pública
│   │   ├── ProjectController.php             # Dashboard + CRUD del estudiante
│   │   ├── ProfileController.php             # Perfil propio y contraseña
│   │   ├── Admin/UserController.php          # Directorio, roles y baja de usuarios
│   │   └── Auth/                             # Login, registro, reset de contraseña
│   ├── Policies/
│   │   ├── ProjectPolicy.php                 # Anti-IDOR de proyectos
│   │   └── UserPolicy.php                    # Anti-IDOR de cuentas y reglas de rol
│   └── Requests/
│       ├── ProjectStoreRequest.php           # Categorías + slug + saneo XSS
│       ├── ProjectUpdateRequest.php          # Edición parcial, `suspended` solo admin
│       ├── ProfileUpdateRequest.php          # Datos + WhatsApp + medios del perfil (solo estudiante)
│       ├── ProfilePasswordUpdateRequest.php  # `current_password`
│       ├── Admin/UserRoleUpdateRequest.php
│       └── Auth/                             # Login, registro, reset
├── Models/
│   ├── User.php            # Rol, perfil 1:1, proyectos 1:N, `avatarUrl()`
│   ├── UserProfile.php     # Centro, carrera, WhatsApp, bio, avatar/banner/diploma
│   ├── Project.php         # Slug, estados, soft deletes, `whatsapp_url`
│   ├── ProjectGallery.php  # Tabla `project_gallery`
│   ├── Center.php / Career.php / Department.php / Municipality.php
├── Observers/
│   ├── ProjectObserver.php # Borra portada anterior y archivos al forceDelete
│   └── ProfileObserver.php # Borra medios anteriores y al eliminar el perfil
├── Providers/AppServiceProvider.php          # Gate `admin`
└── Support/WhatsAppNumber.php                # Normalización canónica del teléfono

bootstrap/app.php           # Routing, health `/up`, sin middleware personalizado
config/                     # Config estándar de Laravel
database/
├── migrations/             # Esquema territorial, institucional y de proyectos
├── seeders/                # 17 departamentos, 45 centros, carreras, usuarios demo
└── factories/UserFactory.php

resources/
├── css/app.css             # Tailwind 4 + tokens de motion + accesibilidad
├── js/app.js               # Componentes Alpine (phoneField, coverField, etc.)
├── img/logo.jpeg
└── views/
    ├── layouts/            # public, auth
    ├── pages/              # home, terms, privacy
    ├── catalog/            # index, search, _filters, _results, _search
    ├── projects/           # index (dashboard), create, edit, show, _form
    ├── profile/            # show, edit
    ├── components/         # ui/*, profile/*, catalog/*, layout/*, legal/*, brand/*, project-card, status-badge
    └── auth/               # login, register, forgot/reset password
    # ⚠️ NO existe resources/views/admin/ (ver §14)

routes/web.php              # 28 rutas, todas web; no hay API
tests/
├── Feature/                # PublicPortal, Dashboard, Profile
├── Feature/Auth/           # Login, Registration, PasswordReset
└── Unit/                   # WhatsAppNumber, Example
skills/                     # 15 Agent Skills de diseño/animación (no las usa Laravel)
public/                     # Front controller + build de Vite + storage link
storage/                    # app/public/projects (imágenes), logs, framework
```

**No hay** capa de servicios, repositorios, eventos de dominio, jobs, colas en uso, API ni paquetes de
administración. Si una tarea parece requerirlos, primero pregunta: el MVP evita complejidad deliberadamente.

---

## 5. Ciclo de una petición

```
routes/web.php
   │
   ├─ middleware: guest | auth | can:admin
   │
   ├─ FormRequest (validación + saneo + normalización + authorize)
   │      └─ si falla: redirect back con errores en español
   │
   ├─ Controller@action
   │      ├─ $this->authorize(...)   → Policy
   │      ├─ consulta Eloquent (with/withCount/paginate)
   │      ├─ Storage::disk('public') → subida/borrado
   │      └─ return view(...) | redirect()->route(...)->with('status', ...)
   │
   ├─ Model / Observer
   │      ├─ casts, accessors (`whatsapp_url`, `whatsapp_display`)
   │      ├─ scopes (`published`)
   │      └─ Observer (portada anterior, forceDelete de archivos)
   │
   └─ Blade (layouts + componentes + Alpine)
```

**Convenciones de respuesta**

- Redirecciones con `->with('status', 'clave')`; el layout muestra el flash con `x-layout.flash`.
- Errores de validación siempre con mensajes en español, definidos en el `FormRequest`.
- Vistas: `view('carpeta.archivo', ['clave' => $valor])`, sin `compact()`.
- Nombres de ruta en español solo en páginas legales (`terms`, `privacy`); el resto en inglés.

---

## 6. Modelo de dominio

### 6.1 Diagrama

```
departments ──< municipalities ──< centers ──< careers
                                      │          │
                                      └────┬─────┘
                                           │
users ──1:1── user_profiles ───────────────┘
  │                  │
  │                  ├── phone_whatsapp  (única fuente del contacto)
  │                  └── avatar_path / banner_path / diploma_path (solo estudiantes)
  │
  └──1:N── projects ──1:N── project_gallery
```

### 6.2 Tablas (esquema real)

| Tabla | Columnas relevantes | Restricciones / índices |
| --- | --- | --- |
| `departments` | `id`, `name(100)`, `code(10)` | `code` único |
| `municipalities` | `id`, `department_id`, `name(100)` | FK → `departments` **CASCADE**; índice `(department_id, name)` |
| `centers` | `id`, `municipality_id`, `name(120)`, `code(20)`, `location(255) null` | FK → `municipalities` **RESTRICT**; `code` único; índice `(municipality_id, name)` |
| `careers` | `id`, `center_id`, `name(120)` | FK → `centers` **CASCADE**; único `(center_id, name)` |
| `users` | `id`, `name`, `email` único, `password`, `role` (`student`\|`admin`), `remember_token` | `role` indexado, default `student` |
| `user_profiles` | `id`, `user_id` único, `center_id`, `career_id`, `phone_whatsapp(20)`, `bio text null`, `avatar_path(255) null`, `banner_path(255) null`, `diploma_path(255) null` | FK usuario **CASCADE**; centro/carrera **RESTRICT**; índice `phone_whatsapp` |
| `projects` | `id`, `user_id`, `title(150)`, `slug(180)` único, `description text`, `category(50)`, `cover_photo(255)`, `status` enum, timestamps, `deleted_at` | FK usuario **CASCADE**; índices `(status, created_at)` y `(category, status)` |
| `project_gallery` | `id`, `project_id`, `image_path(255)` | FK proyecto **CASCADE**; índice `(project_id, id)` |

Además existen las tablas estándar de Laravel: `password_reset_tokens`, `sessions`, `cache`, `jobs`
(estas dos últimas no se usan en el flujo funcional actual).

### 6.3 Invariantes de negocio

- Un `User` tiene como máximo **un** `UserProfile`; el registro lo crea en la misma transacción.
- Un `Project` pertenece a **un** usuario; el borrado del usuario elimina sus proyectos.
- `projects.status` solo admite `draft`, `published`, `suspended`. El valor inicial es `draft`.
- El catálogo público solo consulta `published`.
- `slug` es único **incluyendo proyectos con soft delete**; se genera en `ProjectStoreRequest`.
- `projects.user_id` no se actualiza desde la UI: el dueño se fija al crear.
- `cover_photo` es obligatorio al crear y opcional al editar.
- `project_gallery` existe como modelo/relación y se muestra en la ficha, pero **no hay UI de carga**.
- `user_profiles.avatar_path`, `banner_path` y `diploma_path` guardan **solo la ruta relativa** en el disco `public`
  (`profiles/avatars/`, `profiles/banners/`, `profiles/diplomas/`). Las cuentas `admin` no pueden tener estos campos.
- Los proyectos usan `SoftDeletes`: el borrado del estudiante es lógico y conserva archivos.
  El borrado definitivo (baja de usuario) dispara `forceDeleting` y elimina archivos.

### 6.4 Categorías válidas

Fuente única: `App\Http\Requests\ProjectStoreRequest::CATEGORIES`

```
Tecnología · Agropecuaria · Industria · Turismo · Salud · Servicios · Comercio · Artesanía · Medio Ambiente
```

---

## 7. Lenguaje ubicuo (constantes)

| Concepto | Constante / ubicación | Valores |
| --- | --- | --- |
| Rol | `User::ROLE_*` | `student`, `admin` |
| Estado de proyecto | `Project::STATUS_*` | `draft`, `published`, `suspended` |
| Categoría | `ProjectStoreRequest::CATEGORIES` | 9 valores (ver §6.4) |
| WhatsApp | `WhatsAppNumber::COUNTRY_CODE` | `505` |
| WhatsApp | `WhatsAppNumber::NATIONAL_LENGTH` | `8` |
| WhatsApp | `WhatsAppNumber::NATIONAL_PATTERN` | `/^[578]\d{7}$/` |
| WhatsApp almacenado | `WhatsAppNumber::STORED_PATTERN` | `/^505[578]\d{7}$/` |
| Gate admin | `AppServiceProvider` | `Gate::define('admin', ...)` |

---

## 8. Roles, autorización y matriz de acceso

### 8.1 Reglas

- **Guest:** catálogo, búsqueda, ficha de proyectos `published`, páginas legales, login/registro.
- **Student (`student`):** todo lo anterior + dashboard, perfil propio, CRUD de **sus** proyectos.
- **Admin (`admin`):** todo + directorio de usuarios, cambio de rol, baja de cuentas. Puede editar
  cualquier proyecto vía policy, pero **no existe una UI de moderación** más allá del formulario normal.
- Un admin **no puede cambiarse el rol a sí mismo** ni eliminarse a sí mismo (`UserPolicy`).
- Un estudiante **no puede** editar ni borrar proyectos ajenos (403 por policy).
- Un proyecto `draft`/`suspended` solo lo ven su autor y los admins; para el resto es 403.

### 8.2 Matriz

| Acción | Invitado | Estudiante | Admin |
| --- | :---: | :---: | :---: |
| Ver catálogo / búsqueda | ✅ | ✅ | ✅ |
| Ver ficha publicada | ✅ | ✅ | ✅ |
| Ver borrador/suspendido propio | ❌ | ✅ | ✅ |
| Foto, portada y diploma de perfil | ❌ | ✅ (solo la propia) | ❌ (reservado a estudiantes) |
| Crear proyecto | ❌ | ✅ | ✅ |
| Editar / borrar proyecto propio | ❌ | ✅ | ✅ |
| Editar / borrar proyecto ajeno | ❌ | ❌ | ✅ (policy; sin UI dedicada) |
| Restaurar / borrar definitivamente | ❌ | ❌ | ✅ (solo policy, sin ruta) |
| Ver directorio de usuarios | ❌ | ❌ | ✅ (vista faltante, §14) |
| Cambiar rol / eliminar cuenta | ❌ | ❌ | ✅ (no sobre sí mismo) |

### 8.3 Implementación

- `ProjectPolicy` y `UserPolicy` se enlazan con `#[UsePolicy(...)]` en los modelos.
- El gate `admin` se usa en las rutas: `Route::middleware(['auth', 'can:admin'])`.
- Los controladores llaman `$this->authorize(...)`; los `FormRequest` vuelven a comprobar la habilidad
  en `authorize()` (defensa en profundidad).
- No agregues comprobaciones de rol duplicadas en Blade para decisiones de seguridad; la fuente es la
  policy. En Blade solo se ocultan enlaces de conveniencia.

---

## 9. Flujos clave (paso a paso)

### 9.1 Catálogo público (`CatalogController@index`)

Una sola acción sirve tres rutas y elige la vista según el nombre de ruta:

| Ruta | Vista |
| --- | --- |
| `home` (`/`) | `pages.home` |
| `catalog.search` (`/search`) | `catalog.search` |
| `catalog.index` (`/catalog`) | `catalog.index` |

1. Lee `department_id`, `municipality_id`, `center_id`, `career_id`, `category`, `search`.
   Los IDs se convierten con `$request->integer()`; los strings se leen con `queryString()`, que
   **descarta arrays** y recorta a 150 caracteres (límite de `projects.title`).
2. Si `category` no está en `ProjectStoreRequest::CATEGORIES`, se ignora (no produce 422).
3. Consulta `Project::published()` con eager loading profundo:
   `user.profile`, `user.profile.career`, `user.profile.center`,
   `user.profile.center.municipality`, `user.profile.center.municipality.department`, `gallery`.
4. Filtros encadenados con `when()` y `whereHas()`:
   - `department_id` → `user.profile.center.municipality.department_id`
   - `municipality_id` → `user.profile.center.municipality_id`
   - `center_id` → `user.profile.center_id`
   - `career_id` → `user.profile.career_id`
   - `search` → `whereLike` en `title` **o** `description`
5. Ordena `latest()` (por `created_at`), pagina **12** y conserva la query string.
6. Carga catálogos para los selects: departamentos siempre; municipios solo si hay departamento;
   centros siempre (con municipio); carreras solo si hay centro. Devuelve `filters` para repoblar.
7. Las vistas usan `_filters`, `_results`, `_search` y `x-catalog.browser` para el estado vacío.

**Al agregar un filtro**, actualiza: lectura del parámetro, `when()`, array `filters`, vista
`catalog/_filters.blade.php` y `PublicPortalTest`.

### 9.2 Ficha pública (`CatalogController@show`)

1. Route model binding por **slug**: `Route::get('/projects/{project:slug}', ...)`.
2. `$this->authorize('view', $project)`:
   - `published` → público;
   - `draft`/`suspended` → solo autor o admin; invitado recibe 403;
   - proyecto soft-deleted → 404 (el binding excluye trashed).
3. `loadMissing()` de perfil, cadena territorial y galería.
4. Carga hasta **3** proyectos `published` de la misma categoría, excluyendo el actual, `latest()`.
5. La vista `projects.show` muestra avisos de "Vista previa de borrador" / "Proyecto suspendido"
   cuando corresponde, datos del autor y el botón de WhatsApp.

### 9.3 Registro (`Auth\RegisteredUserController`)

1. `GET /register`: carga centros con carreras ordenadas (`with(['careers' => ...])`) para un select
   agrupado por centro. Evita N+1.
2. `POST /register` → `RegisterRequest`:
   - normaliza `name` (`Str::squish`), `email` (minúsculas + trim) y `phone_whatsapp`
     (`WhatsAppNumber::normalize`);
   - valida nombre (3–120, solo letras/espacios/puntos/apóstrofes/guiones), email único,
     WhatsApp con `STORED_PATTERN`, `career_id` existente, contraseña (8+, letras y números,
     confirmada) y `terms` aceptado.
3. El controlador **no confía en `center_id` del cliente**: busca la carrera y deriva
   `center_id = $career->center_id`.
4. En una transacción crea `User` con `role = student` y su `UserProfile`.
5. Redirige a `/login` con mensaje de éxito (no inicia sesión automáticamente).

**Nota:** después del registro, el estudiante **no puede cambiar centro ni carrera** desde el perfil;
el formulario de perfil solo edita nombre, correo, WhatsApp, bio, foto, portada y diploma.

### 9.4 Perfil (`ProfileController`)

- `show`: carga `profile.center.municipality.department` y `profile.career`; calcula métricas con
  **una** consulta agrupada (`selectRaw('status, count(*)')`). Pinta la portada y el avatar desde los
  accessors `banner_url` / `avatar_url`, pero solo si el usuario es estudiante.
- `edit`: muestra datos, imagen de perfil y formulario de contraseña. La sección `#apariencia` solo
  existe para estudiantes; un admin ve un aviso de que la imagen está reservada.
- `update` → `ProfileUpdateRequest` normaliza y valida; luego, en transacción:
  actualiza `name`/`email` y hace `profile()->firstOrNew([])` con WhatsApp y bio. El `firstOrNew`
  cubre cuentas admin que podrían no tener perfil.
- **Medios (solo `student`):** `avatar` y `banner` aceptan `image|max:2048|mimes:jpg,jpeg,png,webp`; `diploma`
  es obligatorio mientras `diploma_path` esté vacío y acepta `required|file|mimes:pdf,jpg,jpeg,png|max:2048`.
  Cuando ya hay una ruta guardada, el archivo es opcional; omitirlo conserva el diploma actual.
  El controlador guarda con `store('profiles/avatars', 'public')` o `store('profiles/banners', 'public')`
  y persiste únicamente la ruta. Un archivo nuevo gana sobre `remove_avatar`/`remove_banner`/`remove_diploma`;
  sin archivo, esos flags booleanos ponen la columna en `null`. El diploma usa `profiles/diplomas/` y se abre desde el perfil.
- Para admin, `avatar`, `banner`, `diploma`, `remove_avatar`, `remove_banner` y `remove_diploma` usan `prohibited`: enviar archivos
  produce error de validación (no 403) y el controlador ni siquiera intenta almacenarlos.
- `updatePassword` → exige `current_password` y actualiza con el cast `hashed` del modelo.
- El WhatsApp se presenta con `UserProfile::whatsappDisplay` como `+505 XXXX XXXX`.
- La UI reutiliza Alpine `coverField`, que ahora expone `removed`, `remove()` y `undo()`. El avatar
  global se propaga con `x-ui.avatar :src="..."`; `User::avatarUrl()` devuelve `null` para admins.

### 9.5 CRUD de proyectos (`ProjectController`)

| Acción | Comportamiento |
| --- | --- |
| `index` (`/dashboard`) | Proyectos **del usuario autenticado**, `withCount('gallery')`, `latest()`, paginación 10. Métricas por estado con una consulta agrupada. |
| `create` | `authorize('create', Project::class)`; pasa `CATEGORIES` y el perfil del usuario. |
| `store` | `ProjectStoreRequest` autoriza, sanea, genera slug y valida. Guarda la portada en el disco `public` (`projects/`) y crea el proyecto bajo el usuario. Redirige a la ficha pública. |
| `edit` | `authorize('update')`; carga galería y perfil del autor. |
| `update` | `ProjectUpdateRequest` autoriza y sanea campos presentes. Si llega portada nueva, la guarda; si no, conserva la anterior. Redirige a `projects.edit`. |
| `destroy` | `authorize('delete')`; **soft delete**. Redirige al dashboard. |

Detalles que suelen romperse:

- El slug se genera en `prepareForValidation()` y se valida con `Rule::unique`, consultando
  `Project::withTrashed()`. No lo regeneres en el controlador.
- `ProjectStoreRequest` inyecta `status` por defecto `draft`, pero la regla exige que esté presente
  y sea `draft` o `published`.
- `ProjectUpdateRequest` resuelve el proyecto del route binding (o por ID numérico) **incluyendo
  trashed** para autorizar. No cambies esa lógica sin tests.
- El admin puede enviar `suspended` en la edición; un estudiante no.
- La portada se guarda con `$file->store('projects', 'public')`. **Siempre** pasa el disco `public`.

### 9.6 Ciclo de vida de archivos (`ProjectObserver` y `ProfileObserver`)

| Evento | Efecto |
| --- | --- |
| `ProjectObserver@updated` y `cover_photo` cambió | Borra el archivo anterior del disco `public` (si era una ruta válida y distinta). |
| `ProjectObserver@forceDeleting` | Borra la portada y todas las rutas de `project_gallery` **antes** de que la cascada elimine las filas. |
| `ProjectObserver@deleted` (soft delete) | **No borra archivos**: el proyecto puede restaurarse. |
| `ProfileObserver@updated` y `avatar_path`/`banner_path`/`diploma_path` cambió | Borra el archivo anterior si era válido y distinto. |
| `ProfileObserver@deleting` | Borra avatar, portada y diploma antes de eliminar la fila del perfil. |
| Baja de usuario (`Admin\UserController@destroy`) | `forceDelete()` por proyecto → limpia portadas; luego borra el perfil con Eloquent (dispara `ProfileObserver`) y finalmente la cuenta. |

Rutas de archivos: `projects/{nombre-único}.{ext}`, `profiles/avatars/{...}`, `profiles/banners/{...}` y
  `profiles/diplomas/{...}` en `storage/app/public`, servidas por `public/storage`. Las imágenes aceptan
`image|max:2048|mimes:jpg,jpeg,png,webp`; el diploma acepta `file|max:2048|mimes:pdf,jpg,jpeg,png|max:2048`.

### 9.7 Administración (`Admin\UserController`)

- Rutas bajo `['auth', 'can:admin']`, prefijo `/admin`, nombre `admin.*`.
- `index`: `authorize('viewAny', User::class)`. Busca por `q` (nombre o correo) y filtra por `role`
  (`student`/`admin`), ordena `latest()`, pagina 15. **Renderiza `admin.users.index`, vista que aún
  no existe** (§14).
- `updateRole` → `UserRoleUpdateRequest`; `authorize('updateRole')` impide autopromoción. Si el
  nuevo rol es `admin`, limpia `avatar_path`/`banner_path`/`diploma_path` del perfil para que `ProfileObserver`
  borre los archivos.
- `destroy`: `authorize('delete')`; en transacción hace `forceDelete()` de todos los proyectos
  (incluidos trashed), elimina el perfil vía Eloquent para que `ProfileObserver` borre todos los medios
  y luego elimina la cuenta. No confíes en la cascada FK para limpiar archivos.

### 9.8 WhatsApp (`App\Support\WhatsAppNumber`)

Contrato único, compartido por registro, perfil, accessors y el componente Blade:

1. El usuario escribe **8 dígitos nacionales** que empiezan por `5`, `7` u `8`
   (`/^[578]\d{7}$/`).
2. `normalize()` elimina todo lo que no sea dígito, quita un `505` inicial si existe y vuelve a
   anteponer `505` solo cuando quedan exactamente 8 dígitos. Si no, devuelve los dígitos tal cual
   para que la validación los rechace (no "arregla" silenciosamente basura).
3. Se almacena como `505XXXXXXXX` (11 dígitos), validado con `/^505[578]\d{7}$/`.
4. `national()` devuelve los 8 dígitos para prellenar formularios.
5. `Project::whatsappUrl` devuelve `https://wa.me/505XXXXXXXX?text=...` con el mensaje:
   `Hola, vi el proyecto "{title}" en INATEC Emprende y quiero más información.`
   Si no hay teléfono válido, devuelve `null`.
6. El componente `x-ui.phone-input` y el Alpine `phoneField` limitan a 8 dígitos y bloquean
   caracteres no numéricos en tiempo real.

**Si cambias las reglas del teléfono**, actualiza: `WhatsAppNumber`, `WhatsAppNumberTest`,
`RegisterRequest`, `ProfileUpdateRequest`, `x-ui.phone-input`, `phoneField` en `app.js` y los tests
de registro/perfil.

---

## 10. Frontend y UI

- **Blade + componentes anónimos.** No hay Livewire, Inertia, React ni Vue.
- Componentes disponibles:
  - `x-ui.*`: `alert`, `avatar`, `badge`, `button`, `checkbox`, `empty-state`, `filter-select`,
    `icon`, `input`, `pagination`, `password-input`, `phone-input`, `select`, `submit-button`,
    `textarea`.
  - `x-catalog.browser` (estado vacío del catálogo), `x-project-card`, `x-status-badge`,
    `x-layout.flash`, `x-legal.section`, `x-legal.toc`, `x-brand.wordmark`, `x-auth.point`,
    `x-profile.avatar-upload`, `x-profile.banner-upload`.
- `x-ui.avatar` acepta `src`: si llega una URL pinta `<img>` con `object-cover`; si no, cae a las
  iniciales. Los admins nunca reciben `src` porque `User::avatarUrl()` devuelve `null`.
- **Alpine** registra componentes en `resources/js/app.js`:
  `passwordField`, `phoneField`, `mobileNav`, `userMenu`, `shareLink`, `coverField`, `deleteConfirm`.
  Se usan con `x-data="nombre({...})"`. No agregues lógica JS inline si ya existe un componente.
  `coverField` sirve para portada, avatar y banner: valida tipo/tamaño en cliente, muestra vista previa
  y expone `removed`, `remove()` y `undo()` para quitar la imagen actual antes de guardar.
- **Tailwind 4** con `@theme` en `resources/css/app.css`:
  - fuente `Instrument Sans` (cargada por `laravel-vite-plugin/fonts` desde Bunny);
  - easings personalizados `--ease-out`, `--ease-in-out`, `--ease-drawer`;
  - tema claro forzado (`color-scheme: light`), sin modo oscuro;
  - `prefers-reduced-motion` respetado globalmente y con `motion-reduce:*`;
  - `[x-cloak]` para evitar flash de bindings sin inicializar.
- **Accesibilidad:** enlace "Saltar al contenido", `aria-*`, `focus-visible:ring-*`, `aria-current`,
  `role="menu"`, cierre con Escape y click-outside, contadores visibles en campos numéricos.
- **Layouts:** `layouts.public` (sitio público + dashboard) y `layouts.auth` (login/registro/reset).
  Ambos incluyen `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
- **Build:** si tocas CSS o JS, ejecuta `npm run build` (o `npm run dev` mientras desarrollas).
  `public/build` está ignorado por git, pero debe existir para que `@vite` funcione sin servidor Vite.
- **Iconos:** `x-ui.icon` con nombres definidos en el propio componente; no agregues librerías.

---

## 11. Rutas (28, todas web)

| Método | URI | Nombre | Acceso | Acción |
| --- | --- | --- | --- | --- |
| GET | `/` | `home` | Público | `CatalogController@index` |
| GET | `/catalog` | `catalog.index` | Público | `CatalogController@index` |
| GET | `/search` | `catalog.search` | Público | `CatalogController@index` |
| GET | `/projects/{project:slug}` | `projects.show` | Público / autor / admin | `CatalogController@show` |
| GET | `/terminos` | `terms` | Público | `Route::view('pages.terms')` |
| GET | `/privacidad` | `privacy` | Público | `Route::view('pages.privacy')` |
| GET/POST | `/login` | `login` | Invitado | `Auth\AuthenticatedSessionController` |
| GET/POST | `/register` | `register` | Invitado | `Auth\RegisteredUserController` |
| GET/POST | `/forgot-password` | `password.request` / `password.email` | Invitado | `Auth\PasswordResetLinkController` |
| GET/POST | `/reset-password/{token}` | `password.reset` / `password.store` | Invitado | `Auth\NewPasswordController` |
| POST | `/logout` | `logout` | Autenticado | `Auth\AuthenticatedSessionController@destroy` |
| GET | `/dashboard` | `dashboard` | Autenticado | `ProjectController@index` |
| GET | `/profile` | `profile.show` | Autenticado | `ProfileController@show` |
| GET | `/profile/edit` | `profile.edit` | Autenticado | `ProfileController@edit` |
| PUT | `/profile` | `profile.update` | Autenticado | `ProfileController@update` |
| PUT | `/profile/password` | `profile.password.update` | Autenticado | `ProfileController@updatePassword` |
| GET/POST | `/dashboard/projects/create`, `/dashboard/projects` | `projects.create`, `projects.store` | Autenticado | `ProjectController@create/store` |
| GET/PUT/DELETE | `/dashboard/projects/{project}/edit`, `/dashboard/projects/{project}` | `projects.edit`, `projects.update`, `projects.destroy` | Autor / admin | `ProjectController@edit/update/destroy` |
| GET | `/admin/users` | `admin.users.index` | Admin | `Admin\UserController@index` |
| PUT | `/admin/users/{user}/role` | `admin.users.role.update` | Admin | `Admin\UserController@updateRole` |
| DELETE | `/admin/users/{user}` | `admin.users.destroy` | Admin | `Admin\UserController@destroy` |

No existen rutas `api/*` (solo el health check `/up`). Los IDs de proyecto en el dashboard usan
**ID numérico**; la ficha pública usa **slug**.

---

## 12. Pruebas

- Configuración: `phpunit.xml` usa SQLite `:memory:`, `CACHE_STORE=array`, `SESSION_DRIVER=array`,
  `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`.
- Cada test de feature usa `RefreshDatabase`. `DashboardTest` usa `Storage::fake('public')`.
- Cobertura actual (91 tests):
  - `tests/Feature/PublicPortalTest.php`: páginas públicas, solo publicados, filtros por categoría y
    territorio, búsqueda, visibilidad de borradores/suspendidos, totales del home.
  - `tests/Feature/DashboardTest.php`: autenticación, aislamiento por usuario, creación con portada,
    validaciones, actualización sin reemplazar portada, anti-IDOR, soft delete.
  - `tests/Feature/ProfileTest.php`: resumen, actualización de datos/WhatsApp, email único,
    contraseña, subida de avatar/banner/diploma, reemplazo y borrado de archivos, rechazo de admin,
    validación de tipo/tamaño y limpieza al eliminar la cuenta.
  - `tests/Feature/Auth/*`: login (normalización, rate limit, logout), registro (carrera→centro,
    WhatsApp, términos), reset de contraseña.
  - `tests/Unit/WhatsAppNumberTest.php`: normalización, extracción nacional, validación, tipos no string.
- Comandos:

```bash
php artisan test
php artisan test --filter=PublicPortalTest
vendor/bin/pint
```

> **Entorno actual:** `C:\php\php.ini` ya tiene `fileinfo` y `pdo_sqlite` habilitadas, así que
> `php artisan test` y las subidas de imágenes funcionan sin banderas extra. **Reinicia `php artisan serve`
> después de tocar `php.ini`**: el proceso del servidor no recarga extensiones en caliente y responde
> `Unable to guess the MIME type as no guessers are available`. Si en otra máquina ves
> `could not find driver (sqlite)` o `Class "finfo" not found`, descomenta esas dos extensiones en
> `php.ini` (o usa el workaround temporal):
>
> ```bash
> php -d extension=pdo_sqlite -d extension=fileinfo vendor/bin/phpunit
> ```
>
> Estado de la suite en este entorno: **91 tests, 1 fallo preexistente**:
> `PublicPortalTest::test_public_pages_render_without_errors` espera el texto «Proyectos estudiantiles»,
> que el home actual no muestra (el catálogo dice «Emprendimientos publicados»). Es una expectativa
> obsoleta del test, no un fallo del catálogo.

**Al agregar funcionalidad, agrega tests de feature** siguiendo el patrón existente (crear catálogos
mínimos en `setUp`, usar factories, `assertSee` en español, `Storage::fake` para archivos).

---

## 13. Entorno local y comandos

### Instalación desde cero

```bash
composer install
npm install
copy .env.example .env        # Windows (bash: cp)
php artisan key:generate
# Crear la base `inatec-emprende` en MySQL/phpMyAdmin y ajustar .env
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Disponible en `http://localhost:8000`.

- `composer setup` ejecuta install, copia `.env`, `key:generate`, `migrate --force` y build,
  pero **no siembra** la base; ejecuta `php artisan db:seed` después si necesitas catálogos/demo.
- `php artisan migrate:fresh --seed` reinicia todo (destructivo).
- `php artisan pail` para logs en vivo.
- `npm run dev` para Vite en caliente; `npm run build` para producción.
- **Node en esta máquina:** instalado en modo portable en
  `%LOCALAPPDATA%\Programs\nodejs` (Node 24 LTS) y agregado al `PATH` del usuario. Si una terminal
  nueva no lo encuentra, cierra y vuelve a abrirla para que reciba el `PATH` actualizado.

### Credenciales demo (`UserSeeder`)

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Admin | `admin@inatec.edu.ni` | `password` |
| Estudiante | `estudiante@inatec.edu.ni` | `password` |

El estudiante demo queda en `CT-MN-001` (Centro Tecnológico de Managua) y WhatsApp `50588888888`.
**Nunca uses estas credenciales en un despliegue real.**

---

## 14. Deuda técnica y cabos sueltos (verificado)

Estas son limitaciones reales del código actual. **No las "arregles" por sorpresa**; si tu tarea las
toca, confírmalo con el usuario y actualiza README/AGENTS en el mismo cambio.

1. **Falta `resources/views/admin/`.** `Admin\UserController@index` devuelve `admin.users.index`,
   que no existe → `GET /admin/users` lanzará `ViewNotFound`. El resto del módulo (rutas, policy,
   `updateRole`, `destroy`) sí está implementado. Crear la vista es un trabajo pendiente natural.
2. **No hay UI de galería.** Existen `ProjectGallery`, la relación `gallery`, la tabla y la visualización
   en la ficha, pero no hay formulario, rutas ni controlador para subir/eliminar fotos.
3. **No hay acción de moderación.** `suspended` está soportado en el modelo, la migración, la policy y
   `ProjectUpdateRequest` (solo admin), pero no hay ruta ni pantalla específica para suspender/reactivar.
4. **No hay restauración de proyectos soft-deleted.** `ProjectPolicy@restore` y `forceDelete` existen,
   pero no hay rutas ni UI que las usen.
5. **No hay panel de administración ni layout admin.** Solo las rutas de usuarios.
6. **No hay API, notificaciones al autor, correo transaccional real ni jobs.** `MAIL_MAILER=log`; el
   único correo es el de restablecimiento de contraseña.
7. **`CLAUDE.md` está desactualizado.** Contiene el bootstrap de Laravel Boost que pide instalar
   `laravel/boost` y "reemplaza" estas instrucciones. **No ejecutes esa instalación** salvo petición
   explícita: modificaría dependencias y generaría guías nuevas.
8. **Documentación histórica inconsistente.** Versiones anteriores de `AGENTS.md` decían Laravel 11 /
   PHP 8.4. El estado real es Laravel 13.38 / PHP 8.5. Si encuentras otra referencia, corrige la fuente.
9. **Los `skills/` no son dominio del proyecto.** Son 15 guías de diseño/animación (filosofía de Emil
   Kowalski, Swift, Expo, etc.) para el CLI. No las uses como especificación funcional de INATEC.
10. **No hay paginación infinita ni AJAX.** Todo es server-side rendering con recarga completa.
11. **Las imágenes de perfil no se recortan ni recomprimen.** Se guarda el archivo tal como llega
    (validado por tipo y 2 MB) y el encuadre se resuelve con CSS (`object-cover`). Tampoco hay
    moderación ni borrado administrativo de avatares/banners; solo el propio estudiante o la baja
    de la cuenta los eliminan.

---

## 15. Recetas de cambio

### Agregar un filtro al catálogo

1. Lee el parámetro en `CatalogController@index` (`$request->integer()` o `queryString()`).
2. Agrega el `when(...)` correspondiente (usa `whereHas` si navega relaciones).
3. Inclúyelo en el array `filters`.
4. Actualiza `resources/views/catalog/_filters.blade.php` (y `_search` si aplica).
5. Agrega un test en `PublicPortalTest`.

### Agregar un campo a `projects`

1. Nueva migración (no edites una migración ya ejecutada).
2. Agrega el campo a `#[Fillable]` en `Project`.
3. Reglas, mensajes y saneo en `ProjectStoreRequest` y `ProjectUpdateRequest`.
4. Campo en `resources/views/projects/_form.blade.php` (y en `show` si se muestra).
5. Test de creación/validación en `DashboardTest`.
6. Si es una imagen, gestiona el archivo en `ProjectObserver` y `Storage::disk('public')`.

### Agregar un estado de proyecto

1. Constante `STATUS_*` en `Project`.
2. Migración para el `enum` (o cambia el enfoque a `string` si necesitas más flexibilidad).
3. Reglas en `ProjectStoreRequest`/`ProjectUpdateRequest`.
4. Lógica en `ProjectPolicy`, `Project::is*()`, badges (`x-status-badge`) y vistas.
5. Tests de visibilidad y autorización.

### Crear una pantalla de administración

1. Crea `resources/views/admin/...` reutilizando `layouts.public` (o un layout admin nuevo) y `x-ui.*`.
2. Agrega la ruta dentro del grupo `['auth', 'can:admin']` con prefijo `admin.`.
3. Autoriza con la policy correspondiente (`$this->authorize(...)`).
4. Si hay acciones destructivas, usa transacción y `forceDelete` solo cuando corresponda.
5. Test de acceso permitido/denegado.

### Cambiar reglas de WhatsApp

Ver §9.8: toca `WhatsAppNumber`, ambos FormRequests, el componente Blade, `app.js` y sus tests.

---

## 16. Estilo de código

- `declare(strict_types=1);` en **todos** los archivos PHP.
- Tipado estricto de parámetros y retornos; constantes tipadas (`public const string ...`,
  `public const array ...`).
- Atributos modernos de Eloquent en lugar de propiedades:
  `#[Fillable([...])]`, `#[Hidden([...])]`, `#[Table('...')]`, `#[UsePolicy(...)]`,
  `#[ObservedBy(...)]`, `#[Scope]`.
- Clases `final` cuando no se heredan (p. ej. `WhatsAppNumber`).
- Comentarios y docblocks en inglés; mensajes de usuario en español.
- Nombres de variables/métodos en `camelCase`; columnas en `snake_case`.
- Usa `match(true)` y arrow functions cuando el patrón ya existe en el archivo.
- Evita `abort()` genéricos: usa policies/`authorize` para 403 y route binding para 404.
- No uses `DB::raw` sin necesidad; el proyecto prefiere `selectRaw` acotado para métricas.
- No agregues dependencias de Composer/npm sin justificarlo.

---

## 17. Checklist antes de terminar un cambio

- [ ] `vendor/bin/pint` sin cambios pendientes.
- [ ] `php artisan test` (o la suite filtrada) pasa. Si falla por `pdo_sqlite`, documenta el motivo.
- [ ] `npm run build` si tocaste CSS/JS/Blade con clases nuevas.
- [ ] `php artisan migrate` si agregaste migración (y `--seed` si aplica).
- [ ] Rutas nuevas registradas y protegidas con el middleware/policy correctos.
- [ ] Sin N+1: revisa `with()`/`load()` en cualquier listado nuevo.
- [ ] Sin IDOR: un estudiante no puede acceder a recursos ajenos.
- [ ] Archivos: subida validada, nombre único, borrado físico cuando corresponde.
- [ ] Textos en español; código en inglés.
- [ ] `.env`, credenciales y archivos generados fuera del commit.
- [ ] Si cambió la arquitectura, actualiza `AGENTS.md` y `README.md`.

---

## 18. Índice de archivos clave

| Archivo | Por qué importa |
| --- | --- |
| `routes/web.php` | Única fuente de rutas y middleware; 28 rutas. |
| `app/Http/Controllers/CatalogController.php` | Catálogo, búsqueda y ficha; filtros y visibilidad. |
| `app/Http/Controllers/ProjectController.php` | Dashboard y CRUD; portada y soft delete. |
| `app/Http/Controllers/ProfileController.php` | Perfil, WhatsApp y contraseña. |
| `app/Http/Controllers/Admin/UserController.php` | Directorio, roles y baja en cascada. |
| `app/Http/Requests/ProjectStoreRequest.php` | Categorías, slug único, saneo XSS. |
| `app/Http/Requests/ProjectUpdateRequest.php` | Edición parcial y `suspended` solo admin. |
| `app/Http/Requests/ProfileUpdateRequest.php` | Normalización de identidad y WhatsApp. |
| `app/Http/Requests/Auth/RegisterRequest.php` | Registro y derivación centro←carrera. |
| `app/Http/Policies/ProjectPolicy.php` | Reglas anti-IDOR y visibilidad. |
| `app/Http/Policies/UserPolicy.php` | Roles, autopromoción y baja de cuentas. |
| `app/Models/Project.php` | Estados, scope `published`, `whatsapp_url`. |
| `app/Models/User.php` | Rol, perfil, proyectos y `avatarUrl()` (solo estudiantes). |
| `app/Models/UserProfile.php` | `whatsapp_display`, relaciones territoriales, `avatar_url`/`banner_url`. |
| `app/Observers/ProjectObserver.php` | Borrado físico de portadas y galería. |
| `app/Observers/ProfileObserver.php` | Borrado físico de avatar/banner/diploma al reemplazar o eliminar. |
| `app/Support/WhatsAppNumber.php` | Contrato canónico del teléfono. |
| `app/Providers/AppServiceProvider.php` | Gate `admin`. |
| `database/migrations/2026_10_24_*.php` | Esquema real y restricciones de integridad. |
| `database/seeders/DatabaseSeeder.php` | Orden de siembra de catálogos. |
| `resources/js/app.js` | Componentes Alpine y validaciones de cliente. |
| `resources/css/app.css` | Tailwind 4, tokens y accesibilidad. |
| `resources/views/layouts/public.blade.php` | Navegación, flash, estructura pública. |
| `tests/Feature/PublicPortalTest.php` | Contrato del catálogo y visibilidad. |
| `tests/Feature/DashboardTest.php` | Contrato del CRUD y anti-IDOR. |
| `phpunit.xml` | Entorno de pruebas (SQLite en memoria). |
| `README.md` | Documentación funcional y de instalación para humanos. |
