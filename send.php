<?php
set_time_limit(820);
$to      = 'airdrop@ternio.io';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: airdrop@ternio.io' . "\r\n" .
    'Reply-To: airdrop@ternio.io' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>
