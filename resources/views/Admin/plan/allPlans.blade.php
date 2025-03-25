
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
                                             <h1>Plans</strong></h1>
                                          </div>
                                       </div>
                                    </div>
                                    <button type="button" onclick="deleteMultipleRows()" class="btn btn-danger" style="float: right; margin-top:5px; margin-left:10px;" disabled="disabled" id="deleteBtn">Delete Selected Rows</button> 
                                    <a href="{{ url('/addplan') }}"><button type="button" style="float: right; margin-top:5px;" class="btn btn-success add-btn"><i class="ri-add-line align-bottom me-1"></i> Add Plan</button></a>
                                 </div>
                                
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                            <th>Id</th>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Duration</th>
                                            <th>Image</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $imageUrl  = config('global.local_image_url'); ?>
                                          @foreach($data as $plan)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $plan->id }}"></td>
                                            <td>{{$plan->id}}</td>
                                            <td>{{$plan->type}}</td>
                                            <td>{{$plan->name}}</td>
                                            <td>{!! strip_tags(Str::limit(html_entity_decode($plan->description), 100)) !!}</td>
                                            <td>{{$plan->discounted_price}}</td>
                                            <td>{{$plan->duration}}</td>
                                            <td><a href="<?php echo $imageUrl.$plan->image?>" target="_blank"><img src="<?php echo $imageUrl.$plan->image?>" height="50" width="50"></a></td>
                                            <td id="isstatus{{$plan->is_active}}">
                                                @if($plan->is_active==0)
                                                <a onclick="Changestatus({{$plan->id}},1)" class="btn btn-danger" type="button">InActive</a>
                                                @else
                                                <a onclick="Changestatus({{$plan->id}},0)" class="btn btn-success" type="button">Active</a>
                                                @endif
                                            </td>
                                            <td>
                                            <div class="icon-container">
                                                <a href="{{ url('/editplan',$plan->id) }}"><span class="ti-pencil"></span><span class="icon-name"></span></a>
                                                <a href="#" onclick="deletePlan({{$plan->id}},1)"><span class="ti-trash"></span><span class="icon-name"></span></a>
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
<script>
    function deletePlan(Id, IsActive) {

        var data = {
            //"_token": $('#token').val(),
            'Id': Id,
            'IsActive': IsActive,
        };
        Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete plan",
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
                    url: '{{ route("deletePlan") }}',
                    data: data,
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Plan deleted successfully',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            })
                        }else{
                            swal('Error', 'Failed to Plan', 'error');
                        }
                    }
                });
            }
        });
    }
</script>
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
                    url: '{{ route("changePlanStatus") }}',
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
                            swal('Error', 'Failed to update plan status', 'error');
                        }
                    }
                });
            }
        });
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Check or uncheck all checkboxes when "Check All" is clicked
        $('#checkAll').click(function () {
            var isChecked = $(this).prop('checked');
            $('input[name="ids[]"]').prop('checked', $(this).prop('checked'));
            updateDeleteButtonState(); 
        });

        $('input[name="ids[]"]').change(function () {
        updateDeleteButtonState();
    });

    });

    function updateDeleteButtonState() {
        var selectedCount = $('input[name="ids[]"]:checked').length;
        var totalCount = $('input[name="ids[]"]').length;
        $('#deleteBtn').prop('disabled', selectedCount === 0);
        $('#checkAll').prop('checked', selectedCount === totalCount);
    }

    function deleteMultipleRows() {
        var ids = $('input[name="ids[]"]:checked').map(function () {
            return this.value;
        }).get();
      
        Swal.fire({
                title: 'Are you sure ?',
                text: "You want to delete selected plans!",
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
                    url: '{{ route("deleteallplan") }}',
                    data: { 'Id': ids },
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Selected plans deleted successfully',
                                'success'
                            ).then(() => {
                                $('#loading-indicator').show();
                                window.location.reload();
                                $('#loading-indicator').hide();
                                
                            })
                        }else{
                            swal('Error', 'Failed to delete plans', 'error');
                        }
                    }
                });
            }
        });
    }

   
</script>