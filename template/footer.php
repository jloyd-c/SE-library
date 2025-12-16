        <footer class="footer">
          <div class="container-fluid">
            <nav class="float-left">
              <!-- <ul>
                <li><a href="https://github.com/omkar2403/inout/" target="_blank">In Out Management System</a></li>
                <li><a href="https://www.koha-community.org/" target="_blank">Powered By KOHA Community</a></li>
              </ul> -->
            </nav>
             <!--<div class="copyright float-right">©<script>document.write(new Date().getFullYear())</script>, made with <i class="material-icons" style="color:red;">favorite</i> by
              <a href="https://omkar2403.github.io/its_me/" target="_blank">Omkar Kakeru</a> for a better web.
            </div>
          </div> -->




<div class="container"><center><span style="font-size: small;"> &copy; 2025 In Out Management System | <a href="https://mabinicolleges.edu.ph" target="_right"> Mabini College, Inc. </a> </span></center></div>
           
 

        </footer>               
      </div>          
    </div>

    <!-- Core Js -->
    <script src="assets/js/core/popper.min.js" type="text/javascript"></script>
    <!-- Main Js -->
    <script src="assets/js/core/bootstrap-material-design.min.js" type="text/javascript"></script>
    <!-- Scroll Bar -->
    <script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script> 
    <!-- Plugin for the momentJs  -->
    <script src="assets/js/plugins/moment.min.js"></script> 
    <!--  Plugin for Sweet Alert -->
    <script src="assets/js/plugins/sweetalert2.js"></script> 
    <!-- Forms Validations Plugin -->
    <script src="assets/js/plugins/jquery.validate.min.js"></script> 
    <!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
    <script src="assets/js/plugins/jquery.bootstrap-wizard.js"></script> 
    <!--  Plugin for Select, full documentation here: silviomoreto.github.io/bootstrap-select -->
    <script src="assets/js/plugins/bootstrap-selectpicker.js"></script> 
    <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
    <script src="assets/js/plugins/bootstrap-datetimepicker.min.js"></script> 
    <!--  Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
    <script src="assets/js/plugins/bootstrap-tagsinput.js"></script> 
    <!-- Plugin for Fileupload, full documentation here: www.jasny.net/bootstrap/javascript/#fileinput -->
    <script src="assets/js/plugins/jasny-bootstrap.min.js"></script> 
    <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
    <script src="assets/js/plugins/fullcalendar.min.js"></script> 
    <!-- Vector Map plugin, full documentation here: jvectormap.com/documentation/ -->
    <script src="assets/js/plugins/jquery-jvectormap.js"></script> 
    <!--  Plugin for the Sliders, full documentation here: refreshless.com/nouislider/ -->
    <script src="assets/js/plugins/nouislider.min.js"></script> 
    <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
    <script src="assets/js/core/core.js"></script>
    <!-- Library for adding dinamically elements -->
    <script src="assets/js/plugins/arrive.min.js"></script>
    <!-- Chartist JS -->
    <script src="assets/js/plugins/chartist.min.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="assets/js/material-dashboard.min.js?v=2.0.2" type="text/javascript"></script>
    <!-- DATE TIME PICKER AND SLIDERS -->
    <script>
      $(document).ready(function(){
        // initialise Datetimepicker and Sliders
        md.initFormExtendedDatetimepickers();
        if($('.slider').length != 0){
          md.initSliders();
        }
      });
    </script>

    <?php
if(isset($table) && $table){
?>
    <script>
      $(document).ready(function() {
        // Store report type and parameters for custom export
        <?php
        $custom_export_url = "";
        if (isset($_POST['datewiseRep']) || isset($_POST['studentRep']) || isset($_POST['statRep'])) {
            $custom_export_url = "custom_export.php?";
            
            // Determine report type
            if (isset($_POST['datewiseRep'])) {
                $report_type = 'datewise';
            } elseif (isset($_POST['studentRep'])) {
                $report_type = ($_POST['rtype'] == 'Short') ? 'student_short' : 'student_detail';
            } else {
                $report_type = 'statistical';
            }
            
            $custom_export_url .= "type=" . $report_type;
            $custom_export_url .= "&fdate=" . urlencode($_POST['fdate'] ?? '');
            $custom_export_url .= "&tdate=" . urlencode($_POST['tdate'] ?? '');
            $custom_export_url .= "&ftime=" . urlencode($_POST['ftime'] ?? '00:00:00');
            $custom_export_url .= "&ttime=" . urlencode($_POST['ttime'] ?? '23:59:59');
            
            if (isset($_POST['studentRep'])) {
                $custom_export_url .= "&usn=" . urlencode($_POST['usn'] ?? '');
                $custom_export_url .= "&rtype=" . urlencode($_POST['rtype'] ?? 'Detail');
            }
        }
        ?>
        
        // Store the base export URL in a variable
        var baseExportUrl = "<?php echo $custom_export_url; ?>";
        
        $('#datatables').DataTable({
          initComplete: function () {
                this.api().columns().every( function () {
                  var column = this;
                  var select = $('<select><option value="">Show All</option></select>')
                      .appendTo( $(column.footer()).empty() )
                      .on( 'change', function () {
                          var val = $.fn.dataTable.util.escapeRegex(
                              $(this).val()
                          );
   
                          column
                              .search( val ? '^'+val+'$' : '', true, false )
                              .draw();
                      } );
   
                  column.data().unique().sort().each( function ( d, j ) {
                      select.append( '<option value="'+d+'">'+d+'</option>' )
                  } );
              } );
          },
          "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
          ],
          dom: 'Bfrtip',
          buttons: [
                {
                    extend: 'copyHtml5',
                    messageTop: typeof printMsg !== 'undefined' ? printMsg : '',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                // CUSTOM EXPORT BUTTON (REPLACES EXCEL)
                {
                  text: 'Excel',
                  action: function ( e, dt, node, config ) {
                      // Get current search value
                      var searchValue = dt.search();
                      
                      // Build export URL with current filters
                      var exportUrl = baseExportUrl;
                      if (searchValue) {
                          exportUrl += '&search=' + encodeURIComponent(searchValue);
                      }
                      
                      // Open export URL
                      window.location.href = exportUrl;
                  },
                  exportOptions: {
                      columns: ':visible'
                  }
                },
                {
                  extend: 'pdfHtml5',
                  messageTop: typeof printMsg !== 'undefined' ? printMsg : '',
                  exportOptions: {
                      columns: ':visible'
                  }
                },
                'colvis'
          ],
          responsive: true,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records",
          },
        });
      });
    </script>
<?php
  }
?>
  </body>
</html>