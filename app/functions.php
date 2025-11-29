<?php
function istiaqhossain_optivine_admin_title() {
    echo esc_html( apply_filters( 'istiaqhossain_optivine_title', __( 'Optivine Admin', 'istiaqhossain-optivine' ) ) );
}

function istiaqhossain_optivine_admin_head() {
    do_action( 'istiaqhossain_optivine_admin_head' );
}

function istiaqhossain_optivine_admin_body_class() {
    echo esc_attr( apply_filters( 'istiaqhossain_optivine_body_class', 'istiaqhossain-optivine-admin' ) );
}

function istiaqhossain_optivine_admin_footer() {
    do_action( 'istiaqhossain_optivine_admin_footer' );
}

function istiaqhossain_optivine_get_option( $option_key, $default = null ) {

    $value = get_option( ISTIAQHOSSAIN_OPTIVINE_PREFIX . $option_key, $default );

    if ( is_serialized( $value ) ) {
        $value = maybe_unserialize( $value );
    }

    // JSON.
    if ( is_string( $value ) && is_array( json_decode( $value, true ) ) && ( json_last_error() === JSON_ERROR_NONE ) ) {
        $value = json_decode( $value, true );
    }

    return $value;
}

function istiaqhossain_optivine_load_view( $template ) {
    $template_path = ISTIAQHOSSAIN_OPTIVINE_TEMPLATES . $template . '.php';

    if ( ! file_exists( $template_path ) ) {
        return '';
    }

    include $template_path;
    return '';
}
