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
  const text = "Ceceri | E-commerce, UX & Digital Knowledge";
  const typingSpeed = 90; // ms per character
  const target = document.getElementById('typing-text');
  
  let i = 0;
  function type() {
    if (i < text.length) {
      target.textContent += text.charAt(i);
      i++;
      setTimeout(type, typingSpeed);
    }
  }
  type();
});

