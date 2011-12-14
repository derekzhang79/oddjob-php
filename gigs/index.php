<?php ob_start();

require "./_consts/db_consts.php";
require "./_consts/fb_consts.php";

require './_inc/fb-sdk/facebook.php';
require './_wrk/mailer.php';

require "./_inc/db_open.php";


// init job id
$job_id = 0;

//$auth_url = "https://graph.facebook.com/oauth/authorize?client_id=". $APP_ID ."&redirect_uri=http://dev.gullinbursti.cc/projs/oddjob/gigs&scope=email,read_stream,publish_stream,offline_access,user_relationships,user_birthday,user_work_history,user_education_history,user_location";
//header("Location: ". $auth_url);
	
// fb app init
$facebook = new Facebook(array(
  'appId'  => $APP_ID,
  'secret' => $APP_SECRET,
));

// user data
$user = $facebook->getUser(); 

if ($user) {
	try {
		$user_profile = $facebook->api('/me');
		$fb_id = $user_profile['id'];
		$fb_name = $user_profile['name'];
		$fb_location = $user_profile['location']['name'];
		$fb_email = $user_profile['email'];
		//foreach ($user_profile as $key => $val)
		//	echo ("[". $key ."]". $val ."<br />");
		
		$friend_arr = array();
		foreach ($facebook->api('/me/friends') as $data) {
			foreach ($data as $key=>$item_arr)
				$friend_arr[$item_arr['id']] = $item_arr['name'];
		}
	    array_pop($friend_arr);
	
		$like_arr = array();
	    foreach ($facebook->api('/me/likes') as $data) {
			foreach ($data as $key=>$item_arr)
				$like_arr[$item_arr['id']] = $item_arr['name'];
		}
		array_pop($like_arr);
		
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}
}

// login / logout url will be needed depending on current user state.
if ($user) {
	$logoutUrl = $facebook->getLogoutUrl();
	//echo($logoutUrl);

} else { 
	$loginUrl = $facebook->getLoginUrl();
	$auth_url = "https://graph.facebook.com/oauth/authorize?client_id=". $APP_ID ."&redirect_uri=". implode("/", explode('/', $_SERVER['SCRIPT_URI'], -1)) ."&scope=email,read_stream,publish_stream,offline_access,user_relationships,user_birthday,user_work_history,user_education_history,user_location";
	header("Location: ". $auth_url);
}  

$query = 'SELECT * FROM `tblJobs` WHERE `isActive` ="Y";';
$job_arr = mysql_query($query);
			
// 


//foreach ($friend_arr as $key => $val)
//	echo ("[". $key ."]". $val ."<br />");

//sendMail($fb_email, $fb_name, "Odd Job Redeem", "<html><head><title>Birthday Reminders for August</title></head><body><p>Here are the birthdays upcoming in August!</p><table><tr><th>Person</th><th>Day</th><th>Month</th><th>Year</th></tr><tr><td>Joe</td><td>3rd</td><td>August</td><td>1970</td></tr><tr><td>Sally</td><td>17th</td><td>August</td><td>1973</td></tr></table></body></html>");

function calcRating($j_id) {
	$query = 'SELECT `score` FROM `tblJobRatings` WHERE `job_id` ="'. $j_id .'";';
	$rating_res = mysql_query($query);

	// has results
	$rating_tot = 0;
	$rating_ave = 0;

	if (mysql_num_rows($rating_res)) {
		$rating_tot = mysql_num_rows($rating_res);
		while ($rating_row = mysql_fetch_array($rating_res, MYSQL_BOTH))
			$rating_ave += $rating_row[0];

		$rating_ave /= $rating_tot;
		$rating_ave = round($rating_ave);
	}
	
	return ($rating_ave);	
}


function writeStars($r_amt) {
	for ($i=1; $i<=5; $i++) {
		
		if ($r_amt < $i)
			$img_src = "star_empty.png";
		
		else
			$img_src = "star_filled.png";
		
		echo ("<img id=\"imgStar_". $job_id ."_". $i ."\" name=\"imgStar_". $job_id ."_". $i ."\" src=\"./img/". $img_src ."\" width=\"16\" height=\"16\" />");	
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<script type="text/javascript">
			var _kmq = _kmq || [];
		
			function _kms(u) {
				setTimeout(function() {
	      			var s = document.createElement('script'); 
					var f = document.getElementsByTagName('script')[0]; 
					s.type = 'text/javascript'; 
					s.async = true;
	      			s.src = u; 
					f.parentNode.insertBefore(s, f);
	    		}, 1);
	  		}

	  		_kms('//i.kissmetrics.com/i.js');
			_kms('//doug1izaerwt3.cloudfront.net/8afc90ad40b3e6b403aaec5e35d8b1343a9822da.1.js');
			_kmq.push(['identify', '<?php echo($fb_id); ?>']);
			_kmq.push(['record', 'View Job List']);
		</script>
		
	    <title>:: Odd Job ::</title>
		<link href="./css/screen.css" rel="stylesheet" type="text/css" media="screen">
		
		<script type="text/javascript">
		<!--
			function jobWatch(jID) {
				location.href = "./view.php?jID="+jID;
			}
		
			function jobReview(jID) {
				location.href = "./review.php?jID="+jID;
			}
			
			function jobChallenge(jID) {
				location.href = "./challenge.php?jID="+jID;
			}
		-->
		</script>
	</head>
	
	<body><div id="divMainWrapper">
		<?php include './_inc/header.php'; ?>
		<div align="center">			
			<?php include './_inc/notifications.php'; ?>
			<div id="divJobList">
				<?php while ($job_row = mysql_fetch_array($job_arr, MYSQL_BOTH)) {
					$score = 0;
					$job_id = $job_row['id'];  
					$job_name = $job_row['title'];
					$job_info = $job_row['info'];
					$slots_tot = $job_row['slots'];
					$job_long = $job_row['longitude'];
					$job_lat = $job_row['latitude'];
					$merchant_id = $job_row['merchant_id'];
	
					// retrieve job type
			    	$query = 'SELECT `name` FROM `tblJobTypes` WHERE `id` ='. $job_row['type_id'] .';';
					$type_row = mysql_fetch_row(mysql_query($query));
	
					if ($type_row)
						$type_name = $type_row[0];							
							
					// retrieve app info						
					$query = 'SELECT `name`, `itunes_id`, `youtube_id` FROM `tblApps` WHERE `id` ='. $job_row['app_id'] .';';
					$app_row = mysql_fetch_row(mysql_query($query));
	
					if ($app_row) {
						$app_name = $app_row[0];
						$app_id = $app_row[1];
						$youtube_id = $app_row[2];
						$appStore_json = json_decode(file_get_contents("http://itunes.apple.com/lookup?id=". $app_id .""));
					}
					
					// retrieve merchant
					$query = 'SELECT `name`, `address`, `city`, `state`, `zip`, `phone` FROM `tblMerchants` WHERE `id` ='. $job_row['merchant_id'] .';';
					$merchant_row = mysql_fetch_row(mysql_query($query));
	
					if ($merchant_row) {
						$merchant_name = $merchant_row[0];
						$merchant_addr = $merchant_row[1];
						$merchant_city = $merchant_row[2];
						$merchant_state = $merchant_row[3];
						$merchant_zip = $merchant_row[4];
						$merchant_phone = $merchant_row[5];
						
						$query = 'SELECT `tblImages`.`url` FROM `tblImages` INNER JOIN `tblMerchantsImages` ON `tblImages`.`id` = `tblMerchantsImages`.`image_id` WHERE `tblMerchantsImages`.`merchant_id` = "'. $merchant_id .'" AND `tblImages`.`type_id` = "8";';
						$img_row = mysql_fetch_row(mysql_query($query));
	
						if ($img_row)
			    			$merchant_img = $img_row[0];
					}
                    
					
   	
					// retrieve images
					$query = 'SELECT `tblImages`.`id`, `tblImages`.`url` FROM `tblImages` INNER JOIN `tblJobsImages` ON `tblImages`.`id` = `tblJobsImages`.`image_id` WHERE `tblJobsImages`.`job_id` = "'. $job_id .'" AND `tblImages`.`type_id` = "4";';
					$img_row = mysql_fetch_row(mysql_query($query));
	
					if ($img_row)
			    		$img_url = $img_row[1];

       
					$query = 'SELECT `tblLikes`.`name` FROM `tblLikes` INNER JOIN `tblJobsLikes` ON `tblLikes`.`id` = `tblJobsLikes`.`like_id` WHERE `tblJobsLikes`.`job_id` ="'. $job_id .'";';
					$likes_res = mysql_query($query);
		
					// has results
					$like_tot = 0;
					$like_score = 0;
	
					if (mysql_num_rows($likes_res)) {
						$like_tot = mysql_num_rows($likes_res);
						while ($like_row = mysql_fetch_array($likes_res, MYSQL_BOTH))
							$like_score++;
					}
					
					?>
					<table cellpadding="0" cellspacing="0" border="0">					
						<tr><td colspan="2"><?php include './_inc/title.php'; ?></td></tr>
						<tr><td valign="top">
					<?php switch ($job_row['type_id']) {
						
						// watch
						case "9": ?>
							<a href="./view.php?jID=<?php echo ($job_id); ?>"><img src="http://img.youtube.com/vi/<?php echo ($youtube_id); ?>/0.jpg" width="380" height="280" title="<?php echo ($app_name); ?>" alt="<?php echo ($app_name); ?>" /></a>
							<?php break;
						
						// recommend   						
						case "10": 						
							$rating_ave = calcRating($job_id);
							$screenshot_arr = $appStore_json->results[0]->screenshotUrls; ?>
							<div id="divReview">
								<a href="./recommend.php?jID=<?php echo ($job_id); ?>"><img src="<?php echo($screenshot_arr[0]); ?>" width="380" height="280" title="<?php echo($app_name); ?>" alt="<?php echo($app_name); ?>" /></a>
								<!-- <div id="divRatings"><?php writeStars($rating_ave); ?></div> -->
								<!-- <div id="divRatingScore">Average Score: <?php echo ($rating_ave); ?></div> -->
							</div>							
						    <?php break;
						
						// challenge	
						case "11": 
							$screenshot_arr = $appStore_json->results[0]->screenshotUrls; ?>
						    <div id="divReview">
								<a href="./challenge.php?jID=<?php echo ($job_id); ?>"><img src="<?php echo($screenshot_arr[0]); ?>" width="380" height="280" title="<?php echo($app_name); ?>" alt="<?php echo($app_name); ?>" /></a>
							</div>
							<?php break;
					} ?>
						<!-- <div><a href="http://itunes.apple.com/us/app/id<?php echo($app_id); ?>?mt=8" target="_blank"><img src="./img/appStore.png" width="129" height="43" title="View <?php echo ($app_name); ?> on the iTunes Store" alt="View <?php echo ($app_name); ?> on the iTunes Store" /></a></div> -->
						</td><td valign="top"><?php include './_inc/merchant.php'; ?></td></tr>
					
						<tr><td><div id="divJobStats">
							<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
								<td width="100" class="tdJobStats"><img src="#" width="16" height="16" alt="" title="" /><?php echo (rand(5, 200)); ?> Likes</td>
								<td width="100" class="tdJobStats"><img src="#" width="16" height="16" alt="" title="" /><?php echo (rand(5, 50)); ?> Comments</td>
								<td width="180" align="right"><input type="button" id="btnTakeJob_<?php echo($job_id); ?>" name="btnTakeJob_<?php echo($job_id); ?>" value="Take Job" onclick="takeJob(<?php echo($$job_row['type_id']); ?>, <?php echo($job_id); ?>)" /><input type="button" id="btnShare_<?php echo($job_id); ?>" name="btnShare_<?php echo($job_id); ?>" value="Share Job" onclick="shareJob(<?php echo($job_id); ?>);" /></td>
							</tr></table>	
						</div></td><td /></tr>
					</table>

				<?php } ?>
			</div>
		</div>
	</div></body>
</html>

<?php 

require "./_inc/db_close.php";
ob_flush(); 
?>