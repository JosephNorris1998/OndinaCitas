<?php
/**
 * Plugin Name: Sistema de Citas Ondina
 * Plugin URI:  https://github.com/JosephNorris1998/OndinaCitas
 * Description: Sistema de agendamiento de citas con redirección a WhatsApp.
 * Version:     1.0.0
 * Author:      Ondina Mazier
 * License:     GPL-2.0+
 * Text Domain: sistemadecitas
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SDC_VERSION', '1.0.0' );
define( 'SDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once SDC_PLUGIN_DIR . 'includes/class-admin.php';
require_once SDC_PLUGIN_DIR . 'includes/class-appointments.php';

new SDC_Admin();
new SDC_Appointments();
