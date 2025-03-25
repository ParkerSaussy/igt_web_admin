@extends('emails.layouts.app')
@section('content')
<?php $imageUrl  = config('global.local_image_url');?>
<tr>
   <td>
      <table style="width:100%; border-spacing:0; padding: 20px; background-color: #fafafa;" cellpadding="0" cellspacing="0">
         <tr>
            <td style="margin: 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">
               <p>Hi <?php echo $dynamicData[0]['guest_name'];?>! Exciting news! You're invited to join <?php echo $dynamicData[0]['user']['first_name'];?> <?php echo $dynamicData[0]['user']['last_name'];?>  for  <?php echo $dynamicData[0]['trip_name'];?>! Ready to shape the experience?</p>
               
               <?php if( $dynamicData[0]['is_trip_finalised'] != 1){ ?>
                  <h2 style="padding: 0px;"><a href="<?php echo $dynamicData[0]['Url'];?>" style="text-decoration: none; color: #007bff;">Click the link to RSVP!</a></h2>
               <?php }?>

               <p><span class="highlight"><strong>Hosted By : </strong><?php echo Str::ucfirst($dynamicData[0]['user']['first_name']);?> <?php echo Str::ucfirst($dynamicData[0]['user']['last_name']);?><?php echo $dynamicData[0]['commaSeparatedNames'];?></span></p>
               <p><span class="highlight"><strong>Hosted Email : </strong> <?php echo $dynamicData[0]['user']['email'];?></span></p>
               <p><span class="highlight"><strong>Trip Name : </strong> <?php echo $dynamicData[0]['trip_name'];?></span></p>
               <p><span class="highlight"><strong>Description : </strong></span> <?php echo $dynamicData[0]['trip_description'];?></p><br>
               {{-- <p><span class="highlight"><strong>Itinerary Overview : </strong><?php //echo $dynamicData[0]['itinary_details'];?></span></p>
               <p><span class="highlight"><strong>Hosted By Mobile : </strong> <?php// echo $dynamicData[0]['user']['mobile_number'];?> </span></p> --}}
              <p>
               Group Chat, Manage Expenses, and Share Photos all on the It'sGoTime app! Click the RSVP link to download the It’sGoTime app never miss a detail!
              </p>
               
               <p>Thank you & best regards, <br>
                  It'sGoTime, Team
            </p>
               
              
              
            </td>
         </tr>

      </table>
   </td>
</tr>
<tr>
   <td>
      <?php $imageUrl  = config('global.local_image_url');?>
      <table style="width:100%; border-spacing:0; padding: 20px; text-align: center; background: #D3DCE4;" cellpadding="0" cellspacing="0">
         <tr>
            <td style="margin: 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">
               <h3 style="margin: 0 0 10px 0; color: #131B4C; font-size: 18px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 600;">App Download</h3>
               <p style="margin: 0 0 16px 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">To download and start benefiting from our app's features.</p>
            </td>
         </tr>
         <tr>
            <td>
               <a href="javascript:;" target="_blank" style="line-height: 0; margin: 0 5px; display: inline-block;"><img src="<?php echo $imageUrl."/app-store-logo.png"?>"
                  alt="App Store" width="120"></a>
               <a href="javascript:;" target="_blank" style="line-height: 0; margin: 0 5px; display: inline-block;"><img src="<?php echo $imageUrl."/google-play-logo.png"?>"
                  alt="Google Play Store" width="120"></a>
            </td>
         </tr>
      </table>
   </td>
</tr>
<tr>
   <td>
      <table style="width:100%; border-spacing:0; padding: 10px 20px; background: #2AB26E;" cellpadding="0" cellspacing="0">
         <tr>
            <td style="color: #ffffff; font-size: 14px; text-align: left;">© Copyright 2023, ItsGoTime</td>
            <td style="text-align: right; line-height: 0;">
               <a href="#" target="_blank" style="line-height: 0; margin: 0 3px; display: inline-block;"><img src="<?php echo $imageUrl."facebook.png"?>" alt="Facebook" width="22"></a>
               <a href="javascript:;" target="_blank" style="line-height: 0; margin: 0 3px; display: inline-block;"><img src="<?php echo $imageUrl."instagram.png"?>" alt="Instagram" width="22"></a>
               <a href="javascript:;" target="_blank" style="line-height: 0; margin: 0 3px; display: inline-block;"><img src="<?php echo $imageUrl."twitter.png"?>" alt="Twitter" width="22"></a>
            </td>
         </tr>
      </table>
   </td>
</tr>
</table>
</body>
</html>
@endsection