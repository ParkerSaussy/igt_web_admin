   <!-- Modal -->
<div class="modal fade zoomIn" id="logoutModals" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/hzomhqxz.json" trigger="loop" colors="primary:#405189,secondary:#08a88a" style="width:180px;height:180px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Logout</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to logout?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-dismiss="modal">No</button>
                    <a href="/logouts"><button type="button" class="btn w-sm btn-danger" id="deletebutton">Yes</button></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end modal -->




 <script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('vendors/popper.js/dist/umd/popper.min.js') }}"></script>
      <script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendors/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendors/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/js/init-scripts/data-table/datatables-init.js') }}"></script>

    <script src="{{ asset('vendors/chart.js/dist/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.js') }}"></script>
    <script src="{{ asset('vendors/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('vendors/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script>
    <script src="{{ asset('vendors/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
      <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <!-- Editor -->
<script src="https://kendo.cdn.telerik.com/2022.3.1109/js/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.2/croppie.js"></script>

<script src="https://kendo.cdn.telerik.com/2022.3.1109/js/kendo.all.min.js"></script>
<script>
   jQuery(document).ready(function() {
    //only allow 2 digits after dot when input type number
    $('#price').on('input', function() {
        var value = $(this).val();
        var parts = value.split('.');
        
        if (parts.length > 1) {
            parts[1] = parts[1].substring(0, 2); // Limit to two decimal places
            value = parts.join('.');
        }
        
        $(this).val(value);
    });
    //end number

    jQuery("#editor").kendoEditor({
        tools: [
           "bold",
           "italic",
           "underline",
           "undo",
           "redo",
           "strikethrough",
           "justifyLeft",
           "justifyCenter",
           "justifyRight",
           "justifyFull",
           "insertUnorderedList",
           "insertOrderedList",
           "insertUpperRomanList",
           "insertLowerRomanList",
           "indent",
           "outdent",
           "createLink",
           "unlink",
        //    "insertImage",
        //    "insertFile",
           "subscript",
        //    "superscript",
        //    "tableWizard",
        //    "createTable",
        //    "addRowAbove",
        //    "addRowBelow",
        //    "addColumnLeft",
        //    "addColumnRight",
        //    "deleteRow",
        //    "deleteColumn",
        //    "mergeCellsHorizontally",
        //    "mergeCellsVertically",
        //    "splitCellHorizontally",
        //    "splitCellVertically",
        //    "tableAlignLeft",
        //    "tableAlignCenter",
        //    "tableAlignRight",
           "viewHtml",
           "formatting",
           "cleanFormatting",
           "copyFormat",
           "applyFormat",
           "fontSize",
       
           "foreColor",
           "backColor",
           "print",
           ]
    });
});
</script>
    <script>
        
        // $('#dropdown_type').change(function() {
        //     var selectedValue = $(this).val();
        //     var durationInput = $('#duration');

        //     if (selectedValue === 'normal') {
        //         durationInput.prop('disabled', false); // Enable the input
        //        $('.duration').show(); // Hide the div
        //     } else {
        //         durationInput.prop('disabled', true); // Enable the input
        //         $('.duration').show(); //$('.duration').hide(); // Show the div
        //     }
        // });

        // $(".sufee-alert").fadeTo(5000,1).fadeOut(1000);
        function handleDropdownChange() {
        var selectedValue = $('#dropdown_type').val();
        var durationInput = $('#duration');

        if (selectedValue === 'normal') {
            durationInput.prop('disabled', false); // Enable the input
            $('.duration').show(); // Show the div
        } else {
            durationInput.prop('disabled', true); // Disable the input
            $('.duration').show(); // Hide the div
        }
    }

    // Call the function when the page loads
    $(document).ready(function() {
        handleDropdownChange();
        $(".sufee-alert").fadeTo(5000, 1).fadeOut(1000);
    });

    // Call the function when the dropdown value changes
    $('#dropdown_type').change(function() {
        handleDropdownChange();
    });
    </script>

<script>
    // Function to handle the success message fadeout
    function fadeOutSuccessMessage() {
        $('.sufee-alert').fadeIn().delay(5000).fadeOut();
    }

    // Call the function on page load (if the success message is shown)
    $(document).ready(function () {
        fadeOutSuccessMessage();
    });
</script>
<script>
    $(document).ready(function() {
    $('.dataTable').DataTable({
        "language": {
            "info": "", // Remove the header count (Showing X to Y of Z records)
            // Add other language options if needed
        }
    });

   
});
</script>

</body>

</html>
