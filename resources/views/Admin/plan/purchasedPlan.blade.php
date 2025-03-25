
@extends('Admin.layout.mainlayout')
@section('content')
<div id="loading-indicator" style="display: none;">
    <?php $imageUrl = config('global.local_image_url');?>
    <img src="<?php echo $imageUrl."/loader.gif" ?>" height="50">
</div>
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
                              
                                
                                
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Username</th>
                                            <th>Plan Name</th>
                                            <th>Price</th>
                                            <th>Trip Name</th>
                                            <th>Duration</th>
                                            <th>Transaction Id</th>
                                            <th>Payment Through</th>
                                            <th>Purchased On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $imageUrl  = config('global.local_image_url'); ?>
                                          @foreach($data as $plan)
                                        <tr>
                                            <td>{{$plan->id}}</td>
                                            <td>{{$plan->first_name}} {{$plan->last_name}}</td>
                                            <td>{{$plan->name}}</td>
                                            <td>$ {{$plan->price}} </td>
                                            <td>{{$plan->trip_name}}</td>
                                            <td>  @if($plan->duration != "")
                                                {{$plan->duration}} Month
                                            
                                            @endif  
                                            
                                           </td>
                                            <td>{{$plan->transaction_id}} </td>
                                            <td>{{$plan->payment_through}} </td>
                                            <td>{{$plan->purchase_on}} </td>
                                            
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

