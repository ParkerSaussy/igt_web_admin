
@extends('Admin.layout.mainlayout')
@section('content')
<div class="content mt-3">
   <div class="animated fadeIn">
      <div class="row">
         <div class="col-lg-6">
            <div class="card">
               <div class="card-header">
                
                <div class="breadcrumbs">
                    <div class="col-sm-4">
                       <div class="page-header float-left">
                          <div class="page-title">
                             <h1>Add FAQ</strong><small> Form</small></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          <div class="page-title">
                             <ol class="breadcrumb text-right">
                                <li><a href="{{URL('allfaqs')}}">All FAQ's</a></li>
                                <li><a  class="active">Add FAQ</a></li>
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
                    <form method="POST" action="{{ URL('/storefaq') }}" >
                        @csrf

                      

                        <div class="form-group">
                            <label for="company" class=" form-control-label">Question</label>
                             <input type="text" class="form-control" placeholder="Enter Question" value="{{ old('question') }}" name="question" id="question">
                             @error('question') 
                             <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                 <span class="badge badge-pill badge-danger">Validation</span>
                                 <strong> {{ $message }} </strong>
                                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                         <span aria-hidden="true">×</span>
                                     </button>
                             </div></span>
                             @enderror
                        </div>

                        <div class="form-group">
                            <label for="answer" class=" form-control-label">Answer</label>
                            <textarea name="answer" id="editor" rows="9" placeholder="Content..." class="form-control">
                                {{ old('answer') }}
                               </textarea>
                             @error('answer') 
                             <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                 <span class="badge badge-pill badge-danger">Validation</span>
                                 <strong> {{ $message }} </strong>
                                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                         <span aria-hidden="true">×</span>
                                     </button>
                             </div></span>
                             @enderror
                        </div>
                        <div class="form-group">
                            <label class=" form-control-label">Status</label>
                          
                               <div class="form-check">
                                  <div class="radio">
                                     <label for="radio1" class="form-check-label ">
                                     <input type="radio" id="radio1" name="status" value="1" {{ (old('status') == '1') ? 'checked' : '' }} class="form-check-input">Active
                                     </label>
                                  </div>
                                  <div class="radio">
                                     <label for="radio2" class="form-check-label ">
                                     <input type="radio" id="radio2" name="status" value="0" {{ (old('status') == '0') ? 'checked' : '' }} class="form-check-input">Deactive
                                     </label>
                                  </div>
                                  
                               </div>
                               @error('status') 
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



                          