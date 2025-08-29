<?php
/**
 * Archive · Post Grid (Bootstrap)
 * Renders posts for the CURRENT category archive automatically.
 */
$attributes = is_array($attributes ?? null) ? $attributes : [];

$per_page    = (int)($attributes['perPage'] ?? 9);
$cols        = $attributes['cols'] ?? ['xs'=>1,'sm'=>2,'lg'=>3];
$show_excerpt= !empty($attributes['showExcerpt']);
$read_text   = $attributes['readText'] ?? 'Read more';
$image_size  = $attributes['imageSize'] ?? 'large';

$col_xs = max(1, (int)($cols['xs'] ?? 1));
$col_sm = max(1, (int)($cols['sm'] ?? 2));
$col_lg = max(1, (int)($cols['lg'] ?? 3));
$class_xs = 'col-'    . (int) floor(12 / $col_xs);
$class_sm = 'col-sm-' . (int) floor(12 / $col_sm);
$class_lg = 'col-lg-' . (int) floor(12 / $col_lg);

$paged = max(1, get_query_var('paged'));
$term  = get_queried_object();

$args = [
  'post_type'      => 'post',
  'post_status'    => 'publish',
  'posts_per_page' => $per_page,
  'paged'          => $paged,
];

if ($term && isset($term->taxonomy) && $term->taxonomy === 'category') {
  $args['cat'] = (int)$term->term_id;
}

$q = new WP_Query($args);

ob_start(); ?>

<div class="container px-0">
  <?php if ($q->have_posts()) : ?>
  <div class="row g-3 g-md-4 justify-content-center">
    <?php while ($q->have_posts()) : $q->the_post(); ?>
      <div class="<?php echo esc_attr("$class_xs $class_sm $class_lg"); ?>">
        <article class="ag-card card h-100 shadow-sm border-0">
          <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>" class="ag-media d-block">
              <?php the_post_thumbnail($image_size, [
                'class' => 'card-img-top',
                'loading' => 'lazy',
                'decoding'=> 'async',
                'alt'     => esc_attr(get_the_title()),
              ]); ?>
            </a>
          <?php endif; ?>

          <div class="card-body">
            <h3 class="h6 fw-bold mb-2 text-danger">
              <a class="stretched-link text-reset text-decoration-none" href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
              </a>
            </h3>
            <?php if ($show_excerpt) : ?>
              <p class="mb-0 text-muted small ag-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>
          </div>

          <div class="card-footer bg-transparent border-0 pt-0 pb-3 ps-3">
            <a class="btn btn-sm btn-outline-danger" href="<?php the_permalink(); ?>">
              <?php echo esc_html($read_text); ?>
            </a>
          </div>
        </article>
      </div>
    <?php endwhile; ?>
  </div>

  <?php
    // Bootstrap-styled pagination
    $links = paginate_links([
      'total'     => $q->max_num_pages,
      'current'   => $paged,
      'mid_size'  => 1,
      'prev_text' => '«',
      'next_text' => '»',
      'type'      => 'array'
    ]);
    if ($links) :
  ?>
    <nav class="mt-4" aria-label="Archive pagination">
      <ul class="pagination justify-content-center">
        <?php foreach ($links as $l) : ?>
          <li class="page-item"><?php
            // Allow WP to output classes; just wrap
            echo str_replace('page-numbers', 'page-link', $l);
          ?></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  <?php endif; ?>

  <?php else : ?>
    <p class="text-center text-muted my-5">No posts found in this category.</p>
  <?php endif; wp_reset_postdata(); ?>
</div>

<?php echo ob_get_clean();
