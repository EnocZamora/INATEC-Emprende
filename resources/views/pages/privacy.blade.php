@extends('layouts.public')

@section('title', 'Política de privacidad')
@section('description', 'Qué datos recopila INATEC Emprende, para qué se usan, qué información es pública y cómo ejercer tus derechos sobre tus datos personales.')

@section('content')
    @php
        $toc = [
            'responsable' => '01 · Responsable',
            'datos' => '02 · Datos que recopilamos',
            'finalidad' => '03 · Para qué los usamos',
            'publico' => '04 · Qué información es pública',
            'whatsapp' => '05 · WhatsApp y servicios externos',
            'cookies' => '06 · Cookies y almacenamiento',
            'conservacion' => '07 · Conservación',
            'seguridad' => '08 · Seguridad',
            'derechos' => '09 · Tus derechos',
            'cambios' => '10 · Cambios en esta política',
        ];
    @endphp

    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-12">
            <nav class="flex flex-wrap items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-700" aria-current="page">Política de privacidad</span>
            </nav>

            <div class="mt-4 grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
                <div class="max-w-3xl">
                    <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Legal</p>

                    <h1 class="mt-2.5 text-[2rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.5rem]">
                        Política de privacidad
                    </h1>

                    <p class="mt-3 text-[0.9375rem] leading-6 text-slate-600">
                        Qué datos necesita el portal, por qué los necesita y qué puedes decidir sobre ellos. Sin letra pequeña escondida.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[0.6875rem] text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <x-ui.icon name="calendar" class="h-3.5 w-3.5 text-slate-400" />
                        Última actualización: octubre de 2026
                    </span>

                    <a href="{{ route('terms') }}" class="font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
                        Términos de uso
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-12">
        <div class="grid gap-10 lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-14">
            <x-legal.toc :items="$toc" other-label="Términos de uso" :other-url="route('terms')" />

            <article class="min-w-0 max-w-3xl">
                <div class="rounded-xl border border-blue-200/70 bg-blue-50/50 p-4 sm:p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-blue-800 uppercase">En resumen</p>

                    <ul class="mt-2.5 space-y-2 text-[0.8125rem] leading-5 text-blue-900/90">
                        <li class="flex gap-2">
                            <x-ui.icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Solo pedimos los datos necesarios para tu ficha y tus proyectos.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Tu nombre, centro, carrera y WhatsApp son visibles cuando publicas.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-700" />
                            No usamos cookies publicitarias ni rastreadores de terceros.
                        </li>
                    </ul>
                </div>

                <div class="mt-8 space-y-8">
                    <x-legal.section id="responsable" number="01" title="Responsable del tratamiento">
                        <p>
                            El portal es operado por el Tecnológico Nacional (INATEC) a través de los centros tecnológicos. La coordinación de tu centro es el primer punto de contacto para cualquier consulta sobre datos personales.
                        </p>
                        <p>
                            La plataforma funciona con almacenamiento local del portal y no delega el tratamiento de datos a servicios publicitarios.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="datos" number="02" title="Datos que recopilamos">
                        <p>Para crear una cuenta y publicar proyectos solicitamos:</p>
                        <ul>
                            <li>Nombre completo y correo electrónico.</li>
                            <li>Contraseña, almacenada de forma cifrada e irreversible.</li>
                            <li>Centro tecnológico, carrera y número de WhatsApp.</li>
                            <li>Biografía breve, si decides completarla.</li>
                            <li>Contenido de tus proyectos: título, descripción, categoría, portada y galería.</li>
                        </ul>
                        <p>
                            También registramos datos técnicos de sesión necesarios para mantener la seguridad del portal. <strong>No solicitamos documentos de identidad, datos de salud ni datos financieros.</strong>
                        </p>
                    </x-legal.section>

                    <x-legal.section id="finalidad" number="03" title="Para qué los usamos">
                        <ul>
                            <li>Crear y administrar tu cuenta institucional.</li>
                            <li>Mostrar tus proyectos en el catálogo público.</li>
                            <li>Permitir que las personas interesadas te contacten por WhatsApp.</li>
                            <li>Moderar contenido y atender reclamaciones.</li>
                            <li>Mantener la seguridad del servicio y prevenir abusos.</li>
                        </ul>
                        <p>
                            No usamos tus datos para publicidad, perfilado comercial ni venta a terceros. Tampoco elaboramos estadísticas que identifiquen a una persona concreta.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="publico" number="04" title="Qué información es pública">
                        <p>
                            Cuando publicas un proyecto, se muestran públicamente tu nombre, centro, carrera, biografía (si la escribiste), número de WhatsApp y el contenido del proyecto, incluida su portada y galería.
                        </p>
                        <p>
                            Tu correo electrónico y tu contraseña nunca se publican. Los borradores y los proyectos suspendidos solo son visibles para ti y para el personal autorizado.
                        </p>
                        <p>
                            <strong>Publicar es una decisión.</strong> Si no quieres que tu nombre o tu número sean visibles, puedes mantener el proyecto como borrador o solicitar la revisión de tu ficha a la coordinación de tu centro.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="whatsapp" number="05" title="WhatsApp y servicios externos">
                        <p>
                            El botón de contacto abre WhatsApp, un servicio de Meta, con un mensaje prellenado. Al continuar, la conversación se rige por la política de privacidad de WhatsApp y queda fuera del control de INATEC.
                        </p>
                        <p>
                            El portal no recibe copia de los mensajes, los archivos ni los metadatos de esa conversación.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="cookies" number="06" title="Cookies y almacenamiento local">
                        <p>
                            Usamos únicamente cookies necesarias para el funcionamiento del portal: la cookie de sesión, el token de seguridad de los formularios y, si eliges «mantener la sesión iniciada», una cookie de recordatorio.
                        </p>
                        <p>
                            No utilizamos Google Analytics, píxeles publicitarios, cookies de redes sociales ni rastreadores de terceros. Las imágenes y los archivos se guardan en el almacenamiento local del portal.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="conservacion" number="07" title="Conservación de los datos">
                        <p>
                            Conservamos los datos de tu cuenta mientras esté activa y los proyectos mientras no los elimines. Los borradores y proyectos suspendidos permanecen visibles solo para ti y el personal autorizado.
                        </p>
                        <p>
                            Al eliminar un proyecto, este deja de aparecer en el portal. El registro y las imágenes pueden conservarse internamente para una posible restauración institucional; puedes solicitar su eliminación definitiva a la coordinación de tu centro.
                        </p>
                        <p>
                            Los datos de sesión caducan al cerrar sesión o al vencer el plazo configurado. Los respaldos institucionales, si existen, se conservan solo durante el tiempo necesario para operar el servicio.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="seguridad" number="08" title="Seguridad">
                        <p>
                            Las contraseñas se almacenan cifradas y el acceso a la edición de proyectos está restringido por políticas: solo el autor y el personal autorizado pueden modificarlos.
                        </p>
                        <p>
                            Los archivos se guardan en el almacenamiento local del portal. Ningún sistema es completamente infalible; si detectas un acceso indebido o un error de seguridad, comunícalo de inmediato a la coordinación de tu centro.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="derechos" number="09" title="Tus derechos">
                        <p>Puedes solicitar el acceso, la rectificación, la eliminación o la oposición al tratamiento de tus datos personales. También puedes retirar un proyecto del catálogo en cualquier momento desde tu panel.</p>
                        <p>
                            Para corregir tu ficha institucional —centro, carrera o número de WhatsApp— contacta a la coordinación de tu centro tecnológico. La solicitud puede requerir verificación de identidad para proteger tu cuenta.
                        </p>
                        <p>
                            Si consideras que tus datos no fueron tratados correctamente, puedes presentar una reclamación ante las instancias institucionales competentes de INATEC.
                        </p>
                    </x-legal.section>

                    <x-legal.section id="cambios" number="10" title="Cambios en esta política">
                        <p>
                            Podemos actualizar esta política para reflejar cambios en el portal o en las obligaciones institucionales. La versión vigente siempre estará publicada en esta página con su fecha de actualización.
                        </p>
                        <p>
                            Si un cambio afecta de forma sustancial el uso de tus datos, lo comunicaremos en la plataforma antes de aplicarlo.
                        </p>
                    </x-legal.section>
                </div>

                <div class="mt-10 rounded-xl border border-slate-200/80 bg-white p-5">
                    <p class="text-[0.8125rem] font-semibold text-slate-900">Tus datos, tus decisiones</p>
                    <p class="mt-1.5 text-[0.8125rem] leading-6 text-slate-500">
                        Publicar un proyecto es opcional y puedes retirarlo cuando quieras. Si tienes dudas sobre qué información aparece en el catálogo, habla con la coordinación de tu centro antes de publicar.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-4 text-[0.75rem] font-medium">
                        <a href="{{ route('terms') }}" class="inline-flex items-center gap-1 text-blue-700 underline-offset-4 hover:text-blue-800 hover:underline">
                            Leer los términos de uso
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
