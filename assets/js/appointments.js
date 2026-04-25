/* global sdcConfig, jQuery */
( function ( $ ) {
    'use strict';

    /* =====================================================================
       Datos de países con código de marcación
       ===================================================================== */
    var COUNTRIES = [
        { code: 'NI', name: 'Nicaragua',             dial: '+505' },
        { code: 'HN', name: 'Honduras',              dial: '+504' },
        { code: 'CR', name: 'Costa Rica',            dial: '+506' },
        { code: 'GT', name: 'Guatemala',             dial: '+502' },
        { code: 'SV', name: 'El Salvador',           dial: '+503' },
        { code: 'PA', name: 'Panamá',                dial: '+507' },
        { code: 'MX', name: 'México',                dial: '+52'  },
        { code: 'US', name: 'Estados Unidos',        dial: '+1'   },
        { code: 'ES', name: 'España',                dial: '+34'  },
        { code: 'CO', name: 'Colombia',              dial: '+57'  },
        { code: 'VE', name: 'Venezuela',             dial: '+58'  },
        { code: 'AR', name: 'Argentina',             dial: '+54'  },
        { code: 'CL', name: 'Chile',                 dial: '+56'  },
        { code: 'PE', name: 'Perú',                  dial: '+51'  },
        { code: 'EC', name: 'Ecuador',               dial: '+593' },
        { code: 'BO', name: 'Bolivia',               dial: '+591' },
        { code: 'PY', name: 'Paraguay',              dial: '+595' },
        { code: 'UY', name: 'Uruguay',               dial: '+598' },
        { code: 'CU', name: 'Cuba',                  dial: '+53'  },
        { code: 'DO', name: 'República Dominicana',  dial: '+1'   },
        { code: 'PR', name: 'Puerto Rico',           dial: '+1'   },
        { code: 'BR', name: 'Brasil',                dial: '+55'  },
    ];

    /* =====================================================================
       Estado global del formulario
       ===================================================================== */
    var formData = {
        fullname:   '',
        email:      '',
        dialCode:   '',
        phone:      '',
        service:    '',
        date:       '',
        dateLabel:  '',
        time:       '',
        timeLabel:  '',
    };

    var currentStep  = 1;
    var calYear      = 0;
    var calMonth     = 0;

    /* =====================================================================
       Inicialización
       ===================================================================== */
    $( document ).ready( function () {
        if ( ! $( '#sdc-appointment-form' ).length ) { return; }

        populateCountrySelect();
        bindStepNavigation();
        bindServiceCards();
        initCalendar();
        bindPhonePreview();
    } );

    /* =====================================================================
       Selector de país
       ===================================================================== */
    function populateCountrySelect() {
        var $sel = $( '#sdc-country-code' );
        $.each( COUNTRIES, function ( i, c ) {
            $sel.append(
                $( '<option>', {
                    value: c.dial,
                    text:  c.name + ' (' + c.dial + ')',
                    selected: c.code === 'NI',
                } )
            );
        } );
        formData.dialCode = $sel.val();
        updatePhonePreview();
    }

    function bindPhonePreview() {
        $( '#sdc-country-code' ).on( 'change', function () {
            formData.dialCode = $( this ).val();
            updatePhonePreview();
        } );
        $( '#sdc-phone' ).on( 'input', function () {
            formData.phone = $( this ).val().trim();
            updatePhonePreview();
        } );
    }

    function updatePhonePreview() {
        var raw   = formData.dialCode || '';
        var phone = $( '#sdc-phone' ).val().trim();
        var full  = phone ? raw + ' ' + phone : raw;
        $( '#sdc-phone-preview' ).text( 'Número completo: ' + full );
    }

    /* =====================================================================
       Navegación entre pasos
       ===================================================================== */
    function bindStepNavigation() {
        $( document ).on( 'click', '.sdc-btn-next', function () {
            var nextStep = parseInt( $( this ).data( 'next' ), 10 );
            if ( validateStep( currentStep ) ) {
                goToStep( nextStep );
            }
        } );

        $( document ).on( 'click', '.sdc-btn-back', function () {
            var prevStep = parseInt( $( this ).data( 'back' ), 10 );
            goToStep( prevStep );
        } );
    }

    function goToStep( step ) {
        $( '#sdc-step-' + currentStep ).addClass( 'sdc-hidden' );
        $( '.sdc-step-indicator[data-step="' + currentStep + '"]' )
            .removeClass( 'active' ).addClass( 'done' );

        currentStep = step;

        $( '#sdc-step-' + currentStep ).removeClass( 'sdc-hidden' );
        $( '.sdc-step-indicator[data-step="' + currentStep + '"]' )
            .removeClass( 'done' ).addClass( 'active' );

        if ( step === 4 ) {
            populateSummary();
        }

        $( 'html, body' ).animate( {
            scrollTop: $( '#sdc-appointment-form' ).offset().top - 30,
        }, 300 );
    }

    /* =====================================================================
       Validación por paso
       ===================================================================== */
    function validateStep( step ) {
        clearErrors();
        var valid = true;

        if ( step === 1 ) {
            var name  = $( '#sdc-fullname' ).val().trim();
            var email = $( '#sdc-email' ).val().trim();
            var phone = $( '#sdc-phone' ).val().trim();

            if ( ! name ) {
                showError( '#sdc-fullname', 'El nombre es requerido.' );
                valid = false;
            }
            if ( ! email || ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( email ) ) {
                showError( '#sdc-email', 'Ingresa un correo válido.' );
                valid = false;
            }
            if ( ! phone || ! /^\d[\d\s\-]{3,}$/.test( phone ) ) {
                showError( '#sdc-phone', 'Ingresa un número de teléfono válido.' );
                valid = false;
            }

            if ( valid ) {
                formData.fullname = name;
                formData.email    = email;
                formData.dialCode = $( '#sdc-country-code' ).val();
                formData.phone    = phone;
            }
        }

        if ( step === 2 ) {
            var service = $( 'input[name="service"]:checked' ).val();
            if ( ! service ) {
                addGeneralError( '#sdc-step-2 .sdc-services-grid', 'Selecciona un servicio.' );
                valid = false;
            } else {
                formData.service = service;
            }
        }

        if ( step === 3 ) {
            var date = $( '#sdc-selected-date' ).val();
            var time = $( '#sdc-selected-time' ).val();
            if ( ! date ) {
                addGeneralError( '#sdc-step-3 .sdc-calendar-wrap', 'Selecciona una fecha.' );
                valid = false;
            }
            if ( ! time ) {
                addGeneralError( '#sdc-step-3 .sdc-slots-wrap', 'Selecciona una hora.' );
                valid = false;
            }
            if ( valid ) {
                formData.date      = date;
                formData.time      = time;
                formData.timeLabel = $( '.sdc-slot-btn.selected' ).text();
                formData.dateLabel = formatDateLabel( date );
            }
        }

        return valid;
    }

    function showError( selector, message ) {
        var $el = $( selector );
        $el.addClass( 'sdc-error' );
        $el.after( '<span class="sdc-inline-error">' + message + '</span>' );
    }

    function addGeneralError( afterSelector, message ) {
        $( afterSelector ).after( '<span class="sdc-inline-error">' + message + '</span>' );
    }

    function clearErrors() {
        $( '.sdc-inline-error' ).remove();
        $( '.sdc-error' ).removeClass( 'sdc-error' );
    }

    /* =====================================================================
       Tarjetas de servicio – resaltado
       ===================================================================== */
    function bindServiceCards() {
        $( document ).on( 'change', '.sdc-service-card input[type="radio"]', function () {
            $( '.sdc-service-card' ).removeClass( 'selected' );
            $( this ).closest( '.sdc-service-card' ).addClass( 'selected' );
        } );
    }

    /* =====================================================================
       Calendario
       ===================================================================== */
    function initCalendar() {
        var today = new Date();
        calYear  = today.getFullYear();
        calMonth = today.getMonth();

        renderCalendar( calYear, calMonth );

        $( '#sdc-cal-prev' ).on( 'click', function () {
            calMonth--;
            if ( calMonth < 0 ) { calMonth = 11; calYear--; }
            renderCalendar( calYear, calMonth );
        } );

        $( '#sdc-cal-next' ).on( 'click', function () {
            calMonth++;
            if ( calMonth > 11 ) { calMonth = 0; calYear++; }
            renderCalendar( calYear, calMonth );
        } );
    }

    var MONTH_NAMES = [
        'Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre',
    ];

    function renderCalendar( year, month ) {
        var today       = new Date();
        today.setHours( 0, 0, 0, 0 );
        var workingDays = sdcConfig.workingDays || [ 1, 2, 3, 4, 5 ];

        $( '#sdc-cal-title' ).text( MONTH_NAMES[ month ] + ' ' + year );

        var firstDay = new Date( year, month, 1 ).getDay(); // 0=Sun
        var daysInMonth = new Date( year, month + 1, 0 ).getDate();

        var $grid = $( '#sdc-cal-days' ).empty();

        // Espacios vacíos antes del día 1
        for ( var e = 0; e < firstDay; e++ ) {
            $grid.append( '<button class="sdc-cal-day empty" tabindex="-1" aria-hidden="true"></button>' );
        }

        for ( var d = 1; d <= daysInMonth; d++ ) {
            var cellDate = new Date( year, month, d );
            var dow      = cellDate.getDay();
            var isToday  = cellDate.getTime() === today.getTime();
            var isPast   = cellDate < today;
            var isWorking = workingDays.indexOf( dow ) !== -1;
            var dateStr  = formatISO( year, month + 1, d );
            var isSelected = dateStr === $( '#sdc-selected-date' ).val();

            var classes = [ 'sdc-cal-day' ];
            if ( isToday ) { classes.push( 'today' ); }
            if ( isSelected ) { classes.push( 'selected' ); }
            if ( isPast || ! isWorking ) { classes.push( 'disabled' ); }

            var $btn = $( '<button>', {
                type:    'button',
                text:    d,
                'class': classes.join( ' ' ),
                'data-date': dateStr,
                'aria-label': dateStr,
                disabled: ( isPast || ! isWorking ),
            } );

            $btn.on( 'click', function () {
                $( '.sdc-cal-day' ).removeClass( 'selected' );
                $( this ).addClass( 'selected' );
                var selectedDate = $( this ).data( 'date' );
                $( '#sdc-selected-date' ).val( selectedDate );
                $( '#sdc-selected-time' ).val( '' );
                loadSlots( selectedDate );
            } );

            $grid.append( $btn );
        }
    }

    function loadSlots( date ) {
        var $grid = $( '#sdc-slots-grid' ).empty();
        var $hint = $( '#sdc-slots-hint' );
        $hint.text( 'Cargando horarios…' );

        $.post( sdcConfig.ajaxUrl, {
            action: 'sdc_get_slots',
            nonce:  sdcConfig.nonce,
            date:   date,
        }, function ( response ) {
            if ( response.success && response.data && response.data.length ) {
                $hint.hide();
                $.each( response.data, function ( i, slot ) {
                    var $btn = $( '<button>', {
                        type:    'button',
                        'class': 'sdc-slot-btn',
                        text:    slot.label,
                        'data-value': slot.value,
                    } );
                    $btn.on( 'click', function () {
                        $( '.sdc-slot-btn' ).removeClass( 'selected' );
                        $( this ).addClass( 'selected' );
                        $( '#sdc-selected-time' ).val( slot.value );
                    } );
                    $grid.append( $btn );
                } );
            } else {
                $hint.text( 'No hay horarios disponibles para este día.' ).show();
            }
        } ).fail( function () {
            $hint.text( 'Error al cargar horarios. Inténtalo de nuevo.' ).show();
        } );
    }

    /* =====================================================================
       Resumen – paso 4
       ===================================================================== */
    function populateSummary() {
        var fullPhone = ( formData.dialCode || '' ) + ' ' + ( formData.phone || '' );
        $( '#sum-name' ).text( formData.fullname );
        $( '#sum-email' ).text( formData.email );
        $( '#sum-phone' ).text( fullPhone.trim() );
        $( '#sum-service' ).text( formData.service );
        $( '#sum-date' ).text( formData.dateLabel || formData.date );
        $( '#sum-time' ).text( formData.timeLabel || formData.time );

        // Enlace WhatsApp
        var waNumber = sdcConfig.whatsapp || '50585374625';
        var msg = buildWhatsAppMessage( fullPhone );
        var url = 'https://wa.me/' + waNumber + '?text=' + encodeURIComponent( msg );

        $( '#sdc-whatsapp-btn' ).off( 'click' ).on( 'click', function () {
            window.open( url, '_blank', 'noopener,noreferrer' );
        } );
    }

    function buildWhatsAppMessage( fullPhone ) {
        return [
            '¡Hola! Deseo confirmar mi cita:',
            '',
            '👤 Nombre: '    + formData.fullname,
            '📧 Correo: '    + formData.email,
            '📱 Teléfono: '  + fullPhone.trim(),
            '⚖️ Servicio: '  + formData.service,
            '📅 Fecha: '     + ( formData.dateLabel || formData.date ),
            '🕐 Hora: '      + ( formData.timeLabel  || formData.time ),
        ].join( '\n' );
    }

    /* =====================================================================
       Utilidades
       ===================================================================== */
    function formatISO( y, m, d ) {
        return y + '-' + pad2( m ) + '-' + pad2( d );
    }

    function pad2( n ) {
        return n < 10 ? '0' + n : '' + n;
    }

    function formatDateLabel( iso ) {
        var parts = iso.split( '-' );
        if ( parts.length !== 3 ) { return iso; }
        var monthIndex = parseInt( parts[ 1 ], 10 ) - 1;
        if ( monthIndex < 0 || monthIndex > 11 ) { return iso; }
        return parts[ 2 ] + ' de ' + MONTH_NAMES[ monthIndex ] + ' de ' + parts[ 0 ];
    }

} )( jQuery );
