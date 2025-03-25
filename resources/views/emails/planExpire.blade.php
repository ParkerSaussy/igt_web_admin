@extends('emails.layouts.app')

@section('content')
<?php $imageUrl  = config('global.local_image_url');?>
        <tr>
            <td>
                <table style="width:100%; border-spacing:0; padding: 20px; background-color: #fafafa;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="margin: 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <h2>Your Subscription Plan Expires Soon</h2>
                            <p>Dear <?php echo $dynamicData['firstName'];?> <?php echo $dynamicData['lastName'];?>,</p>
                            <p>We hope this email finds you well. We want to remind you that your subscription plan with [Your Company Name] is set to expire soon. We value your continued support, and we'd like to help you ensure uninterrupted service.</p>
                    
                            <h3>Subscription Details:</h3>
                            <ul>
                                <li><strong>Plan Expiration Date:</strong> <?php echo $dynamicData['expiryDate'];?></li>
                            </ul>
                    
                            <h3>Renew Your Subscription:</h3>
                            <p>To renew your subscription plan and continue enjoying our services, please follow these simple steps:</p>
                           
                    
                            <p><strong>Act Now:</strong> We recommend renewing your subscription plan at your earliest convenience to prevent any interruption in your access to our services. If your subscription expires, you may lose access to premium features.</p>
                    
                            <h3>Questions or Assistance:</h3>
                            <p>If you have any questions or need assistance with the renewal process, our support team is here to help. You can reach out to us at [Support Email Address] or [Support Phone Number].</p>
                    
                        </div>
                        
                            <p>Best regards,<br>
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