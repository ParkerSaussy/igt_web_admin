
@extends('Admin.layout.mainlayout')
@section('content')
<div class="content mt-3">
   <div class="animated fadeIn">
      <div class="row">
         <div class="col-lg-8">
            <div class="card">
               <div class="card-header">
                
                <div class="breadcrumbs">
                    <div class="col-sm-4">
                       <div class="page-header float-left">
                          <div class="page-title">
                             <h1>Inquiry Reply<small> Form</small></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          <div class="page-title">
                             <ol class="breadcrumb text-right">
                                <li><a href="{{URL('allinquiries')}}">All Inquiries</a></li>
                                <li><a  class="active">Inquiry Reply</a></li>
                             </ol>
                          </div>
                       </div>
                    </div>
                 </div>
            </div>
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
                    <form method="POST" action="{{ URL('/updateinquiry') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}"/>
                        <input type="hidden" name="user_id" value="{{ $data->user_id }}"/>
                        <input type="hidden" name="old_message" value="{{ $data->message }}"/>
                        <input type="hidden" name="email" value="{{ $data->email }}"/>
                        <input type="hidden" name="first_name" value="{{ $data->first_name }}"/>
                        <input type="hidden" name="last_name" value="{{ $data->last_name }}"/>

                        <div class="form-group">
                            <label for="company" class="form-control-label">User Message</label>
                           <div>
                            <span><strong>{{ $data->message }}</strong></span>
                           </div>

                         
                        </div>
                       
                        <div class="form-group">
                            <label for="company" class="form-control-label">Reply Message</label>
                           <textarea name="reply" id="editor" rows="9" placeholder="Content..." class="form-control">
                            {{old('reply')}}
                           </textarea>

                            @error('reply') 
                            <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                <span class="badge badge-pill badge-danger">Validation</span>
                                <strong> {{ $message }} </strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                            </div></span>
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


                          