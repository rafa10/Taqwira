﻿/*================================================================================
NOTE:
------
PLACE HERE YOUR OWN JS CODES AND IF NEEDED.
WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR CUSTOM SCRIPT IT'S BETTER LIKE THIS.
================================================================================= */

$(document).ready(function(){

    // =================================================================================================================
    // Compound framework materialize ==================================================================================
    // =================================================================================================================
    $(".button-collapse").sideNav();
    $('.tooltipped').tooltip({delay: 50});
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

    // =================================================================================================================
    // Open modal form type for all entities (crud) ====================================================================
    // =================================================================================================================
    $('.modal_trigger').click(function() {
        url = $(this).attr("data-href");
        //On lance un ajax pour charger le formulaire
        $.getJSON($(this).attr("data-href"), function(data) {})
            .done(function(data) {
                if (data.status=="ok")
                {
                    if (data.page=="refresh")
                    {
                        $('#form_modal').closeModal();
                        location.reload();
                    }
                    else
                    {
                        $('#form_modal').html(data.html);
                        $('#form_modal').attr('data-href', url);
                        refresh_Materialize_compound();
                        $('#form_modal').openModal();
                    }
                }
            })

        });

    // =================================================================================================================
    // Send modal form type for all entities (crud) ====================================================================
    // =================================================================================================================
    $("#form_modal").on("click",'#save',function(){
        form = $(this).closest('form');
        // console.log(form);
        $.ajax({
            data: new FormData(form[0]),
            url: form.attr('action'),
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('#form_modal').closeModal();
                $('.loader_form').removeClass('hide');
            },
            success: function(data_json){
                data = $.parseJSON(data_json);
                // console.log(data);
                if (data.status=="ok") {
                    if ( data.page=="index") {
                        $('#form_modal').closeModal();

                    } else if (data.page=="edit") {
                        $('#modal_form').html(data.html);
                        refresh_Materialize_compound();
                        $('select').material_select('destroy');

                    } else if (data.page=="refresh") {
                        $('#form_modal').closeModal();
                        location.reload();
                        $('#loader-wrapper').remove();
                    }
                    else {
                        $('.loader_form').addClass('hide');
                        $('#form_modal').html(data.html);
                        refresh_Materialize_compound();
                        $('#form_modal').openModal();
                    }
                }
            },
            error: function() {
                alert('Une erreur est survenue');
                $('#form_modal').closeModal();

            }

        });
    });


    // =================================================================================================================
    // Btn for modal confirm delete all entities type ==================================================================
    // =================================================================================================================
    $('.btn-warning-confirm').click(function(){
        that = $(this);
        swal({  title: "Êtes-vous sûr ?",
                // text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Oui, supprimez-le !",
                cancelButtonText: "Non, annulez !",
                closeOnConfirm: false,
                closeOnCancel: false },
            function(isConfirm){
                if (isConfirm) {
                    // On lance la suppression
                    $.get(that.attr("data-href"), function() {
                        swal("Supprimé!", "Votre centre a été supprimé!", "success");
                        location.reload();
                    });
                } else {
                    swal("Annulé", "", "error");
                }
            });
    });

    // =================================================================================================================
    // Btn Add & Remove ROLE_ADMIN for user ============================================================================
    // =================================================================================================================
    $('.btn-add-role').click(function(){
        url = $(this);
        swal({  title: "",
                text: "Ajouter un rôle administrateur ?",
                type: "info",   showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true, },
            function(){
                // On lance l'ajout de role
                $.get(url.attr("data-href"), function() {
                    location.reload();
                });
            });
    });

// =====================================================================================================================
    $('.btn-remove-role').click(function(){
        url = $(this);
        swal({  title: "",
                text: "Supprimé rôle administrateur ?",
                type: "info",   showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true, },
            function(){
                // On lance l'ajout de role
                $.get(url.attr("data-href"), function() {
                    location.reload();
                });
            });
    });

    // =================================================================================================================
    // Btn booking match & subscription paid action ====================================================================
    // =================================================================================================================
    $('.btn-booking-paid').click(function() {
        that = $(this);
        swal({
                title: "Confirmer le paiement",
                // text: "Confirmer votre choix",
                type: "info", showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            },
            function () {
                setTimeout(function () {
                    $.get(that.attr("data-href"), function() {
                        swal("", "Réservation bien payée!");
                        location.reload();
                    });
                }, 2000);
            });
    });

    // =================================================================================================================
    $("#form_checkbox_paid").on("click",'#paid',function(){
        form = $(this).closest('form');
        $.ajax({
            data: new FormData(form[0]),
            url: form.attr('action'),
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){},
            success: function(data_json){
                data = $.parseJSON(data_json);
                if (data.status=="ok") {
                    if (data.page=="refresh") {
                        swal({
                                title: "Confirmer le paiement",
                                // text: "Confirmer votre choix",
                                type: "info", showCancelButton: true,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true,
                            },
                            function () {
                                setTimeout(function () {
                                    swal("Réservation bien payée!");
                                    location.reload();
                                });
                            }, 2000);
                    }
                }
            },
            error: function() {
                swal("", "Aucune réservation sélectionnée")

            }

        });
    });

    // =================================================================================================================
    // =================================================================================================================
    // =================================================================================================================
    // $('input.autocomplete').autocomplete({
    //     data: {
    //         "Apple": null,
    //         "Microsoft": null,
    //         "Google": 'https://placehold.it/250x250'
    //     },
    //     limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
    //     onAutocomplete: function(val) {
    //         // Callback function when value is autcompleted.
    //     },
    //     minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
    // });

    // =================================================================================================================
    // Btn active center ===============================================================================================
    // =================================================================================================================
    $(".center_stat").click(function() {
        that = $(this);
        // this function will get executed every time the #home element is clicked (or tab-spacebar changed)
        if(that.is(":checked") == true) // "this" refers to the element that fired the event
        {
            $.get(that.attr("data-href"), function() {
                swal("Activé!", "Votre centre a été activé!", "success");
            });
        } else{
            $.get(that.attr("data-href"), function() {
                swal("désactivé!", "Votre centre a été désactivé!", "success");
            });

        }
    });

    // =================================================================================================================
    // Btn enable & disable share program center =======================================================================
    // =================================================================================================================
    $(".center_share_program").click(function() {
        if($(this).is(":checked") == true) {
            $.getJSON($(this).attr("data-href"), function(data) {})
                .done(function(data) {
                    if (data.status=="ok") {
                        if (data.page=="enable") {
                            swal("Activé!", "Le partager programme a été activé!", "success");
                        }
                    }
                })

        } else {
            $.getJSON($(this).attr("data-href"), function(data) {})
                .done(function(data) {
                    if (data.status=="ok") {
                        if (data.page=="disable") {
                            swal("désactivé!", "Le partager programme a été désactivé!", "success");
                        }
                    }
                })
        }


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

        // Upload avatar
        $('.dropify').dropify({
            messages: {
                default: 'Glissez-déposez un logo ici ou cliquez',
                replace: 'Glissez-déposez un logo ou cliquez pour remplacer',
                remove:  'Supprimer',
                error:   'Désolé, le fichier trop volumineux'
            }
        });
        // ColorPicker
        $('#mycp').colorpicker({
            component: '.btn',
            format: 'hex'
        });

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

        //On lance un ajax pour refresh le baskets =====================================================================
        $.getJSON($('#baskets-dropdown').attr("data-route"), function(data) {})
            .done(function(data) {
                if (data.status=="ok") {
                    if (data.page=="show") {
                        $('#baskets-dropdown').html(data.html)
                    }
                }
            })

        // On lance un ajax pour charger le formulaire modifier centre =================================================
        $.getJSON($('#form_modal').attr("data-href"), function(data) {})
            .done(function(data) {
                if (data.status == "ok"){
                    if (data.page == "edit") {
                        $('#preloader-page-content').remove();
                        $('#form-center-edit').html(data.html);
                        refresh_Materialize_compound();
                        $('#form-center-edit').addClass('card-panel');
                        $('#cancel').remove();

                    } else {
                        swal("", "Une erreur est survenue");

                    }
                }
            });

        // On lance un ajax pour charger le booking match to day =================================================
        $.getJSON($('#booking-to_day').attr("data-route"), function(data) {})
            .done(function(data) {
                if (data.status == "ok"){
                    if (data.page == "show") {
                        $('#preloader-page-content').addClass('hide');
                        $('#booking-to_day').html(data.html);
                        initialise();
                    } else {
                        swal("", "Une erreur est survenue");

                    }
                }
            });

        // On lance un ajax pour charger le calender ===================================================================
        $.getJSON($('#full-calendar').attr("data-route"), function(data) {})
            .done(function(data) {
                if (data.status == "ok")
                {
                    if (data.page == "show") {
                        $('#preloader-page-content').remove();
                        $('#full-calendar').html(data.html);
                        calendar_app_compound(data.booking);

                    } else {
                        swal("", "Une erreur est survenue");

                    }
                }
            });

        function calendar_app_compound(booking) {
            /* initialize the calendar
             -----------------------------------------------------------------*/
            $('#calendar').fullCalendar({
                locale: 'fr',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultDate: new Date(),
                editable: true,
                droppable: true, // this allows things to be dropped onto the calendar
                eventLimit: true, // allow "more" link when too many events
                events: booking,
                timeFormat: 'H(:mm)' // uppercase H for 24-hour clock
            });
        }

    });

    // =================================================================================================================
    // Display calendar by field =======================================================================================
    // =================================================================================================================
    $('ul.tabs li a').bind('click', function(){
        $('#calendar').remove();
        $url = $(this).attr("data-route");
        $id = $(this).attr("data-id");
        $.getJSON($url, function(data) {})
            .done(function(data) {
                if (data.status == "ok")
                {
                    if (data.page == "show") {
                        $('#preloader-page-content').remove();
                        $('#full-calendar').html(data.html);
                        calendar_app_compound(data.booking, $id);

                    } else {
                        swal("", "Une erreur est survenue");

                    }
                }
            });

        function calendar_app_compound(booking, $id) {
            /* initialize the calendar
             -----------------------------------------------------------------*/
            $('#calendar'+ $id).fullCalendar({
                locale: 'fr',
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultDate: new Date(),
                editable: true,
                droppable: true, // this allows things to be dropped onto the calendar
                eventLimit: true, // allow "more" link when too many events
                events: booking,
                timeFormat: 'H(:mm)' // uppercase H for 24-hour clock
            });
        }

    });

    // =================================================================================================================
    // Form search booking Match & Subscription back office ============================================================
    // =================================================================================================================
    $(".form-booking-search").on("click",'#search-match',function(){
        form = $(this).closest('form');
        $.ajax({
            data: new FormData(form[0]),
            url: form.attr('action'),
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('#booking-to_day').remove();
                $('#booking-search').addClass('hide');
                $('#preloader-page-content').removeClass('hide');
            },
            success: function(data_json){
                data = $.parseJSON(data_json);
                if (data.status=="ok") {
                    if (data.page=="search") {
                        $('#preloader-page-content').addClass('hide');
                        $('#booking-search').html(data.html);
                        initialise();
                        $('#booking-search').removeClass('hide');

                    } else {
                        swal("", "Une erreur est survenue");
                    }
                }
            },
            error: function() {
                swal("", "Une erreur est survenue");
            }

        });

    });

    // Initialise booking match & subscription modal ===================================================================
    function initialise(){

        $('.tooltipped').tooltip({delay: 50});
        // Refresh compound match ======================================================================================
        $('.modal_trigger').click(function() {
            url = $(this).attr("data-href");
            //On lance un ajax pour charger le formulaire
            $.getJSON($(this).attr("data-href"), function(data) {})
                .done(function(data) {
                    if (data.status=="ok"){
                        if (data.page=="refresh"){
                            $('#form_modal').closeModal();
                            location.reload();

                        } else {
                            $('#form_modal').html(data.html);
                            $('#form_modal').attr('data-href', url);
                            refresh_Materialize_compound();
                            $('#form_modal').openModal();

                        }
                    }
                })

        });

        // Btn subscription action =====================================================================================
        $('.btn-booking-subscription').click(function() {
            that = $(this);
            swal({
                    title: "Confirmer votre séance",
                    // text: "Confirmer votre choix",
                    type: "info", showCancelButton: true,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true,
                },
                function () {
                    setTimeout(function () {
                        $.get(that.attr("data-href"), function() {
                            swal("", "Réservation bien ajouté dans le pannier!");
                        });
                        //On lance un ajax pour refresh le baskets
                        $.getJSON($('#baskets-dropdown').attr("data-route"), function(data) {})
                            .done(function(data) {
                                if ( isNaN( parseInt($('.basket-badget').text()) )){
                                    $('.basket-badget').removeClass('hide');
                                    $nb = 0;
                                } else {
                                    $nb = parseInt($('.basket-badget').text());
                                }
                                if (data.status=="ok") {
                                    if (data.page=="show") {
                                        $nb++;
                                        $('#baskets-dropdown').empty().html(data.html);
                                        $('.basket-badget').empty().append($nb);
                                    }
                                }
                            })

                    }, 1000);
                });
        });
    }

// === End =============================================================================================================
});

