<?php
defined('ABSPATH') || exit;
function obem_home(){
?>
<div class="wrap">
<h1 class="obem-main-heading">:::Welcome to <span style="color: green;">OB</span> Event Manager:::</h1><hr>
<center>
<img src="<?php echo plugins_url( '../assets/img/obem-main.png', __FILE__ )?>">
<br>
<h2>Organize your events more easily...</h2>
<h3 class="shortcode-head">
<?php
$event_page_id = get_option("obem_page_id");
if ($event_page_id != null) {
?>
Use shortcode <span style="color: green;"><a class="obem-front-link" target="_blank" href="<?php echo get_page_link($event_page_id); ?>">[ob-event-manager]</a></span> any of your post | page.
<?php
} else { ?>
Use shortcode <span style="color: green;">[ob-event-manager]</span> any of your post | page.
<?php	
}
?>
</h3>
<h3><a href="<?php echo admin_url(); ?>admin.php?page=ObEventManagerImport" class="button button-primary button-large" href="">Import event csv data >></a></h3>
<h3><a href="<?php echo admin_url(); ?>edit.php?post_type=ob-event" class="button button-primary button-large" href="">Go to all events list >></a> 
<a href="<?php echo admin_url(); ?>post-new.php?post_type=ob-event" class="button button-primary button-large" href="">Create a new event >></a></h3>

</center>
</div>
<style>
.obem-front-link {text-decoration: none; color: green;}
.obem-main-heading {text-align: center;}
.obem-main-heading:hover {
    -ms-transform: scale(1.5); /* IE 9 */
    -webkit-transform: scale(1.5); /* Safari 3-8 */
    transform: scale(1.5); 
}
</style>
<?php
}