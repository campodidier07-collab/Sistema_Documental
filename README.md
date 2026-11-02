# Sistema Documental — Gestión de Semilleros de Investigación (SIGESI)

Sistema web para la gestión documental y el seguimiento integral de semilleros de investigación: registro de proyectos, control de participantes por rol, generación de reportes y flujo de aprobación de productos de investigación.

![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4.0-4E56A6?logo=livewire&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

## Índice

- [Descripción](#descripción)
- [Stack tecnológico](#stack-tecnológico)
- [Arquitectura](#arquitectura)
- [Instalación](#instalación)
- [Comandos disponibles](#comandos-disponibles)
- [Despliegue](#despliegue)
- [Autor](#autor)
- [Licencia](#licencia)

## Descripción

Sistema Documental centraliza la administración de semilleros de investigación: gestión de usuarios por rol, registro de proyectos y productos de investigación, control de acceso multi-sede (por centro de formación) y generación de reportes en PDF/Excel.

El sistema está organizado por rol de usuario, replicando la misma estructura en controladores, componentes Livewire y vistas para cada uno de los siguientes roles:

| Rol | Responsabilidad principal |
|---|---|
| `SuperAdmin` | Administración global: centros de formación, administradores del sistema y cualquier otro usuario |
| `Admin` (`administrador_sistema`) | Gestión de usuarios, catálogos y aprobación de productos Minciencias de un centro de formación |
| `DirectorSemilleros` | Creación de semilleros y asignación de líderes en su centro |
| `LiderSemillero` | Gestión operativa de un semillero: asigna líderes de proyecto y revisa evidencias |
| `LiderProyecto` | Ejecución de un proyecto: aprendices, evidencias y co-investigadores vinculados |
| `Coinvestigador` | Rol global (sin centro fijo): colabora en proyectos vinculados y gestiona sus propios productos Minciencias |

Un usuario puede tener, además de su rol principal, uno o varios **roles adicionales** y cambiar de "rol activo" desde el menú "Mis roles" — el acceso a cada módulo queda aislado al rol activo en cada momento.

> Los roles `AsesorSemillero`, `DirectorInvestigacion` e `InvestigadorAsociado` de un diseño anterior del sistema fueron eliminados y reemplazados por `LiderProyecto` y `Coinvestigador` en el rediseño de roles de agosto de 2026.

## Stack tecnológico

- **Backend:** Laravel 12 (PHP 8.2)
- **Frontend reactivo:** Livewire 4.0 + Flux UI 2.9
- **Estilos:** Tailwind CSS 4.0
- **Autenticación:** Laravel Fortify + Spatie Permission 6.24
- **Base de datos:** MySQL
- **Testing:** PHPUnit 11.5 (SQLite en memoria)
- **Reportes:** DomPDF (PDF) + PhpSpreadsheet (Excel)
- **Bundler:** Vite 7

## Arquitectura

El proyecto sigue una organización modular por rol:

```
app/Http/Controllers/{Rol}/
app/Livewire/{Rol}/
resources/views/{rol}/
```

El acceso a los datos es multi-tenant por centro de formación (`training_center_id`), controlado de forma centralizada en `app/Support/TrainingCenterAccess.php`. Toda consulta que liste usuarios o datos sensibles pasa por esta clase para evitar fugas de información entre centros.

## Instalación

```bash
git clone https://github.com/campodidier07-collab/Sistema_Documental.git
cd Sistema_Documental
composer setup      # instala dependencias, copia .env, genera key, migra y compila assets
```

O paso a paso:

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configura DB_* en .env antes de migrar
php artisan migrate
npm install
npm run build
```

## Comandos disponibles

```bash
composer dev             # Servidor + queue + Vite en paralelo
composer test            # PHPUnit + linting con Pint
composer lint            # Solo Laravel Pint
php artisan migrate      # Ejecutar migraciones
npm run dev              # Vite con hot reload
npm run build            # Build de producción
```

## Despliegue

El despliegue a producción (Hostinger) es automático mediante GitHub Actions al hacer push a `main`: instala dependencias, ejecuta migraciones y recompila cachés de configuración, rutas y vistas. Los workflows de tests y linting corren en cada push/PR contra `main`.

## Autor

- **Didier Puentes** ([@campodidier07-collab](https://github.com/campodidier07-collab)) — Desarrollador principal, mantenimiento y evolución del sistema.
