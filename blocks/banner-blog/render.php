<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Bottom-left image as card</title>
<style>
  *{box-sizing:border-box}
  body{margin:0;background:#fff;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif}

  .grey-rect{
    width: 1480px; height: 650px;            /* desktop size */
    margin: 40px auto;
    background:#d9d9d9;
    border-radius: 22px;
    position: relative;                      /* anchor for absolute children */
    overflow: hidden;                        /* clip inside rounded corners */
    box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
  }

  /* The white card is an IMAGE anchored bottom-left */
  .card-bg{
    position: absolute;
    left: 0;
    bottom: 0;
    width: 25%;                              /* tweak as needed */
    height: auto;
    display: block;                          /* remove inline gap */
    pointer-events: none;                    /* decorative */
    z-index: 0;                              /* behind overlay content */
  }

  /* Content layered on top of the image */
  .card-content{
    position: absolute;
    left: 0px;                               /* move to where the white area starts */
    bottom: 20px;                            /* sit inside the shape */
    z-index: 1;

    display: flex;                           /* circle left, text right */
    align-items: center;
    gap: 16px;
  }

  .circle{
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #d9d9d9;
    flex: 0 0 60px;                          /* fixed size */
  }

  .text{ display:flex; flex-direction:column; }

  .title{
    margin: 0 0 6px;
    font-weight: 700;
    color: #8c2626;
    font-size: 20px;
    line-height: 1.1;
  }
  .meta{
    margin: 0;
    color: #666;
    font-size: 13px;
  }

  /* ---------- Mobile (≤ 768px) ---------- */
@media (max-width: 760px){
  :root{ --pad: 2px; }

  /* 2px padding + safe areas */
  body{
    padding:
      calc(var(--pad) + env(safe-area-inset-top))
      calc(var(--pad) + env(safe-area-inset-right))
      calc(var(--pad) + env(safe-area-inset-bottom))
      calc(var(--pad) + env(safe-area-inset-left));
  }

  .grey-rect{
    margin: 0;
    width: calc(100vw - (2 * var(--pad)));
    height: calc(100svh - (2 * var(--pad)));   /* or 100dvh if you prefer */
    border-radius: 16px;
  }

  /* White image/card width scales a bit with the viewport */
  .card-bg{ width: clamp(55%, 65vw, 70%); }

  /* Fluid positioning & spacing */
  .card-content{
    left:   clamp(15px, 6vw, 50px);
    bottom: clamp(8px,  5vw, 30px);
    gap:    clamp(8px,  3vw, 12px);
  }

  .circle{
    width:      clamp(36px, 11vw, 44px);
    height:     clamp(36px, 11vw, 44px);
    flex-basis: clamp(36px, 11vw, 44px);
  }

  .title{
    font-size:     clamp(14px, 4.5vw, 16px);
    margin-bottom: clamp(2px,  1vw,  4px);
  }
  .meta{
    font-size: clamp(11px, 3.7vw, 13px);
  }
}
/* Safety net for very small phones (≤ 422px) */
@media (max-width: 450px){
  .card-bg{ width: 70%; }                /* make the white image span full width */

  .card-content{
    left: 12px;                           /* small inner padding */
    right: 12px;                          /* ← constrain width so text can't spill past the image */
    bottom: 12px;
    gap: 10px;
  }

  .circle{ width: 40px; height: 40px; flex-basis: 40px; }

  /* allow wrapping inside the available width */
  .text{ min-width: 0; }
  .title{ font-size: 15px; margin-bottom: 2px; }
  .meta{ font-size: 12px; }

  /* If you prefer truncation instead of wrapping, swap these in: */
  /* .title, .meta{ white-space: nowrap; overflow: hidden; text-overflow: ellipsis; } */
}

</style>
</head>
<body>

  <div class="grey-rect">
    <!-- WHITE card artwork -->
    <img
      class="card-bg"
      src="/wp-content/themes/allinclusive2.0/blocks/banner-blog/image/author.png"
      alt="" aria-hidden="true" />

    <!-- Content on top -->
    <div class="card-content">
      <div class="circle"></div>
      <div class="text">
        <div class="title">Lorem Ipsum Author</div>
        <p class="meta">September 15, 2025</p>
      </div>
    </div>
  </div>

</body>
</html>
