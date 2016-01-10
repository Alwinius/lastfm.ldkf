 <?php
 	$bot_id = "56844913:AAG32PEmP3Uquw_m65fKI2Ec083A_ThkFs4";
 	include "db_connect.php";
 	$delete =  "DELETE FROM last_fm_charts_all";
	$kill_it_all = mysql_query($delete);
	$getusers = mysql_query("SELECT `username` FROM `ldkf_lastfm`"); 
	$i=0;
	while($getuser = mysql_fetch_row($getusers)){
		$users[$i]=$getuser[0];
		$i++;
	}
	$d=0;
	foreach($users as $user_in){
		$methode="'method=user.getTopArtists&limit=40&user='".$user_in."'&period=overall'";
		exec("python /var/www/projekte/last_fm/get.py $methode", $out);
		if(isset($out[0])) {
			$decode=json_decode($out[0]);
			$user_info_array = get_object_vars($decode);
			if(isset($user_info_array["topartists"])) {
			$user_info = get_object_vars($user_info_array["topartists"]);	
			foreach($user_info["artist"] as $top) {
				$info=get_object_vars($top);
				$name=str_replace("'", " ", $info["name"]);
				$playcount=$info["playcount"];
				$url=$info["url"];
				$image = get_object_vars($info["image"][0]);
				$image_path=$image['#text'];
				if(!isset($image_path) or $image_path=="") {
					$image_path="pic/empty.png";
				}
				$getcount = mysql_query("SELECT `playcount` FROM `last_fm_charts_all` WHERE artist LIKE '$name'"); 
				$count =	mysql_fetch_row($getcount);
				echo $name."<br>";
				$counter=$count[0];
				if(isset($counter) and $counter!="") {
					$getuser_add = mysql_query("SELECT `user` FROM `last_fm_charts_all` WHERE artist LIKE '$name'"); 
					$users_gotten =	mysql_fetch_row($getuser_add);
					$user_db=$users_gotten[0];
					$counter_insert=$counter+$playcount;
					$user_insert=$user_db."&&".$user_in;
					$update = "UPDATE last_fm_charts_all SET user = '$user_insert', playcount ='$counter_insert'  where artist = '$name'";
					$updaten = mysql_query($update);  
				}
				else {
					$eintrag = "INSERT INTO last_fm_charts_all (playcount, artist, user) VALUES ('$playcount', '$name', '$user_in')"; 
    				$eintragen = mysql_query($eintrag);
				}
			}
			}
			unset($out);
		} 
		$d++;
		sleep(1);
	}  
   				
?>
