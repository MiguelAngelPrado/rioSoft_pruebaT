
<!-- Bootstrap 4 -->
<script src="{{ URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ URL::asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
  <!-- pace-progress -->
<script src="{{ URL::asset('assets/plugins/pace-progress/pace.min.js') }}"></script>
<!-- jquery-validation -->
<script src="{{ URL::asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"></script>
<!-- Ekko Lightbox -->
<script src="{{ URL::asset('assets/plugins/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
<!-- AdminLTE App -->
<!-- DataTables  & Plugins -->
<script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ URL::asset('assets/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<script src="{{ URL::asset('assets/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<!-- FLOT CHARTS -->
<script src="{{ URL::asset('assets/plugins/flot/jquery.flot.js') }}"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="{{ URL::asset('assets/plugins/flot/plugins/jquery.flot.resize.js') }}"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="{{ URL::asset('assets/plugins/flot/plugins/jquery.flot.pie.js') }}"></script>

<!-- Filterizr-->
<script src="{{ URL::asset('assets/plugins/filterizr/jquery.filterizr.min.js') }}"></script>
<script src="{{ URL::asset('assets/dist/js/demo.js') }}"></script>
<!-- Bootstrap Switch -->
<script src="{{ URL::asset('assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jQuery Mapael -->
<script src="{{ URL::asset('assets/plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ URL::asset('assets/plugins/chart.js/Chart.min.js') }}"></script>

  <!-- morrisjs JS
    ============================================ -->
    <script src="{{ URL::asset('assets/sparkline/jquery.sparkline.min.js') }}"></script>

    <script src="{{ URL::asset('assets/c3/c3-charts/d3.min.js') }}"></script>
    <script src="{{ URL::asset('assets/c3/c3-charts/c3.min.js') }}"></script>

<script>

$(function () {
   $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox({
        alwaysShowClose: true
      });
    });
   
    $('.filter-container').filterizr({gutterPixels: 3});
    $('.btn[data-filter]').on('click', function() {
      $('.btn[data-filter]').removeClass('active');
      $(this).addClass('active');
    });

   $('.knob').knob({
      /*change : function (value) {
       //console.log("change : " + value);
       },
       release : function (value) {
       console.log("release : " + value);
       },
       cancel : function () {
       console.log("cancel : " + this.value);
       },*/
      draw: function () {

        // "tron" case
        if (this.$.data('skin') == 'tron') {

          var a   = this.angle(this.cv)  // Angle
            ,
              sa  = this.startAngle          // Previous start angle
            ,
              sat = this.startAngle         // Start angle
            ,
              ea                            // Previous end angle
            ,
              eat = sat + a                 // End angle
            ,
              r   = true

          this.g.lineWidth = this.lineWidth

          this.o.cursor
          && (sat = eat - 0.3)
          && (eat = eat + 0.3)

          if (this.o.displayPrevious) {
            ea = this.startAngle + this.angle(this.value)
            this.o.cursor
            && (sa = ea - 0.3)
            && (ea = ea + 0.3)
            this.g.beginPath()
            this.g.strokeStyle = this.previousColor
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
            this.g.stroke()
          }

          this.g.beginPath()
          this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
          this.g.stroke()

          this.g.lineWidth = 2
          this.g.beginPath()
          this.g.strokeStyle = this.o.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
          this.g.stroke()

          return false
        }
      }
    });

  $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox({
        alwaysShowClose: true
      });
  });

  $('.select2').select2();

  //Initialize Select2 Elements
  $('.select2bs4').select2({
    theme: 'bootstrap4'
  });

  bsCustomFileInput.init();

  $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
  });

   $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": false,
      "autoWidth": true,
      "responsive": true,
      language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "No se encontraron registros",
        info: "Página _PAGE_ de _PAGES_",
        infoEmpty: "No hay registros disponibles",
        infoFiltered: "(Filtrando página _MAX_ total de registros)",
        search : "Buscar",
        searchPlaceholder: "Search records",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Previo"
        }
      },
    });

    $('.datatable').on('search.dt', function (e, settings) {
       table.search( this.value ).draw();
    })


    


     $('#example3').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": false,
      "autoWidth": true,
      "responsive": true,
      "buttons": ["csv", "excel", "pdf", "print"],
      language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "No se encontraron registros",
        info: "Página _PAGE_ de _PAGES_",
        infoEmpty: "No hay registros disponibles",
        infoFiltered: "(Filtrando página _MAX_ total de registros)",
        search : "Buscar",
        paginate: {
          first: "Primero",
          last: "Último",
          next: "Siguiente",
          previous: "Previo",
        }
      },
    }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');

      $('#prm_nombre').select2({
            ajax: {
                dataType: 'json',
                url: '{{ url("listado/nombres") }}',
                delay: 250,
                //minimumInputLength: 3,
                data: function(params) {
                        var query = {
                            search: params.term
                        }

                        console.log('query :', query)
                        return query;
                },
                processResults: function (data, page) {
                  return {
                    results: data
                  };
                },
                theme: "classic"
            }
        });
    
});
</script>
@yield('scripts')