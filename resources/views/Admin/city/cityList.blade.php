
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
                                             <h1>Cities</h1>
                                          </div>
                                       </div>
                                    </div>
                                    <button type="button" onclick="deleteMultipleRows()" class="btn btn-danger" style="float: right; margin-top:5px; margin-left:10px;" disabled="disabled" id="deleteBtn">Delete Selected Rows</button> 
                                    <a href="{{ url('/addcity') }}"><button type="button" style="float: right; margin-top:5px;" class="btn btn-success add-btn"><i class="ri-add-line align-bottom me-1"></i> Add City</button></a>
                                 </div>
                                   
                            </div>
                            <div class="card-body">
                                <div class="grid-topbar-custom">
                                    <div class="grid-show-text">Show <span>100</span> entries</div>
                                    <div class="grid-search-box">
                                        <form action="{{ url('allcity') }}" method="GET">
                                            <div class="input-group mb-3">
                                                <label class="mb-0 mr-2">Search:</label>
                                                <input type="text" class="form-control" name="search" value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-secondary" type="submit">Search</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <table id="" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                            <th>Id</th>
                                            <th>City Name</th>
                                            <th>State Name</th>
                                            <th>Country Name</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                          @foreach($cities  as $city)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $city->id }}"></td>
                                            <?php $status = $city->is_deleted;?>
                                            <td>{{$city->id}}</td>
                                            <td>{{$city->city_name}}</td>
                                            <td>{{$city->state}}</td>
                                            <td>{{$city->country_name}}</td>
                                            
                                          
                                            <td id="isstatus{{$city->is_active}}">
                                                @if($city->is_deleted==0)
                                                <a onclick="Changestatus({{$city->id}},1)" class="btn btn-success" type="button">Active</a>
                                                @else
                                                <a onclick="Changestatus({{$city->id}},0)" class="btn btn-danger" type="button">InActive</a>
                                                @endif
                                            </td>
                                            <td>
                                            <div class="icon-container">
                                                <a href="{{ url('/editcity',$city->id) }}"><span class="ti-pencil"></span><span class="icon-name"></span></a>
                                                <a href="#" onclick="Changestatus({{$city->id}},1)"><span class="ti-trash"></span><span class="icon-name"></span></a>
                                            </div>
                                        </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $cities ->appends(['search' => request('search')])->links('Admin.pagination.custom') }}
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
   // updateDeleteButtonState();
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
                text: "You want to delete selected cities !",
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
                    url: '{{ route("deleteallcity") }}',
                    data: { 'Id': ids },
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Selected cities deleted successfully',
                                'success'
                            ).then(() => {
                                $('#loading-indicator').show();
                                window.location.reload();
                                $('#loading-indicator').hide();
                                
                            })
                        }else{
                            swal('Error', 'Failed to delete cities', 'error');
                        }
                    }
                });
            }
        });
    }

   
</script>
@endsection