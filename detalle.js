// ...existing code...
// Lee el proyecto a mostrar desde el query string (?proyecto=Bocado)
const params = new URLSearchParams(window.location.search);
const idParam = params.get('proyecto');


// Relación real de proyectos e imágenes para banners y detalles
const proyectos = [
    {
        id: 'bocado',
        titulo: 'Bocado',
        descripcion: 'Controlar el número exacto de raciones consumidas en el comedor de la empresa.',
        icono: '🚀',
        tags: ['App', 'Comedor', 'digital'],
        imagen: 'img/Bocado Logo.png',
        banner: 'img/Vista principal app.jpeg'
    },
    {
        id: 'conectagh',
        titulo: 'Conecta GH',
        descripcion: 'Plataforma para conectar y gestionar recursos humanos, integrando datos de empleados, asistencia y desempeño en tiempo real.',
        icono: '🔗',
        tags: ['Recursos Humanos', 'Gestión', 'WebApp'],
        imagen: 'img/Logo Gh.png',
        banner: 'img/Imagen app Conecta Gh.jpeg'
    },
    {
        id: 'gestionocupacional',
        titulo: 'Gestión Ocupacional',
        descripcion: 'Plataforma digital para la gestión integral de la Seguridad y Salud en el Trabajo (SST).',
        icono: '🧑‍⚕️',
        tags: ['SST', 'Gestión', 'WebApp'],
        imagen: 'img/app gestion ocupacional.png',
        banner: 'img/app gestion ocupacional.png'
    }
];

// Normaliza para evitar problemas de espacios/codificación
function normalizar(str) {
    return (str || '').toLowerCase().replace(/[^a-z0-9]/g, '');
}
console.log('ID recibido en query:', idParam);
console.log('IDs disponibles:', proyectos.map(p => p.id));
// Normaliza el id recibido y los ids de los proyectos para evitar errores por mayúsculas, espacios, etc.
const proyecto = proyectos.find(p => normalizar(p.id) === normalizar(idParam));

const detalleMain = document.getElementById('detalle-main');

// Funciones reutilizables para secciones
function renderBanner({banner, titulo, color, subtitulo, btnColor, btnLabel, btnAria, extraIcons = ''}) {
    return `
        <section class="banner-bocado animate-fade-in" role="region" aria-label="Banner ${titulo}" style="background:linear-gradient(90deg,${color} 60%,#f0f9ff 100%);box-shadow:0 8px 32px rgba(56,189,248,0.10);padding:3rem 2.5rem 2.5rem 2.5rem;margin-bottom:1.5rem;border-radius:22px;display:flex;align-items:center;gap:2.5rem;flex-wrap:wrap;transition:background 0.5s, box-shadow 0.5s;">
            <div class="banner-img-block" style="position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:220px;">
                <img src="${banner}" alt="Vista principal de la app ${titulo}" class="banner-img animate-pop" style="margin-bottom:1.2rem;">
                <div style="display:flex;gap:1.5rem;justify-content:center;">${extraIcons}</div>
            </div>
            <div class="banner-content" style="flex:1;min-width:260px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;">
                <h1 class="banner-title" style="font-size:2.1rem;margin-bottom:0.5rem;text-align:right;line-height:1.15;color:${btnColor};font-weight:800;text-shadow:0 2px 8px #fff,0 1px 0 #e0f2fe;max-width:520px;">${titulo}</h1>
                <div style="font-size:0.98rem;font-weight:600;color:#fff;background:${btnColor};padding:0.3rem 1rem;border-radius:8px;box-shadow:0 1px 4px rgba(56,189,248,0.13);margin-bottom:0.7rem;">${subtitulo}</div>
                <button type="button" class="btn-primary animate-pulse" style="font-size:1.1rem;padding:0.7rem 2rem;margin:1rem 0 0.5rem 0;display:inline-block;background:${btnColor};color:#fff;outline:2px solid transparent;transition:background 0.3s, outline 0.3s;" aria-label="${btnAria}">${btnLabel}</button>
            </div>
        </section>
    `;
}

function renderResumen({icon, color, titulo, resumen, detalles}) {
    return `
        <section class="detalle-section animate-fade-in" style="margin-top:0.5rem;">
            <h2 class="detalle-section-title" style="display:flex;align-items:center;gap:0.5rem;color:${color};">
                <span style="font-size:1.5rem;">${icon}</span> Resumen
            </h2>
            <div class="detalle-resumen" style="font-size:1.13rem;color:#23263a;background:linear-gradient(120deg,#e0f2fe 60%,#f0f9ff 100%);padding:2rem 1.5rem 1.5rem 1.5rem;border-radius:18px;max-width:700px;box-shadow:0 4px 18px rgba(56,189,248,0.08);margin-bottom:1.2rem;position:relative;overflow:hidden;">
                <div style="font-size:2.2rem;position:absolute;top:-18px;right:18px;opacity:0.13;pointer-events:none;">${icon}</div>
                <div style="font-size:1.18rem;font-weight:600;color:${color};margin-bottom:0.7rem;letter-spacing:0.5px;">${titulo}</div>
                ${resumen}
                ${detalles}
            </div>
        </section>
    `;
}

function renderFuncionalidades({icon, color, funcionalidades}) {
    return `
        <section class="detalle-section animate-fade-in" style="margin-top:0.5rem;">
            <h2 class="detalle-section-title" style="display:flex;align-items:center;gap:0.5rem;color:${color};">
                <span style="font-size:1.5rem;">${icon}</span> Funcionalidades del Aplicativo
            </h2>
            <div class="funcionalidades-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.2rem;max-width:800px;">
                ${funcionalidades}
            </div>
        </section>
    `;
}

if (!detalleMain) {
    console.error('No se encontró el elemento #detalle-main');
} else if (!proyecto) {
    detalleMain.innerHTML = `
        <div style="color:#dc2626;font-weight:bold;font-size:1.2rem;padding:2rem;" role="alert" aria-live="assertive">
            Proyecto no encontrado.<br>
            <span style='font-size:1rem;color:#374151;'>ID recibido: <b>${idParam}</b><br>IDs válidos: <b>${proyectos.map(p => p.id).join(', ')}</b></span>
            <br>Verifica que el enlace generado use el id correcto.
        </div>
    `;
} else if (proyecto.id === 'bocado') {
    detalleMain.innerHTML =
        renderBanner({
            banner: proyecto.banner,
            titulo: 'Bocado: <span style="color:#22c55e;">Control Inteligente</span> de Comedores Empresariales',
            color: '#e0f2fe',
            subtitulo: 'Optimizado para comedores empresariales',
            btnColor: '#38bdf8',
            btnLabel: 'Solicite su demo gratis',
            btnAria: 'Solicite su demo gratis',
            extraIcons: '<span class="aprobacion animate-bounce" title="Aprobado" aria-label="Aprobado" style="position:absolute;right:-30px;top:20px;font-size:2.5rem;color:#38bdf8;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));user-select:none;">👍</span><span class="aprobacion animate-bounce" title="Innovador" aria-label="Innovador" style="position:absolute;left:-30px;top:60px;font-size:2.5rem;color:#22c55e;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));user-select:none;">🥗</span>'
        }) +
        renderResumen({
            icon: '🍽️',
            color: '#0ea5e9',
            titulo: 'La solución moderna para el control de raciones',
            resumen: '<p style="margin-bottom:0.7rem;">Bocado es la plataforma que optimiza la gestión de raciones y comensales en empresas, asegurando que solo se pague por lo realmente consumido. Elimina el fraude, reduce el desperdicio y agiliza el registro de cada trabajador.</p><p style="margin-bottom:0.7rem;">Con Bocado, su empresa logra un <span style="color:#38bdf8;font-weight:600;">ahorro inmediato</span>, <span style="color:#22c55e;font-weight:600;">control total</span> y <span style="color:#8b5cf6;font-weight:600;">experiencia sin colas</span>.</p>',
            detalles: '<div style="display:flex;gap:1.2rem;margin-top:1.2rem;flex-wrap:wrap;"><div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(56,189,248,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;"><span style="font-size:1.4rem;color:#38bdf8;">⏱️</span> Registro en 2 segundos</div><div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(34,197,94,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;"><span style="font-size:1.4rem;color:#22c55e;">📊</span> Reportes y ahorro en tiempo real</div><div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(139,92,246,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;"><span style="font-size:1.4rem;color:#8b5cf6;">🔒</span> Control y validación anti-fraude</div></div>'
        }) +
        renderFuncionalidades({
            icon: '🥗',
            color: '#0ea5e9',
            funcionalidades: `<div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(56,189,248,0.07);display:flex;align-items:flex-start;gap:1rem;"><span style="font-size:2rem;color:#38bdf8;flex-shrink:0;">⏱️</span><div><b>Registro ultrarrápido</b><br /><span style="font-size:1.01rem;color:#444;">Escaneo QR o código de barras en 2 segundos por trabajador.</span></div></div><div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(34,197,94,0.07);display:flex;align-items:flex-start;gap:1rem;"><span style="font-size:2rem;color:#22c55e;flex-shrink:0;">📶</span><div><b>Modo Offline</b><br /><span style="font-size:1.01rem;color:#444;">Funciona sin señal y sincroniza automáticamente al recuperar conexión.</span></div></div><div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(139,92,246,0.07);display:flex;align-items:flex-start;gap:1rem;"><span style="font-size:2rem;color:#8b5cf6;flex-shrink:0;">🚦</span><div><b>Semaforización Visual</b><br /><span style="font-size:1.01rem;color:#444;">Pantalla completa en verde (Apto) o rojo (Denegado) para control inmediato.</span></div></div><div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(56,189,248,0.07);display:flex;align-items:flex-start;gap:1rem;"><span style="font-size:2rem;color:#38bdf8;flex-shrink:0;">🔗</span><div><b>Integración y reportería</b><br /><span style="font-size:1.01rem;color:#444;">Exporta datos y conecta con sistemas de análisis y nómina.</span></div></div><div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(14,165,233,0.07);display:flex;align-items:flex-start;gap:1rem;"><span style="font-size:2rem;color:#0ea5e9;flex-shrink:0;">🩺</span><div><b>Control de salud laboral</b><br /><span style="font-size:1.01rem;color:#444;">Valida restricciones médicas y asegura raciones correctas según EMO.</span></div></div>`
        });

    // Animaciones y gráfico dinámico
    setTimeout(() => {
        document.querySelectorAll('.animate-fade-in').forEach(el => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        });
        // Fallback: forzar visibilidad si por algún motivo no se aplica la animación
        setTimeout(() => {
            document.querySelectorAll('.animate-fade-in').forEach(el => {
                el.style.opacity = 1;
                el.style.transform = 'translateY(0)';
            });
        }, 800);
    }, 100);

    // Gráfico realista con canvas
    const canvas = document.getElementById('graficoAhorro');
    if (canvas && canvas.getContext) {
        const ctx = canvas.getContext('2d');
        // Datos ejemplo: antes y 3 meses
        const datos = [180, 155, 150, 148];
        const labels = ['Antes', 'Mes 1', 'Mes 2', 'Mes 3+'];
        const colores = ['#6366f1', '#22c55e', '#22c55e', '#22c55e'];
        ctx.clearRect(0, 0, 650, 220);
        // Ejes
        ctx.strokeStyle = '#d1d5db';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.moveTo(60, 20);
        ctx.lineTo(60, 200);
        ctx.lineTo(630, 200);
        ctx.stroke();
        // Barras
        for (let i = 0; i < datos.length; i++) {
            const x = 120 + i * 130;
            const y = 200 - datos[i];
            ctx.fillStyle = colores[i];
            ctx.fillRect(x, y, 48, datos[i]);
            // Etiquetas
            ctx.fillStyle = '#6b7280';
            ctx.font = '16px Segoe UI, Arial';
            ctx.textAlign = 'center';
            ctx.fillText(labels[i], x + 24, 220);
            // Valor
            ctx.fillStyle = '#222';
            ctx.font = 'bold 18px Segoe UI, Arial';
            ctx.fillText(datos[i], x + 24, y - 10);
        }
    }
} else if (proyecto.id === 'conectagh') {
    document.getElementById('detalle-main').innerHTML = `
        <section class="banner-bocado animate-fade-in" role="region" aria-label="Banner Conecta GH" style="background:linear-gradient(90deg,#e0e7ff 60%,#fff 100%);box-shadow:0 8px 32px rgba(99,102,241,0.08);padding:2.5rem 2rem 0.2rem 2rem;transition:background 0.5s, box-shadow 0.5s;">
            <div class="banner-img-block" style="position:relative;">
                <img src="${proyecto.banner}" alt="Vista de la app Conecta GH" class="banner-img animate-pop" style="max-width:320px;border-radius:18px;box-shadow:0 8px 32px rgba(99,102,241,0.13);">
                <span class="aprobacion animate-bounce" title="Me gusta" aria-label="Me gusta" style="position:absolute;right:-30px;top:20px;font-size:2.5rem;color:#22c55e;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));user-select:none;">👍</span>
                <span class="aprobacion animate-bounce" title="Innovador" aria-label="Innovador" style="position:absolute;left:-30px;top:60px;font-size:2.5rem;color:#6366f1;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));user-select:none;">✨</span>
            </div>
            <div class="banner-content" style="flex:1;min-width:260px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;">
                <h1 class="banner-title" style="font-size:2.1rem;margin-bottom:0.5rem;text-align:right;line-height:1.15;color:#222;font-weight:800;text-shadow:0 2px 8px #fff,0 1px 0 #e0e7ff;max-width:520px;">Conecta GH: <span style='color:#6366f1;'>La Conexión Directa</span> entre su Empresa y su Talento</h1>
                <h2 style="font-size:1.18rem;font-weight:500;color:#374151;text-align:right;background:rgba(99,102,241,0.08);padding:0.7rem 1.2rem;border-radius:12px;box-shadow:0 2px 8px rgba(99,102,241,0.04);max-width:420px;">¡Digitalice la Experiencia del Empleado y Libere a su Equipo de RR.HH.!</h2>
                <button type="button" class="btn-primary animate-pulse" style="font-size:1.1rem;padding:0.7rem 2rem;margin:1rem 0 0.5rem 0;display:inline-block;background:#6366f1;color:#fff;outline:2px solid transparent;transition:background 0.3s, outline 0.3s;" aria-label="Solicite su demo gratis">Solicite su demo gratis</button>
            </div>
        </section>
        <section class="detalle-section animate-fade-in" style="margin-top:0.5rem;">
            <h2 class="detalle-section-title" style="display:flex;align-items:center;gap:0.5rem;">
                <span style="font-size:1.5rem;">📱</span> Resumen
            </h2>
            <div class="detalle-resumen" style="font-size:1.13rem;color:#23263a;background:linear-gradient(120deg,#e0e7ff 60%,#f3f4f6 100%);padding:2rem 1.5rem 1.5rem 1.5rem;border-radius:18px;max-width:700px;box-shadow:0 4px 18px rgba(99,102,241,0.08);margin-bottom:1.2rem;position:relative;overflow:hidden;">
                <div style="font-size:2.2rem;position:absolute;top:-18px;right:18px;opacity:0.13;pointer-events:none;">💡</div>
                <div style="font-size:1.18rem;font-weight:600;color:#6366f1;margin-bottom:0.7rem;letter-spacing:0.5px;">La app móvil definitiva para RR.HH.</div>
                <p style="margin-bottom:0.7rem;">Coencta GH es la aplicación móvil definitiva que centraliza y simplifica la gestión de Recursos Humanos para cada empleado. Diseñada para transformar la comunicación interna, nuestra plataforma elimina el papeleo, reduce las consultas repetitivas a RR.HH. y empodera a su personal con acceso instantáneo a toda su información laboral.</p>
                <p style="margin-bottom:0.7rem;">Con Coencta GH, su empresa avanza hacia un entorno de trabajo más <span style="color:#22c55e;font-weight:600;">transparente</span>, <span style="color:#6366f1;font-weight:600;">eficiente</span> y <span style="color:#8b5cf6;font-weight:600;">moderno</span>.</p>
                <div style="display:flex;gap:1.2rem;margin-top:1.2rem;flex-wrap:wrap;">
                    <div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(99,102,241,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;">
                        <span style="font-size:1.4rem;color:#6366f1;">🔒</span> Acceso seguro y personalizado
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(99,102,241,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;">
                        <span style="font-size:1.4rem;color:#22c55e;">⚡</span> Información laboral al instante
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(99,102,241,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;">
                        <span style="font-size:1.4rem;color:#8b5cf6;">📢</span> Comunicación interna sin fricciones
                    </div>
                </div>
            </div>
        </section>
        <section class="detalle-section animate-fade-in" style="margin-top:0.5rem;">
            <h2 class="detalle-section-title" style="display:flex;align-items:center;gap:0.5rem;">
                <span style="font-size:1.5rem;">🛠️</span> Funcionalidades del Aplicativo
            </h2>
            <div class="funcionalidades-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.2rem;max-width:800px;">
                <div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(99,102,241,0.07);display:flex;align-items:flex-start;gap:1rem;">
                    <span style="font-size:2rem;color:#6366f1;flex-shrink:0;">📅</span>
                    <div>
                        <b>Historial Laboral y Asistencia Transparente</b><br />
                        <span style="font-size:1.01rem;color:#444;">
                            Registro diario de asistencia, vinculación con marcaciones de ingreso y salida, control de permisos, licencias y vacaciones.
                        </span>
                    </div>
                </div>
                <div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(99,102,241,0.07);display:flex;align-items:flex-start;gap:1rem;">
                    <span style="font-size:2rem;color:#22c55e;flex-shrink:0;">🌴</span>
                    <div>
                        <b>Gestión Eficiente de Tiempos Libres</b><br />
                        <span style="font-size:1.01rem;color:#444;">
                            <b>Control de Vacaciones:</b> Visualización clara de los días de vacaciones acumulados, programados y pendientes. Los empleados pueden solicitar periodos de descanso de forma rápida y sencilla.
                        </span>
                    </div>
                </div>
                <div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(99,102,241,0.07);display:flex;align-items:flex-start;gap:1rem;">
                    <span style="font-size:2rem;color:#8b5cf6;flex-shrink:0;">📄</span>
                    <div>
                        <b>Documentación y Boletas Digitales</b><br />
                        <span style="font-size:1.01rem;color:#444;">
                            Visualiza y descarga todas las boletas y/o documentos laborales personales y generales.
                        </span>
                    </div>
                </div>
                <div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(99,102,241,0.07);display:flex;align-items:flex-start;gap:1rem;">
                    <span style="font-size:2rem;color:#f59e42;flex-shrink:0;">📢</span>
                    <div>
                        <b>Comunicación Directa y Centralizada</b><br />
                        <span style="font-size:1.01rem;color:#444;">
                            Reciba anuncios, noticias corporativas e información importante directamente en su móvil, asegurando que ningún mensaje crítico se pierda.
                        </span>
                    </div>
                </div>
                <div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(99,102,241,0.07);display:flex;align-items:flex-start;gap:1rem;">
                    <span style="font-size:2rem;color:#8b5cf6;flex-shrink:0;">💬</span>
                    <div>
                        <b>Comunicación Interna</b><br />
                        <span style="font-size:1.01rem;color:#444;">
                            Notificaciones, anuncios y encuestas directas a los empleados.
                        </span>
                    </div>
                </div>
                <div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(99,102,241,0.07);display:flex;align-items:flex-start;gap:1rem;">
                    <span style="font-size:2rem;color:#f59e42;flex-shrink:0;">🔗</span>
                    <div>
                        <b>Integración de Datos</b><br />
                        <span style="font-size:1.01rem;color:#444;">
                            Sincronización con sistemas de nómina y control de horarios.
                        </span>
                    </div>
                </div>
            </div>
        </section>
    `;
    setTimeout(() => {
        document.querySelectorAll('.animate-fade-in').forEach(el => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        });
    }, 100);
} else if (proyecto.id === 'gestionocupacional') {
    document.getElementById('detalle-main').innerHTML = `
        <section class="banner-publicitario animate-fade-in" role="region" aria-label="Banner Gestión Ocupacional" style="background:linear-gradient(90deg,#bbf7d0 0%,#f0fdf4 100%);box-shadow:0 8px 32px rgba(16,185,129,0.13);padding:1.2rem 2rem 1.2rem 2rem;margin-bottom:1.5rem;border-radius:18px;display:flex;align-items:center;gap:1.5rem;transition:background 0.5s, box-shadow 0.5s;">
            <img src="img/Logo gestion ocupacional.png" alt="Logo Gestión Ocupacional" style="width:80px;height:80px;object-fit:contain;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(16,185,129,0.10);">
            <div style="flex:1;">
                <h2 style="font-size:1.5rem;font-weight:800;color:#15803d;margin-bottom:0.3rem;">¡Promociona la Seguridad y Salud en tu Empresa!</h2>
                <p style="font-size:1.08rem;color:#166534;max-width:600px;">Descubre cómo nuestra plataforma digital puede ayudarte a cumplir normativas, reducir riesgos y mejorar el bienestar de tus colaboradores. Solicita una demo personalizada y lleva la gestión ocupacional de tu empresa al siguiente nivel.</p>
            </div>
            <button type="button" class="btn-primary animate-pulse" style="font-size:1.1rem;padding:0.7rem 2rem;background:#10b981;color:#fff;outline:2px solid transparent;transition:background 0.3s, outline 0.3s;" aria-label="Solicita tu demo">Solicita tu demo</button>
        </section>
        <section class="banner-bocado animate-fade-in" style="background:linear-gradient(90deg,#f1f5f9 60%,#e0e7ff 100%);box-shadow:0 8px 32px rgba(16,185,129,0.08);padding:2.5rem 2rem 0.2rem 2rem;">
            <div class="banner-img-block" style="position:relative;">
                <img src="${proyecto.banner}" alt="Gestión Ocupacional" class="banner-img animate-pop" style="max-width:320px;border-radius:18px;box-shadow:0 8px 32px rgba(16,185,129,0.13);background:#fff;" onerror="this.onerror=null;this.src='img/Logo gestion ocupacional.png';this.nextElementSibling.style.display='block';">
                <div style="display:none;color:#dc2626;font-weight:bold;margin-top:1rem;">Imagen principal no encontrada, mostrando logo alternativo.</div>
                <span class="aprobacion animate-bounce" title="SST" style="position:absolute;right:-30px;top:20px;font-size:2.5rem;color:#10b981;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));user-select:none;">🧺</span>
                <span class="aprobacion animate-bounce" title="Salud" style="position:absolute;left:-30px;top:60px;font-size:2.5rem;color:#6366f1;filter:drop-shadow(0 2px 6px rgba(0,0,0,0.10));user-select:none;">💼</span>
            </div>
            <div class="banner-content" style="flex:1;min-width:260px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;">
                <h1 class="banner-title" style="font-size:2.1rem;margin-bottom:0.5rem;text-align:right;line-height:1.15;color:#222;font-weight:800;text-shadow:0 2px 8px #fff,0 1px 0 #e0e7ff;max-width:520px;">Cuidar a tu Equipo Nunca Fue Tan Sencillo.</h1>
                <h2 style="font-size:1.18rem;font-weight:500;color:#374151;text-align:right;background:rgba(16,185,129,0.08);padding:0.7rem 1.2rem;border-radius:12px;box-shadow:0 2px 8px rgba(16,185,129,0.04);max-width:420px;">La plataforma que transforma la gestión de riesgos en prevención activa, garantizando el bienestar de tus colaboradores y evitando multas laborales.</h2>
                <button type="button" class="btn-primary animate-pulse" style="font-size:1.1rem;padding:0.7rem 2rem;margin:1rem 0 0.5rem 0;display:inline-block;background:#10b981;color:#fff;">Solicite su demo gratis</button>
            </div>
        </section>
        <section class="detalle-section animate-fade-in" style="margin-top:0.5rem;">
            <h2 class="detalle-section-title" style="display:flex;align-items:center;gap:0.5rem;color:#10b981;">
                <span style="font-size:1.5rem;">🧺</span> Resumen
            </h2>
            <div class="detalle-resumen" style="font-size:1.13rem;color:#23263a;background:linear-gradient(120deg,#f1f5f9 60%,#e0e7ff 100%);padding:2rem 1.5rem 1.5rem 1.5rem;border-radius:18px;max-width:700px;box-shadow:0 4px 18px rgba(16,185,129,0.08);margin-bottom:1.2rem;position:relative;overflow:hidden;">
                <div style="font-size:2.2rem;position:absolute;top:-18px;right:18px;opacity:0.13;pointer-events:none;">🧺</div>
                <div style="font-size:1.18rem;font-weight:600;color:#10b981;margin-bottom:0.7rem;letter-spacing:0.5px;">Gestión moderna y centralizada de SST</div>
                <p style="margin-bottom:0.7rem;">La plataforma de Gestión Ocupacional permite administrar, monitorear y reportar todos los aspectos de Seguridad y Salud en el Trabajo en una sola solución digital. Cumpla normativas, automatice reportes y mejore la cultura preventiva en su organización.</p>
                <p style="margin-bottom:0.7rem;">Obtenga <span style="color:#10b981;font-weight:600;">control total</span>, <span style="color:#6366f1;font-weight:600;">cumplimiento normativo</span> y <span style="color:#8b5cf6;font-weight:600;">información en tiempo real</span>.</p>
                <div style="display:flex;gap:1.2rem;margin-top:1.2rem;flex-wrap:wrap;">
                    <div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(16,185,129,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;">
                        <span style="font-size:1.4rem;color:#10b981;">📋</span> Gestión documental integral
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(16,185,129,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;">
                        <span style="font-size:1.4rem;color:#6366f1;">📊</span> Reportes automáticos
                    </div>
                    <div style="background:#fff;border-radius:10px;padding:0.8rem 1.2rem;box-shadow:0 2px 8px rgba(16,185,129,0.07);display:flex;align-items:center;gap:0.7rem;font-size:1.05rem;">
                        <span style="font-size:1.4rem;color:#8b5cf6;">🗣️</span> Comunicación interna
                    </div>
                    <div style="background:#fff;border-radius:14px;padding:1.2rem 1.2rem 1.1rem 1.2rem;box-shadow:0 2px 12px rgba(99,102,241,0.07);display:flex;align-items:flex-start;gap:1rem;">
                        <span style="font-size:2rem;color:#f59e42;flex-shrink:0;">🔗</span>
                        <div>
                            <b>Integración de Datos</b><br />
                            <span style="font-size:1.01rem;color:#444;">
                                Sincronización con sistemas de nómina y control de horarios.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    `;
    setTimeout(() => {
        document.querySelectorAll('.animate-fade-in').forEach(el => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        });
    }, 100);
}
