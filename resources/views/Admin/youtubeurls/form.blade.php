
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
                             <h1>Edit Urls</strong><small> Form</small></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          {{-- <div class="page-title">
                             <ol class="breadcrumb text-right">
                                <li><a href="{{URL('allfaqs')}}">All URL's</a></li>
                                <li><a  class="active">Edit FAQ</a></li>
                             </ol>
                          </div> --}}
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
                    <form method="POST" action="{{ URL('/updateurl') }}" >
                        @csrf
                        <div class="form-group">
                            <label for="company" class=" form-control-label">Screen 1 Url</label>
                             <input type="text" class="form-control" placeholder="Enter Question" value="{{ $staticValues['screen1'] }}" name="screen1" id="screen1">
                             @error('screen1') 
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
                            <label for="company" class=" form-control-label">Screen 2 Url</label>
                             <input type="text" class="form-control" placeholder="Enter Question" value="{{ $staticValues['screen2'] }}" name="screen2" id="screen1">
                             @error('screen2') 
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
                            <label for="company" class=" form-control-label">Screen 3 Url</label>
                             <input type="text" class="form-control" placeholder="Enter Question" value="{{ $staticValues['screen3'] }}" name="screen3" id="screen1">
                             @error('screen3') 
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
                            <label for="company" class=" form-control-label">Screen 4 Url</label>
                             <input type="text" class="form-control" placeholder="Enter Question" value="{{ $staticValues['screen4'] }}" name="screen4" id="screen1">
                             @error('screen4') 
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
                            <label for="company" class=" form-control-label">Screen 5 Url</label>
                             <input type="text" class="form-control" placeholder="Enter Question" value="{{ $staticValues['screen5'] }}" name="screen5" id="screen1">
                             @error('screen5') 
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



                          