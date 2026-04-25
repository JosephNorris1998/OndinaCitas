<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$services = array(
    'Asesoría Jurídica de Empresas',
    'Contratos Civiles y Mercantiles',
    'Mediaciones',
    'Arbitrajes',
    'Registros de Marcas Nacionales e Internacionales y Procesos de Oposición',
    'Trámites Institucionales',
    'Registros como Inversionista, Exportador e Importador',
    'Servicios Notariales en General',
    'Otro Servicio',
);
?>
<div class="sdc-wrapper" id="sdc-appointment-form">

    <!-- ── Indicador de pasos ── -->
    <div class="sdc-steps-nav" aria-label="Pasos del formulario">
        <div class="sdc-step-indicator active" data-step="1">
            <span class="sdc-step-number">1</span>
            <span class="sdc-step-label">Tus datos</span>
        </div>
        <div class="sdc-step-divider"></div>
        <div class="sdc-step-indicator" data-step="2">
            <span class="sdc-step-number">2</span>
            <span class="sdc-step-label">Servicio</span>
        </div>
        <div class="sdc-step-divider"></div>
        <div class="sdc-step-indicator" data-step="3">
            <span class="sdc-step-number">3</span>
            <span class="sdc-step-label">Fecha y hora</span>
        </div>
        <div class="sdc-step-divider"></div>
        <div class="sdc-step-indicator" data-step="4">
            <span class="sdc-step-number">4</span>
            <span class="sdc-step-label">Confirmar</span>
        </div>
    </div>

    <!-- ── PASO 1: Datos personales ── -->
    <div class="sdc-step" id="sdc-step-1">
        <h2 class="sdc-step-title">Tus datos de contacto</h2>

        <div class="sdc-field-group">
            <label for="sdc-fullname">Nombre completo <span aria-hidden="true">*</span></label>
            <input type="text" id="sdc-fullname" name="fullname"
                placeholder="Ej: María García López"
                autocomplete="name" required />
        </div>

        <div class="sdc-field-group">
            <label for="sdc-email">Correo electrónico <span aria-hidden="true">*</span></label>
            <input type="email" id="sdc-email" name="email"
                placeholder="correo@ejemplo.com"
                autocomplete="email" required />
        </div>

        <div class="sdc-field-group">
            <label for="sdc-phone">Número de teléfono <span aria-hidden="true">*</span></label>
            <div class="sdc-phone-row">
                <select id="sdc-country-code" name="country_code" class="sdc-country-select" aria-label="Código de país">
                    <!-- Populated by JS -->
                </select>
                <input type="tel" id="sdc-phone" name="phone"
                    placeholder="8888 8888"
                    autocomplete="tel-national" required />
            </div>
            <span class="sdc-phone-preview" id="sdc-phone-preview"></span>
        </div>

        <div class="sdc-nav">
            <button type="button" class="sdc-btn sdc-btn-next" data-next="2">Siguiente →</button>
        </div>
    </div>

    <!-- ── PASO 2: Servicio ── -->
    <div class="sdc-step sdc-hidden" id="sdc-step-2">
        <h2 class="sdc-step-title">Selecciona el servicio</h2>

        <div class="sdc-services-grid" role="group" aria-label="Servicios disponibles">
            <?php foreach ( $services as $svc ) : ?>
            <label class="sdc-service-card">
                <input type="radio" name="service" value="<?php echo esc_attr( $svc ); ?>" />
                <span class="sdc-service-label"><?php echo esc_html( $svc ); ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <div class="sdc-nav">
            <button type="button" class="sdc-btn sdc-btn-back" data-back="1">← Anterior</button>
            <button type="button" class="sdc-btn sdc-btn-next" data-next="3">Siguiente →</button>
        </div>
    </div>

    <!-- ── PASO 3: Fecha y hora ── -->
    <div class="sdc-step sdc-hidden" id="sdc-step-3">
        <h2 class="sdc-step-title">Elige fecha y hora</h2>

        <div class="sdc-datetime-wrap">
            <!-- Calendario inline -->
            <div class="sdc-calendar-wrap">
                <div class="sdc-cal-header">
                    <button type="button" class="sdc-cal-nav" id="sdc-cal-prev" aria-label="Mes anterior">&#8249;</button>
                    <span id="sdc-cal-title"></span>
                    <button type="button" class="sdc-cal-nav" id="sdc-cal-next" aria-label="Mes siguiente">&#8250;</button>
                </div>
                <div class="sdc-cal-weekdays">
                    <span>Do</span><span>Lu</span><span>Ma</span><span>Mi</span><span>Ju</span><span>Vi</span><span>Sa</span>
                </div>
                <div class="sdc-cal-days" id="sdc-cal-days" role="grid" aria-label="Calendario"></div>
                <input type="hidden" id="sdc-selected-date" name="selected_date" />
            </div>

            <!-- Selector de hora -->
            <div class="sdc-slots-wrap">
                <h3>Horarios disponibles</h3>
                <p class="sdc-slots-hint" id="sdc-slots-hint">Selecciona un día para ver los horarios.</p>
                <div class="sdc-slots-grid" id="sdc-slots-grid" role="group" aria-label="Horarios"></div>
                <input type="hidden" id="sdc-selected-time" name="selected_time" />
            </div>
        </div>

        <div class="sdc-nav">
            <button type="button" class="sdc-btn sdc-btn-back" data-back="2">← Anterior</button>
            <button type="button" class="sdc-btn sdc-btn-next" data-next="4">Siguiente →</button>
        </div>
    </div>

    <!-- ── PASO 4: Resumen y confirmación ── -->
    <div class="sdc-step sdc-hidden" id="sdc-step-4">
        <h2 class="sdc-step-title">Confirma tu cita</h2>

        <div class="sdc-summary" id="sdc-summary">
            <div class="sdc-summary-row"><strong>Nombre:</strong> <span id="sum-name"></span></div>
            <div class="sdc-summary-row"><strong>Correo:</strong> <span id="sum-email"></span></div>
            <div class="sdc-summary-row"><strong>Teléfono:</strong> <span id="sum-phone"></span></div>
            <div class="sdc-summary-row"><strong>Servicio:</strong> <span id="sum-service"></span></div>
            <div class="sdc-summary-row"><strong>Fecha:</strong> <span id="sum-date"></span></div>
            <div class="sdc-summary-row"><strong>Hora:</strong> <span id="sum-time"></span></div>
        </div>

        <p class="sdc-whatsapp-note">
            Al presionar <strong>"Confirmar por WhatsApp"</strong> serás redirigido a WhatsApp con toda la información de tu cita para confirmarla con la MSc. Ondina Mazier.
        </p>

        <div class="sdc-nav">
            <button type="button" class="sdc-btn sdc-btn-back" data-back="3">← Anterior</button>
            <button type="button" class="sdc-btn sdc-btn-whatsapp" id="sdc-whatsapp-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.52 3.48A11.93 11.93 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.17 1.6 5.99L0 24l6.18-1.57A11.94 11.94 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.21-3.48-8.52zM12 22c-1.85 0-3.65-.5-5.22-1.44l-.37-.22-3.87.99 1.02-3.78-.24-.39A9.94 9.94 0 0 1 2 12C2 6.48 6.48 2 12 2c2.67 0 5.18 1.04 7.07 2.93A9.94 9.94 0 0 1 22 12c0 5.52-4.48 10-10 10zm5.44-7.4c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.79-1.67-2.09-.17-.3-.02-.46.13-.61.13-.13.3-.35.44-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37s-1.04 1.02-1.04 2.48 1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.5 1.69.64.71.22 1.36.19 1.87.11.57-.08 1.76-.72 2.01-1.41.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.34z"/></svg>
                Confirmar por WhatsApp
            </button>
        </div>
    </div>

</div><!-- .sdc-wrapper -->
