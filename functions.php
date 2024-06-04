<?php
// Add custom Theme Functions here

function ti_custom_javascript() {
	wp_enqueue_style( 'th-layout', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap-grid.min.css' );
	wp_enqueue_script( 'th-fonts-kit', 'https://kit.fontawesome.com/4da38f8016.js', array('jquery'), '1.0.0', true );
}
add_action('wp_enqueue_scripts', 'ti_custom_javascript');


// Function to handle the layout shortcode
function th_sim_layout_shortcode($atts, $content = null) {
    // Shortcode attributes with defaults
    $atts = shortcode_atts(
        array(
            'style' => 'default', // Default style
        ), 
        $atts, 
        'layout'
    );

    // Process nested shortcodes within the layout
    $content = do_shortcode($content);

    // Return the content wrapped in a div with a specific style
    return '<div class="container th-pricing"><div class="row">' . $content . '</div></div>';
}

// Function to handle the message shortcode
function th_sim_package_shortcode($atts) {

    // Shortcode attributes with defaults
    $atts = shortcode_atts(
        array(
            'product_id' => 'Default message', // Default message text
            'title' => 'black',          // Default color
            'coverage' => '16px',             // Default font size
            'data' => '16px'             // Default font size
        ), 
        $atts,
    );

    // Get the product variation object
    $variation = new WC_Product_Variation( $atts['product_id'] );

    // Get the price
    $price = $variation->get_price();

    // Get the parent product ID
    $parent_product_id = $variation->get_parent_id();

    // Get the current WooCommerce currency symbol
    $currency_symbol = get_woocommerce_currency_symbol();

    // Create the custom add-to-cart link
    $add_to_cart_url = add_query_arg(array(
        'add-to-cart' => $parent_product_id,
        'variation_id' => $atts['product_id'],
        'quantity' => 1 // You can customize the quantity if needed
    ), wc_get_cart_url());

    // Get the variation thumbnail ID
    $variation_thumbnail_id = $variation->get_image_id();
    // Check if the variation has a thumbnail
    if ( $variation_thumbnail_id ) {
        // Get the variation thumbnail URL
        $variation_thumbnail_url = wp_get_attachment_url( $variation_thumbnail_id );
    } else {
        // Get the parent product object
        $parent_product = wc_get_product( $parent_product_id );

        // Get the parent product thumbnail ID
        $parent_thumbnail_id = $parent_product->get_image_id();

        // Get the parent product thumbnail URL
        $variation_thumbnail_url = wp_get_attachment_url($parent_thumbnail_id);
    }

    // Get all attributes for the variation
    $attributes = $variation->get_attributes();

    // Check if the attribute exists and return its value
    if (isset($attributes['pa_validity'])) {
        $term_id = $attributes['pa_validity'];
        // Get the term object
        $term = get_term_by('slug', $term_id, 'pa_validity');
        $validity = $term->name;
    } else {
        $validity = 'N/A';
    }


    $markup = '';
    $markup .= '
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="th-sim-item">

            <div class="th-item-header">
                <div class="th-sim-item-header-info">
                    <p class="th-operator-title">'. esc_html( $atts['title'] ).'</p>
                </div>
                <div class="th-sim-item-image">
                    <img src="' . esc_attr( esc_url( $variation_thumbnail_url ) ) . '" width="140" alt="Change" class="lazyLoad isLoaded">
                </div>
            </div>

            <div class="sim-item-info">
                <ul class="sim-item-list">
                    <li>
                        <div class="th-sim_item-row">
                            <i class="fa-solid fa-globe"></i>
                            <p class="sim-item-row-left-key">COVERAGE</p>
                            <p class="sim-item-row-right-value">'. esc_html( $atts['coverage'] ).'</p>
                        </div>
                    </li>
                    <li>
                        <div class="th-sim_item-row">
                            <i class="fa-solid fa-arrow-right-arrow-left fa-rotate-90"></i>
                            <p class="sim-item-row-left-key">DATA</p>
                            <p class="sim-item-row-right-value" > '. esc_html( $atts['data'] ).' GB </p>
                        </div>
                    </li>
                    <li>
                        <div class="th-sim_item-row">
                            <i class="fa-solid fa-calendar"></i>
                            <p class="sim-item-row-left-key">VALIDITY</p>
                            <p class="sim-item-row-right-value"> '. esc_html( $validity ).' </p>
                        </div>
                    </li>
                    <!-- <li>
                        <div class="th-sim_item-row last">
                            <i class="fa-solid fa-tag fa-rotate-90"></i>
                            <p class="key sim-item-row-left-key" >PRICE</p>
                            <p class="sim-item-row-right-value"> '. esc_html( $currency_symbol ) . ' '. esc_html( $price ) .' </p>
                        </div>
                    </li> -->
                </ul>
            </div>
            <div class="th-sim-item-bottom">
                <div class="th-sim-item-bottom-button" >
                    <a href="'. esc_attr( esc_url( $add_to_cart_url ) ).'" class="btn btn-sim-item-btn btn-block" > BUY NOW <span>'. esc_html( $currency_symbol ) . ' '. esc_html( $price ) .'</span></a>
                </div>
            </div>
        </div>
    </div>
    ';
    // Return the styled message
    return $markup;
}

// Register the layout shortcode
add_shortcode('th_sim_layout', 'th_sim_layout_shortcode');

// Register the message shortcode
add_shortcode('th_sim_package', 'th_sim_package_shortcode');

/**
 * 
 * [th_sim_layout]
 * [th_sim_package product_id="772" title="Package A" coverage="Pakistan" data="2"]
 * [th_sim_package product_id="770" title="Package B" coverage="All Pakistan" data="3"]
 * [th_sim_package product_id="771" title="Package C" coverage="Pakistan" data="5"]
 * [/th_sim_layout]
 * 
 */