
@extends('Admin.layout.mainlayout')
@section('content')
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    @if ($message = Session::get('success'))
                    <div class="sufee-alert alert with-close alert-primary alert-dismissible fade show">
                        <span class="badge badge-pill badge-primary">Success</span>
                        <strong> {{ $message }} </strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                    </div>
                    @endif

                    @if ($message = Session::get('fail'))
                    <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                        <span class="badge badge-pill badge-danger">Success</span>
                        <strong> {{ $message }} </strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                               
                                <div class="breadcrumbs">
                                    <div class="col-sm-4">
                                       <div class="page-header float-left">
                                          <div class="page-title">
                                             <h1>User Trips</strong></h1>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="page-header float-right">
                                           <div class="page-title">
                                              <ol class="breadcrumb text-right">
                                                 <li><a href="{{URL('users')}}">All Users</a></li>
                                                 <li><a class="active">User Trips</a></li>
                                              </ol>
                                           </div>
                                        </div>
                                     </div>
                                 </div>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Host Name</th>
                                            <th>Trip Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Invities</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                          @foreach($data as $trips)
                                        <tr>
                                            <td>{{$trips->id}}</td>
                                            <td>{{$trips->user->first_name}} {{$trips->user->last_name}}</td>
                                            <td>{{$trips->trip_name}}</td>
                                            <td>{{$trips->trip_final_start_date}}</td>
                                            <td>{{$trips->trip_final_end_date}}</td>
                                            <td>{{$trips->guests_count}}</td>
                                           
                                            <td>
                                            <div class="icon-container">
                                                <a href="{{ url('users/userTrips', [$trips->created_by, $trips->id]) }}">
                                                    <span class="ti-eye"></span><span class="icon-name"></span>
                                                </a>
                                            </div>
                                        </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
            </div><!-- .animated -->
        </div><!-- .content -->
@endsection


