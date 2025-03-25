@extends('emails.layouts.app')

@section('content')
<?php $imageUrl  = config('global.local_image_url');?>
        <tr>
            <td>
                <table style="width:100%; border-spacing:0; padding: 20px; background-color: #fafafa;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="margin: 0; color:#131B4C; font-size: 16px; line-height: 24px; font-family: 'Poppins', sans-serif; font-weight: 400;">
                            <p>Hello <?php echo $dynamicData['firstName'];?> <?php echo $dynamicData['lastName'];?>,</p>
    
                            <p>Congratulations! You've successfully purchased a subscription plan with us. You now have access to a range of premium features and benefits.</p>
                            
                            <p><strong>Subscription Details:</strong></p>
                            <ul>
                                <li>Subscription Plan: <?php echo $dynamicData['planName'];?></li>
                                
                                <li>Expiration Date: <?php echo $dynamicData['planExpiryDate'];?></li>
                            </ul>
                            
                            <p>We hope you enjoy using our premium services. If you have any questions or need assistance, please feel free to contact our support team.</p>
                            
                            <p>Thank you for choosing us. We appreciate your business!</p>
                            
                          
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