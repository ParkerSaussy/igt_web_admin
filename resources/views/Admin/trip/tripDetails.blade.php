@extends('Admin.layout.mainlayout')
@section('content')
    <?php $imageUrl = config('global.cover_images'); ?>
   
           
                <div class="breadcrumbs">
                    <div class="col-sm-4">
                       <div class="page-header float-left">
                          <div class="page-title">
                             <h1>Trip Details</strong></h1>
                          </div>
                       </div>
                    </div>
                    <div class="col-sm-8">
                       <div class="page-header float-right">
                          <div class="page-title">
                             <ol class="breadcrumb text-right">
                                <li><a href="{{URL('alltrips')}}">All Trips</a></li>
                                <li><a class="active">Trip Details</a></li>
                             </ol>
                          </div>
                       </div>
                    </div>
                 </div>
           
       
    <div class="content mt-3">
        <div class="row">
            <div class="col-md-6">
                <div class="card border card-height">
                    <div class="card-header">
                        <strong class="card-title">Trips</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <img src="<?php echo $imageUrl . $data[0]['trip_img_url']; ?>" alt="trip cover image">
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <span><strong>Name</strong></span>
                                <h4 class="card-title mb-3"> <?php echo $data[0]['trip_name']; ?></h4>
                            </div>
                        </div>
                        <span><strong>Description</strong></span>
                        <p class="card-text"><?php echo $data[0]['itinary_details']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border card-height">
                    <div class="card-header">
                        <strong class="card-title">Host Details</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h6><strong>Name</strong></h6>
                                <?php echo $data[0]->user['first_name'] . ' ' . $data[0]->user['last_name']; ?>
                            </div>
                            <div class="col-md-3">
                                <h6><strong>Mobile</strong></h6>
                                <?php echo $data[0]->user['country_code']; ?><?php echo $data[0]->user['mobile_number']; ?>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Email</strong></h6>
                                <?php echo $data[0]->user['email']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border card-height">
                    <div class="card-header">
                        <strong class="card-title">Trip Poll cities</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data[0]->city as $city)
                                    <tr>
                                        <td><?php echo $city['city_id']; ?></td>
                                        <td><?php echo $city['city_name']; ?></td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border card-height">
                    <div class="card-header">
                        <strong class="card-title">Guest List</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Mobile</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data[0]->guests as $guest)
                                    <tr>
                                        <td><?php echo $guest['first_name'] . ' ' . $guest['last_name']; ?></td>
                                        <td><?php echo $guest['email_id']; ?></td>
                                        <td><?php echo $guest['phone_number']; ?></td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
