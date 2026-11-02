<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EstadoEnum;
use App\Enums\EstadoRevisionEnum;
use App\Enums\TipoDocumentoEnum;
use App\Enums\TipoEvidenciaEnum;
use App\Enums\TipoProyectoOrigenEnum;
use App\Models\EntityPosition;
use App\Models\GrupoInvestigacion;
use App\Models\InvestigationType;
use App\Models\LinkageType;
use App\Models\MincienciasProduct;
use App\Models\MincienciasProductFile;
use App\Models\MincienciasSubcategory;
use App\Models\MincienciasTypology;
use App\Models\Person;
use App\Models\Project;
use App\Models\ProjectAuthor;
use App\Models\ProjectEvidence;
use App\Models\ProjectLearner;
use App\Models\ProjectModality;
use App\Models\ResearchLine;
use App\Models\Seedling;
use App\Models\TechnologicalLine;
use App\Models\ThematicArea;
use App\Models\TrainingCenter;
use App\Models\TrainingProgram;
use App\Models\User;
use App\Support\TrainingCenterAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Siembra datos de prueba realistas para una experiencia completa del sistema:
     * - Usuarios con todos los roles principales y contraseña compartida Password123!
     * - Grupos de investigación, líneas vinculadas y directores
     * - Semilleros, líderes de semillero y miembros
     * - Proyectos con aprendices, líderes de proyecto y autores
     * - Evidencias en distintas etapas (Formulación, Desarrollo, Producto Final)
     * - Productos Minciencias con estados de aprobación
     */
    public function run(): void
    {
        $this->command->info('Iniciando siembra de datos de demostración...');

        $centro = TrainingCenter::first();
        if (! $centro) {
            $this->command->error('No hay centros de formación creados. Ejecuta TrainingCenterSeeder primero.');
            return;
        }

        $position = EntityPosition::where('training_center_id', $centro->id)->first()
            ?? EntityPosition::first();

        $linkage = LinkageType::where('training_center_id', $centro->id)->first()
            ?? LinkageType::first();

        $programAdso = TrainingProgram::where('training_center_id', $centro->id)->first()
            ?? TrainingProgram::first();

        $programAdmin = TrainingProgram::where('training_center_id', $centro->id)->skip(1)->first()
            ?? $programAdso;

        $researchLine = ResearchLine::where('training_center_id', $centro->id)->first()
            ?? ResearchLine::first();

        $techLine = TechnologicalLine::where('training_center_id', $centro->id)->first()
            ?? TechnologicalLine::first();

        $thematicArea = ThematicArea::where('training_center_id', $centro->id)->first()
            ?? ThematicArea::first();

        $modality = ProjectModality::where('training_center_id', $centro->id)->first()
            ?? ProjectModality::first();

        $invType = InvestigationType::where('training_center_id', $centro->id)->first()
            ?? InvestigationType::first();

        $passwordHash = Hash::make('Password123!');

        // ─────────────────────────────────────────────────────────────
        // 1. USUARIOS POR ROL
        // ─────────────────────────────────────────────────────────────

        $usersData = [
            [
                'email' => 'admin@sena.edu.co',
                'documento' => '1001001001',
                'rol' => 'administrador_sistema',
                'p_nom' => 'Carlos',
                's_nom' => 'Mario',
                'p_ape' => 'Restrepo',
                's_ape' => 'Gómez',
                'genero' => 'masculino',
            ],
            [
                'email' => 'dir.semilleros@sena.edu.co',
                'documento' => '1002002002',
                'rol' => 'director_semilleros',
                'p_nom' => 'Ana',
                's_nom' => 'María',
                'p_ape' => 'Valencia',
                's_ape' => 'Morales',
                'genero' => 'femenino',
            ],
            [
                'email' => 'dir.grupo@sena.edu.co',
                'documento' => '1003003003',
                'rol' => 'director_grupo_investigacion',
                'p_nom' => 'Mauricio',
                's_nom' => '',
                'p_ape' => 'Gómez',
                's_ape' => 'Duque',
                'genero' => 'masculino',
            ],
            [
                'email' => 'lider.semillero@sena.edu.co',
                'documento' => '1004004004',
                'rol' => 'lider_semillero',
                'p_nom' => 'Diana',
                's_nom' => 'Carolina',
                'p_ape' => 'Peña',
                's_ape' => 'Castro',
                'genero' => 'femenino',
            ],
            [
                'email' => 'lider.proyecto@sena.edu.co',
                'documento' => '1005005005',
                'rol' => 'lider_proyecto',
                'p_nom' => 'Felipe',
                's_nom' => '',
                'p_ape' => 'Morales',
                's_ape' => 'Ospina',
                'genero' => 'masculino',
            ],
            [
                'email' => 'lider.proyecto2@sena.edu.co',
                'documento' => '1005005006',
                'rol' => 'lider_proyecto',
                'p_nom' => 'Marcela',
                's_nom' => '',
                'p_ape' => 'Ríos',
                's_ape' => 'Vargas',
                'genero' => 'femenino',
            ],
            [
                'email' => 'coinvestigador.sdi@sena.edu.co',
                'documento' => '1006006006',
                'rol' => 'co_investigador_sdi',
                'p_nom' => 'Andrés',
                's_nom' => 'Camilo',
                'p_ape' => 'Rojas',
                's_ape' => 'Beltrán',
                'genero' => 'masculino',
            ],
            [
                'email' => 'coinvestigador.gdi@sena.edu.co',
                'documento' => '1007007007',
                'rol' => 'co_investigador_gdi',
                'p_nom' => 'Laura',
                's_nom' => 'Sofía',
                'p_ape' => 'Mendoza',
                's_ape' => 'Quintero',
                'genero' => 'femenino',
            ],
        ];

        $users = [];

        foreach ($usersData as $ud) {
            $user = User::updateOrCreate(
                ['email' => $ud['email']],
                [
                    'training_center_id' => $centro->id,
                    'tipo_documento' => TipoDocumentoEnum::CedulaCiudadana,
                    'numero_documento' => $ud['documento'],
                    'password' => $passwordHash,
                    'estado' => EstadoEnum::Activo,
                    'primary_role_name' => $ud['rol'],
                    'email_verified_at' => now(),
                ]
            );

            // Perfil Person
            Person::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'entity_position_id' => $position->id,
                    'linkage_type_id' => $linkage->id,
                    'training_program_id' => $programAdso->id,
                    'primer_nombre' => $ud['p_nom'],
                    'segundo_nombre' => $ud['s_nom'] ?: null,
                    'primer_apellido' => $ud['p_ape'],
                    'segundo_apellido' => $ud['s_ape'],
                    'genero' => $ud['genero'],
                    'celular' => 3105550000 + (int) substr($ud['documento'], -4),
                    'telefono' => 7220000 + (int) substr($ud['documento'], -4),
                    'eps' => 'Sura EPS',
                    'email_institucional' => $ud['email'],
                ]
            );

            $user->syncRoles([$ud['rol']]);
            $users[$ud['rol'] . ($ud['documento'] === '1005005006' ? '_2' : '')] = $user;
        }

        $this->command->info('✓ Usuarios con roles creados correctamente.');

        // ─────────────────────────────────────────────────────────────
        // 2. GRUPO DE INVESTIGACIÓN (GIDE)
        // ─────────────────────────────────────────────────────────────

        $grupo = GrupoInvestigacion::updateOrCreate(
            ['codigo' => 'COL0019283'],
            [
                'training_center_id' => $centro->id,
                'creator_id' => $users['administrador_sistema']->id,
                'director_id' => $users['director_grupo_investigacion']->id,
                'nombre' => 'GIDE — Grupo de Investigación, Desarrollo e Innovación Empresarial',
                'logo' => 'grupos/default.png',
                'descripcion' => 'Grupo enfocado en la generación de nuevo conocimiento, desarrollo tecnológico e innovación aplicada a la formación integral y al sector productivo regional.',
                'estado' => 'activo',
            ]
        );

        if ($researchLine) {
            $grupo->lineasInvestigacion()->syncWithoutDetaching([$researchLine->id]);
        }

        // Vincular al co_investigador_gdi con su grupo
        $users['co_investigador_gdi']->update(['grupo_investigacion_id' => $grupo->id]);

        $this->command->info('✓ Grupo de investigación GIDE configurado.');

        // ─────────────────────────────────────────────────────────────
        // 3. SEMILLEROS DE INVESTIGACIÓN (SISDEV e INNOVA-AGRO)
        // ─────────────────────────────────────────────────────────────

        $semilleroSisdev = Seedling::updateOrCreate(
            ['codigo' => 'SEM-SISDEV-01'],
            [
                'training_center_id' => $centro->id,
                'creator_id' => $users['director_semilleros']->id,
                'leader_id' => $users['lider_semillero']->id,
                'nombre' => 'SISDEV — Semillero de Innovación y Desarrollo de Software',
                'logo' => 'seedlings/default-sisdev.png',
                'descripcion' => 'Semillero dedicado a la investigación formativa en ingeniería de software, arquitecturas cloud, microservicios e inteligencia artificial aplicada.',
                'estado' => EstadoEnum::Activo,
            ]
        );

        $semilleroAgro = Seedling::updateOrCreate(
            ['codigo' => 'SEM-AGRO-02'],
            [
                'training_center_id' => $centro->id,
                'creator_id' => $users['director_semilleros']->id,
                'leader_id' => null,
                'nombre' => 'INNOVA-AGRO — Tecnologías Aplicadas a la Agroindustria',
                'logo' => 'seedlings/default-agro.png',
                'descripcion' => 'Investigación en automatización de cultivos, agricultura de precisión y sensores IoT.',
                'estado' => EstadoEnum::Activo,
            ]
        );

        // Miembros del semillero SISDEV
        $semilleroSisdev->members()->syncWithoutDetaching([
            $users['lider_proyecto']->id,
            $users['lider_proyecto_2']->id,
            $users['co_investigador_sdi']->id,
        ]);

        $this->command->info('✓ Semilleros SISDEV e INNOVA-AGRO creados.');

        // ─────────────────────────────────────────────────────────────
        // 4. PROYECTOS DE INVESTIGACIÓN
        // ─────────────────────────────────────────────────────────────

        $proyecto1 = Project::updateOrCreate(
            ['nombre' => 'Sistema Inteligente de Gestión Documental y Semilleros SENA'],
            [
                'project_creator_id' => $users['lider_proyecto']->id,
                'seedling_id' => $semilleroSisdev->id,
                'lider_proyecto_user_id' => $users['lider_proyecto']->id,
                'research_line_id' => $researchLine->id,
                'technological_line_id' => $techLine?->id,
                'thematic_area_id' => $thematicArea?->id,
                'project_modality_id' => $modality?->id,
                'investigation_type_id' => $invType?->id,
                'descripcion' => 'Desarrollo de una plataforma web modular para la trazabilidad, gestión de evidencias y control de productos de investigación en centros de formación SENA.',
                'fecha_inicio' => now()->subMonths(6)->toDateString(),
                'fecha_fin' => now()->addMonths(4)->toDateString(),
                'estado' => EstadoEnum::Activo,
                'vinculacion_macro_proyecto' => false,
                'tipo_financiacion' => 'capacidad_instalada',
                'tipo_proyecto_origen' => TipoProyectoOrigenEnum::Semilleros,
            ]
        );

        // Autores del Proyecto 1
        ProjectAuthor::updateOrCreate(
            ['project_id' => $proyecto1->id, 'user_id' => $users['lider_proyecto']->id],
            ['activo' => true]
        );
        ProjectAuthor::updateOrCreate(
            ['project_id' => $proyecto1->id, 'user_id' => $users['co_investigador_sdi']->id],
            ['activo' => true]
        );

        $proyecto2 = Project::updateOrCreate(
            ['nombre' => 'Plataforma IoT de Monitoreo Bioclimático en Ambientes de Formación'],
            [
                'project_creator_id' => $users['lider_proyecto_2']->id,
                'seedling_id' => $semilleroSisdev->id,
                'lider_proyecto_user_id' => $users['lider_proyecto_2']->id,
                'research_line_id' => $researchLine->id,
                'technological_line_id' => $techLine?->id,
                'thematic_area_id' => $thematicArea?->id,
                'project_modality_id' => $modality?->id,
                'investigation_type_id' => $invType?->id,
                'descripcion' => 'Red de sensores inalámbricos de bajo costo para medición en tiempo real de temperatura, humedad y calidad de aire en ambientes académicos.',
                'fecha_inicio' => now()->subMonths(4)->toDateString(),
                'fecha_fin' => now()->addMonths(2)->toDateString(),
                'estado' => EstadoEnum::Activo,
                'vinculacion_macro_proyecto' => false,
                'tipo_financiacion' => 'capacidad_instalada',
                'tipo_proyecto_origen' => TipoProyectoOrigenEnum::Semilleros,
            ]
        );

        ProjectAuthor::updateOrCreate(
            ['project_id' => $proyecto2->id, 'user_id' => $users['lider_proyecto_2']->id],
            ['activo' => true]
        );

        $this->command->info('✓ Proyectos de investigación registrados.');

        // ─────────────────────────────────────────────────────────────
        // 5. APRENDICES VINCULADOS A PROYECTOS
        // ─────────────────────────────────────────────────────────────

        $aprendicesP1 = [
            [
                'nombre' => 'Sebastián Pardo Gómez',
                'doc' => '1098234001',
                'ficha' => '2824915',
                'email' => 'spardo@soy.sena.edu.co',
                'prog_id' => $programAdso->id,
            ],
            [
                'nombre' => 'Valentina Torres Ruiz',
                'doc' => '1098234002',
                'ficha' => '2824915',
                'email' => 'vtorres@soy.sena.edu.co',
                'prog_id' => $programAdso->id,
            ],
            [
                'nombre' => 'Camilo Andrés Ortiz Castro',
                'doc' => '1098234003',
                'ficha' => '2824915',
                'email' => 'cortiz@soy.sena.edu.co',
                'prog_id' => $programAdso->id,
            ],
        ];

        foreach ($aprendicesP1 as $ap) {
            ProjectLearner::updateOrCreate(
                ['project_id' => $proyecto1->id, 'numero_documento' => $ap['doc']],
                [
                    'created_by_user_id' => $users['lider_proyecto']->id,
                    'nombre_completo' => $ap['nombre'],
                    'ficha' => $ap['ficha'],
                    'telefono' => 3124567890,
                    'email' => $ap['email'],
                    'training_program_id' => $ap['prog_id'],
                ]
            );
        }

        $aprendicesP2 = [
            [
                'nombre' => 'Daniela Castro Silva',
                'doc' => '1098234004',
                'ficha' => '2718290',
                'email' => 'dcastro@soy.sena.edu.co',
                'prog_id' => $programAdmin->id,
            ],
            [
                'nombre' => 'Juan José Henao Morales',
                'doc' => '1098234005',
                'ficha' => '2718290',
                'email' => 'jhenao@soy.sena.edu.co',
                'prog_id' => $programAdmin->id,
            ],
        ];

        foreach ($aprendicesP2 as $ap) {
            ProjectLearner::updateOrCreate(
                ['project_id' => $proyecto2->id, 'numero_documento' => $ap['doc']],
                [
                    'created_by_user_id' => $users['lider_proyecto_2']->id,
                    'nombre_completo' => $ap['nombre'],
                    'ficha' => $ap['ficha'],
                    'telefono' => 3119876543,
                    'email' => $ap['email'],
                    'training_program_id' => $ap['prog_id'],
                ]
            );
        }

        $this->command->info('✓ Aprendices vinculados a los proyectos.');

        // ─────────────────────────────────────────────────────────────
        // 6. EVIDENCIAS DE PROYECTOS (Flujo de aprobación)
        // ─────────────────────────────────────────────────────────────

        // Evidencia 1: Formulación (Aprobada por Líder de Semillero)
        ProjectEvidence::updateOrCreate(
            ['project_id' => $proyecto1->id, 'nombre' => 'Documento de Formulación y Objetivos'],
            [
                'tipo' => TipoEvidenciaEnum::Formulacion,
                'uploaded_by' => $users['lider_proyecto']->id,
                'archivo' => 'evidencias/formulacion_sgd.pdf',
                'url_archivo' => 'evidencias/formulacion_sgd.pdf',
                'descripcion' => 'Definición de alcance, metodología y cronograma del proyecto de gestión documental.',
                'estado_revision_lider' => EstadoRevisionEnum::Aprobado,
                'observacion_lider' => 'Objetivos claros, alineados a los lineamientos del semillero SISDEV.',
                'revisado_lider_por' => $users['lider_semillero']->id,
                'revisado_lider_at' => now()->subMonths(5),
            ]
        );

        // Evidencia 2: Desarrollo (En proceso continuo)
        ProjectEvidence::updateOrCreate(
            ['project_id' => $proyecto1->id, 'nombre' => 'Diseño de Arquitectura y Modelo Relacional'],
            [
                'tipo' => TipoEvidenciaEnum::Desarrollo,
                'uploaded_by' => $users['lider_proyecto']->id,
                'archivo' => 'evidencias/arquitectura_c4.pdf',
                'url_archivo' => 'evidencias/arquitectura_c4.pdf',
                'descripcion' => 'Diagramas de arquitectura C4, especificación de componentes y modelo entidad-relación.',
            ]
        );

        // Evidencia 3: Producto Final (Aprobado por Líder y Director)
        ProjectEvidence::updateOrCreate(
            ['project_id' => $proyecto1->id, 'nombre' => 'Software SGD y Manual Técnico v1.0'],
            [
                'tipo' => TipoEvidenciaEnum::ProductoFinal,
                'uploaded_by' => $users['lider_proyecto']->id,
                'archivo' => 'evidencias/software_manual_v1.zip',
                'url_archivo' => 'evidencias/software_manual_v1.zip',
                'descripcion' => 'Paquete completo de release v1.0 con manual de usuario y guía de despliegue.',
                'estado_revision_lider' => EstadoRevisionEnum::Aprobado,
                'observacion_lider' => 'Validado técnica y funcionalmente en el semillero.',
                'revisado_lider_por' => $users['lider_semillero']->id,
                'revisado_lider_at' => now()->subMonths(1),
                'estado_revision_director' => EstadoRevisionEnum::Aprobado,
                'observacion_director' => 'Excelente producto de investigación aplicada para el centro de formación.',
                'revisado_director_por' => $users['director_semilleros']->id,
                'revisado_director_at' => now()->subWeeks(2),
            ]
        );

        $this->command->info('✓ Evidencias y flujo de aprobación configurados.');

        // ─────────────────────────────────────────────────────────────
        // 7. PRODUCTOS MINCIENCIAS (GIDE)
        // ─────────────────────────────────────────────────────────────

        $productoMin1 = MincienciasProduct::updateOrCreate(
            ['nombre' => 'Artículo: Modelos de Trazabilidad en Semilleros de Investigación SENA'],
            [
                'user_id' => $users['co_investigador_gdi']->id,
                'training_center_id' => $centro->id,
                'grupo_investigacion_id' => $grupo->id,
                'research_line_id' => $researchLine->id,
                'technological_line_id' => $techLine?->id,
                'thematic_area_id' => $thematicArea?->id,
                'project_modality_id' => $modality?->id,
                'investigation_type_id' => $invType?->id,
                'descripcion' => 'Artículo científico sobre metodologías de trazabilidad académica en educación técnica y tecnológica.',
                'fecha_inicio' => now()->subMonths(5)->toDateString(),
                'fecha_fin' => now()->subMonth()->toDateString(),
                'estado' => EstadoEnum::Activo,
                'estado_revision' => EstadoRevisionEnum::Aprobado,
                'observacion_admin' => 'Aprobado por el director de grupo para categorización Minciencias.',
                'revisado_por' => $users['director_grupo_investigacion']->id,
                'revisado_at' => now()->subWeeks(3),
            ]
        );

        MincienciasProductFile::updateOrCreate(
            ['minciencias_product_id' => $productoMin1->id],
            [
                'archivo' => 'minciencias/articulo_trazabilidad.pdf',
                'nombre_original' => 'articulo_trazabilidad_sena_2026.pdf',
                'url_archivo' => 'minciencias/articulo_trazabilidad.pdf',
                'descripcion' => 'Texto completo del artículo y carta de aceptación.',
                'uploaded_by' => $users['co_investigador_gdi']->id,
            ]
        );

        $productoMin2 = MincienciasProduct::updateOrCreate(
            ['nombre' => 'Software Registrado: Plataforma SGD de Gestión Documental'],
            [
                'user_id' => $users['co_investigador_gdi']->id,
                'training_center_id' => $centro->id,
                'grupo_investigacion_id' => $grupo->id,
                'research_line_id' => $researchLine->id,
                'technological_line_id' => $techLine?->id,
                'thematic_area_id' => $thematicArea?->id,
                'project_modality_id' => $modality?->id,
                'investigation_type_id' => $invType?->id,
                'descripcion' => 'Soporte lógico desarrollado para la gestión de semilleros y grupos de investigación institucional.',
                'fecha_inicio' => now()->subMonths(3)->toDateString(),
                'fecha_fin' => now()->toDateString(),
                'estado' => EstadoEnum::Activo,
                'estado_revision' => EstadoRevisionEnum::Pendiente,
                'observacion_admin' => null,
            ]
        );

        MincienciasProductFile::updateOrCreate(
            ['minciencias_product_id' => $productoMin2->id],
            [
                'archivo' => 'minciencias/soporte_logico_sgd.pdf',
                'nombre_original' => 'registro_soporte_logico_sgd.pdf',
                'url_archivo' => 'minciencias/soporte_logico_sgd.pdf',
                'descripcion' => 'Certificado de registro y manual de usuario.',
                'uploaded_by' => $users['co_investigador_gdi']->id,
            ]
        );

        $this->command->info('✓ Productos Minciencias creados con éxito.');
        $this->command->info('🎉 Datos de demostración listos para su uso.');
    }
}
