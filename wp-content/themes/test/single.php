<?php get_header(); ?>

<div class="container">
	<div class="row">
		<?php if(have_posts()): ?>
			<?php while(have_posts()): ?>
				<?php the_post(); ?>
				<div class="col-md-12">
					<div class="card">
						<?= (has_post_thumbnail()) ?
							the_post_thumbnail('', ['class' => 'card-img-top', 'alt'   => 'Card image cap']):
							'<img class="card-img-top" src="https://picsum.photos/1275/680?image=0" alt="Card image cap">'
						?>
						<div class="card-body">
							<h5 class="card-title"><?php the_title() ?></h5>
							<p class="card-text"><?php the_content() ?></p>
						</div>
					</div>
				</div>
			<?php endwhile; ?>

		<?php else: ?>
			<p> Постов нет ...</p>
		<?php endif; ?>

		<?php get_footer(); ?>

	</div>
</div>
