<?php
defined('ABSPATH') || exit;
function obem_import(){
global $wpdb;
$message = '';
$tablename = $wpdb->prefix . "posts";

if (isset($_POST["import"]) && wp_verify_nonce( $_POST['obem_import_nonce'], '_obem_import_nonce' ) && is_admin() && current_user_can('administrator')) {

    
    $fileName = $_FILES["file"]["tmp_name"];
    
    if ($_FILES["file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");

         $column = fgetcsv($file,1000,",",'"');
        
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {

            $event_details_event_start_date =sanitize_text_field( $column[0] );
            $event_details_event_end_date = sanitize_text_field( $column[1] );
            $event_details_event_start_time = sanitize_text_field( $column[2] );
            $event_details_event_end_time = sanitize_text_field( $column[3] );
            $event_details_event_name = sanitize_text_field( $column[4] );
            $event_details_event_location = sanitize_text_field( $column[5] );
            $event_details_event_category = sanitize_text_field( $column[6] );
            $event_details_event_type = sanitize_text_field( $column[7] );
            $event_details_event_description = sanitize_textarea_field( $column[8] );

            $my_event = array(
             'post_title' => $event_details_event_start_date,
             'post_status' => 'publish',
             'post_type' => 'ob-event',
            );

            $event_id = wp_insert_post($my_event);

            add_post_meta($event_id, 'event_details_event_start_date', $event_details_event_start_date, true);
            add_post_meta($event_id, 'event_details_event_end_date', $event_details_event_end_date, true);
            add_post_meta($event_id, 'event_details_event_start_time', $event_details_event_start_time, true);
            add_post_meta($event_id, 'event_details_event_end_time', $event_details_event_end_time, true);
            add_post_meta($event_id, 'event_details_event_name', $event_details_event_name, true);
            add_post_meta($event_id, 'event_details_event_location', $event_details_event_location, true);
            add_post_meta($event_id, 'event_details_event_category', $event_details_event_category, true);
            add_post_meta($event_id, 'event_details_event_type', $event_details_event_type, true);
            add_post_meta($event_id, 'event_details_event_description', $event_details_event_description, true);

            if (! empty($event_id)) {
                    $type = "success";
                    $message = "You have successfully imported all events data!";
                    $link = '<a href="'.admin_url().'/edit.php?post_type=ob-event">Go to all events</a>';
                    $color='green';
                } else {
                    $type = "error";
                    $message = "Problem in Importing CSV Data";
                    $link = '<a href="'.admin_url().'/admin.php?page=ObEventManagerImport">Import again</a>';
                    $color='red';
                }
            }
      }
   }
?>
<div class="wrap">
<h1>Upload CSV FILE <a class="OBcsvButton"  href="<?php echo plugins_url( '../assets/obem_sample_csv.csv', __FILE__ ); ?>" download>Sample CSV</a></h1><hr>
<form class="form-horizontal" action="" method="post" name="uploadCSV"
    enctype="multipart/form-data">
    <?php wp_nonce_field( '_obem_import_nonce', 'obem_import_nonce' ); ?>
    <div class="input-row">
       <input
            type="file" name="file" id="file" accept=".csv">
        <h3><button type="submit" class="button button-primary button-large" id="submit" name="import"
            class="btn-submit">Import CSV file</button></h3>
    
    </div>
    <div id="labelError"><h4 style="color: <?php echo $color; ?>"><?php if(isset($message)) : echo $message; endif; ?>&nbsp;<?php if (isset($link)) : echo $link; endif;  ?></h4></div>
</form>
</div>
<style>
.OBcsvButton {
    -moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
    -webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
    box-shadow:inset 0px 1px 0px 0px #ffffff;
    background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ffffff), color-stop(1, #f6f6f6));
    background:-moz-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
    background:-webkit-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
    background:-o-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
    background:-ms-linear-gradient(top, #ffffff 5%, #f6f6f6 100%);
    background:linear-gradient(to bottom, #ffffff 5%, #f6f6f6 100%);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f6f6f6',GradientType=0);
    background-color:#ffffff;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    border-radius:6px;
    border:1px solid #dcdcdc;
    display:inline-block;
    cursor:pointer;
    color:#666666;
    font-family:Arial;
    font-size:14px;
    font-weight:bold;
    padding:2px 10px;
    text-decoration:none;
    text-shadow:0px 1px 0px #ffffff;
}
.OBcsvButton:hover {
    background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f6f6f6), color-stop(1, #ffffff));
    background:-moz-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
    background:-webkit-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
    background:-o-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
    background:-ms-linear-gradient(top, #f6f6f6 5%, #ffffff 100%);
    background:linear-gradient(to bottom, #f6f6f6 5%, #ffffff 100%);
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f6f6f6', endColorstr='#ffffff',GradientType=0);
    background-color:#f6f6f6;
}
.OBcsvButton:active {
    position:relative;
    top:1px;
}

</style>
<?php
}