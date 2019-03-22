<?php get_header(); ?>

<div class="container">
    <div class="row">
        <div class="col">
            <div class="row">
<?php if(have_posts()): ?>
    <?php while(have_posts()): ?>
        <?php the_post(); ?>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><?php the_title() ?></h5>
                </div>
                <div class="card-body">
	                <?= (has_post_thumbnail()) ?
		                the_post_thumbnail('thumbnail', ['class' => 'float-left mr-3', 'alt'   => 'Card image cap']):
		                '<img class="float-left mr-3" src="https://picsum.photos/150/150?image=0" alt="Card image cap"  width="150" height="150">'
	                ?>
                    <p class="card-text"><?php the_excerpt() ?></p>
                </div>
                <div class="card-footer">
                    <a href="<?php the_permalink() ?>" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    <?php the_posts_pagination([
            'end_size' => 2,
            'mid)size' => 2,
            'type'     => 'list'
    ]); ?>
<?php else: ?>
    <p> Постов нет ...</p>
<?php endif; ?>
            </div>
        </div>
        <?php get_sidebar() ?>
    </div>
</div>

<?php

$query = new WP_Query(array(
    'category_name' => 'edge-case-2,markup',
    'posts_per_page' => -1,
    'orderby'       => 'title',
    'order'         => 'ASC'
));

if ($query->have_posts()) {

    while ($query->have_posts()) {
        $query->the_post();
        echo '<h3>' . the_title() . '</h3>';
    }
}
wp_reset_postdata();

?>

<?php get_footer(); ?>
