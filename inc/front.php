<?php
defined('ABSPATH') || exit;
// Shortcode for listing events
add_shortcode( 'ob-event-manager', 'obem_front_listing' );
function obem_front_listing( $atts ) {
    ob_start();
     // define attributes and their defaults
    extract( shortcode_atts( array (
        'type' => 'ob-event',
        'order' => 'date',
        'orderby' => 'title',
        'posts' => -1,
     ), $atts ) );
 
    // define query parameters based on attributes
    $options = array(
        'post_type' => $type,
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $posts,
    );
    $query = new WP_Query( $options );
    // run the loop based on the query
    if ( $query->have_posts() ) {
    ?>
    <center>
    <input id="txtSearch" placeholder="Search By Keyword..." class="search-input-spcial" />
    <label>&nbsp;--OR--&nbsp;</label>
    <?php
    $dates = array();
    while ( $query->have_posts() ) : $query->the_post();
    	array_push( $dates, get_the_title() );
    	$filter_dates = array_unique($dates);
	 endwhile;
     wp_reset_postdata();
     //print_r($filter_dates);
    ?>
    <select id="dteSearch" class="search-input-spcial">
	  <option value="">Search By Date...</option>
	  <?php foreach ($filter_dates as $filter_date ) { 
	  echo '<option value="'.$filter_date.'">'.$filter_date.'</option>';
	 } ?>
	</select>
    </center>
     <table id="eventListing" class="display" style="width:100%">
        <thead>
        <tr>
            <th>Start Date</th>
            <th>Start Time</th>
            <th>Event Name</th>
            <th>Event Location</th>
        </tr>
        </thead>
        <tbody>

           <?php
            while ( $query->have_posts() ) : $query->the_post(); ?>
              <tr class="obem-event-<?php the_ID(); ?>" <?php post_class(); ?>>
              <?php
               echo '<th>'.obem_details_get_meta( 'event_details_event_start_date' ).'</th>
                    <th>'.obem_details_get_meta( 'event_details_event_start_time' ).'</th>
                    <th><a href="#obem-det'.get_the_ID().'" rel="modal:open">'.obem_details_get_meta( 'event_details_event_name' ).'</a></th>
                <th>'.obem_details_get_meta( 'event_details_event_location' ).'</th>';
            echo'</tr>'; ?>
            <div id="obem-det<?php echo get_the_ID(); ?>" class="modal">
                <h2>Details :</h2>
                    <p>Date : <?php echo obem_details_get_meta( 'event_details_event_start_date' ); ?> - <?php echo obem_details_get_meta( 'event_details_event_end_date' ); ?></p>
                    <p>Time : <?php echo obem_details_get_meta( 'event_details_event_start_time' ); ?> - <?php echo obem_details_get_meta( 'event_details_event_end_time' ); ?></p>
                    <p>Category : <?php echo obem_details_get_meta( 'event_details_event_category' ); ?></p>
                    <p>Event Type : <?php echo obem_details_get_meta( 'event_details_event_type' ); ?></p>
                    <p>Description : <?php echo obem_details_get_meta( 'event_details_event_description' ); ?></p>
            </div>
            <?php
            endwhile;
            wp_reset_postdata();
          ?>

        </tbody>
        <tfoot>
        <tr>
            <th>Start Date</th>
            <th>Start Time</th>
            <th>Event Name</th>
            <th>Event Location</th>
          </tr>
        </tfoot>
    </table>
    <style>
.search-input-spcial{
  width: 45%;
  height: 35px;
  margin-bottom: 15px;
}
#eventListing_filter{
    display: none;
}
    </style>
        <script>
            jQuery(document).ready(function($) {
                
                $('#txtSearch').on('keyup', function() {
                    $('#eventListing')
                        .DataTable()
                        .search($('#txtSearch').val(), false, true)
                        .draw();
                });

                $('#dteSearch').on('change', function() {
                    $('#eventListing')
                        .DataTable()
                        .search($('#dteSearch').val(), false, true)
                        .draw();
                });
 
            var table = $('#eventListing').DataTable({
             "columnDefs": [
                        { "visible": false,  "targets": [0,1], "order": [0]},
                        ],
                        "searching": true,
                    	"displayLength": 10,
                        "rowGroup": {
                  
                        dataSrc: 0,
                        startRender: null,
                        endRender: function ( rows, group) {
                            var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                   					 };
                            //var numFormat = window.getFormatNumberTable().display;
                            var sumWeight = rows
                                .data()
                                .pluck(2)
                                .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);

                            return $('<tr/>')
                                .append( '<td>'+ 'OB "EVENT" Manager: ' +  sumWeight +'</td>' );
                                },
                            },
        			      	"drawCallback": function ( settings ) {
        			            var api = this.api();
        			            var rows = api.rows( {page:'current'} ).nodes();
        			            var last = null;
        			            api.rows({page:'current'}).data().each(function (data, i){
        			                var group = data[0];
        			                //var groupLink =  $('<div>').text(group).html() ;
        			                var groupLink = '<span>' + $('<div>').text(group).html() + '</span>';
                        
        			                
        			                if ( last !== group ) {
        			                    $(rows).eq( i ).before(
        			                        '<tr class="group start-date"><td colspan="5">'+groupLink+'</td></tr>'
        			                    );
        			 
        			                    last = group;
        			                }
        			            });
        			              api.rows({page:'current'}).data().each(function (data, i){
        			                var group = data[1];
        			                var groupLink = $('<div>').text(group).html();
        			                
        			                if ( last !== group ) {
        			                    $(rows).eq( i ).before(
        			                        '<tr class="group start-time"><td colspan="5">'+groupLink+'</td></tr>'
        			                    );
        			 
        			                    last = group;
        			                }
        			            });
        			        }
        			    });
        		

            } );

        </script>
       <?php
        wp_enqueue_style('get-datatable-style' );
        wp_enqueue_script('get-datatable-script' );
        wp_enqueue_style('get-modal-style' );
        wp_enqueue_script('get-modal-script' );
        $eventvariable = ob_get_clean();
        return $eventvariable;
        }
    }