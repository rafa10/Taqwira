$(document).ready(function(){

    // =================================================================================================================
    // Compound framework materialize ==================================================================================
    // =================================================================================================================
    $(".button-collapse").sideNav();
    $('.tooltipped').tooltip({delay: 50});
    $('.stepper').activateStepper();
    $('.modal').modal();
    $('select').material_select();
    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 2, // Creates a dropdown of 15 years to control year
        labelMonthNext: 'Mois suivant',
        labelMonthPrev: 'Mois précédent',
        labelMonthSelect: 'Selectionner le mois',
        labelYearSelect: 'Selectionner une année',
        monthsFull: [ 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ],
        monthsShort: [ 'Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec' ],
        weekdaysFull: [ 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi' ],
        weekdaysShort: [ 'Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam' ],
        weekdaysLetter: [ 'D', 'S', 'T', 'Q', 'Q', 'S', 'S' ],
        today: 'Aujourd\'hui',
        clear: 'Effacer',
        close: 'Fermer',
        format: 'dd-mm-yyyy'
    });
    // =================================================================================================================
    // Smooth target ===================================================================================================
    // =================================================================================================================
    $("a[href*='#']:not([href='#'])").click(function() {
        if (
            location.hostname == this.hostname
            && this.pathname.replace(/^\//,"") == location.pathname.replace(/^\//,"")
        ) {
            var anchor = $(this.hash);
            anchor = anchor.length ? anchor : $("[name=" + this.hash.slice(1) +"]");
            if ( anchor.length ) {
                $("html, body").animate( { scrollTop: anchor.offset().top }, 1000);
            }
        }
    });

    //==================================================================================================================
    // Form check booking login ( front office ) =======================================================================
    //==================================================================================================================
    $(".form-login-booking").on("click",'.btn-check-booking-mail',function(){
        if ($(".formMailValidate").valid()) {
            form = $(this).closest('form');
            $('.alert-message').remove();
            $.ajax({
                data: new FormData(form[0]),
                url: form.attr('action'),
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                },
                success: function (data_json) {
                    data = $.parseJSON(data_json);
                    if (data.status == "ok") {
                        if (data.page == "error_mail") {
                            $('.mess-error-mail').append('<div class="card alert-message z-depth-0 red lighten-5"><div class="card-content red-text"><p><i class="material-icons left">error</i>Impossible de trouver la réservation associée</p></div></div>');

                        } else {
                            window.location.href = '/booking/management/' + data.reference;
                        }
                    }
                },
                error: function () {
                    alert("Une erreur est survenue");
                }

            });

        } else { return false}
    });

    // ==================================================================================================================

    $(".form-login-booking").on("click",'.btn-check-booking-name',function(){
        if ($(".formNameValidate").valid()) {
            form = $(this).closest('form');
            $('.alert-message').remove();
            $.ajax({
                data: new FormData(form[0]),
                url: form.attr('action'),
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                },
                success: function (data_json) {
                    data = $.parseJSON(data_json);
                    if (data.status == "ok") {
                        if (data.page == "error_name") {
                            $('.mess-error-name').append('<div class="card alert-message z-depth-0 red lighten-5"><div class="card-content red-text"><p><i class="material-icons left">error</i>Impossible de trouver la réservation associée</p></div></div>');

                        } else {
                            window.location.href = '/booking/management/' + data.reference;
                        }
                    }
                },
                error: function () {
                    alert("Une erreur est survenue");
                }

            });

        } else { return false}
    });

    //==================================================================================================================
    // Form check video login ( front office ) =========================================================================
    //==================================================================================================================
    $(".form-login-video").on("click",'#check-video-mail',function(){
        if ($(".formValidate").valid()) {
            form = $(this).closest('form');
            $('.alert-message').remove();
            $.ajax({
                data: new FormData(form[0]),
                url: form.attr('action'),
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                },
                success: function (data_json) {
                    data = $.parseJSON(data_json);
                    if (data.status == "ok") {
                        if (data.page == "refresh") {
                            $('.mess-error-mail').append('<div class="card alert-message z-depth-0 red lighten-5"><div class="card-content red-text"><p><i class="material-icons left">error</i>Impossible de trouver la réservation associée</p></div></div>');
                        } else {
                            window.location.href = '/video/management/' + data.reference;
                        }
                    }
                },
                error: function () {
                    alert("Une erreur est survenue");
                }

            });

        } else { return false}
    });


    // =================================================================================================================
    // Materialize refresh compound modal form =========================================================================
    // =================================================================================================================
    function refresh_Materialize_compound()
    {
        $('select').material_select();
        Materialize.updateTextFields();
        $('#textarea1').val('New Text');
        $('#textarea1').trigger('autoresize');

        $('.timepicker').pickatime({
            default: 'now',
            twelvehour: false, // change to 12 hour AM/PM clock from 24 hour
            donetext: 'OK',
            autoclose: false,
            vibrate: true // vibrate the device when dragging clock hand
        });

        $('.datepicker').pickadate({
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year
            format: 'dd/mm/yyyy',
        });

    }


    // =================================================================================================================
    // Content display if on load page =================================================================================
    // =================================================================================================================
    jQuery(window).load(function () {

        // On lance un ajax pour charger le booking match to day =================================================
        $.getJSON('/booking/form/search', function(data) {})
            .done(function(data) {
                if (data.status == "ok"){
                    if (data.page == "show") {
                        getRegion(data.region_city);
                        getCenter(data.center);
                    } else {
                        swal("", "Une erreur est survenue");
                    }
                }
            });

    });
    // Input Autocomplete Region & Ville ===============================================================================
    function getRegion($region_ville){
        $('input.autocomplete-region').autocomplete({
            data: $region_ville,
            limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
            onAutocomplete: function(val) {
                // Callback function when value is autcompleted.
                $.ajax({
                    url: '/booking/city_or_region/'+val,
                    type: 'GET',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function(){},
                    success: function(data_json){
                        data = $.parseJSON(data_json);
                        if (data.status=="ok") {
                            if (data.page=="search") {
                                $('#autocompelet-center').empty();
                                $('#autocompelet-center').html(data.html);
                                $('select').material_select();
                            }
                        }
                    },
                    error: function() {
                        alert("Une erreur est survenue");
                    }

                });
            },
            minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
        });
    }
    // =================================================================================================================
    function getCenter($center){
        $('input.autocomplete-center').autocomplete({
            data: $center,
            limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
            onAutocomplete: function(val) {
                // Callback function when value is autcompleted.
            },
            minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
        });
    }

    //==================================================================================================================
    // Form search booking match =======================================================================================
    //==================================================================================================================
    $("#form-booking-search").on("click",'#search',function(){
        if ($(".form-validation").valid()) {
            form = $(this).closest('form');
            $('.alert-message').remove();
            $.ajax({
                data: new FormData(form[0]),
                url: form.attr('action'),
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    $('#booking-search').empty();
                    $('#map-page-content').empty();
                    $('#preloader-page-content').removeClass('hide');
                },
                success: function(data_json){
                    data = $.parseJSON(data_json);
                    if (data.status=="ok") {
                        if (data.page=="search") {
                            $('#preloader-page-content').addClass('hide');
                            $('#booking-search').html(data.html);
                            initialise();
                        }
                    }
                },
                error: function() {
                    alert("Une erreur est survenue");
                }

            });

        } else {
            return false;

        }

    });

    // Initialise booking match & subscription modal ===================================================================
    function initialise(){

        $('.tooltipped').tooltip({delay: 50});
        $('.modal').modal();
        $('.carousel.carousel-slider').carousel({fullWidth: true});
        // initMap();

    }

    //==================================================================================================================
    // Form inscription center =========================================================================================
    //==================================================================================================================
    $('#fos_user_registration_form_center_region').on('change',function(){
        var regionID = $(this).val();
        console.log(regionID);
        $.ajax({
            type:'GET',
            url:'/register/region/'+regionID,
            cache: false,
            contentType: false,
            processData: false,
            success:function(data_json){
                data = $.parseJSON(data_json);
                if (data.status=="ok") {
                    if (data.page=="show") {
                        $('#city').empty();
                        $('#city').html(data.html);
                        $('select').material_select();

                    } else {
                        swal("", "Une erreur est survenue");
                    }
                }
            }
        });
    });

// === End =============================================================================================================
});


