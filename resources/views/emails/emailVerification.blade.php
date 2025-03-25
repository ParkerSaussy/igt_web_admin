@extends('emails.layouts.app')

@section('content')


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Track Your Game</title>

    <!-- Responsive Metatag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
</head>
<body style="margin:0; padding: 20px; font-family: 'Roboto', sans-serif; font-size: 16px; color: #000000; background-color: #f5f5f5;">
    <?php $imageUrl = \Config::get('constants.imageurl');
    $image = $imageUrl."logo_colored.svg"; ?>
    <table style="width:100%; border-spacing:0; max-width: 700px; margin: 0 auto; background-color: #ffffff; padding: 20px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-align: center; padding: 20px 0;">
                <img src="<?php echo $image ;?>" alt="OWN Logo" width="190">
            </td>
        </tr>
        <tr>
            <td style="font-family: 'Roboto', sans-serif; font-size: 30px; font-weight: 700; line-height: 40px; color: #000000; text-align: center; margin-bottom: 20px; display: block;">
                Verify your e-mail to finish signing up process
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
               <a href="<?php echo $link; ?>" style="font-family: 'Roboto', sans-serif; font-size: 18px; color: #ffffff; text-decoration: none; background-color: #518273; padding:16px 50px; display:inline-block; margin-bottom: 20px;" target="_blank">Verify</a> 
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
               
            </td>
        </tr>
    </table>
</body>
</html>
@endsection
