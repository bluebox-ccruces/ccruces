// Cargar proyectos dinámicamente desde proyectos.json
let proyectos = [];

async function cargarProyectos() {
    try {
        const resp = await fetch('proyectos.json');
        if (!resp.ok) throw new Error('No se pudo cargar proyectos.json');
        proyectos = await resp.json();
        renderizarProyectos();
    } catch (e) {
        console.error('Error cargando proyectos:', e);
    }
}

// Función para crear una tarjeta de proyecto
function crearTarjetaProyecto(proyecto) {
    return `
        <div class="project-card fade-in" data-id="${proyecto.id}" role="listitem" tabindex="0" aria-label="Proyecto: ${proyecto.titulo}">
            <div class="project-image">
                ${proyecto.imagen ? `<img src="${proyecto.imagen}" alt="Imagen de ${proyecto.titulo}" style="width:100%;height:200px;object-fit:contain;background:#fff;" loading="lazy" decoding="async" />` : `
                    <span class="icon-light" style="display:inline;" aria-label="Icono claro de ${proyecto.titulo}">${proyecto.iconoClaro || proyecto.icono}</span>
                    <span class="icon-dark" style="display:none;" aria-label="Icono oscuro de ${proyecto.titulo}">${proyecto.iconoOscuro || proyecto.icono}</span>
                `}
            </div>
            <div class="project-content">
                <h3 class="project-title">${proyecto.titulo}</h3>
                <p class="project-description">${proyecto.descripcion}</p>
                <div class="project-tags">
                    ${proyecto.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
                </div>
            </div>
        </div>
    `;
// Cambia iconos según tema
function actualizarIconosPorTema() {
    const dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.querySelectorAll('.icon-light').forEach(el => {
        el.style.display = dark ? 'none' : 'inline';
    });
    document.querySelectorAll('.icon-dark').forEach(el => {
        el.style.display = dark ? 'inline' : 'none';
    });
}
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', actualizarIconosPorTema);
window.addEventListener('DOMContentLoaded', actualizarIconosPorTema);
}

// Renderizar proyectos en el DOM
function renderizarProyectos() {
    const container = document.getElementById('projects-container');
    if (container) {
        container.innerHTML = proyectos.map(crearTarjetaProyecto).join('');
        // Agregar eventos a las tarjetas
        container.querySelectorAll('.project-card').forEach(card => {
            card.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                // Navegar a la vista de detalle con id
                window.location.href = 'detalle.html?proyecto=' + encodeURIComponent(id);
            });
        });
    }
}

// Smooth scroll para navegación
document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            const offsetTop = targetElement.offsetTop - 80;
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
        
        // Actualizar link activo
        document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
        this.classList.add('active');
    });
});

// Cambiar navegación al hacer scroll
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.boxShadow = '0 6px 12px rgba(0, 0, 0, 0.15)';
    } else {
        navbar.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    }
    
    // Actualizar link activo según la sección visible
    const sections = document.querySelectorAll('section[id]');
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.clientHeight;
        
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });
    
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    cargarProyectos();
});
