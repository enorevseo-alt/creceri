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

(function () {
  function debounce(fn, delay) {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, arguments), delay);
    };
  }

  function bpCols(el) {
    // Read per-breakpoint counts from data attributes
    const xs = parseInt(el.getAttribute('data-cols-xs')) || 1;
    const sm = parseInt(el.getAttribute('data-cols-sm')) || xs;
    const lg = parseInt(el.getAttribute('data-cols-lg')) || sm;
    const xl = parseInt(el.getAttribute('data-cols-xl')) || lg;

    // Bootstrap 5 breakpoints
    const w = window.innerWidth;
    // <576 xs, 576–991 sm, 992–1199 lg, ≥1200 xl
    if (w >= 1200)
      return {
        perSlide: xl,
        rowColsClass: `row row-cols-${xs} row-cols-sm-${sm} row-cols-lg-${lg} row-cols-xl-${xl} g-3 g-md-4`,
      };
    if (w >= 992)
      return {
        perSlide: lg,
        rowColsClass: `row row-cols-${xs} row-cols-sm-${sm} row-cols-lg-${lg} g-3 g-md-4`,
      };
    if (w >= 576)
      return {
        perSlide: sm,
        rowColsClass: `row row-cols-${xs} row-cols-sm-${sm} g-3 g-md-4`,
      };
    return { perSlide: xs, rowColsClass: `row row-cols-${xs} g-3 g-md-4` };
  }

  function chunk(arr, n) {
    const out = [];
    for (let i = 0; i < arr.length; i += n) out.push(arr.slice(i, i + n));
    return out;
  }

  function rebuildCarousel(root) {
    // Dispose existing BS instance to avoid stale state
    const inst = bootstrap.Carousel.getInstance(root);
    if (inst) inst.dispose();

    const inner = root.querySelector('.carousel-inner');
    const indicators = root.parentElement.querySelector('.carousel-indicators');

    // Collect ALL card columns currently present (across any slides)
    const cols = inner.querySelectorAll('.col');
    if (!cols.length) return;

    const cards = Array.from(cols).map((col) => col.innerHTML);

    // Determine perSlide for current breakpoint
    const { perSlide, rowColsClass } = bpCols(root);

    // Build new slides
    const slides = chunk(cards, Math.max(1, perSlide));

    // Rebuild indicators
    if (indicators) {
      indicators.innerHTML = '';
      if (slides.length > 1) {
        slides.forEach((_, i) => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.setAttribute('data-bs-target', '#' + root.id);
          btn.setAttribute('data-bs-slide-to', String(i));
          btn.setAttribute('aria-label', `Slide ${i + 1}`);
          if (i === 0) {
            btn.className = 'active';
            btn.setAttribute('aria-current', 'true');
          }
          indicators.appendChild(btn);
        });
      }
    }

    // Rebuild inner
    inner.innerHTML = '';
    slides.forEach((group, sIdx) => {
      const item = document.createElement('div');
      item.className = 'carousel-item' + (sIdx === 0 ? ' active' : '');

      const row = document.createElement('div');
      row.className = rowColsClass;

      group.forEach((html) => {
        const col = document.createElement('div');
        col.className = 'col';
        col.innerHTML = html;
        row.appendChild(col);
      });

      item.appendChild(row);
      inner.appendChild(item);
    });

    // Hide controls/indicators if only one slide
    const controlsPrev = root.querySelector('.carousel-control-prev');
    const controlsNext = root.querySelector('.carousel-control-next');
    const multi = slides.length > 1;
    if (controlsPrev) controlsPrev.style.display = multi ? '' : 'none';
    if (controlsNext) controlsNext.style.display = multi ? '' : 'none';
    if (indicators) indicators.style.display = multi ? '' : 'none';

    // Re-init Bootstrap carousel (it will also honor your data attributes)
    new bootstrap.Carousel(root);
  }

  function initAll() {
    document
      .querySelectorAll('.carousel.generic-carousel')
      .forEach(rebuildCarousel);
  }

  // Run on load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // Rebuild on resize (debounced)
  window.addEventListener('resize', debounce(initAll, 150));
})();
