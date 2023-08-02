@extends('layouts.default')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Personajes</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active">Personajes</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
       {!! Form::open(['route' => 'home','method'=>'get','id'=>'frm_listado']) !!}
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-9">
              {!! Form::select('prm_nombre',[],null,['class'=>'form-control select2','placeholder' => 'Buscar por Nombre','id' => 'prm_nombre']) !!}
            </div>
            <input type="hidden" name="pagina" id="pagina" value="1">
            <div class="col-sm-3">
              <a onclick="frm_send()" class="btn btn-light">Buscar <i class="fa fa-search"></i> </a>
              <a onclick="limpiar_filtro()" class="btn btn-light">Limpiar Filtro <i class="fa fa-clear"></i> </a>
            </div>
            
          </div>
        </div>
      </div>
      {!! Form::close() !!}
      <div class="row">
        <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <a class="btn btn-light" href="javascript:void(0)" data-filter="all"> Todos</a>
                <a class="btn btn-light" href="javascript:void(0)" data-filter="Human">Human</a>
                <a class="btn btn-light" href="javascript:void(0)" data-filter="Alien">Alien</a>
                <a class="btn btn-light" href="javascript:void(0)" data-filter="Humanoid">Humanoid</a>
                <a class="btn btn-light" href="javascript:void(0)" data-filter="Animal">Animal</a>
                <a class="btn btn-light" href="javascript:void(0)" data-filter="Cronenberg">Cronenberg</a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-body">

                <div class="filter-container row">
                  @if($id_personaje == null)
                  @foreach($data->results as $item)
                  <div class="filtr-item col-sm-4 text-center" data-category="{{$item->species}}">
                    <div class="card">
                      <div class="ribbon-wrapper ribbon-lg">
                        <div class="ribbon bg-success">
                          {{$item->status}} 
                        </div>
                      </div>
                      <div class="">
                        <div class="row">
                          <div class="col-7">
                            <h2 class="lead"><b>{{ $item->name }}</b>({{ $item->species }})</h2>
                            <p class="text-muted text-sm"><b>Location: </b> {{ $item->location->name}} </p>
                          </div>
                          <div class="col-5 text-center">
                            <img src="{{ $item->image }}" alt="user-avatar" class="img-circle img-fluid">
                          </div>
                        </div>
                      </div>
                      <div class="card-footer">
                        <div class="text-right">
                          <a href="{{ route('personaje.show',$item->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-user"></i> Detalle
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  @endforeach
                  @else
                     <div class="filtr-item col-sm-4 text-center" data-category="{{$data->species}}">
                      <div class="card">
                        <div class="ribbon-wrapper ribbon-lg">
                          <div class="ribbon bg-success">
                            {{$data->status}} 
                          </div>
                        </div>
                        <div class="">
                          <div class="row">
                            <div class="col-7">
                              <h2 class="lead"><b>{{ $data->name }}</b>({{ $data->species }})</h2>
                              <p class="text-muted text-sm"><b>Location: </b> {{ $data->location->name}} </p>
                            </div>
                            <div class="col-5 text-center">
                              <img src="{{ $data->image }}" alt="user-avatar" class="img-circle img-fluid">
                            </div>
                          </div>
                        </div>
                        <div class="card-footer">
                          <div class="text-right">
                            <a href="{{ route('personaje.show',$data->id) }}" class="btn btn-sm btn-primary">
                              <i class="fas fa-user"></i> Detalle
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div><!-- /.card-body -->
              @if($id_personaje == null)
              <div class="card-footer">
                Página {{ $pagina }}
                <nav aria-label="Contacts Page Navigation">
                  <ul class="pagination justify-content-center m-0">
                    @php
                        $prev = ($data->info->prev != null ? (explode('=',$data->info->prev) )[1] : null);
                        $next = ($data->info->next != null ? (explode('=',$data->info->next) )[1] : null);
                    @endphp
                    @if($prev != null)
                        <li class="btn page-item"><a class="page-link" onclick="endPag('{{$prev}}')">Anterior</a></li>
                    @endif
                        <li class="btn page-item"><a class="page-link" onclick="endPag('{{$next}}')">Siguiente</a></li>
                  </ul>
                </nav>
              </div>
              @endif
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      </div>
      <!-- /.card -->

    </section>
    <!-- /.content -->
@endsection
@section('scripts')
<script type="text/javascript">
  function limpiar_filtro(){
    $('#prm_nombre').val('').change();
    document.getElementById('frm_listado').submit();
  }
  function frm_send(){
    document.getElementById('frm_listado').submit();
  }
  function endPag(url){
      $('#pagina').val(url);
      document.getElementById('frm_listado').submit();
  }
</script>
@endsection
