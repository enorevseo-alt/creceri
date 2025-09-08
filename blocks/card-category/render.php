<?php
  // Required attributes only
  $buttons      = $attributes['button'] ?? [];
  $header_title = $attributes['header'] ?? '';
  $sub_title    = $attributes['sub-title'] ?? '';
?>
<section class="container py-5">
  <!-- Header -->
  <div class="text-center mb-4">
    <?php if ($header_title !== ''): ?>
      <h2 class="fw-bold mb-2"><?php echo esc_html($header_title); ?></h2>
    <?php endif; ?>
    <?php if ($sub_title !== ''): ?>
      <p class="text-muted mb-0"><?php echo esc_html($sub_title); ?></p>
    <?php endif; ?>
  </div>

  <!-- Tabs -->
  <?php if (!empty($buttons)) : ?>
    <ul class="nav nav-pills gap-2 mb-4" id="visitTabs" role="tablist">
      <?php foreach ($buttons as $btn) :
        $button_name = $btn['button_name'] ?? '';
        $tag         = $btn['panel_tag'] ?? '';
        $is_active   = ($btn['status'] ?? '') === 'active';
      ?>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link <?php echo $is_active ? 'active' : ''; ?> rounded-3 px-3 py-2"
            id="<?php echo esc_attr($tag); ?>-tab"
            type="button"
            role="tab"
            data-bs-toggle="tab"
            data-bs-target="#<?php echo esc_attr($tag); ?>-pane"
            aria-controls="<?php echo esc_attr($tag); ?>-pane"
            aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
            <?php echo esc_html($button_name); ?>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="tab-content">
      <?php foreach ($buttons as $btn) :
        $panel_tag = $btn['panel_tag'] ?? '';
        $is_active = ($btn['status'] ?? '') === 'active';
      ?>
        <div class="tab-pane fade <?php echo $is_active ? 'show active' : ''; ?>"
             id="<?php echo esc_attr($panel_tag); ?>-pane"
             role="tabpanel"
             aria-labelledby="<?php echo esc_attr($panel_tag); ?>-tab">

          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 slider-row">
            <?php if (!empty($btn['items'])) : ?>
              <?php foreach ($btn['items'] as $card) :
                $image        = $card['image'] ?? '';
                $heading_data = $card['heading_data'] ?? '';
                $text         = $card['text'] ?? '';
                $url          = $card['url'] ?? '';
              ?>
                <div class="col">
                  <div class="card shadow-sm border-0 rounded-4 h-100">
                    <?php if ($image): ?>
                      <img class="card-img-top rounded-3 object-cover fixed-h"
                           src="<?php echo esc_url($image); ?>"
                           alt="<?php echo esc_attr($heading_data); ?>">
                    <?php endif; ?>
                    <div class="card-body pt-0">
                      <?php if ($heading_data !== ''): ?>
                        <h5 class="fw-bold mb-1"><?php echo esc_html($heading_data); ?></h5>
                      <?php endif; ?>
                      <?php if ($text !== ''): ?>
                        <p class="text-muted mb-3"><?php echo esc_html($text); ?></p>
                      <?php endif; ?>
                      <?php if ($url !== ''): ?>
                        <a href="<?php echo esc_url($url); ?>" class="btn btn-cta w-100 fw-semibold rounded-3 btn-danger">Explore</a>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <button class="slider-prev d-md-none" aria-label="Previous">&#10094;</button>
          <button class="slider-next d-md-none" aria-label="Next">&#10095;</button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script>
(function(){
  function setup(row){
    if (!row || row.dataset.sliderInit) return;
    row.dataset.sliderInit = '1';
    const pane = row.closest('.tab-pane');
    const prev = pane?.querySelector('.slider-prev');
    const next = pane?.querySelector('.slider-next');
    const getStep = () => {
      const first = row.children[0];
      const w = first ? first.getBoundingClientRect().width : row.clientWidth;
      const gap = 16;
      return w + gap;
    };
    const scrollBySlide = dir => row.scrollBy({ left: dir * getStep(), behavior: 'smooth' });
    prev && prev.addEventListener('click', () => scrollBySlide(-1));
    next && next.addEventListener('click', () => scrollBySlide(1));
  }

  function init(){
    document.querySelectorAll('.slider-row').forEach(setup);
  }

  if (document.readyState !== 'loading') init();
  else document.addEventListener('DOMContentLoaded', init);

  document.addEventListener('shown.bs.tab', (e) => {
    const pane = document.querySelector(e.target.getAttribute('data-bs-target'));
    pane && setup(pane.querySelector('.slider-row'));
  });
})();
</script>
