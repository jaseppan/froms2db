<?php  

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

            <?php 
            $args = ['id' => intval($_GET['preview_id'])];
            echo forms2db_form($args);
            ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();

?>