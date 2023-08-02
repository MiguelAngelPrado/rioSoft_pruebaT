@include('includes.head')

<body class="hold-transition layout-top-nav">
  @include('sweetalert::alert')
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
@include('includes.header')

  <!-- Main Sidebar Container -->
  <!--<aside class="main-sidebar sidebar-dark-primary elevation-4"> -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      @yield('content')
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; {{date('Y')-1}} {{date('Y')}} <a href="#">Sistema</a>.</strong>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
@include('includes.script')
</body>
</html>
