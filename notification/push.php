<?php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
// use Minishlink\WebPush\VAPID;

include('../function_query.php');
require 'web-push/vendor/autoload.php';

// var_dump(VAPID::createVapidKeys());
// die;


$publicKey = "BEpBvudaQpJ-P3amYyhEm9AFMW1D4XTtuQPmDltnMgzX9JtVIPiCtvd0VFZPuqEZf5CS0Y2_dqgbm-n2nx3pkII";
$privateKey = "L6CgfXKAyym9LE7Z3K2A2NCNprGLtor-FzxGq-LtBJs";

$fetch_mess = SETUP::SETUP_MESS_BOX($_GET['id']);

$message = json_encode([
    'title' => $fetch_mess['head_line'],
    'body' => $fetch_mess['message'],
    'icon' => 'https://products.mycreativecode.com/rc_center/img/logo.png',
    'badge' => 'https://products.mycreativecode.com/rc_center/img/logo.png',
    'extraData' => ''
]);




$query2 = DB::conn()->prepare("SELECT * FROM `webpush_member`  ");
$query2->execute();
$fetch_list = $query2->fetchAll(PDO::FETCH_ASSOC);
foreach($fetch_list AS $fetch) {
$info_em = SETUP::ADMIN_SETUP($fetch['member_id']);

if($fetch_mess['designation_id'] == $info_em['designation_id'] ){

    $auth = [
        'VAPID' => [
            'subject' => 'https://products.mycreativecode.com/rc_center/', // can be a mailto: or your website address
            'publicKey' => $publicKey, // (recommended) uncompressed public key P-256 encoded in Base64-URL
            'privateKey' => $privateKey, // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
        ],
    ];
    $webPush = new WebPush($auth);

   
        $subscription = Subscription::create([
                "endpoint" => $fetch['endpoint'],
                "keys" => [
                    'p256dh' => $fetch['p256dh'],
                    'auth' => $fetch['authKey']
                ]
            ]);
        $webPush->queueNotification($subscription, $message);
    
 foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();

 if ($report->isSuccess()) {
    echo "Message sent successfully for {$endpoint}.<br>";
   } else {
  echo "Message failed to sent for {$endpoint}: {$report->getReason()}.<br>";
  }
 }

}

}
