/**
 * handle custom datatable pagination, export functionality
 */
jQuery(document).ready(function ($) {
    var table = $('#myTable').DataTable({
        orderCellsTop: true,
        fixedHeader: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: 'Blfrtip',
        buttons: ['excel', 'pdf', 'print']
    });

    $('#myTable thead tr.filters th').each(function (i) {
        $('input', this).on('keyup change', function () {
            if (table.column(i).search() !== this.value) {
                table.column(i).search(this.value).draw();
            }
        });
    });
});
