<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SDC_Appointments {

    public function __construct() {
        add_shortcode( 'sistema_de_citas', array( $this, 'render_form' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_sdc_get_slots', array( $this, 'ajax_get_slots' ) );
        add_action( 'wp_ajax_nopriv_sdc_get_slots', array( $this, 'ajax_get_slots' ) );
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'sdc-styles',
            SDC_PLUGIN_URL . 'assets/css/appointments.css',
            array(),
            SDC_VERSION
        );

        wp_enqueue_script(
            'sdc-scripts',
            SDC_PLUGIN_URL . 'assets/js/appointments.js',
            array( 'jquery' ),
            SDC_VERSION,
            true
        );

        $working_days = get_option( 'sdc_working_days', array( 1, 2, 3, 4, 5 ) );
        $start        = (int) get_option( 'sdc_schedule_start', 13 );
        $end          = (int) get_option( 'sdc_schedule_end', 18 );
        $duration     = (int) get_option( 'sdc_slot_duration', 60 );

        wp_localize_script( 'sdc-scripts', 'sdcConfig', array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'sdc_nonce' ),
            'workingDays' => array_values( array_map( 'intval', (array) $working_days ) ),
            'startHour'   => $start,
            'endHour'     => $end,
            'duration'    => $duration,
            'whatsapp'    => preg_replace( '/[^0-9]/', '', get_option( 'sdc_whatsapp_number', '50585374625' ) ),
        ) );
    }

    /**
     * Returns available time slots for a given date via AJAX.
     * Kept server-side so future booking persistence can block slots.
     */
    public function ajax_get_slots() {
        check_ajax_referer( 'sdc_nonce', 'nonce' );

        $date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
        if ( ! $date || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            wp_send_json_error( 'Fecha inválida.' );
            return;
        }

        $start    = (int) get_option( 'sdc_schedule_start', 13 );
        $end      = (int) get_option( 'sdc_schedule_end', 18 );
        $duration = (int) get_option( 'sdc_slot_duration', 60 );
        $slots    = array();

        $current = $start * 60;
        $finish  = $end * 60;

        while ( $current + $duration <= $finish ) {
            $h       = intdiv( $current, 60 );
            $m       = $current % 60;
            $label   = sprintf( '%02d:%02d', $h, $m );
            $ampm_h  = $h === 0 ? 12 : ( $h > 12 ? $h - 12 : $h );
            $ampm    = $h >= 12 ? 'pm' : 'am';
            $label12 = sprintf( '%d:%02d %s', $ampm_h, $m, $ampm );
            $slots[] = array(
                'value' => $label,
                'label' => $label12,
            );
            $current += $duration;
        }

        wp_send_json_success( $slots );
    }

    public function render_form() {
        ob_start();
        include SDC_PLUGIN_DIR . 'templates/appointment-form.php';
        return ob_get_clean();
    }
}
