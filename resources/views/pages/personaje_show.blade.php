@extends('layouts.default')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{$data->name}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">Incio</a></li>
              <li class="breadcrumb-item active">Personaje</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-sm-6 invoice-col">
                  <div class="text-center">
                    <img class="img-fluid img-circle" src="{{ $data->image }}" alt="img {{$data->name}}">
                  </div>
            </div>

             <div class="col-sm-3 invoice-col">
               
                  Personaje
                  <address>
                    Name: <strong>{{$data->name}}</strong><br>
                    Status: <strong>{{ $data->status }}</strong><br>
                    Species: <strong>{{ $data->species }}</strong><br>
                    Type: <strong>{{ $data->type }}</strong><br>
                    Gender: <strong>{{ $data->gender }}</strong><br>
                  </address>
               
              </div>
                <!-- /.col -->
                <div class="col-sm-3 invoice-col">
                  Location
                  <address>
                    Name: <strong>{{ $data->location->name}}</strong><br>
                  </address>
                </div>
                <!-- /.col -->
                
          </div>
        </div>
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
@endsection
