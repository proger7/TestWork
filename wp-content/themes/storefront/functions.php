<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);


require_once( ABSPATH . 'wp-admin/includes/image.php' );
require( dirname(__FILE__) . '/../../../wp-load.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php' );



require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */


/* Custom Code */

add_action( 'add_meta_boxes', 'image_metabox' );
 
function image_metabox() {
 
	add_meta_box(
		'image_meta', 
		'Image Upload for descriotion', 
		'image_uploader_callback', 
		'product', 
		'normal', 
		'default' 
	);
 
}

function image_uploader_callback( $product ) { 

    global $post;
    $postID = $post->ID;

    wp_nonce_field( basename( __FILE__ ), 'image_nonce' );
    $image_stored_meta = get_post_meta( $postID );

	?>
	
     <div class="image-preview-wrapper">
        <img id="image-preview" src="<?php if ( isset ( $image_stored_meta['image_meta'] ) ) echo $image_stored_meta['image_meta'][0]; ?>" style="max-width: 250px;">
        <input type="hidden" class="attechments-ids" name="image_meta" id="image-meta">
        <button type="button" id="image-upload-button" class="button">Upload Image</button>
        <button type="button" id="image-remove-button" class="button">Remove Image</button>
    </div>
    <script type="text/javascript">
        jQuery(function($) {

            $('body').on('click', '#image-upload-button', function(e) {
                e.preventDefault();

                var button = $(this),
                custom_uploader = wp.media({
                    title: 'Insert image',
                    button: { text: 'Use this image' },
                    multiple: true 
                }).on('select', function() {
                    var attech_ids = '';
                    attachments
                    var attachments = custom_uploader.state().get('selection'),
                    attachment_ids = new Array(),
                    i = 0;
                    attachments.each(function(attachment) {
                        attachment_ids[i] = attachment['id'];
                        attech_ids += ',' + attachment['id'];
                        if (attachment.attributes.type == 'image') {
                            $(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.url + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        } else {
                            $(button).siblings('ul').append('<li data-attechment-id="' + attachment['id'] + '"><a href="' + attachment.attributes.url + '" target="_blank"><img class="true_pre_image" src="' + attachment.attributes.icon + '" /></a><i class=" dashicons dashicons-no delete-img"></i></li>');
                        }

                        i++;
                    });

                    var ids = $(button).siblings('.attechments-ids').attr('value');
                    if (ids) {
                        var ids = ids + attech_ids;
                        $(button).siblings('.attechments-ids').attr('value', ids);
                    } else {
                        $(button).siblings('.attechments-ids').attr('value', attachment_ids);
                    }
                    $(button).siblings('#image-remove-button').show();
                })
                .open();
            });

            $('body').on('click', '#image-remove-button', function() {
                $(this).hide().prev().val('').prev().addClass('button').html('Add Media');
                $(this).parent().find('ul').empty();
                return false;
            });

        });

    </script>
    
    <?php
}

// Display Custom Admin Product Fields
add_action( 'woocommerce_product_options_general_product_data', 'add_admin_product_custom_general_fields' );

function add_admin_product_custom_general_fields() {
    global $product_object;

    echo '<div class="options_group custom_dates_fields">
        <p class="form-field custom_date_from_field" style="display:block;">
            <label for="_custom_date_from">' . esc_html__( 'Custom date range', 'woocommerce' ) . '</label>
            ' . wc_help_tip( __("This is a description for that date range fields (in a help tip)…", "woocommerce") ) . '
            <input type="text" class="short" name="_custom_date_from" id="_custom_date_from" value="' . esc_attr( $product_object->get_meta('_custom_date_from') ) . '" placeholder="' . esc_html( _x( 'From&hellip;', 'placeholder', 'woocommerce' ) ) . ' YYYY-MM-DD" maxlength="10" pattern="' . esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" />
        </p>
        <p class="form-field custom_date_to_field" style="display:block;">
            <input type="text" class="short" name="_custom_date_to" id="_custom_date_to" value="' . esc_attr( $product_object->get_meta('_custom_date_to') ) . '" placeholder="' . esc_html( _x( 'To&hellip;', 'placeholder', 'woocommerce' ) ) . '  YYYY-MM-DD" maxlength="10" pattern="' . esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ) . '" />
    </div>';

    ?>
    <script>
    jQuery( function($){
        $( '.custom_dates_fields' ).each( function() {
            $( this ).find( 'input' ).datepicker({
                defaultDate: '',
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                showButtonPanel: true,
                onSelect: function() {
                    var datepicker = $( this );
                        option         = $( datepicker ).next().is( '.hasDatepicker' ) ? 'minDate' : 'maxDate',
                        otherDateField = 'minDate' === option ? $( datepicker ).next() : $( datepicker ).prev(),
                        date           = $( datepicker ).datepicker( 'getDate' );

                    $( otherDateField ).datepicker( 'option', option, date );
                    $( datepicker ).change();
                }
            });
            $( this ).find( 'input' ).each( function() { date_picker_select( $( this ) ); } );
        });
    })
    </script>
    <?php
}

// Save Custom Admin Product Fields values
add_action( 'woocommerce_admin_process_product_object', 'save_admin_product_custom_general_fields_values' );

function save_admin_product_custom_general_fields_values( $product ){
    if( isset($_POST['_custom_date_from']) && isset($_POST['_custom_date_to']) ) {
        $product->update_meta_data( '_custom_date_from', esc_attr($_POST['_custom_date_from']) );
        $product->update_meta_data( '_custom_date_to', esc_attr($_POST['_custom_date_to']) );
    }
}

add_action( 'woocommerce_before_add_to_cart_form', 'production_time', 11 ); 

function production_time() {
    global $product;

    $production_time_from = $product->get_meta( '_custom_date_from' );
    $production_time_to = $product->get_meta( '_custom_date_to' );
    $product_select = $product->get_meta( 'custom_product_select_metabox' );

    $productImage = $product->get_meta( 'image_meta' );

    if ( ! empty($production_time_from) ) {
        echo '<p class="ri ri-clock">' . sprintf( __( ' Date from: %s', 'woocommerce' ), $production_time_from ) . '</p>';
    }
    if ( ! empty($production_time_to) ) {
        echo '<p class="ri ri-clock">' . sprintf( __( ' Date to: %s', 'woocommerce' ), $production_time_to ) . '</p>';
    }
    if ( ! empty($product_select) ) {
        echo '<p class="ri ri-clock">' . sprintf( __( ' Option value: %s', 'woocommerce' ), $product_select ) . '</p>';
    }

    if ( ! empty($productImage) ) {
        echo '<p class="ri ri-clock">' . sprintf( __( ' Image path: %s', 'woocommerce' ), $productImage ) . '</p>';
    }


}

// Create the custom product select metabox
add_action ('woocommerce_product_options_general_product_data', 'ecommercehints_custom_product_select_metabox');

function ecommercehints_custom_product_select_metabox() {
   echo '<div class="options_group">';
   woocommerce_wp_select(array ( // A select type field
		'id'                => 'custom_product_select_metabox',
		'value'             => get_post_meta(get_the_ID(), 'custom_product_select_metabox', true),
		'label'             => 'Custom Select Field Label',
		'description'       => 'This is the description',
		'desc_tip'          => true, // If true, place description in question mark tooltip.
		'options' => array(
			'rare' => 'Rare',
			'frequent' => 'Frequent',
			'unusual' => 'Unusual'
		),
  ));
   echo '</div>';
}

// Save data selected on update
add_action ('woocommerce_process_product_meta', 'ecommercehints_save_field_on_update', 10, 2);

function ecommercehints_save_field_on_update ($id, $post) {
    update_post_meta ($id, 'custom_product_select_metabox', $_POST['custom_product_select_metabox']);
}



function remove_custom_fields() {

    echo '<div class="buttons_group" style="margin-bottom: 40px;display:flex;margin-left:10px;margin-top:30px">';
    echo '<div class="options_group">';
    echo '<button id="remove_custom_fields" class="button button-primary button-large">REMOVE ALL CUSTOM FIELDS</button>';
    echo '</div>';

    echo '<form method="POST" class="options_group">';
    echo '<input type="hidden" name="product_id" value="'.get_the_ID().'">';
    echo '<input type="submit" class="button button-primary button-large" name="submit_update" id="submit_custom" value="UPDATE CUSTOM">';
    echo '</div>';
    echo '</div>';


    if( isset($_POST['submit_update']) && isset($_POST['product_id']) ) {
        $product = new WC_Product_Simple($_POST['product_id']);
        $product->save();
    }
    ?>
    <script>
        jQuery(document).ready(function() {
            jQuery(document).on('click', '#remove_custom_fields', function(e) {
                e.preventDefault();
                jQuery('#_custom_date_from').val("");
                jQuery('#_custom_date_to').val("");
                jQuery('#image-meta').val("");
                jQuery('#custom_product_select_metabox').val("");
            });
        });
    </script>
    <?php
}

add_action ('woocommerce_product_options_general_product_data', 'remove_custom_fields', 10, 5);

/* Create Product in Frontend */
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function createProduct() {

	if('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['tc_price']) ) {

	    //grab the values
	    $pPrice = $_POST["tc_price"];
	    $post_title = sanitize_text_field($_POST["tc_title"]);
	    $type = sanitize_text_field($_POST["custom_product_select_metabox"]);
	    $datePublished = $_POST["_custom_date_from"];


        /* Image Upload */
        $upload = wp_handle_upload( 
            $_FILES[ 'thumb_main_image' ], 
            array( 'test_form' => false ) 
        );

        $attachment_id = wp_insert_attachment(
            array(
                'guid'           => $upload[ 'url' ],
                'post_mime_type' => $upload[ 'type' ],
                'post_title'     => basename( $upload[ 'file' ] ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ),
            $upload[ 'file' ]
        );

        if( is_wp_error( $attachment_id ) || ! $attachment_id ) {
            wp_die( 'Upload error.' );
        }

        wp_update_attachment_metadata(
            $attachment_id,
            wp_generate_attachment_metadata( $attachment_id, $upload[ 'file' ] )
        );

	    //add them in an array
	    $post = array(
	        'post_status' => "publish",
	        'post_title' => $post_title,
	        'post_type' => "product",
	        'post_date'     =>   $datePublished,
	    );
	    //create product
	    $product_id = wp_insert_post( $post, __('Cannot create product', 'bones') );

	    //add price to the product, this is where you can add some descriptions such as sku's and measurements
	    update_post_meta( $product_id, '_regular_price', $pPrice );
	    update_post_meta( $product_id, '_price', $pPrice );
	    update_post_meta( $product_id, 'custom_product_select_metabox', $type);
        $media = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' );
        update_post_meta( $product_id, '_thumbnail_id', $attachment_id );


	    echo "<h2 style='border: 2px solid gray;padding: 5px'>Product <b>'".$post_title."'</b> was added successfully!</h2>";
	    echo "<br/>";
	}

	ob_start();
	?>


	<form method="post" action="" enctype="multipart/form-data">
		<div class="form-field" style="display: flex;justify-content: space-between;width: 50%;align-items: center;">
			<label for="tc_title" title="Product Name">Product Name</label>
	    	<input type="text" name="tc_title">
		</div>
		<br/>
		<div class="form-field" style="display: flex;justify-content: space-between;width: 50%;align-items: center;">
		    <label for="tc_price" title="Price">Price</label>
		    <input type="number" name="tc_price"/>
		</div>
		<br/>
		<div class="form-field" style="display: flex;justify-content: space-between;width: 50%;align-items: center;">
		    <label for="custom_product_select_metabox" title="Type">Type</label>
		    <select class="select short" name="custom_product_select_metabox">
		    	<option value="rare">Rare</option>
		    	<option value="frequent">Frequent</option>
		    	<option value="unusual">Unusual</option>
		    </select>
		</div>
		<br/>
		<div class="form-field" style="display: flex;justify-content: space-between;width: 50%;align-items: center;">
            <label for="_custom_date_from">Date Published</label>
            <input type="text" class="short" name="_custom_date_from" value="" placeholder="YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
		</div>
		<br/>
		<div class="form-field" style="display: flex;justify-content: space-between;width: 50%;align-items: center;">
			<input name="thumb_main_image" id="image" type="file" />
		</div>
		<br/>
	    <input type="submit" value="Submit"/>
	</form>

	<?php
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}

add_shortcode("create_product", "createProduct");
