<?php

use Illuminate\Support\Facades\Route;
use App\Mail\TestMail;

use FFMpeg;

Route::get('/test-ffmpeg', function () {
    // Ensure you have a video file to test
    $videoFilePath = storage_path('app/public/meeting.mp4');

    if (file_exists($videoFilePath)) {
        FFMpeg::fromDisk('public')
            ->open('meeting.mp4')
            ->export()
            ->toDisk('public')
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->save('converted-video.mp4');

        return 'Video conversion is successful!';
    } else {
        return 'Video file not found!';
    }
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/send-test-mail', function () {
    $to = "mbilal44559@gmail.com";
$subject = "HTML email";

$message = "
<html>
<head>
<title>HTML email</title>
</head>
<body>
<p>This email contains HTML Tags!</p>
<table>
<tr>
<th>Firstname</th>
<th>Lastname</th>
</tr>
<tr>
<td>John</td>
<td>Doe</td>
</tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <webmaster@example.com>' . "\r\n";

mail($to,$subject,$message,$headers);
die;
    Mail::to('rishavbeas@gmail.com')->send(new TestMail());
    return 'Test email sent!';
});

