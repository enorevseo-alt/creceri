<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Article with Collapsible Sticky TOC</title>
<style>
  :root{ --brand:#8c2626; --gap:48px; --radius:12px; }
  *{ box-sizing:border-box; }
  html,body{ height:100%; margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; }
  /* Make sure NO ancestor clips overflow (kills sticky) */
  html, body, .wrapper, .grid, main, aside { overflow: visible; }

  .wrapper{ max-width:1200px; margin:0 auto; padding:32px 24px; }
  h1{ font-size:clamp(28px,4vw,48px); margin:12px 0 24px; }

  /* 2-col layout */
  .grid{
    display:grid;
    grid-template-columns:minmax(0,1fr) 320px; /* main | sidebar */
    gap:var(--gap);
    align-items:start;
  }

  .hero{ height:300px; border-radius:16px; background:#d9d9d9; margin-bottom:24px; }
  main section{ scroll-margin-top:96px; }
  .callout{ background:#fafafa; border-left:4px solid #d1d5db; padding:14px 16px; border-radius:6px; margin:16px 0; }

  /* === TOC (details) === */
  aside.toc{
    position:sticky;
    position:-webkit-sticky;
    top:100px;                 /* desktop stick offset */
    align-self:start;
    height:fit-content;
  }

  details#toc{
    border:1px solid #e5e7eb;
    border-radius:var(--radius);
    background:#fff;
  }

  /* Summary (header bar) */
  .toc-summary{
    list-style:none;           /* remove default marker (Firefox) */
    cursor:pointer;
    padding:10px 12px;
    display:flex; align-items:center; gap:10px;
    border-radius:var(--radius);
    color:var(--brand);
    user-select:none;
  }
  .toc-summary::-webkit-details-marker{ display:none; } /* hide default arrow */

  .chev{
    width:16px; height:16px; flex:0 0 16px;
    transition:transform .2s ease;
  }
  details[open] .chev{ transform:rotate(180deg); }

  /* TOC list */
  .toc-nav{ padding:0 12px 12px; }
  .toc-title{ margin:6px 0 8px; font:700 16px/1.2 system-ui; text-transform:uppercase; color:var(--brand); }
  .toc-nav ol{ list-style:none; margin:0; padding:0; }
  .toc-nav li{ margin:10px 0; }
  .toc-nav a{ color:var(--brand); text-decoration:none; }
  .toc-nav .sub{ list-style:disc; margin:6px 0 0 18px; color:#6b7280; }
  .toc-nav .sub a{ color:inherit; }

  /* ------- Mobile: collapse + sticky bar ------- */
  @media (max-width: 900px){
    .grid{ grid-template-columns:1fr; }

    aside.toc{
      top:12px;                /* sticky offset on mobile */
      z-index:10;
    }

    /* Make the TOC panel expand/collapse */
    details#toc{
      position:sticky; top:12px;     /* keep the whole details sticky */
      max-width:100%;
      border-radius:10px;
      overflow:hidden;               /* nice clipped corners when open */
      box-shadow:0 8px 20px rgba(0,0,0,.08);
    }

    /* Panel when open: limit height and scroll list */
    details[open] .toc-nav{
      max-height: calc(100dvh - 80px);
      overflow:auto;
    }
  }
</style>
</head>
<body>
<div class="wrapper">
  <h1>Lorem Ipsum Dolor Sit Amet</h1>

  <div class="grid">
    <main>
      <div class="hero" aria-hidden="true"></div>

      <section id="intro">
        <p class="callout">Intro text…</p>
        <p class="callout">More intro…</p>
      </section>

      <section id="sec1">
        <h2>Section One: Lorem Ipsum</h2>
        <p>Content…</p><p>More…</p><p>More…</p>
        <section id="sec1-1"><h3>Subsection 1.1</h3><p>Details…</p></section>
        <section id="sec1-2"><h3>Subsection 1.2</h3><p>Details…</p></section>
      </section>

      <section id="sec2">
        <h2>Section Two: Lorem Ipsum Dolor Sit</h2>
        <section id="sec2-1"><h3>Subsection 2.1</h3><p>Details…</p></section>
        <section id="sec2-2"><h3>Subsection 2.2</h3><p>Details…</p></section>
      </section>

      <section id="sec3">
        <h2>Section Three: Lorem Ipsum Dolor</h2>
        <section id="sec3-1"><h3>Subsection 3.1</h3><p>Details…</p></section>
        <section id="sec3-2"><h3>Subsection 3.2</h3><p>Details…</p></section>
      </section>

      <section id="conclusion"><h2>Conclusion</h2><p>Wrap-up…</p></section>
      <section id="faq"><h2>FAQ</h2><p>Q&A…</p></section>
      <section id="resources"><h2>Sample Resources</h2><p>Links…</p></section>
    </main>

    <aside class="toc">
      <details id="toc">
        <summary class="toc-summary">
          <svg class="chev" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span>Table of Contents</span>
        </summary>

        <nav class="toc-nav" aria-label="Table of contents">
          <h2 class="toc-title" aria-hidden="true">Table of Contents</h2>
          <ol>
            <li><a href="#intro">1. Introduction</a></li>
            <li>
              <a href="#sec1">2. Section One: Lorem Ipsum</a>
              <ul class="sub">
                <li><a href="#sec1-1">Subsection 1.1</a></li>
                <li><a href="#sec1-2">Subsection 1.2</a></li>
              </ul>
            </li>
            <li>
              <a href="#sec2">3. Section Two</a>
              <ul class="sub">
                <li><a href="#sec2-1">Subsection 2.1</a></li>
                <li><a href="#sec2-2">Subsection 2.2</a></li>
              </ul>
            </li>
            <li>
              <a href="#sec3">4. Section Three</a>
              <ul class="sub">
                <li><a href="#sec3-1">Subsection 3.1</a></li>
                <li><a href="#sec3-2">Subsection 3.2</a></li>
              </ul>
            </li>
            <li><a href="#conclusion">5. Conclusion</a></li>
            <li><a href="#faq">6. FAQ</a></li>
            <li><a href="#resources">7. Sample Resources</a></li>
          </ol>
        </nav>
      </details>
    </aside>
  </div>
</div>

<script>
  // Keep TOC open on desktop, collapsible on mobile
  const toc = document.getElementById('toc');
  const mq = window.matchMedia('(max-width: 900px)');
  function setTocMode() {
    toc.open = !mq.matches;      // open on desktop, closed on mobile
  }
  setTocMode();
  mq.addEventListener?.('change', setTocMode);

  // Smooth scroll + auto-collapse on mobile after clicking a link
  document.querySelectorAll('.toc-nav a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      e.preventDefault();
      const id = a.getAttribute('href');
      document.querySelector(id)?.scrollIntoView({behavior:'smooth', block:'start'});
      if (mq.matches) toc.open = false;   // collapse after navigation on mobile
      history.replaceState(null, '', id);
    });
  });
</script>
</body>
</html>
