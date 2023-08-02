<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
   <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- pace-progress -->
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/pace-progress/themes/black/pace-theme-flat-top.css') }}">
    <!-- Ekko Lightbox -->
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/ekko-lightbox/ekko-lightbox.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ URL::asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ URL::asset('assets/c3/c3/c3.min.css') }}">
  <!-- adminlte-->
  <link rel="stylesheet" href="{{ URL::asset('assets/dist/css/adminlte.min.css') }}">
  <!-- jQuery -->
  <script src="{{ URL::asset('assets/plugins/jquery/jquery.min.js') }}"></script>
</head>