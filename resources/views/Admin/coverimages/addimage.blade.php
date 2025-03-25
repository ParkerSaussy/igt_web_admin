
@extends('Admin.layout.mainlayout')



@section('content')
<div class="content mt-3">
   <div class="animated fadeIn">
      <div class="row">
         <div class="col-lg-12">
            <div class="card">
               <div class="card-header">
            
                <div class="breadcrumbs">
                    <div class="col-sm-4">
                       <div class="page-header float-left">
                          <div class="page-title">
                             <h1>Add Image<small> Form</small></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          <div class="page-title">
                             <ol class="breadcrumb text-right">
                              
                                <li><a href="{{URL('coverimages')}}">All Images</a></li>
                                <li><a  class="active">Add Image</a></li>
                             </ol>
                          </div>
                       </div>
                    </div>
                 </div></div>
               <div class="card-body card-block">
                   @if ($message = Session::get('success'))
                    <div class="sufee-alert alert with-close alert-success  alert-dismissible fade show">
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    @endif
                    @if ($message = Session::get('msg'))
                    <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    @endif
                    
                        @csrf
                        <div class="row">
                            <div class="col-md-5 text-center">
                            <div id="upload-demo"></div>
                            </div>
                            <div class="col-md-3" style="padding:5%;">
                            <strong>Select image to crop:</strong>
                            <input type="file" id="image" accept=".jpg, .jpeg, .png">
                    
                            <button class="btn btn-success btn-block upload-image" style="margin-top:2%; display: none;">Cropping Image</button>
                            <div id="save-image-container" style="display: none;">
                              <button class="btn btn-primary btn-block save-image" style="margin-top:2%">Save Image</button>
                            </div>
                            </div>
                    
                            <div class="col-md-4">
                            <div id="preview-crop-image" style="background:#9d9d9d;width:300px;padding:50px 50px;height:300px;"></div>
                            </div>
                          </div>
                    
                  

               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- .animated -->
</div>
<!-- .content -->
</div><!-- /#right-panel -->
<!-- Right Panel -->


@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var resize = $('#upload-demo').croppie({
        enableExif: true,
        enableOrientation: true,    
        viewport: { // Default { width: 100, height: 100, type: 'square' } 
            width: 460,
            height: 180,
            type: 'square' //square ,circle
            //ratio: 23 / 9 // Set the desired ratio here
        },
        boundary: {
            width: 500,
            height: 200
        }
    });
    $('#image').on('change', function () { 
      var reader = new FileReader();
        reader.onload = function (e) {
          resize.croppie('bind',{
            url: e.target.result
          }).then(function(){
            $('#save-image-container').hide();
            $('.upload-image').show();
            console.log('jQuery bind complete');
          });
        }
        reader.readAsDataURL(this.files[0]);
    });
    
    $('.upload-image').on('click', function (ev) {
    resize.croppie('result', {
        type: 'canvas',
        size: 'viewport'
    }).then(function (img) {
        $.ajax({
            url: "{{route('croppie.upload-image')}}",
            type: "POST",
            data: {"image": img},
            success: function (data) {
                html = '<img src="' + img + '" />';
                $("#preview-crop-image").html(html);

                // After the AJAX request completes, show the Save Image button
                $('#save-image-container').show();
            }
        });
    });
});

//save image
$('.save-image').on('click', function (ev) {
    resize.croppie('result', {
        type: 'canvas',
        size: 'viewport'
    }).then(function (img) {
        $.ajax({
            url: "{{route('croppie.save-image')}}",
            type: "POST",
            data: {"image": img},
            success: function (data) {
            if (data.status === true) {
                    Swal.fire(
                      'Good job!',
                       data.message,
                      'success'
                      ).then(() => {
                              window.location.href = data.redirect_url;
                      })
              }else{
                  swal('Error', 'Image upload failed', 'error');
              }
            }
        });
    });
});

});
    </script>

                          