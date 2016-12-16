<?php
/**
 * The template for displaying all single posts.
 *
 */

get_header(); ?>

<div class="row">

	<div id="primary" class="content-area">

		<div class="large-12 columns">

			<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="featured-image">
						<?php // Featured Image
						if ( has_post_thumbnail() ) { 
				
							echo '<a href="' . esc_url( get_permalink() ) . '">';
							the_post_thumbnail( 'gateway-post-image' );
							echo '</a>';
				
						} // end featured image ?>
					</div><!-- .featured-image -->
				
					<header class="entry-header">
						<?php if (get_current_user_id()){
						if (get_user_meta(get_current_user_id(), "video_watched_" . get_the_ID(), true) == "true"){
						echo '<a class="watched-button inline-block watched" href=""><button>Completed</button></a>';
						}
						else {
						echo '<a class="watched-button inline-block" href=""><button>Mark Completed</button></a>';
						}
						}
						the_title( '<h3 class="entry-title inline-block pl15">', '</h3>' ); ?>
					</header><!-- .entry-header -->
					<?php if ((get_post_meta( get_the_ID(), "vimeo_link", true )) || (get_post_meta( get_the_ID(), "vimeo_link", true ))){ ?>
					<div class="embed-container"><iframe id="vimeoplayer" src="//player.vimeo.com/video/<?php echo get_post_meta( get_the_ID(), "vimeo_link", true ); ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					</div>
					<?php } ?>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php
							wp_link_pages( array(
								'before' => '<div class="page-links">' . __( 'Pages:', 'gateway' ),
								'after'  => '</div>',
							) );
						?>
					</div><!-- .entry-content -->
					
					<footer class="entry-footer clearfix">
				
					</footer><!-- .entry-footer -->
				
				</article><!-- #post-## -->
				

				<hr>

			<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->

		</div><!-- .large-12 -->

	</div><!-- #primary -->

</div><!-- .row -->

<?php get_footer(); ?>