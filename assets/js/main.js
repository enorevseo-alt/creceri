 // Mobile: open/close main menu
  const header = document.querySelector('.site-header');
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.getElementById('primary-nav');

  function setExpanded(open){
    header.dataset.navOpen = String(open);
    toggle.setAttribute('aria-expanded', String(open));
  }

  toggle.addEventListener('click', () => {
    const open = header.dataset.navOpen !== 'true';
    setExpanded(open);
    if(open){
      // move focus to first item for accessibility
      const firstLink = nav.querySelector('a,button');
      firstLink && firstLink.focus({preventScroll:true});
    }
  });

  // Dropdown: click to toggle on mobile, hover handled via CSS on desktop
  const dropdown = document.querySelector('.dropdown');
  const ddButton = dropdown.querySelector('.nav-button');
  ddButton.addEventListener('click', () => {
    const isMobile = window.matchMedia('(max-width: 768px)').matches;
    if(!isMobile) return; // desktop hover handles it
    const open = dropdown.dataset.open !== 'true';
    dropdown.dataset.open = String(open);
    ddButton.setAttribute('aria-expanded', String(open));
  });

  // Close mobile menu if viewport grows beyond breakpoint
  window.addEventListener('resize', () => {
    if (window.matchMedia('(min-width: 769px)').matches) {
      setExpanded(false);
      dropdown.dataset.open = 'false';
      ddButton.setAttribute('aria-expanded', 'false');
    }
  });

  // Close dropdown when clicking outside (mobile)
  document.addEventListener('click', (e) => {
    const isMobile = window.matchMedia('(max-width: 768px)').matches;
    if(!isMobile) return;
    if(!dropdown.contains(e.target) && !ddButton.contains(e.target)){
      dropdown.dataset.open = 'false';
      ddButton.setAttribute('aria-expanded', 'false');
    }
  });

  // Guides rail: click-to-expand accordion behavior
  (function(){
    const rail = document.querySelector('.guides .g-rail');
    if(!rail) return;
    const items = Array.from(rail.querySelectorAll('.g-item'));
    if(items.length === 0) return;

    // Ensure one active by default
    if(!items.some(i => i.classList.contains('active'))){
      items[0].classList.add('active');
      const link = items[0].querySelector('.g-link');
      link && link.setAttribute('aria-expanded','true');
    }

    const setActive = (el) => {
      items.forEach(it => {
        const link = it.querySelector('.g-link');
        if(it === el){
          it.classList.add('active');
          link && link.setAttribute('aria-expanded','true');
        } else {
          it.classList.remove('active');
          link && link.setAttribute('aria-expanded','false');
        }
      });
    };

    rail.addEventListener('click', (e) => {
      const item = e.target.closest('.g-item');
      if(!item || !rail.contains(item)) return;
      e.preventDefault();

      const isDeckMobile = window.matchMedia('(max-width: 760px)').matches;
      if(isDeckMobile){
        if(rail.classList.contains('deck-animating')) return;
        const first = rail.querySelector('.g-item');
        if(!first) return;
        rail.classList.add('deck-animating');
        rail.appendChild(first);
        setTimeout(() => rail.classList.remove('deck-animating'), 500);
        return;
      }

      setActive(item);
    });

    rail.addEventListener('keydown', (e) => {
      if(e.key !== 'Enter' && e.key !== ' ' ) return;
      const item = e.target.closest('.g-item');
      if(!item || !rail.contains(item)) return;
      e.preventDefault();
      const isDeckMobile = window.matchMedia('(max-width: 760px)').matches;
      if(isDeckMobile){
        if(rail.classList.contains('deck-animating')) return;
        const first = rail.querySelector('.g-item');
        if(!first) return;
        rail.classList.add('deck-animating');
        rail.appendChild(first);
        setTimeout(() => rail.classList.remove('deck-animating'), 500);
        return;
      }
      setActive(item);
    });
  })();
