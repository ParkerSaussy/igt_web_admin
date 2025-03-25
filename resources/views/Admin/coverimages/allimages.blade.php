
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
                                             <h1>Cover Images</strong></h1>
                                          </div>
                                       </div>
                                    </div>
                                    <button type="button" onclick="deleteMultipleRows()" class="btn btn-danger" style="float: right; margin-top:5px; margin-left:10px;" disabled="disabled" id="deleteBtn">Delete Selected Rows</button> 
                                    <a href="{{ url('/addimage') }}"><button type="button" style="float: right; margin-top:5px;" class="btn btn-success add-btn"><i class="ri-add-line align-bottom me-1"></i> Add Image</button></a>
                                 </div>
                               
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                            <th>Id</th>
                                            <th>Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $imageUrl  = config('global.cover_images'); ?>
                                          @foreach($data as $image)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $image->id }}"></td>
                                            <td>{{$image->id}}</td>
                                            <td><a href="<?php echo $imageUrl.$image->image_name?>" target="_blank"><img src="<?php echo $imageUrl.$image->image_name?>" height="50" width="127"></a></td>
                                           
                                            <td>
                                            <div class="icon-container">
                                                <?php  if ($image->is_deleted == 0) {?>
                                                <a href="#" onclick="deleteImage({{$image->id}},1)"><span class="ti-trash"></span><span class="icon-name"></span></a>
                                                <?php } ?>
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
    function deleteImage(Id, IsActive) {

        var data = {
            //"_token": $('#token').val(),
            'Id': Id,
            'IsActive': IsActive,
        };
        Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete cover image",
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
                    url: '{{ route("deleteCoverImage") }}',
                    data: data,
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Cover image deleted successfully',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            })
                        }else{
                            swal('Error', 'Failed to delete cover image', 'error');
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
                text: "You want to delete selected images!",
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
                    url: '{{ route("deleteallimages") }}',
                    data: { 'Id': ids },
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Selected images deleted successfully',
                                'success'
                            ).then(() => {
                                $('#loading-indicator').show();
                                window.location.reload();
                                $('#loading-indicator').hide();
                                
                            })
                        }else{
                            swal('Error', 'Failed to delete images', 'error');
                        }
                    }
                });
            }
        });
    }

   
</script>