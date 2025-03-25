
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
                                             <h1>Inquiries</h1>
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
                                            <th>Fullname</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                          @foreach($getInquiries as $inquiries)
                                        <tr>
                                           
                                            <td>{{$inquiries->id}}</td>
                                            <td>{{$inquiries->first_name}}</td>
                                            <td>{{$inquiries->email}}</td>
                                            <td>{{$inquiries->message}}</td>
                                          
                                            <td id="isstatus{{$inquiries->is_replied}}">
                                                @if($inquiries->is_replied==0)
                                                <a href="{{ url('/inquryReply',$inquiries->id) }}" class="btn btn-success" type="button">Make Reply</a>
                                               @else
                                               <span>Replied</span>
                                                @endif
                                            </td>
                                           
                                           
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
           // alert("Hello");

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
                        url: '{{ route("deletecity") }}',
                        data: data,
                        success: function(response) {
                            if (response == 'success') {
                                $('#loading-indicator').hide();
                                Swal.fire(
                                    'Good job!',
                                    'Status has been changed successfully',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                })
                            }else{
                                swal('Error', 'Failed to change city status', 'error');
                            }
                        }
                    });
                }
            });
        }
            </script>
@endsection