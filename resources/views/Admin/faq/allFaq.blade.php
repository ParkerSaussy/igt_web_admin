
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
                                             <h1>FAQ's</strong></h1>
                                          </div>
                                       </div>
                                    </div>
                                   
                                    <button type="button" onclick="deleteMultipleRows()" class="btn btn-danger" style="float: right; margin-top:5px; margin-left:10px;" disabled="disabled" id="deleteBtn">Delete Selected Rows</button> 
                                    <a href="{{ url('/addfaq') }}"><button type="button" style="float: right; margin-top:5px;" class="btn btn-success add-btn"><i class="ri-add-line align-bottom me-1"></i> Add Faq</button></a>
                                    
                                </div>
                                
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                      
                                            <th>Id</th>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $imageUrl  = config('global.local_image_url'); ?>
                                          @foreach($data as $faq)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $faq->id }}"></td>
                                            <td>{{$faq->id}}</td>
                                            <td>{{$faq->question}}</td>
                                            <td>{{ mb_substr(strip_tags($faq->answer), 0, 100)}}</td>
                                          
                                            <td id="isstatus{{$faq->is_active}}">
                                                @if($faq->is_active==0)
                                                <a onclick="Changestatus({{$faq->id}},1)" class="btn btn-danger" type="button">InActive</a>
                                                @else
                                                <a onclick="Changestatus({{$faq->id}},0)" class="btn btn-success" type="button">Active</a>
                                                @endif
                                            </td>
                                            <td>
                                            <div class="icon-container">
                                                <a href="{{ url('/editfaq',$faq->id) }}"><span class="ti-pencil"></span><span class="icon-name"></span></a>
                                                <a href="#" onclick="deleteFaq({{$faq->id}},1)"><span class="ti-trash"></span><span class="icon-name"></span></a>
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
    function deleteFaq(Id, IsActive) {

        var data = {
            //"_token": $('#token').val(),
            'Id': Id,
            'IsActive': IsActive,
        };
        Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete FAQ",
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
                    url: '{{ route("deletefaq") }}',
                    data: data,
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'FAQ deleted successfully',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            })
                        }else{
                            swal('Error', 'Failed to FAQ', 'error');
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
                text: "You want to change faq status!",
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
                    url: '{{ route("changeFaqStatus") }}',
                    data: data,
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Faq status changed successfully',
                                'success'
                            ).then(() => {
                                $('#loading-indicator').show();
                                window.location.reload();
                                $('#loading-indicator').hide();
                                
                            })
                        }else{
                            swal('Error', 'Failed to update faq status', 'error');
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
                text: "You want to delete selected faq !",
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
                    url: '{{ route("deleteallfaq") }}',
                    data: { 'Id': ids },
                    success: function(response) {
                        if (response == 'success') {
                            $('#loading-indicator').hide();
                            Swal.fire(
                                'Good job!',
                                'Selected Faqs deleted successfully',
                                'success'
                            ).then(() => {
                                $('#loading-indicator').show();
                                window.location.reload();
                                $('#loading-indicator').hide();
                                
                            })
                        }else{
                            swal('Error', 'Failed to delete faqs', 'error');
                        }
                    }
                });
            }
        });
    }

   
</script>