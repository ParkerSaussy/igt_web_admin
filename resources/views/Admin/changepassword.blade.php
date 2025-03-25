
@extends('Admin.layout.mainlayout')
@section('content')
<div class="content mt-3">
   <div class="animated fadeIn">
      <div class="row">
         <div class="col-lg-6">
            <div class="card">
               <div class="card-header"><strong>Change Password</strong><small> Form</small></div>
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
                    <form method="POST" action="{{ URL('/updatepassword') }}">
                        @csrf
                        <div class="form-group">
                            <label for="company" class=" form-control-label">Old Password</label>
                             <input type="password" class="form-control" placeholder="Enter Old Password" value="{{ old('opassword') }}" name="opassword" id="oldpassword">
                              @error('opassword')<span style="color:red;">{{$message}}</span>
                             @enderror
                        </div>
                        <div class="form-group">
                            <label for="newPaswordinput" class="form-label">New Password</label>
                            <input type="password" class="form-control" placeholder="Enter New Password" value="{{ old('npassword') }}" name="npassword" id="newPasword">
                            @error('npassword')<span style="color:red;">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                             <label for="confirmpassword" class="form-label">Confirm Password</label>
                             <input type="password" class="form-control" placeholder="Enter Confirm Password" value="{{ old('cpassword') }}" name="cpassword" id="confirmpassword">
                             @error('cpassword')<span style="color:red;">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
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


                          