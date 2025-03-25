
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
                             <h1>Edit Plan</strong><small> Form</small></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          <div class="page-title">
                             <ol class="breadcrumb text-right">
                    
                                <li><a href="{{URL('allplans')}}">All Plans</a></li>
                                <li><a  class="active">Edit Plan</a></li>
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
                    <form method="POST" action="{{ URL('/updateplan') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}"/>
                        <input type="hidden" name="old_image" value="{{ $data->image }}"/>

                        <div class="form-group">
                            
                            <label for="select"  id="" class=" form-control-label">Plan Type</label>
                           
                            <select name="type" id="dropdown_type" class="form-control">
                            <option value="">Please select type</option>
                            <option value="normal" {{ $data->type == 'normal' ? 'selected' : '' }} >Normal </option>
                            <option value="single" {{ $data->type == 'single' ? 'selected' : '' }}>Single Trip </option>
                        
                            </select>
                            @error('type') 
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
                            <label for="company" class=" form-control-label">Plan Name</label>
                             <input type="text" class="form-control" placeholder="Enter Plan Name" value="{{ $data->name }}" name="name" id="name">
                             @error('name') 
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
                            <label for="company" class=" form-control-label">Description</label>
                            <textarea name="description" id="editor" rows="9" placeholder="Content..." class="form-control">
                                <?php echo htmlspecialchars_decode($data->description);?>
                               </textarea>
                             @error('description') 
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
                            <label for="company" class=" form-control-label">Actual Price</label>
                             <input type="number" step="0.01" class="form-control" placeholder="Enter Plan price" value="{{ $data->price }}" name="price" id="price">
                             @error('price') 
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
                            <label for="company" class=" form-control-label">Discounted Price</label>
                             <input type="number"  class="form-control" placeholder="Enter plan discounted price" value="{{ $data->discounted_price }}"  name="discounted_price" step="0.1" id="d_price">
                             @error('discounted_price') 
                             <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                 <span class="badge badge-pill badge-danger">Validation</span>
                                 <strong> {{ $message }} </strong>
                                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                         <span aria-hidden="true">×</span>
                                     </button>
                             </div></span>
                             @enderror
                        </div>
                        <?php if($data->type == 'normal'){ ?>
                            <div class="form-group duration">
                                <label for="company" class=" form-control-label">Validity(Month)</label>
                                 <input type="number" class="form-control" placeholder="Ex like 1 month" value="{{ $data->duration }}" name="duration" id="name">
                                 @error('duration') 
                                 <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                     <span class="badge badge-pill badge-danger">Validation</span>
                                     <strong> {{ $message }} </strong>
                                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                             <span aria-hidden="true">×</span>
                                         </button>
                                 </div></span>
                                 @enderror
                            </div>
                        <?php } ?>
                       
                        
                        {{-- <div class="form-group">
                           
                            <label for="file-input" class=" form-control-label">Image</label>
                           
                            <input type="file" id="file-input" name="image" class="form-control-file">
                                @error('image') 
                            <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                 <span class="badge badge-pill badge-danger">Validation</span>
                                 <strong> {{ $message }} </strong>
                                 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                         <span aria-hidden="true">×</span>
                                     </button>
                            </div></span>
                             @enderror

                             <?php //$imageUrl  = config('global.local_image_url'); ?>
                             <div style="margin-top:10px;">
                             <a href="<?php //echo $imageUrl.$data->image_name?>" target="_blank"><img src="<?php echo $imageUrl.$data->image?>" height="50" width="50"></a>
                             </div>
                           
                        </div> --}}
                        <div class="form-group">
                            <?php $imageUrl  = config('global.local_image_url'); ?>
                            <label class="form-control-label">Image</label>
                            <div class="form-check" style="display: flex; gap:30px;">
                                <div class="radio">
                                   <label for="star" class="form-check-label ">
                                   <input type="radio" id="star" name="image" value="star.png" class="form-check-input" @if($data->image == 'star.png') checked @endif>
                                   <img src="<?php echo $imageUrl?>/star.png" alt="">
                                   </label>
                                </div>
                                <div class="radio">
                                   <label for="dimond" class="form-check-label ">
                                   <input type="radio" id="dimond" name="image" value="diamond.png" class="form-check-input" @if($data->image == 'diamond.png') checked @endif>
                                   <img src="<?php echo $imageUrl?>/diamond.png" alt="">
                                   </label>
                                </div>
                                <div class="radio">
                                    <label for="crown" class="form-check-label ">
                                    <input type="radio" id="crown" name="image" value="crown.png" class="form-check-input" @if($data->image == 'crown.png') checked @endif>
                                    <img src="<?php echo $imageUrl?>/crown.png" alt="">
                                    </label>
                                 </div>
                                 <div class="radio">
                                    <label for="none" class="form-check-label ">
                                    <input type="radio" id="none" name="image" value="none" class="form-check-input" @if($data->image == 'none') checked @endif>None
                                    </label>
                                 </div>
                             </div>
                        </div>

                        <div class="form-group">
                            <label for="company" class=" form-control-label">Apple Pay Key</label>
                             <input type="text" class="form-control" placeholder="Enter Apple Pay Key" value="{{ $data->apple_pay_key }}" name="apple_pay_key" id="name">
                             @error('apple_pay_key') 
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
                                     <input type="radio" id="radio1" name="status" value="1" {{ $data->is_active == 1 ? 'checked' : '' }} class="form-check-input">Active
                                     </label>
                                  </div>
                                  <div class="radio">
                                     <label for="radio2" class="form-check-label ">
                                     <input type="radio" id="radio2" name="status" value="0" {{ $data->is_active == 0 ? 'checked' : '' }} class="form-check-input">Deactive
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


                          