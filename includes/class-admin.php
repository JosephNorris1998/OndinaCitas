<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SDC_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_menu() {
        add_menu_page(
            __( 'Sistema de Citas', 'sistemadecitas' ),
            __( 'Citas Ondina', 'sistemadecitas' ),
            'manage_options',
            'sistemadecitas',
            array( $this, 'render_settings_page' ),
            'dashicons-calendar-alt',
            30
        );
    }

    public function register_settings() {
        register_setting( 'sdc_settings_group', 'sdc_whatsapp_number', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '+50585374625',
        ) );

        register_setting( 'sdc_settings_group', 'sdc_schedule_start', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 13,
        ) );

        register_setting( 'sdc_settings_group', 'sdc_schedule_end', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 18,
        ) );

        register_setting( 'sdc_settings_group', 'sdc_slot_duration', array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
            'default'           => 60,
        ) );

        register_setting( 'sdc_settings_group', 'sdc_working_days', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_working_days' ),
            'default'           => array( 1, 2, 3, 4, 5 ),
        ) );
    }

    public function sanitize_working_days( $value ) {
        if ( ! is_array( $value ) ) {
            return array( 1, 2, 3, 4, 5 );
        }
        return array_map( 'absint', $value );
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $whatsapp    = get_option( 'sdc_whatsapp_number', '+50585374625' );
        $start       = (int) get_option( 'sdc_schedule_start', 13 );
        $end         = (int) get_option( 'sdc_schedule_end', 18 );
        $duration    = (int) get_option( 'sdc_slot_duration', 60 );
        $working_days = get_option( 'sdc_working_days', array( 1, 2, 3, 4, 5 ) );

        $days_labels = array(
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Configuración – Sistema de Citas', 'sistemadecitas' ); ?></h1>

            <?php settings_errors( 'sdc_settings_group' ); ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'sdc_settings_group' ); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="sdc_whatsapp_number"><?php esc_html_e( 'Número de WhatsApp', 'sistemadecitas' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="sdc_whatsapp_number" name="sdc_whatsapp_number"
                                value="<?php echo esc_attr( $whatsapp ); ?>"
                                class="regular-text"
                                placeholder="+50585374625"
                            />
                            <p class="description"><?php esc_html_e( 'Incluye el código de país, ej: +50585374625', 'sistemadecitas' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Días laborables', 'sistemadecitas' ); ?></th>
                        <td>
                            <?php foreach ( $days_labels as $num => $label ) : ?>
                                <label style="margin-right:12px;">
                                    <input type="checkbox"
                                        name="sdc_working_days[]"
                                        value="<?php echo esc_attr( $num ); ?>"
                                        <?php checked( in_array( $num, (array) $working_days, true ) ); ?>
                                    />
                                    <?php echo esc_html( $label ); ?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sdc_schedule_start"><?php esc_html_e( 'Hora de inicio (24h)', 'sistemadecitas' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="sdc_schedule_start" name="sdc_schedule_start"
                                value="<?php echo esc_attr( $start ); ?>"
                                min="0" max="23" step="1" class="small-text"
                            />
                            <p class="description"><?php esc_html_e( 'Ej: 13 = 1:00 pm', 'sistemadecitas' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sdc_schedule_end"><?php esc_html_e( 'Hora de fin (24h)', 'sistemadecitas' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="sdc_schedule_end" name="sdc_schedule_end"
                                value="<?php echo esc_attr( $end ); ?>"
                                min="0" max="24" step="1" class="small-text"
                            />
                            <p class="description"><?php esc_html_e( 'Ej: 18 = 6:00 pm (última cita empieza a las 5:00 pm)', 'sistemadecitas' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sdc_slot_duration"><?php esc_html_e( 'Duración del turno (minutos)', 'sistemadecitas' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="sdc_slot_duration" name="sdc_slot_duration"
                                value="<?php echo esc_attr( $duration ); ?>"
                                min="15" max="240" step="15" class="small-text"
                            />
                        </td>
                    </tr>
                </table>

                <?php submit_button( __( 'Guardar cambios', 'sistemadecitas' ) ); ?>
            </form>

            <hr>
            <h2><?php esc_html_e( 'Shortcode', 'sistemadecitas' ); ?></h2>
            <p><?php esc_html_e( 'Inserta el siguiente shortcode en la página donde deseas mostrar el formulario de citas:', 'sistemadecitas' ); ?></p>
            <code style="font-size:1.1em;">[sistema_de_citas]</code>
        </div>
        <?php
    }
}
