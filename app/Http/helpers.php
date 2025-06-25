<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function camel_case($str)
{
    return \Illuminate\Support\Str::camel($str);
}

function generateOtp()
{
    return rand(1000, 9999);
}

function alertSuccess($message): array
{
    return [
        'alert' => [
            'status' => 'success',
            'message' => $message
        ]
    ];
}

function alertDanger($message): array
{
    return [
        'alert' => [
            'status' => 'danger',
            'message' => $message
        ]
    ];
}


function yearFilter()
{
    $years = [];

    for ($i = date('Y'); $i >= (date('Y') - 20); $i--) {
        $years[$i] = $i;
    }

    return collect($years)->prepend('Select Year', '');
}

function dwmFilter()
{
    $dwm = [];

    /*for ($i = date('Y'); $i >= (date('Y') - 20); $i--) {
        $years[$i] = $i;
    }*/
    $dwm = ['Daily' => 'Daily', 'Weekly' => 'Weekly', 'Monthly' => 'Monthly'];
    /* $dwm['Weekly '] = ['Weekly '];
     $dwm['Monthly'] = ['Monthly'];*/

    return collect($dwm)->prepend('Select', '');
}


function sendCustomEmail($toEmail, $subject, $bodyContent, $attachmentPath = null)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtpout.secureserver.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@sevenelevensl.com';
            $mail->Password = 'Sigma@2024';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('info@sevenelevensl.com', 'Mailer');
            $mail->addAddress($toEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $bodyContent;
            if ($attachmentPath) {
                $mail->addAttachment($attachmentPath);
            }
            // Send email
            $mail->send();

            return [
                'success' => true,
                'message' => 'Email sent successfully!'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send email.',
                'error' => $mail->ErrorInfo
            ];
        }
    }