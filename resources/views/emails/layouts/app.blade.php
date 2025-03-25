<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>LesGO</title>

	<!-- Responsive Metatag -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin:0; padding: 20px; font-family: 'Poppins', sans-serif; font-size: 16px; color: #000000; background-color: #ffffff;">
    <table style="width:100%; border-spacing:0; max-width: 700px; margin: 0 auto; background-color: #ffffff;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-align: center; padding: 20px; background-color: #2AB26E; line-height: 0;">
                <a href="javascript:;" target="_blank" style="line-height: 0; display: inline-block;">
                    <?php $imageUrl  = config('global.local_image_url');?>
                    <img src="<?php echo $imageUrl."logo.png"?>" alt="LesGO Logo" width="170">
                </a>
            </td>
        </tr>

        @yield('content')

      