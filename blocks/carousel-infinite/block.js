/* childtheme/carousel-infinite */
(() => {
  const whenReady = (img) => {
    try { img.loading = 'eager'; } catch(_) {}
    if (img.decode) return img.decode().catch(() => {});
    if (img.complete && img.naturalWidth > 0) return Promise.resolve();
    return new Promise(res => {
      img.addEventListener('load', res,  { once:true });
      img.addEventListener('error', res, { once:true });
    });
  };

  const init = (wrap) => {
    const track = wrap.querySelector('.ci-track');
    if (!track) return;

    const getOriginals = () => {
      const n = parseInt(track.dataset.loopN, 10) || 0;
      const kids = Array.from(track.children);
      return n > 0 ? kids.slice(0, n) : kids;
    };

    const measureOneSet = (originals) => {
      if (!originals.length) return 0;
      const first = originals[0];
      const last  = originals[originals.length - 1];
      return Math.round((last.offsetLeft + last.offsetWidth) - first.offsetLeft);
    };

    const ensureCopies = () => {
      const originals = getOriginals();
      const oneSet = measureOneSet(originals);
      if (!oneSet) return { oneSet: 0 };

      const vw = wrap.clientWidth;
      const neededCopies = Math.max(2, Math.ceil(vw / oneSet) + 1);
      const currentCopies = Math.max(1, Math.floor(track.children.length / originals.length));

      for (let i = currentCopies; i < neededCopies; i++) {
        originals.forEach(node => track.appendChild(node.cloneNode(true)));
      }

      track.style.setProperty('--ci-distance', oneSet + 'px');
      return { oneSet, neededCopies };
    };

    const start = async () => {
      const { oneSet } = ensureCopies();
      if (!oneSet) return;

      const imgs = Array.from(track.querySelectorAll('.ci-img'));
      await Promise.all(imgs.map(whenReady));

      const confirmed = measureOneSet(getOriginals());
      track.style.setProperty('--ci-distance', confirmed + 'px');

      wrap.classList.add('is-ready'); 
    };

    const isSmallOrTouch = () =>
      window.matchMedia('(max-width: 991.98px)').matches ||
      window.matchMedia('(hover: none), (pointer: coarse)').matches;

    const allCards = () => Array.from(track.querySelectorAll('.ci-card'));

    const ensureA11yAttrs = (el) => {
      if (!el.hasAttribute('tabindex')) el.setAttribute('tabindex', '0');
      if (!el.hasAttribute('aria-expanded')) el.setAttribute('aria-expanded', 'false');
    };

    const closeAll = () => {
      allCards().forEach(c => { c.classList.remove('is-open'); c.setAttribute('aria-expanded', 'false'); });
      wrap.classList.remove('is-paused');
      track.style.removeProperty('animation-play-state');
    };

    const toggleCard = (card) => {
      ensureA11yAttrs(card);
      const willOpen = !card.classList.contains('is-open');
      allCards().forEach(c => { if (c !== card) { c.classList.remove('is-open'); c.setAttribute('aria-expanded', 'false'); }});
      card.classList.toggle('is-open', willOpen);
      card.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      wrap.classList.toggle('is-paused', willOpen);
    };

    let clickModeEnabled = false;
    let onDocClick = null;
    const onTrackClick = (e) => {
      const card = e.target.closest('.ci-card');
      if (!card) return;
      e.preventDefault();
      e.stopPropagation();
      toggleCard(card);
    };
    const onTrackKeydown = (e) => {
      if (e.key !== 'Enter' && e.key !== ' ') return;
      const card = e.target.closest('.ci-card');
      if (!card) return;
      e.preventDefault();
      toggleCard(card);
    };

    const enableClickMode = () => {
      if (clickModeEnabled) return;
      track.addEventListener('click', onTrackClick);
      track.addEventListener('keydown', onTrackKeydown);
      allCards().forEach(ensureA11yAttrs);
      // Close when clicking anywhere that's not a card (inside or outside the carousel)
      onDocClick = (e) => { if (!e.target.closest('.ci-card')) closeAll(); };
      document.addEventListener('click', onDocClick);
      clickModeEnabled = true;
    };

    const disableClickMode = () => {
      if (!clickModeEnabled) return;
      track.removeEventListener('click', onTrackClick);
      track.removeEventListener('keydown', onTrackKeydown);
      if (onDocClick) document.removeEventListener('click', onDocClick);
      onDocClick = null;
      closeAll();
      clickModeEnabled = false;
    };

    const bindClickMode = () => {
      if (isSmallOrTouch()) enableClickMode();
      else disableClickMode();
    };

    start();
    bindClickMode();

    let t;
    const onResize = () => {
      clearTimeout(t);
      t = setTimeout(() => {
        const was = wrap.classList.contains('is-ready');
        if (was) track.style.animationPlayState = 'paused';
        ensureCopies();
        if (was) requestAnimationFrame(() => {
          if (wrap.classList.contains('is-paused')) {
            track.style.animationPlayState = 'paused';
          } else {
            track.style.removeProperty('animation-play-state');
          }
        });
        bindClickMode();
      }, 150);
    };
    window.addEventListener('resize', onResize, { passive: true });
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.ci-wrap').forEach(init);
  });
})();
