<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO Metatag -->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <!-- Responsive Metatag -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ItsGoTime</title>
    <?php $imageUrl = config('global.local_image_url'); ?>

    <link rel="icon" type="image/x-icon" href="<?php echo $imageUrl; ?>favicon.png">
    <!-- <link rel="shortcut icon" href="./assets/images/favicon.png" type="image/x-icon"> -->

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.poll.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/poll.css') }}">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'>
</head>

<body>
    <?php $imageUrl = config('global.local_image_url'); ?>
    <div class="hero-section" style="background-image: url(<?php echo $imageUrl . 'hero-banner-bg.png'; ?>)">
        <div class="container">
            <div class="logo-box">
                <a href="javascript:;">
                    <img src=" <?php echo $imageUrl . 'logo.png'; ?>" alt="LesGo">
                </a>
            </div>
            <div class="trip-info-box">
                <div class="trip-location">
                    <?php //echo $tripData['totalVip']
                    ?>
                    <h1>{{ $tripData['data']['trip_name'] }}</h1>
                    {{-- <p><img src="<?php //echo $imageUrl."calendar-ic.svg"
                    ?>" alt=""> RSVP by 5th June, 2023</p> --}}
                </div>
                <?php $timestamp = strtotime($tripData['data']['response_deadline']);
                
                // Format the timestamp as 'dS M, Y'
                $formattedDate = date('jS M, Y', $timestamp);
                ?>
                <div class="trip-due-date">Due Date: <?php echo $formattedDate; ?> </div>
            </div>
        </div>
    </div>

    <div class="trip-information">
        <div class="container">
            <form id="dataForm">
                <div class="trip-detail trip-box">
                    <h2>Trip Details</h2>
                    <div class="white-box">
                        <p><?php echo $tripData['data']['trip_description']; ?></p>
                    </div>
                </div>
                <?php $isTripFinalized = $tripData['data']['is_trip_finalised']; ?>
                <?php if($isTripFinalized !=1){?>
                <div class="trip-option-form trip-box">
                    <h2>Date Options</h2>
                    <div class="white-box">
                        <h3>Select One or More Dates</h3>

                        <?php foreach ($dateList['data'] as $dates) {
                            $startDate = $dates['start_date'];
                            $startDateTimestamp = strtotime($startDate);
                            $startformattedDate = date('d M', $startDateTimestamp);

                            $endDate = $dates['end_date'];
                            $endDateTimestamp = strtotime($endDate);
                            $endformattedDate = date('d M', $endDateTimestamp); ?>
                        <div class="vote-main-box">
                            <div class="checkbox-with-user">
                                <label class="custom-checkbox">

                                    <input type="checkbox" class="date-checkbox"
                                        data-is-default="{{ $dates['is_default'] }}" data-id="{{ $dates['id'] }}"
                                        name="date[]" id="{{ $dates['is_default'] }}" value="{{ $dates['id'] }}"
                                        @if ($dates['userVoted'] == 1) checked @endif>
                                    <span class="checkmark"></span> <?php if ($dates['is_default'] == 1) {
                                        echo "I can't make any of these dates";
                                    } else {
                                        echo $startformattedDate . ' to ' . $endformattedDate;
                                    } ?>
                                </label>
                                <div class="user-count" onclick="showProfileImageForDateList({{ $dates['id'] }})"
                                    data-bs-toggle="modal" data-bs-target="#userListModal">
                                    <?php //$imageUrl  = config('global.local_image_url');
                                    ?>

                                    {{-- <img src="<?php //echo $imageUrl
                                    ?>/user-img-02.png" alt="">
                                    <img src="<?php //echo $imageUrl
                                    ?>/user-img-02.png" alt=""> --}}
                                    <?php 
                                $imagePath = config('global.local_image_url');
                                if (isset($dates['trip_date_polls']) ) {
                                    foreach ($dates['trip_date_polls'] as $poll) {
                                        if (isset($poll['guest_details']['users_detail_profile_image'])) {
                                            $profileImage = $poll['guest_details']['users_detail_profile_image'];
                                             $imageUrl = $profileImage['profile_image'];
                                             $defaultImageUrl = "https://lesgo.dashtechinc.com/uploads/images/admin.png";
                                               echo '<img src="' . $imageUrl . '" alt="" style="height: 30px; width: 30px; border-radius: 50px; border: 1px;" onerror="this.onerror=null; this.src=\'' . $defaultImageUrl . '\'">';
                                       }?>
                                    <?php } ?>
                                    {{-- <img src="<?php echo $imageUrl; ?>" alt="" style="height: 30px; width:30px; border-radius: 50px;
                                    border: 1px;"> --}}
                                    <?php $tripDatePollsCount = count($dates['trip_date_polls']);
                                    if($tripDatePollsCount > 1){ ?>
                                    <span data-toggle="modal" data-target="#userListModal">+<?php echo $tripDatePollsCount - 1; ?></span>
                                    <?php  }
                                    ?>
                                    <?php }
                                
                               ?>

                                </div>

                            </div>
                            <?php $vipVoted = $dates['vipVoted'];
                            $totalVip = $dates['totalVip'];
                            //$totalVoted = $dates['totalVoted'];
                            // $dates['totalGuest'];
                            $percentage = 0;
                            if ($totalVip == 0) {
                                $colorCode = '#BA1A1A';
                            } elseif ($vipVoted == 0) {
                                $colorCode = '#BA1A1A';
                            } elseif ($totalVip == $vipVoted) {
                                $colorCode = '#2AB26E';
                            } elseif ($vipVoted < $totalVip) {
                                $colorCode = '#E5B80B';
                            } else {
                                $colorCode = '#131B4C';
                            }
                            $totalusers = $dates['totalGuest'];
                            
                            if ($totalusers > 0) {
                                $percentage = ($dates['totalVoted'] / $totalusers) * 100; // Multiply by 2 if each user can vote multiple times
                            
                                // Round the percentage to two decimal places
                                $percentage = round($percentage, 2);
                            }
                            
                            ?>
                            <div class="progress vote-progress">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $percentage }}%; background-color: {{ $colorCode }} !important"
                                    aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                {{-- <span>{{$percentage}}%</span> --}}
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>



                <div class="trip-option-form trip-box">
                    <h2>Location Options</h2>
                    <div class="white-box">
                        <h3>Select One or More City/Venues</h3>
                        <?php foreach ($cityList['data'] as $cities) {?>
                        <div class="vote-main-box">
                            <div class="checkbox-with-user">

                                <label class="custom-checkbox">
                                    <input type="checkbox" class="city-checkbox" name="city[]"
                                        data-is-default="{{ $cities['city_name_details']['is_default'] }}"
                                        id="{{ $cities['city_name_details']['is_default'] }}"
                                        data-id="{{ $cities['id'] }}" value="{{ $cities['id'] }}"
                                        @if ($cities['userVoted'] == 1) checked @endif>
                                    <span class="checkmark"></span>
                                    <?php if ($cities['city_name_details']['is_default'] == 1) {
                                        echo "I can't make any of these cities";
                                    } else {
                                        echo $cities['city_name_details']['city_name'] . ',' . $cities['city_name_details']['country_name'] . ', (' . $cities['city_name_details']['time_zone'] . ')';
                                    } ?>
                                    {{-- <?php //echo $cities['city_name_details']['city_name']
                                    ?>, <?php //echo $cities['city_name_details']['country_name']
                                    ?>, (PST) --}}
                                </label>

                                <div class="user-count" onclick="showProfileImageForCityList({{ $cities['id'] }})"
                                    data-bs-toggle="modal" data-bs-target="#userListModal">
                                    <?php if (isset($cities['trip_city_polls'])) {
                                        foreach ($cities['trip_city_polls'] as $poll) {
                                            if (isset($poll['guest_details']['users_detail_profile_image'])) {
                                                $profileImage = $poll['guest_details']['users_detail_profile_image'];
                                                $imageUrl = $profileImage['profile_image'];
                                                $defaultImageUrl = 'https://lesgo.dashtechinc.com/uploads/images/admin.png';
                                                echo '<img src="' . $imageUrl . '" alt="" style="height: 30px; width: 30px; border-radius: 50px; border: 1px;" onerror="this.onerror=null; this.src=\'' . $defaultImageUrl . '\'">';
                                            }
                                        }
                                    } ?>
                                    <?php $tripCityPollsCount = count($cities['trip_city_polls']);
                                      if($tripCityPollsCount > 1){ ?>
                                    <span>+<?php echo $tripCityPollsCount - 1; ?></span>
                                    <?php  }
                                      ?>
                                </div>
                            </div>
                            <?php $vipVoted = $cities['vipVoted'];
                            $totalVip = $cities['totalVip'];
                            //$totalVoted = $dates['totalVoted'];
                            // $dates['totalGuest'];
                            $percentage = 0;
                            if ($totalVip == 0) {
                                $colorCode = '#BA1A1A';
                            } elseif ($vipVoted == 0) {
                                $colorCode = '#BA1A1A';
                            } elseif ($totalVip == $vipVoted) {
                                $colorCode = '#2AB26E';
                            } elseif ($vipVoted < $totalVip) {
                                $colorCode = '#E5B80B';
                            } else {
                                $colorCode = '#131B4C';
                            }
                            $totalusers = $cities['totalGuest'];
                            
                            if ($totalusers > 0) {
                                $percentage = ($cities['totalVoted'] / $totalusers) * 100; // Multiply by 2 if each user can vote multiple times
                            
                                // Round the percentage to two decimal places
                                $percentage = round($percentage, 2);
                            }
                            
                            ?>
                            <div class="progress vote-progress">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ $percentage }}%; background-color: {{ $colorCode }} !important"
                                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                {{-- <span>{{$percentage}}%</span> --}}
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php }else{?>
                <div class="itinerary-detail trip-box">
                    <h2>Final Trip Date And City</h2>
                    <div class="white-box">
                        <p><b>Trip Start Date</b> :
                            {{ \Carbon\Carbon::parse($tripData['data']['trip_final_start_date'])->format('l jS \\of F Y h:i A') }}
                        </p>
                        <p><b>Trip End Date</b> :
                            {{ \Carbon\Carbon::parse($tripData['data']['trip_final_end_date'])->format('l jS \\of F Y h:i A') }}
                        </p>
                        <?php 
                            foreach ($cityList['data'] as $cities) {
                               if($cities['city_name_details']['id'] == $tripData['data']['trip_final_city']){ ?>
                        <b>City</b> : <?php echo $cities['city_name_details']['city_name']; ?>
                        <?php  } }
                            ?>
                    </div>
                </div>
                <div class="itinerary-detail trip-box">
                    <h2>Final Trip Comment</h2>
                    <div class="white-box">


                        <p><b>Comment</b> : {{ $tripData['data']['trip_finalizing_comment'] }}</p>
                    </div>
                </div>
                <?php } ?>
                <?php if($tripData['data']['itinary_details'] != ""){ ?>
                <div class="itinerary-detail trip-box">
                    <h2>Itinerary Details</h2>
                    <div class="white-box">
                        <p>{{ $tripData['data']['itinary_details'] }}</p>
                    </div>
                </div>
                <?php } ?>
                <div class="invitees trip-box">
                    <h2>Invitees</h2>
                    <div class="white-box">
                        <div class="vip-top-box">
                            <div class="row">
                                <?php  $serialNumber = 1;
                               foreach ($totalGuest as $guest) {
                                    // Check if the guest is VIP
                                    if ($guest->role == "VIP") {
                                        $status = $guest->invite_status;
                                            if($status == "Approved"){
                                                $className = "green-bg" ;
                                            }else{
                                                $className = "red-bg" ;
                                            }
                                        ?>
                                <div class="col-6 col-md-6">
                                    <div class="vip-box">
                                        <h4>VIP-<?php echo $guest->first_name; ?> <?php echo $guest->last_name; ?></h4>
                                        <div class="<?php echo $className; ?>"></div>
                                    </div>
                                </div>
                                <?php $serialNumber++; ?>
                                <?php }
                                }
                                ?>

                            </div>
                        </div>
                        <div class="row">
                            <?php $serialNumber = 1; ?>
                            <?php foreach ($totalGuest as $guest) {
                                 $status = $guest->invite_status;
                                 if($status == "Sent"){
                                    $className = "pending";
                                 }elseif ($status == "Approved") {
                                    $className = "accepted" ;
                                 }else{
                                    $className = "declined" ;
                                 }


                                 
                                 ?>
                            <div class="col-12 col-lg-6">
                                <div class="guest-box <?php echo $className; ?>">
                                    <div class="titile-with-status">
                                        <h5>Guest-<?php echo $serialNumber; ?></h5>
                                        <span class="<?php echo $className; ?>-bg">
                                            <?php if ($guest->invite_status == 'Sent') {
                                                echo 'Pending';
                                            } else {
                                                echo $guest->invite_status;
                                            } ?>
                                        </span>
                                    </div>
                                    <div class="guest-info">
                                        <div class="info-box"><span>Name:</span> <?php echo $guest->first_name; ?>
                                            <?php echo $guest->last_name; ?></div>
                                        <div class="info-box"><span>Email:</span> <?php echo $guest->email_id; ?></div>
                                    </div>
                                </div>
                            </div>
                            <?php  $serialNumber++; } ?>



                        </div>
                    </div>
                </div>
                <div class="advertisement trip-box">
                    <h2>Advertisement</h2>
                    <div class="white-box">
                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="ads-img">
                                    <img src="assets/images/hero-banner-bg.png" alt="">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="ads-img">
                                    <img src="assets/images/hero-banner-bg.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="app-download trip-box">
                    <!-- <h2>App Download</h2> -->
                    <div class="white-box">
                        <div class="app-icon">
                            <a href="javascript:;" target="_blank"> <img
                                    src=" https://lesgo.dashtechinc.com/uploads/images/app-store-logo.png"
                                    alt="App Store Logo"></a>
                            <a href="javascript:;" target="_blank"> <img
                                    src=" https://lesgo.dashtechinc.com/uploads/images/google-play-logo.png"
                                    alt="Play Store Logo"></a>
                        </div>
                    </div>
                </div>
                <?php if($isTripFinalized !=1){?>
                <div class="submit-btn">
                    <button id="submitButton" type="button" class="btn-green">I'm In</button>
                    <button type="button" id="declined" class="btn-red">I'm Out</button>
                </div>
                <?php } ?>



            </form>
        </div>
    </div>

    <!-- User List Modal Start -->
    <div class="modal fade" id="userListModal" tabindex="-1" aria-labelledby="userListModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userListModalLabel">Invitees</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="user-list-modal">

                        <div class="user-list">
                            <div class="user-img" id="users">
                                <img id="profile-image" src="" alt="Profile Image"
                                    style="height: 50px; width:50px;">
                            </div>
                            <div class="user-info">
                                <h6></h6>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- User List Modal End -->

    <!-- Script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('assets/js/bootstrap.poll.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <script>
       
        function showProfileImageForDateList(id) {
            var userData = <?php echo json_encode($dateList['data']); ?>;
            var modalBody = document.querySelector(".user-list-modal");

            // Clear any existing user entries in the modal
            modalBody.innerHTML = "";

            var matchingDateList = userData.find(function(dateList) {
                if (dateList.id === id) {
                    return true; // Return true to indicate a match
                }
                return false; // Return false for non-matching items
            });
            var tripDatePolls = userData.trip_date_polls;


            if (matchingDateList) {

                var tripDatePolls = matchingDateList.trip_date_polls;

                tripDatePolls.forEach(function(tripDatePolls) {

                    var userContainer = document.createElement("div");
                    userContainer.classList.add("user-list");

                    var userImage = document.createElement("div");
                    userImage.classList.add("user-img");

                    var img = document.createElement("img");


                   // img.src = tripDatePolls.guest_details.users_detail_profile_image.profile_image; // Set the image URL directly

                    img.alt = "Profile Image";
                    if(tripDatePolls.guest_details.users_detail_profile_image == null){
                        img.src = "https://lesgo.dashtechinc.com/uploads/images/admin.png";
                    }else{
                        img.src = tripDatePolls.guest_details.users_detail_profile_image.profile_image; // Set the image URL directly
                    }
                    img.onerror = function() {
                        // Remove the event listener to prevent infinite loops
                        img.onerror = null;

                        // Set the src attribute to the default image URL
                        img.src = "https://lesgo.dashtechinc.com/uploads/images/admin.png";
                    };


                    img.style.height = "50px"; // Set the height
                    img.style.width = "50px"; // Set the width
                    img.style.margin = "0px 10px 0px 0px";
                    img.style.border = "1px";
                    img.style.borderRadius = "50px";

                    var userInfo = document.createElement("div");
                    userInfo.classList.add("user-info");

                    var userInfo = document.createElement("div");
                    userInfo.classList.add("user-info");

                    var userName = document.createElement('h6');
                    userName.textContent = tripDatePolls.guest_details.first_name + ' ' + tripDatePolls
                        .guest_details.last_name;

                    var dateTIme = document.createElement('span');
                    dateTIme.textContent = tripDatePolls.created_at;
                    // Append elements to the userContainer
                    userContainer.appendChild(img);
                    userInfo.appendChild(userName);
                    userInfo.appendChild(dateTIme);
                    userContainer.appendChild(userInfo);

                    // Append the userContainer to the modal body
                    modalBody.appendChild(userContainer);
                });

                // Show the modal
                $('#userListModal').modal('show');
            } else {
                // Handle the case where no matching users are found
                console.error("No matching users found.");
            }

        }
    </script>
    <script>
        // Function to show the popup and set the profile image based on the ID
        function showProfileImageForCityList(id) {
            var userData = <?php echo json_encode($cityList['data']); ?>;
            var modalBody = document.querySelector(".user-list-modal");

            // Clear any existing user entries in the modal
            modalBody.innerHTML = "";

            var matchingDateList = userData.find(function(cityList) {
                if (cityList.id === id) {
                    return true; // Return true to indicate a match
                }
                return false; // Return false for non-matching items
            });
            var tripCityPolls = userData.trip_city_polls;


            if (matchingDateList) {

                var tripCityPolls = matchingDateList.trip_city_polls;

                tripCityPolls.forEach(function(tripCityPolls) {

                    var userContainer = document.createElement("div");
                    userContainer.classList.add("user-list");

                    var userImage = document.createElement("div");
                    userImage.classList.add("user-img");

                    var img = document.createElement("img");
                    if(tripCityPolls.guest_details.users_detail_profile_image == null){
                        img.src = "https://lesgo.dashtechinc.com/uploads/images/admin.png";
                    }else{
                        img.src = tripCityPolls.guest_details.users_detail_profile_image.profile_image; // Set the image URL directly
                    }
                    // console.log(img.src);
                    img.alt = "Profile Image";
                    img.onerror = function() {
                        // Remove the event listener to prevent infinite loops
                        img.onerror = null;

                        // Set the src attribute to the default image URL
                        img.src = "https://lesgo.dashtechinc.com/uploads/images/admin.png";
                    };
                    img.style.height = "50px"; // Set the height
                    img.style.width = "50px"; // Set the width
                    img.style.margin = "0px 10px 0px 0px";
                    img.style.border = "1px";
                    img.style.borderRadius = "50px";

                    var userInfo = document.createElement("div");
                    userInfo.classList.add("user-info");

                    var userInfo = document.createElement("div");
                    userInfo.classList.add("user-info");

                    var userName = document.createElement('h6');
                    userName.textContent = tripCityPolls.guest_details.first_name + ' ' + tripCityPolls
                        .guest_details.last_name;

                    var dateTIme = document.createElement('span');
                    dateTIme.textContent = tripCityPolls.created_at;
                    // Append elements to the userContainer
                    userContainer.appendChild(img);
                    userInfo.appendChild(userName);
                    userInfo.appendChild(dateTIme);
                    userContainer.appendChild(userInfo);

                    // Append the userContainer to the modal body
                    modalBody.appendChild(userContainer);
                });

                // Show the modal
                $('#userListModal').modal('show');
            } else {
                // Handle the case where no matching users are found
                console.error("No matching users found.");
            }

        }
    </script>
    
    <script>
        $(document).ready(function() {
            $('#submitButton').click(function() {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var currentUrl = window.location.href;

                // Split the URL by "/"
                var segments = currentUrl.split("/");

                // Extract the last two segments (43 and 176)
                var tripId = segments[segments.length - 2];
                var guestId = segments[segments.length - 1];
                // e.preventDefault(); // Prevent the form from submitting

                var selectedCityIds = [];
                var selectedDateIds = [];
                //var tripId =  {{ $tripData['data']['id'] }};
                //alert(tripId);
                // Iterate through the selected checkboxes
                $('.city-checkbox:checked').each(function() {
                    selectedCityIds.push($(this).data('id'));
                });
                $('.date-checkbox:checked').each(function() {
                    selectedDateIds.push($(this).data('id'));
                });
                //alert(selectedCityIds.length);
                // Check if at least one checkbox is selected
                if (selectedCityIds.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select atleast one location option',
                    })

                    return;
                }
                if (selectedDateIds.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select atleast one date option',
                    })

                    return;
                }
                //alert("Yes");
                // Prepare the data to be sent
                var data = {
                    city_ids: selectedCityIds,
                    date_ids: selectedDateIds,
                    tripId: tripId,
                    guestId: guestId

                };

                // Send the AJAX request
                $.ajax({
                    type: 'POST',
                    url: '{{ route('insertWebPoll') }}', // Use the correct route name
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        var isSuccess = response.success;
                        if (isSuccess) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Your work has been saved',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            location.reload();

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',

                            })
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // Log any errors
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#declined').click(function() {

                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var currentUrl = window.location.href;

                // Split the URL by "/"
                var segments = currentUrl.split("/");

                // Extract the last two segments (43 and 176)
                var tripId = segments[segments.length - 2];
                var guestId = segments[segments.length - 1];
                // e.preventDefault(); // Prevent the form from submitting


                var data = {
                    tripId: tripId,
                    guestId: guestId

                };

                // Send the AJAX request
                $.ajax({
                    type: 'POST',
                    url: '{{ route('invitationDeclined') }}', // Use the correct route name
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        var isSuccess = response.success;
                        if (isSuccess) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Your work has been saved',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            location.reload();

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!',

                            })
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // Log any errors
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('.date-checkbox').change(function() {
                var isChecked = $(this).prop('checked');
                var isDefault = $(this).attr('id') == 1;

                if (isDefault && isChecked) {


                    $('.date-checkbox').not(this).prop('checked', false);
                }


                if (!isDefault && isChecked) {
                    $('.date-checkbox[data-is-default="1"]').prop('checked', false);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.city-checkbox').change(function() {
                var isChecked = $(this).prop('checked');
                var isDefault = $(this).attr('id') == 1;

                if (isDefault && isChecked) {


                    $('.city-checkbox').not(this).prop('checked', false);
                }


                if (!isDefault && isChecked) {
                    $('.city-checkbox[data-is-default="1"]').prop('checked', false);
                }
            });
        });
    </script>



</body>

</html>
