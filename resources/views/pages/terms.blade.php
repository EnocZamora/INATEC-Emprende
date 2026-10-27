@extends('layouts.public')

@section('title', 'Términos de uso')
@section('description', 'Condiciones de uso de INATEC Emprende: cuentas institucionales, publicación de proyectos, escaparate equitativo y contacto directo por WhatsApp.')

@section('content')
    @php
        $toc = [
            'objeto' => '01 · Objeto y alcance',
            'cuentas' => '02 · Cuentas institucionales',
            'publicacion' => '03 · Publicación de proyectos',
            'contenido' => '04 · Reglas de contenido',
            'equidad' => '05 · Escaparate equitativo',
            'whatsapp' => '06 · Contacto por WhatsApp',
            'propiedad' => '07 · Propiedad intelectual',
            'moderacion' => '08 · Moderación y suspensión',
            'disponibilidad' => '09 · Disponibilidad y cambios',
            'legislacion' => '10 · Legislación aplicable',
        ];
    @endphp

    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-12">
            <nav class="flex flex-wrap items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-700" aria-current="page">Términos de uso</span>
            </nav>

            <div class="mt-4 grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
                <div class="max-w-3xl">
                    <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Legal</p>

                    <h1 class="mt-2.5 text-[2rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.5rem]">
                        Términos de uso
                    </h1>

                    <p class="mt-3 text-[0.9375rem] leading-6 text-slate-600">
                        Las reglas básicas para publicar proyectos, usar el catálogo y contactar a sus autores. Están escritas para leerse, no para esconderse.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[0.6875rem] text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <x-ui.icon name="calendar" class="h-3.5 w-3.5 text-slate-400" />
                        Última actualización: octubre de 2026
                    </span>

                    <a href="{{ route('privacy') }}" class="font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
                        Política de privacidad
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-12">
        <div class="grid gap-10 lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-14">
            <x-legal.toc :items="$toc" other-label="Política de privacidad" :other-url="route('privacy')" />

            <article class="min-w-0 max-w-3xl">
                <div class="rounded-xl border border-blue-200/70 bg-blue-50/50 p-4 sm:p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-blue-800 uppercase">En resumen</p>

                    <ul class="mt-2.5 space-y-2 text-[0.8125rem] leading-5 text-blue-900/90">
                        <li class="flex gap-2">
                            <x-ui.icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Publicar es gratuito y ninguna posición del catálogo se compra.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-700" />
                            El contacto ocurre directamente por WhatsApp, fuera de esta plataforma.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Cada estudiante responde por la información e imágenes que publica.
                        </li>
                    </ul>
                </div>

                <div class="mt-8 space-y-8">
                    <x-legal.section id="objeto" number="01" title="Objeto y alcance">
                        <p>
                            <strong>INATEC Emprende</strong> es una plataforma digital institucional del Tecnológico Nacional (INATEC) para difundir los proyectos de las y los estudiantes de sus centros tecnológicos.
                        </p>
                        <p>
                            El portal es un escaparate, no un mercado: no procesa pagos, no intermedia contrataciones y no garantiza acuerdos comerciales entre visitantes y estudiantes.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="cuentas" number="02" title="Cuentas institucionales">
                        <p>
                            El acceso está reservado a estudiantes y personal de INATEC. Al crear una cuenta, la persona declara que su vínculo con la institución es real y que los datos de su ficha son correctos.
                        </p>
                        <p>
                            La cuenta es personal. No compartas tu contraseña ni permitas que otra persona publique desde tu perfil. El centro y la carrera se toman de los registros institucionales y pueden ser corregidos por la coordinación correspondiente.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="publicacion" number="03" title="Publicación de proyectos">
                        <p>
                            Cada estudiante administra sus propios proyectos. Puede guardarlos como borrador o publicarlos en el catálogo. Solo el autor y el personal autorizado pueden editarlos, suspenderlos o retirarlos.
                        </p>
                        <p>
                            Al publicar, el proyecto puede aparecer en la portada, en el catálogo, en los resultados de búsqueda y en las páginas de especialidad. Publicar no altera el orden cronológico del catálogo.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="contenido" number="04" title="Reglas de contenido">
                        <p>No se permite publicar contenido que:</p>
                        <ul>
                            <li>Sea falso, engañoso o atribuya a la institución algo que no ha dicho.</li>
                            <li>Incluya plagio, marcas registradas o material de terceros sin autorización.</li>
                            <li>Contenga datos personales de otras personas sin su consentimiento.</li>
                            <li>Sea discriminatorio, violento, sexual, ilegal o acose a alguien.</li>
                            <li>Prometa inversiones, pagos, préstamos o esquemas comerciales engañosos.</li>
                            <li>Incluya enlaces maliciosos, spam o software no solicitado.</li>
                        </ul>
                        <p>
                            La descripción debe corresponder a un proyecto técnico real y reflejar su estado actual. Las imágenes deben ser del proyecto o contar con permiso para usarlas.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="equidad" number="05" title="Escaparate equitativo">
                        <p>
                            El catálogo se ordena por fecha de publicación y por los filtros que elige cada visitante: especialidad, departamento, centro o carrera. No existen anuncios, posiciones pagadas ni algoritmos de promoción.
                        </p>
                        <p>
                            <strong>Ningún pago, favor o convenio altera la visibilidad de un proyecto.</strong> Esa regla es parte de la identidad del portal y no está sujeta a excepciones comerciales.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="whatsapp" number="06" title="Contacto por WhatsApp">
                        <p>
                            El botón de contacto abre WhatsApp —un servicio de Meta— con un mensaje prellenado que incluye el nombre del proyecto. El número lo proporciona el estudiante en su ficha institucional y se muestra públicamente en los proyectos que decide publicar.
                        </p>
                        <p>
                            La conversación ocurre fuera de la plataforma y bajo la responsabilidad de quienes participan. INATEC no recibe copia de los mensajes ni participa en los acuerdos que puedan surgir.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="propiedad" number="07" title="Propiedad intelectual">
                        <p>
                            El estudiante conserva la autoría y los derechos sobre su proyecto. Al publicarlo, autoriza a INATEC a mostrarlo en esta plataforma con fines institucionales y educativos, sin uso comercial.
                        </p>
                        <p>
                            Las marcas, logotipos y materiales de INATEC no pueden utilizarse para fines ajenos a la institución ni sugerir un respaldo que no existe.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="moderacion" number="08" title="Moderación y suspensión">
                        <p>
                            La coordinación del centro o el equipo administrador puede suspender un proyecto si incumple estas reglas, si existe una reclamación o si la información es inconsistente. Un proyecto suspendido deja de aparecer en el catálogo, pero su autor conserva acceso para corregirlo.
                        </p>
                        <p>
                            Los casos graves pueden derivarse a las instancias institucionales correspondientes. La suspensión no elimina automáticamente el proyecto ni sus archivos.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="disponibilidad" number="09" title="Disponibilidad y cambios">
                        <p>
                            El portal puede suspenderse por mantenimiento o por causas técnicas. INATEC puede actualizar funciones y estas condiciones para mejorar el servicio o cumplir obligaciones institucionales.
                        </p>
                        <p>
                            Los cambios sustanciales se comunicarán en la plataforma. El uso continuado después de una actualización implica la aceptación de la nueva versión.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="legislacion" number="10" title="Legislación aplicable">
                        <p>
                            Estas condiciones se rigen por la legislación de la República de Nicaragua. Cualquier controversia se atenderá primero con la coordinación del centro tecnológico correspondiente y, si persiste, por las instancias competentes de INATEC.
                        </p>
                    </x-legal.section>
                </div>

                <div class="mt-10 rounded-xl border border-slate-200/80 bg-white p-5">
                    <p class="text-[0.8125rem] font-semibold text-slate-900">¿Necesitas aclarar algo?</p>
                    <p class="mt-1.5 text-[0.8125rem] leading-6 text-slate-500">
                        Consulta con la coordinación de tu centro tecnológico. Este documento resume las condiciones de uso del portal y no sustituye las normas institucionales de INATEC.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-4 text-[0.75rem] font-medium">
                        <a href="{{ route('privacy') }}" class="inline-flex items-center gap-1 text-blue-700 underline-offset-4 hover:text-blue-800 hover:underline">
                            Leer la política de privacidad
                            <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>

                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1 text-slate-600 underline-offset-4 hover:text-slate-900 hover:underline">
                            Volver al catálogo
                            <x-ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection
