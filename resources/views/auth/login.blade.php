@extends('layouts.basic')

@section('content')
<div class="content-wrapper">
       <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"> <a href="../../index2.html" class="h1"><b>{{ config('app.name', 'Laravel') }}</b></a></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="row">
          <div class="col-xl-7 d-none d-lg-none d-xl-block">
            <div class="">
                <img src="{{ asset('assets/img/inicio.png')}}" width="95%" height="10%">
            </div>
          </div>
          <!-- /.col-md-6 -->
          <div class="col-xl-4 col-lg-12">
             <div class="card card-outline card-primary" width="100%">
                <div class="card-header text-center">
                    <img src="https://www.colomos.ceti.mx/imagenes/ofertaEducativa/tecdesarrollosoft.png" class="img-fluid" width="100%" height="100%">
                </div>
                <div class="card-body">
                   <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                                <div class="input-group mb-3">
                                  <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('Correo electrónico') }}" value="{{ old('email') }}">
                                  <div class="input-group-append">
                                    <div class="input-group-text">
                                      <span class="fas fa-envelope"></span>
                                    </div>
                                  </div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Clave</label>
                            <div class="input-group mb-3">
                              <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Clave') }}" required autocomplete="current-password">
                              <div class="input-group-append">
                                <div class="input-group-text">
                                  <span class="fas fa-lock"></span>
                                </div>
                              </div>
                              @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                              @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                          <div class="col-8">
                            <div class="icheck-primary">
                              <input type="checkbox" id="remember">
                              <label for="remember">
                                Recordar
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-right-from-bracket"></i> Acceder</button>
                        </div>
                      </form>
                </div>
                <!-- /.card-body -->
            </div>
              <!-- /.card -->
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

</div>
@endsection
