<?php ob_start();

require "./_consts/db_consts.php";
require "./_consts/fb_consts.php";

require './_inc/fb-sdk/facebook.php';
require './_wrk/mailer.php';
require './_wrk/curl.php';

require "./_inc/db_open.php";


// init job id
$job_id = 0;


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
		
		//foreach ($user_profile['location'] as $key => $val)
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

} else {
	$loginUrl = $facebook->getLoginUrl();
	header("Location: ". $loginUrl);
}  


// check for querystr
if (isset($_GET['jID'])) {
	$job_id = $_GET['jID'];
	
	$query ='SELECT `device_id` FROM `tblUsers` WHERE `fb_id` = "'. $fb_id .'"';
	$user_row = mysql_fetch_row(mysql_query($query));
	$device_id = $user_row[0];
	
	// retrieve job info
	$query = 'SELECT `title`, `info`, `slots`, `object_id`, `longitude`, `latitude`, `app_id`, `type_id`, `merchant_id` FROM `tblJobs` WHERE `id` ='. $job_id .';';
	$job_row = mysql_fetch_row(mysql_query($query));
	
	// has result
	if ($job_row) {
		$job_name = $job_row[0];
		$job_info = $job_row[1];
		$slots_tot = $job_row[2];
		$job_long = $job_row[4];
		$job_lat = $job_row[5];
		$merchant_id = $job_row[8];
		
		if ($job_row[7] != "9")
			header('Location: index.php');
		
		// retrieve job type
    	$query = 'SELECT `name` FROM `tblJobTypes` WHERE `id` ='. $job_row[7] .';';
		$type_row = mysql_fetch_row(mysql_query($query));
		
		if ($type_row)
			$type_name = $type_row[0];							
								
		// retrieve app info						
		$query = 'SELECT `name`, `itunes_id`, `youtube_id` FROM `tblApps` WHERE `id` ='. $job_row[6] .';';
		$app_row = mysql_fetch_row(mysql_query($query));
		
		if ($app_row) {
			$app_name = $app_row[0];
			$app_id = $app_row[1];
			$youtube_id = $app_row[2];
			$appStore_json = json_decode(file_get_contents("http://itunes.apple.com/lookup?id=". $app_id .""));
			$app_rate = round($appStore_json->results[0]->averageUserRating);
		}
    	
		// retrieve images
		$query = 'SELECT `tblImages`.`id`, `tblImages`.`url` FROM `tblImages` INNER JOIN `tblJobsImages` ON `tblImages`.`id` = `tblJobsImages`.`image_id` WHERE `tblJobsImages`.`job_id` = "'. $job_id .'" AND type_id = "4";';
		$img_row = mysql_fetch_row(mysql_query($query));
		
		if ($img_row)
    		$img_url = $img_row[1];

        
		$query = 'SELECT `name` FROM `tblObjects` WHERE `id` ='. $job_row[3] .';';
		$obj_row = mysql_fetch_row(mysql_query($query));
		$obj_name = $obj_row[0]; 
        

		 // retrieve merchant
		$query = 'SELECT `name`, `address`, `city`, `state`, `zip`, `phone` FROM `tblMerchants` WHERE `id` ='. $merchant_id .';';
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
		//echo ($like_tot);
	}
}


function actionJobPost($fb, $j_id, $action, $object) {
	
	//echo ("j_id:[". $j_id ."] action:[". $action ."] object:[". $object ."]");
	$ret_obj = $fb->api('/me/'. 'oddjobb' .':'. $action, 'post', array(
		$object => implode("/", explode('/', $_SERVER['SCRIPT_URI'], -1)) .'/opengraph.php?jID='. $j_id,));
		
	//echo ("post_id:[".$ret_obj['id'] ."]");
}

//foreach ($friend_arr as $key => $val)
//	echo ("[". $key ."]". $val ."<br />");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# oddjobb: http://ogp.me/ns/fb/oddjobb#">
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
			_kmq.push(['record', 'View Watch']);
		</script>
		
		<meta property="fb:app_id"      content="139514356142393"> 
	    <meta property="og:type"        content="oddjobb:<?php echo($obj_name)?>"> 
	    <meta property="og:url"         content="<?php echo($_SERVER['SCRIPT_URI'] ."?". $_SERVER['QUERY_STRING']); ?>">
	    <meta property="og:title"       content="<?php echo($job_name)?>"> 
	    <meta property="og:description" content="<?php echo($job_info)?>"> 
	    <meta property="og:image"       content="<?php echo($appStore_json->results[0]->artworkUrl60)?>">
	    <meta property="og:locale"      content="en_us">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta http-equiv="Content-language" value="en" />
		
	    <title>Odd Job :: <?php echo($type_name ." ". $app_name)?></title>
		<link href="./css/screen.css" rel="stylesheet" type="text/css" media="screen">
		
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>
	    
		<script type="text/javascript">
		   /* $(document).ready(function() {
				$("#frmWatch").validate( {
					debug: false,
					rules: {
						hidJobID: "required",
						hidFBID: "required"
					},
					messages: {
						hidJobID: "NO JOB!",
						hidFBID: "NO FACEBOOK1",
					},
					submitHandler: function(form) {
						// do other stuff for a valid form
						$.post('./view_submit.php', $("#frmWatch").serialize(), function(data) {
							$('#divJobComplete').html(data);
						});
					}
				});
			}); 
			*/    
		</script>    
	
		<script type="text/javascript">
		<!--
		-->
		</script> 
	</head>
	
	<body><div id="divMainWrapper">
		<?php include './_inc/header.php'; ?>
		<div align="center">			
			<?php include './_inc/notifications.php'; ?>
			<table cellpadding="0" cellspacing="0" border="0">					
			<tr><td colspan="2"><?php include './_inc/title.php'; ?></td></tr>
			<tr><td colspan="2">
			<?php if (isset($_GET['a'])) { ?>
			    <div id="divJobComplete">
					<div>THANX FOR WATCHING!!!</div>
					<div>You have completed Odd Job #21! Please locate your mobile device to claim your reward.</div>
					<div align="center">sending code...</div>
					<div align="center"><input type="button" id="btnSendApp" name="btnSendApp" value="Send App" /></div>
				    <div><a href="#">Replay Video</a></div>
					<div>Did you like this trailer?</div>
					<div>Share this trailer with your friends
					<div><?php
						$friendID_arr = array_rand($friend_arr, 3);
						for ($i=0; $i<3; $i++)
							echo ("<a href=\"http://facebook.com/profile.php?id=". $friendID_arr[$i] ."\" target=\"_blank\"><img id=\"imgFBAvatar\" src=\"http://graph.facebook.com/". $friendID_arr[$i] ."/picture\" width=\"48\" height=\"48\" border=\"0\" title=\"". $friend_arr[$friendID_arr[$i]] ."\" alt=\"". $friend_arr[$friendID_arr[$i]] ."\" /></a>");	
					?><input type="button" value="Send Friends Trailer"></div>
				</div>
				<div style="display:none;"><?php sendPush($device_id, "Install ". $app_name ." for your redemption code"); ?></div>
			<? } else { ?>
				<div id="player"></div>
				<script>
					var tag = document.createElement('script');
						tag.src = "http://www.youtube.com/player_api";
     
					var firstScriptTag = document.getElementsByTagName('script')[0];
						firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

					var player;
					function onYouTubePlayerAPIReady() {
						player = new YT.Player('player', {
							width: '480',
							height: '320',
							videoId: '<?php echo ($youtube_id)?>', 
							playerVars: { 
								'autoplay': 0, 
								'controls': 1 
							}, events: {
								'onReady': onPlayerReady,
								'onStateChange': onPlayerStateChange
							}
						});
					}

					function onPlayerReady(event) {
						event.target.playVideo();
					}

					var isPaused = false;
					var isSubmitted = false;
					function onPlayerStateChange(event) {
					
						switch (event.data) {
							case YT.PlayerState.PLAYING:
								if (!isPaused) {
									isPaused = true;
									setTimeout(stopVideo, 100);
								}
								//if (!isSubmitted) {
								//	isSubmitted = true;
								//	setTimeout(onJobComplete, 10000);
								//}
								
								break;
					
							case YT.PlayerState.ENDED:
								frmWatch.submit();
								break;
						}
					}
				
					function stopVideo() {
						player.stopVideo();
					}
					
					function onJobComplete () {
						//document.frmWatch.submit();
					}
				</script>  
				<form id="frmWatch" name ="frmWatch" method="post" action="./view_submit.php">
					<input type="hidden" id="hidJobID" name="hidJobID" value="<?php echo ($job_id) ?>" />
					<input type="hidden" id="hidFBID" name="hidFBID" value="<?php echo ($fb_id) ?>" />
				</form>
			<?php } ?>
			</td></tr>
			
			<tr><td colspan="2">
				<div id="divJobStats">
					<table cellpadding="0" cellspacing="0" border="0"><tr>
						<td width="100" class="tdJobStats"><img src="#" width="16" height="16" alt="" title="" /> <?php echo (rand(5, 200)); ?> Likes</td>
						<td width="100" class="tdJobStats"><img src="#" width="16" height="16" alt="" title="" /> <?php echo (rand(5, 50)); ?> Comments</td>
						<td width="100" class="tdJobStats"><input type="button" id="btnShare" name="btnShare" value="Share This App" onclick="share();" /></td>
						<td width="180" align="right"><a href="http://itunes.apple.com/us/app/id<?php echo($app_id); ?>?mt=8" target="_blank"><img src="./img/appStore.png" width="129" height="43" title="View <?php echo ($app_name); ?> on the iTunes Store" alt="View <?php echo ($app_name); ?> on the iTunes Store" /></a></td>
					</tr></table>	
				</div>
			</td></tr>
			
			<tr><td colspan="2"><hr /></td></tr>
			<tr><td><?php include './_inc/appStore.php'; ?></td><td valign="top"><?php include './_inc/merchant.php'; ?></td></tr>  
			</table>		
		</div>
		<?php include './_inc/footer.php'; ?>
	</div></body>
</html>

<?php 

if (isset($_GET['a']))
	actionJobPost($facebook, $job_id, strtolower($type_row[0]), $obj_name);

require "./_inc/db_close.php";
ob_flush(); 
?>


<?php
	//foreach ($appStore_json->results[0] as $key => $val)
	//	echo ("appStore['results']['". $key ."'] = ". $val ."<br />\n");   
?>