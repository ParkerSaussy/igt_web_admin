@extends('emails.layouts.app')

@section('content')
<?php $imageUrl  = config('global.local_image_url');?>
        <tr>
            <td>
                <table style="width:100%; border-spacing:0; padding: 20px; background-color: #fafafa;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="margin: 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <p>Hello <?php echo $dynamicData['firstName'];?>,</p>
                            {{-- <p>We hope this message finds you well. We wanted to inform you that the deadline for the event "<strong><?php //echo $dynamicData['tripName'];?></strong>" has passed.</p>
                            <p>The deadline was set for: <strong><?php// echo $dynamicData['tripDate'];?></strong></p>
                            <p>Unfortunately, we have not received the required deliverables or completed work by the specified deadline. We understand that unforeseen circumstances can arise, and we are here to assist if you require an extension or any other support to complete the task/project.</p>
                            <p>Please reach out to us as soon as possible to discuss the current status and any necessary adjustments to the timeline or scope of the event. We value your commitment and contribution to our team, and we want to ensure we can work together effectively to achieve our goals.</p>
                            <p>Thank you for your attention to this matter.</p> --}}
                            <p> We hope this message finds you well. Today is the deadline to respond to "<strong><?php echo $dynamicData['tripName'];?></strong>".</p>
                            <p> If you wish to attend, please respond right now. If the deadline has already passed, you can still try to respond, in case the host hasn't finalized the dates, and if they have, we recommend reaching out to the host directly. </p>
                        
                            <p>Thank you & best regards, <br>
                                ItsGoTime Team
                            </p>
                        </td>
                    </tr>
                    <tr>
                       
                    </tr>
                </table>
            </td>
        </tr>
    
      

        <tr>
            <td> <?php $imageUrl  = config('global.local_image_url');?>
                <table style="width:100%; border-spacing:0; padding: 20px; text-align: center; background: #D3DCE4;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="margin: 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <h3 style="margin: 0 0 10px 0; color: #131B4C; font-size: 18px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 600;">App Download</h3>
                            <p style="margin: 0 0 16px 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">Lorem Ipsum is simply dummy text of industry.</p>
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
                       
                        <td style="color: #ffffff; font-size: 14px; text-align: left;">© Copyright 2023, LesGO</td>
                
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