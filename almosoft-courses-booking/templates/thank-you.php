<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package storefront
 */

get_header(); ?>
	<main>
		<div class="container" class="content-area">
			<div class="c-course__inner">
				<div class="container">
					<div class="c-page-banner__inner">
					  <h1>Thank you</h1>
					</div>
				  </div>
				<p><?php
					echo $data->message;
				?></p>

			</div><!-- #main -->
		</div><!-- #primary -->
	</main>
<?php

get_footer();
