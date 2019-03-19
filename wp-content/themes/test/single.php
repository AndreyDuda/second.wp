<?php get_header(); ?>

<div class="container">
	<div class="row">
		<?php if(have_posts()): ?>
			<?php while(have_posts()): ?>
				<?php the_post(); ?>
				<div class="col-md-12">
					<div class="card">
						<img class="card-img-top" src="..." alt="Card image cap">
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