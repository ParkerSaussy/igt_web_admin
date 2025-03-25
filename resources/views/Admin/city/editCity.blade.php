
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
                             <h1>Edit City</strong><small> Form</small></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          <div class="page-title">
                             <ol class="breadcrumb text-right">
                                <li><a href="{{URL('allcity')}}">Cities</a></li>
                                <li><a  class="active">Edit City</a></li>
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
                    @if ($message = Session::get('fail'))
                    <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                        {{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    @endif
                    <form method="POST" action="{{ URL('/updatecity') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}"/>
                        <div class="form-group">
                            <label for="company" class=" form-control-label">City Name</label>
                             <input type="text" class="form-control" value="{{ $data->city_name }}" name="cityName" id="cityName">
                             @error('cityName') 
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
                           <label for="company" class=" form-control-label">State Name</label>
                            <input type="text" class="form-control" placeholder="Enter State Name" value="{{ $data->state }}" name="state" id="cityName">
                            @error('state') 
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
                        <label for="company" class=" form-control-label">State Abbreviation </label>
                         <input type="text" class="form-control" placeholder="Enter State Abbreviation" value="{{ $data->state_abbr }}" name="abbreviation" id="stateName">
                         @error('abbreviation') 
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
                            <label for="company" class=" form-control-label">Country Name</label>
                             <input type="text" class="form-control" value="{{ $data->country_name }}" name="countryName" id="countryName">
                             @error('countryName') 
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
                            
                            <label for="select" class=" form-control-label">Timezone</label>
                           
                            <select name="timeZone" id="timeZone" class="form-control">
                           
                            @foreach ($getTimezone as $value => $label)
                                <option value="{{ $label->abbr }}" {{  $label->abbr  == $data->time_zone ? 'selected' : '' }} >
                                    {{ $label->value }}</option>
                            @endforeach
                            
                            </select>
                            @error('timeZone') 
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
                                     <input type="radio" id="radio1" name="status" value="0" @if($data->is_deleted === 0) checked @endif class="form-check-input">Active
                                     </label>
                                  </div>
                                  <div class="radio">
                                     <label for="radio2" class="form-check-label ">
                                     <input type="radio" id="radio2" name="status" value="1" @if($data->is_deleted === 1) checked @endif class="form-check-input">Deactive
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


                          