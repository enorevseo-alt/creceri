document.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('navbar');
  if (!nav) return;

  const links = nav.querySelectorAll('a.nav-link, .dropdown-menu a.dropdown-item');

  const norm = (href) => {
    try {
      const u = new URL(href, window.location.origin);
      let p = u.pathname.toLowerCase();
      if (p.length > 1 && p.endsWith('/')) p = p.slice(0, -1);
      return p;
    } catch { return href; }
  };

  const current = norm(window.location.pathname);

  links.forEach(a => a.classList.remove('active'));

  // 1) exact match
  let best = null, bestLen = -1;
  links.forEach(a => {
    const p = norm(a.getAttribute('href'));
    if (p === current) { best = a; bestLen = p.length; }
  });

  // 2) fallback: longest prefix (but not "/")
  if (!best) {
    links.forEach(a => {
      const p = norm(a.getAttribute('href'));
      if (p !== '/' && current.startsWith(p) && p.length > bestLen) {
        best = a; bestLen = p.length;
      }
    });
  }

  if (best) {
    best.classList.add('active');

    // if it's a dropdown item, also mark its parent toggle active
    const menu = best.closest('.dropdown-menu');
    if (menu) {
      const parentToggle = menu.parentElement?.querySelector('> a.dropdown-toggle, > .nav-link.dropdown-toggle');
      if (parentToggle) parentToggle.classList.add('active');
    }
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const isDesktop = () => window.matchMedia('(min-width: 992px)').matches;

  // Submenu parents: first tap opens, second tap navigates
  document.querySelectorAll('#navbar .dropdown-submenu > .dropdown-toggle').forEach(parentLink => {
    parentLink.addEventListener('click', function (e) {
      if (isDesktop()) return; // desktop uses hover/CSS

      const submenu = this.nextElementSibling; // <ul class="dropdown-menu">
      if (!submenu) return;

      const isOpen = submenu.classList.contains('show');

      // close sibling submenus
      const siblings = this.closest('.dropdown-menu')
        .querySelectorAll(':scope > .dropdown-submenu > .dropdown-menu.show');
      siblings.forEach(s => { if (s !== submenu) s.classList.remove('show'); });

      if (!isOpen) {
        e.preventDefault();      // stop navigation on first tap
        e.stopPropagation();     // prevent Bootstrap from auto-closing parent
        submenu.classList.add('show');
        this.setAttribute('aria-expanded', 'true');
      }
      // if already open: allow navigation on second tap
    });
  });

  // When the top-level dropdown hides, collapse any open submenus
  document.querySelectorAll('#navbar .nav-item.dropdown').forEach(dd => {
    dd.addEventListener('hide.bs.dropdown', () => {
      dd.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
      dd.querySelectorAll('.dropdown-submenu > .dropdown-toggle[aria-expanded="true"]')
        .forEach(t => t.setAttribute('aria-expanded','false'));
    });
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('heroBg');
  if (el && window.bootstrap) {
    bootstrap.Carousel.getOrCreateInstance(el, {
      interval: 2000,   // 2s
      ride: 'carousel',
      pause: false,
      touch: true,
      wrap: true
    });
  }
});

