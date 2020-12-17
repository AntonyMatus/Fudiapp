<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Twilio\Rest\Client;
use App\Admin;
use App\Language;
use Twilio;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	// Usuarios
    function sendPush($title,$description,$uid = 0,$img = null)
	{
		$content = ["en" => $description];
		$head 	 = ["en" => $title];

		$daTags = [];

		if($uid > 0)
		{
			$daTags = ["field" => "tag", "key" => "user_id", "relation" => "=", "value" => $uid];
		}
		else
		{
			$daTags = ["field" => "tag", "key" => "user_id", "relation" => "!=", "value" => 'NAN'];
		}

		// 'include_player_ids' => array($PlayerId),
		$fields = array(
		'app_id' => "5884d744-8ce5-4953-9922-6d5bc1a6ffd5",
		'included_segments' => array('All'),
		'filters' => [$daTags],
		'data' => array("foo" => "bar"),
		'contents' => $content,
		'headings' => $head,
		'big_picture' => $img
		);


		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Authorization: Basic ZjdjNmU0NTMtNWRmYi00MGExLWFhMWYtNzlhNmFmYmExNTNl'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

	    return $response;
	}

	// Repartidores
	function sendPushD($title,$description, $uid = 0)
	{
		$content = ["en" => $description];
		$head 	 = ["en" => $title];

		$daTags = [];

		if($uid > 0)
		{
			$daTags = ["field" => "tag", "key" => "dboy_id", "relation" => "=", "value" => $uid];
		}
		else
		{
			$daTags = ["field" => "tag", "key" => "dboy_id", "relation" => "!=", "value" => 'NAN'];
		}

		$fields = array(
		'app_id' => "0974fd9d-d6b2-4eca-a57e-7ec2d7d14343",
		'included_segments' => array('All'),
		'filters' => [$daTags],
		'data' => array("foo" => "bar"),
		'contents' => $content,
		'headings' => $head,
		);


		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Authorization: Basic MzA2NDdiMDEtMGZjZS00NGYzLTg0NjItZTUzNWJiM2Q4MGY1'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

	    return $response;
	}

	function sendPushEx($title,$description)
	{
		$content = ["en" => $description];
		$head 	 = ["en" => $title];



		$fields = array(
		'app_id' => "0974fd9d-d6b2-4eca-a57e-7ec2d7d14343",
		'included_segments' => array('All'),
		// 'filters' => [$daTags],
		'data' => array("foo" => "bar"),
		'contents' => $content,
		'headings' => $head,
		);


		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Authorization: Basic MzA2NDdiMDEtMGZjZS00NGYzLTg0NjItZTUzNWJiM2Q4MGY1'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

	    return $response;
	}

	// Comercios
	function sendPushS($title,$description,$uid = 0)
	{
		$content = ["en" => $description];
		$head 	 = ["en" => $title];

		$daTags = [];

		if($uid > 0)
		{
			$daTags = ["field" => "tag", "key" => "store_id", "relation" => "=", "value" => $uid];
		}
		else
		{
			$daTags = ["field" => "tag", "key" => "store_id", "relation" => "!=", "value" => 'NAN'];
		}

		$fields = array(
		'app_id' => "01ae41c8-455b-4756-a116-7fb85f142157",
		'included_segments' => array('All'),
		'filters' => [$daTags],
		'data' => array("foo" => "bar"),
		'contents' => $content,
		'headings' => $head,
		);


		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Authorization: Basic OWNlNGE4ZjYtOTBmZC00ZGY2LWI1ZTYtYjkyM2QzMWY4Mzkz'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

	    return $response;
	}

	// Comercios Web
	public function NewPushSend($title,$description,$uid)
	{

		$content = ["en" => $description];
		$head 	 = ["en" => $title];

		$daTags = ["field" => "tag", "key" => "store_id", "relation" => "=", "value" => $uid];

		$fields = array(
			'app_id' => "3bcee1aa-2706-4c9f-9345-8e8b01a72614",
			'included_segments' => array('All'),
			'filters' => [$daTags],
			'data' => array("foo" => "bar"),
			'contents' => $content,
			'headings' => $head,
		);

		$fields = json_encode($fields);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Authorization: Basic ZjFkOTQxM2UtYTM1OC00MTgzLTk4YTAtZmFmZThiYTNlMGE2'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

	    return $response;
	}




    public function currency()
    {
    	$admin = Admin::find(1);

    	if($admin->currency)
    	{
    		return $admin->currency;
    	}
    	else
    	{
    		return "Rs.";
    	}
    }

    public static function sendSms($phone,$hash)
	{
		$sid = 'ACdf8b57f206c8945767af31e320a884e6';
		$token = '3dd862a96d5c4765b23176dcb7e081dc';
		$client = new Client($sid, $token);

		$otp = rand(11111,99999);

		$send = $client->messages->create(
			// the number you'd like to send the message to
			"+521".$phone,
			[
				// A Twilio phone number you purchased at twilio.com/console
				'from' =>  '+12056512299',
				// the body of the text message you'd like to send
				'body' => '<#> Tu codigo Eatse App: '.$otp.' '.$hash
			]
		);

		return $otp;
	}

	public function getLang()
	{
		$res = new Language;

		return $res->getAll();
	}
}
