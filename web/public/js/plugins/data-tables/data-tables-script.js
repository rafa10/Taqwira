$(document).ready(function(){
    // table date form all entity
    $('#data-table-simple').DataTable({
        colReorder: true,
        stateSave: true,
        scrollY: '70vh',
        scrollCollapse: true,
        paging: false,
        "order": [[ 0, "desc" ]],
        "language": {
            "decimal":        "",
            "emptyTable":     "Aucune donnée disponible",
            "info":           "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "infoEmpty":      "Affiche 0 à 0 de 0 entrées",
            "infoFiltered":   "(filtered from _MAX_ total entries)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Afficher  _MENU_ entrées",
            "loadingRecords": "Loading...",
            "processing":     "Processing...",
            "search":         "Chercher:",
            "zeroRecords":    "Aucun enregistrements correspondants trouvés",
            "paginate": {
                "first":      "Premier",
                "last":       "Prochain",
                "next":       "Dernier",
                "previous":   "Précédent"
            },
            "aria": {
                "sortAscending":  ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
        }
    });
    //Table data for display to day bookings
    $('#data-table-today-booking').DataTable({
        "searching": false,
        colReorder: true,
        stateSave: true,
        scrollY: '20vh',
        scrollCollapse: true,
        paging: false,
        "order": [[ 0, "desc" ]],
        "language": {
            "decimal":        "",
            "emptyTable":     "Aucune donnée disponible",
            "info":           "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "infoEmpty":      "Affiche 0 à 0 de 0 entrées",
            "infoFiltered":   "(filtered from _MAX_ total entries)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Afficher  _MENU_ entrées",
            "loadingRecords": "Loading...",
            "processing":     "Processing...",
            "search":         "Chercher:",
            "zeroRecords":    "Aucun enregistrements correspondants trouvés",
            "paginate": {
                "first":      "Premier",
                "last":       "Prochain",
                "next":       "Dernier",
                "previous":   "Précédent"
            },
            "aria": {
                "sortAscending":  ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
        }
    });
    
    // var table = $('#data-table-row-grouping').DataTable({
    //     "columnDefs": [
    //         { "visible": false, "targets": 2 }
    //     ],
    //     "order": [[ 2, 'asc' ]],
    //     "displayLength": 25,
    //     "drawCallback": function ( settings ) {
    //         var api = this.api();
    //         var rows = api.rows( {page:'current'} ).nodes();
    //         var last=null;
    //
    //         api.column(2, {page:'current'} ).data().each( function ( group, i ) {
    //             if ( last !== group ) {
    //                 $(rows).eq( i ).before(
    //                     '<tr class="group"><td colspan="5">'+group+'</td></tr>'
    //                 );
    //
    //                 last = group;
    //             }
    //         } );
    //     }
    // });
    //
    // // Order by the grouping
    // $('#data-table-row-grouping tbody').on( 'click', 'tr.group', function () {
    //     var currentOrder = table.order()[0];
    //     if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {
    //         table.order( [ 2, 'desc' ] ).draw();
    //     }
    //     else {
    //         table.order( [ 2, 'asc' ] ).draw();
    //     }
    // } );


    });