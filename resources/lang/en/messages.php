<?php
return [
    //signup

    'signup_success' => '(after OTP verified) Your account has been successfully created. Welcome to our community!',
    'signup_fail' => 'Account registration failed. Please try again',
    'email_taken' => 'The provided email address is already registered with different type of sign in.',
    'mobile_exists' => 'The provided mobile number is already associated with an account. Please sign in or use a different number.',

    //signin
    'signin_success' => 'Welcome back! You have successfully signed in.',
    'signin_fail' => 'Sign in failed. Please check your email/mobile and password and try again.',
    'no_data' => 'No data provided for sign-in. Please enter your email/mobile and password.',
    'invalid_email_password' => 'Invalid email/mobile or password. Please try again.',
    'deactivated_account' => 'Your account has been deactivated. Please contact support for assistance.',
    'email/mobile_not_registerd' => 'The provided email/mobile is not registered. Please sign up or use a different email/mobile.',

    //reset password
    'password_change_success' => 'Your password has been successfully changed.',
    'password_change_fail' => 'Password change failed. Please try again.',

    //cms
    'cms_success' => 'Content has been successfully updated.',
    'no_data_found' => 'No data found.',

    //verify otp
    'otp_verified' => 'Thank you for registering with our platform. Your One-Time Password (OTP) verification is successful.',
    'otp_expired' => 'The One-Time Password (OTP) verification has expired.',
    'otp_not_match' => 'The entered OTP does not match. Please try again.',

    //send otp
    'sent_success_on_email' => 'OTP has been sent to your email for verification.',
    'sent_success_on_mobile' => 'OTP has been sent to your mobile number for verification.',
    'otp_limit_over' => 'You have reached the limit for requesting OTPs.',

    //change password
    'change_password_success' => 'Your password has been successfully changed.',
    'invalid_old_password' => 'The old password you entered is incorrect.',

    //editprofile
    
    'profile_success' => 'Your profile information has been successfully updated.',
    'profile_fail' => 'Profile update failed. Please try again.',

    //update mobile number
    
     
    'mobile_success' => 'Your mobile number has been successfully updated.',
    'fail_to_update_mobile' => 'Mobile number update failed. Please try again.',
    
    // Add more messages as needed
    'invitee_not_available' => 'Invitee not available',

    //Raj Dev
    //Trip Create
    'trip_created_successfully' => 'Trip created successfully.',
    'trip_updated_successfully' => 'Trip updated successfully.',
    'trip_final_successfully' => 'Trip finalized successfully.',
    'start_date_is_required' => 'Start date is required.',
    'start_date_must_be_date' => 'Start date must be date',
    'end_date_is_required' => 'End date is required.',
    'end_date_must_be_date' => 'End date must be date',
    'city_id_is_required' => 'City id is required.',
    'failed_to_create_trip' => 'Failed to create trip.',
    'trip_id_is_required' => 'Trip id is required.',
    'trip_id_must_be_an_integer' => 'Trip id must be an integer',
    'date_added_successfully' => 'Date addedd successfully.',
    'failed_to_add_dates' => 'Failed to add dates',
    'cities_added_successfully' => 'Cities addedd successfully.',
    'failed_to_add_cities' => 'Failed to add cities',
    'first_name_is_required' => 'First name is required.',
    'email_id_is_required' => 'Email id is required.',
    'invalid_email_id' => 'Invalid email id.',
    'phone_number_is_required' => 'Phone number is required.',
    'role_is_required' => 'Role is required.',
    'invalid_role' => 'Invalid role',
    'co_host_is_required' => 'Co host is required.',
    'invalid_co_host_status' => 'Invalid co-host status',
    'invite_status_required' => 'Invite status required.',
    'invalid_invite_status' => 'Invalid invite status',
    'no_guest_added_no_guest_failed' => ':successCount Guests added successfully. :failCount already added',
    'failed_to_add_guests' => 'Failed to add guests',
    'cities_found_successfully' => 'Cities found successfully.',
    'guest_id_required' => 'Guest id is required.',
    'co_host_role_changed_successfully' => 'Co-host role changed successfully.',
    'guest_not_available' => 'Guest not available',
    'failed_to_change_role' => 'Failed to change role.',
    'failed_to_update_data_no_access' => 'Failed to update data. No access',
    'invitee_role_changed_successfully' => 'Invitee role changed successfully.',
    'failed_to_chnage_role' => 'Failed to change role.',
    'cover_image_field_is_required' => 'Cover image field is required',
    'invalid_file' => 'Invalid file',
    'cover_image_uploaded_successfully' => 'Cover image uploaded successfully',
    'failed_to_upload_image' => 'Failed to upload image',
    'invitee_removed_successfully' => 'Invitee removed successfully.',
    'failed_to_remove_invitee' => 'Failed to remove invitee.',
    'failed_to_remove_invitee_no_access' => 'Failed to remove invitee. No access',
    'invitation_sent_successfully' => 'Invitation sent successfully.',
    'failed_to_send_invitation_no_guest_found' => 'Failed to send invitation no guest found.',
    'failed_to_send_invitation' => 'Failed to send invitation.',
    'failed_to_send_invitation_no_access' => 'Failed to send invitation. No access',
    'trip_type_is_required' => 'Trip type is required.',
    'invalid_request' => 'Invalid request',
    'trip_list_found' => 'Trip list found.',
    'no_trip_found' => 'No trip found.',
    'trip_date_id_is_required' => 'Trip date id is required.',
    'is_selected_is_required' => 'Is selected is required.',
    'invalid_selection' => 'Invalid selection.',
    'poll_removed_successfully' => 'Poll removed successfully.',
    'poll_added_successfully' => 'Poll added successfully.',
    'something_went_wrong' => 'Something went wrong.',
    'trip_city_id_is_required' => 'Trip city id is required.',
    'no_guest_found' => 'No guest found.',
    'guest_list_found' => 'Guest list found.',
    'cover_images_found_successfully' => 'Cover images found successfully.',
    'no_cover_images_found' => 'No cover images found.',
    'failed_to_get_cover_images' => 'Failed to get cover images.',
    'date_lists_found' => 'Date lists found',
    'no_dates_found' => 'No dates found.',
    'city_lists_found' => 'City lists found',
    'no_cities_found' => 'No cities found.',
    'trip_detail_found' => 'Trip detail found.',
    'no_trip_detail_found' => 'No trip detail found.',
];