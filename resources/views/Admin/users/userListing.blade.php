
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
                                <div class="breadcrumbs">
                                    <div class="col-sm-4">
                                       <div class="page-header float-left">
                                          <div class="page-title">
                                             <h1>User Management</h1>
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
                                            <th>Firstname</th>
                                            <th>Lastname</th>
                                            <th>Email</th>
                                            <th>Email Verify</th>
                                            <th>Mobile</th>
                                            <th>Paypal</th>
                                            <th>Venmo Username</th>
                                            <th>Trips</th>
                                            <th>Status</th>
                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                          @foreach($users as $user)
                                        <tr>
                                            
                                            <td>{{$user->id}}</td>
                                            <td>{{$user->first_name}} </td>
                                            <td>{{$user->last_name}}</td>
                                            <td>{{$user->email}}</td>
                                            <td><?php if($user->is_email_verify == 1){?>
                                               <?php echo "Yes"; ?>
                                            <?php }else{?>
                                                <?php echo "No"; ?>
                                            <?php } ?></td>
                                            <td>{{$user->country_code}}{{$user->mobile_number}}</td>
                                            <td>{{$user->paypal_username}}</td>
                                            <td>{{$user->venmo_username}} </td>
                                            <td><?php if($user->trips_count > 0){?>
                                                <a href="{{ url('users/userTrips',$user->id) }}"><span style="color:blue;">{{$user->trips_count}}</span></a> 
                                            <?php }?></td>
                                            
                                            <td>
                                                <?php if($user->is_email_verify == 1){?>
                                                @if($user->is_active==1)
                                                <a onclick="Changestatus({{$user->id}},0)" class="btn btn-success" type="button">Active</a>
                                                @else
                                                <a onclick="Changestatus({{$user->id}},1)" class="btn btn-danger" type="button">InActive</a>
                                                @endif
                                                <?php }?>
                                               
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
        <script>
    function Changestatus(Id, IsActive) {

        var data = {
            //"_token": $('#token').val(),
            'Id': Id,
            'IsActive': IsActive,
        };
        Swal.fire({
                title: 'Are you sure?',
                text: "You want to change status!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#loading-indicator').show();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("changeuserstatus") }}',
                    data: data,
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Status changed successfully',
                                'success'
                            ).then(() => {
                                $('#loading-indicator').show();
                                window.location.reload();
                                $('#loading-indicator').hide();
                                
                            })
                        }else{
                            swal('Error', 'Failed to update user status', 'error');
                        }
                    }
                });
            }
        });
    }
        </script>
@endsection