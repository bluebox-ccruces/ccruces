// Componente de Navbar y Footer reutilizable
function renderNavbar() {
    return `
    <nav class="navbar">
      <div class="container">
        <div class="logo">Portafolio</div>
        <ul class="nav-links">
          <li><a href="index.html#inicio">Inicio</a></li>
          <li><a href="index.html#quienes-somos">Quiénes Somos</a></li>
          <li><a href="index.html#proyectos">Proyectos</a></li>
          <li class="submenu">
            <a href="#">Blog ▾</a>
            <ul class="submenu-list">
              <li><a href="blog.html">Entradas del Blog</a></li>
            </ul>
          </li>
        </ul>
        <button id="theme-toggle" aria-label="Cambiar tema claro/oscuro" style="background:none;border:none;cursor:pointer;font-size:1.7rem;margin-left:1.5rem;" title="Cambiar tema">
          <span id="theme-icon" aria-hidden="true">🌙</span>
        </button>
      </div>
    </nav>
    `;
}

function renderFooter() {
  return `
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Portafolio Personal. Todos los derechos reservados.</p>
        </div>
    </footer>
  `;
}


function injectLayout() {
  // Navbar
  const navPlaceholder = document.getElementById('navbar-placeholder');
  if (navPlaceholder) navPlaceholder.outerHTML = renderNavbar();
  // Footer
  const footerPlaceholder = document.getElementById('footer-placeholder');
  if (footerPlaceholder) footerPlaceholder.outerHTML = renderFooter();
  // Theme toggle logic
  setTimeout(() => {
    const btn = document.getElementById('theme-toggle');
    const icon = document.getElementById('theme-icon');
    if (btn && icon) {
      // Inicializar icono según tema
      const setIcon = (dark) => { icon.textContent = dark ? '☀️' : '🌙'; };
      const applyTheme = (dark) => {
        document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
        setIcon(dark);
      };
      // Leer preferencia
      let dark = localStorage.getItem('theme') === 'dark' || (window.matchMedia('(prefers-color-scheme: dark)').matches && !localStorage.getItem('theme'));
      applyTheme(dark);
      btn.onclick = () => {
        dark = !dark;
        localStorage.setItem('theme', dark ? 'dark' : 'light');
        applyTheme(dark);
      };
      // Escuchar cambios del sistema
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) {
          dark = e.matches;
          applyTheme(dark);
        }
      });
    }
  }, 100);
}

document.addEventListener('DOMContentLoaded', injectLayout);
