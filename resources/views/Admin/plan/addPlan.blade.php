
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
                             <h1>Add Plan</strong><small> Form</small></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          <div class="page-title">
                             <ol class="breadcrumb text-right">
                                <li><a href="{{URL('allplans')}}">All Plans</a></li>
                                <li><a  class="active">Add Plan</a></li>
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
                    <form method="POST" action="{{ URL('/storeplan') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            
                            <label for="select" class=" form-control-label">Plan Type</label>
                           
                            <select name="type" id="dropdown_type" class="form-control" >
                            <option value="">Please select type</option>
                            <option value="normal" {{ old('type') == "normal" ? 'selected' : '' }}>Normal </option>
                            <option value="single" {{ old('type') == "single" ? 'selected' : '' }}>Single Trip </option>
                        
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
                             <input type="text" class="form-control" placeholder="Enter Plan Name" value="{{ old('name') }}" name="name" id="name">
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
                                {{ old('description') }}
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
                             <input type="number"  class="form-control" placeholder="Enter Plan price" value="{{ old('price') }}" name="price" step="0.1" id="numeric_input">
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
                             <input type="number"  class="form-control" placeholder="Enter plan discounted price" value="{{ old('discounted_price') }}" name="discounted_price" step="0.1" id="d_price">
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

                        <div class="form-group duration" style="display: none;">
                            <label for="company" class=" form-control-label">Validity(Month)</label>
                             <input type="number" class="form-control" placeholder="Ex like 1 month" value="{{ old('duration') }}" name="duration" id="duration">
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
{{--                         
                        <div class="form-group">
                           
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
                            
                           
                        </div> --}}

                        <div class="form-group">
                            <?php $imageUrl  = config('global.local_image_url'); ?>
                            <label class="form-control-label">Image</label>
                            <div class="form-check" style="display: flex; gap:30px;">
                                <div class="radio">
                                   <label for="star" class="form-check-label ">
                                   <input type="radio" id="star" name="image" value="star.png" {{ old('image') === 'star.png' ? 'checked' : '' }} class="form-check-input">
                                   <img src="<?php echo $imageUrl?>/star.png" alt="">
                                   </label>
                                </div>
                                <div class="radio">
                                   <label for="dimond" class="form-check-label ">
                                   <input type="radio" id="dimond" name="image" value="diamond.png" {{ old('image') === 'diamond.png' ? 'checked' : '' }} class="form-check-input">
                                   <img src="<?php echo $imageUrl?>/diamond.png" alt="">
                                   </label>
                                </div>
                                <div class="radio">
                                    <label for="crown" class="form-check-label ">
                                    <input type="radio" id="crown" name="image" value="crown.png" {{ old('image') === 'crown.png' ? 'checked' : '' }} class="form-check-input">
                                    <img src="<?php echo $imageUrl?>/crown.png" alt="">
                                    </label>
                                 </div>
                                 <div class="radio">
                                    <label for="none" class="form-check-label ">
                                    <input type="radio" id="none" name="image" value=""  {{ old('image') === 'none' ? 'checked' : '' }}class="form-check-input">None
                                    </label>
                                 </div>
                             </div>
                        </div>

                        <div class="form-group">
                            <label for="company" class=" form-control-label">Apple Pay Key</label>
                             <input type="text" class="form-control" placeholder="Enter Apple Pay Key" value="{{ old('apple_pay_key') }}" name="apple_pay_key" id="name">
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

  
<script>
    document.getElementById("numeric_input").addEventListener("input", function() {
        // Remove non-numeric and non-decimal characters
        this.value = this.value.replace(/[^0-9\.]/g, '');
        
        // If there's more than one decimal point, remove all but the first one
        const decimalCount = (this.value.match(/\./g) || []).length;
        if (decimalCount > 1) {
            this.value = this.value.replace(/\.(?=.*\.)/g, '');
        }
        
        // Limit to two decimal places
        const parts = this.value.split('.');
        if (parts.length > 1 && parts[1].length > 2) {
            parts[1] = parts[1].substr(0, 2);
            this.value = parts.join('.');
        }
    });
</script>
<script>
    document.getElementById("duration").addEventListener("input", function() {
        // Remove non-numeric and non-decimal characters
        this.value = this.value.replace(/[^0-9\.]/g, '');
        
        // If there's more than one decimal point, remove all but the first one
        const decimalCount = (this.value.match(/\./g) || []).length;
        if (decimalCount > 1) {
            this.value = this.value.replace(/\.(?=.*\.)/g, '');
        }
        
        // Limit to two decimal places
        const parts = this.value.split('.');
        if (parts.length > 1 && parts[1].length > 2) {
            parts[1] = parts[1].substr(0, 2);
            this.value = parts.join('.');
        }
    });
</script>

<script>
    document.getElementById("d_price").addEventListener("input", function() {
        // Remove non-numeric and non-decimal characters
        this.value = this.value.replace(/[^0-9\.]/g, '');
        
        // If there's more than one decimal point, remove all but the first one
        const decimalCount = (this.value.match(/\./g) || []).length;
        if (decimalCount > 1) {
            this.value = this.value.replace(/\.(?=.*\.)/g, '');
        }
        
        // Limit to two decimal places
        const parts = this.value.split('.');
        if (parts.length > 1 && parts[1].length > 2) {
            parts[1] = parts[1].substr(0, 2);
            this.value = parts.join('.');
        }
    });
</script>



                          