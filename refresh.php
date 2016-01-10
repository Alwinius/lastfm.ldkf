 <?php
 	$bot_id = "56844913:AAG32PEmP3Uquw_m65fKI2Ec083A_ThkFs4";
 	include "db_connect.php";
 	$delete =  "DELETE FROM last_fm_charts";
	$kill_it_all = mysql_query($delete);  
	$getusers = mysql_query("SELECT `username` FROM `ldkf_lastfm`"); 
	$i=0;
	while($getuser = mysql_fetch_row($getusers)){
		$users[$i]=$getuser[0];
		$i++;
	}
	$d=0;
	foreach($users as $user_in){
		$methode="'method=user.getTopArtists&user='".$user_in."'&period=7day'";
//exec("python get.py 'method=user.getTopArtists&user=cerdun&period=7day'", $out);
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
				$getcount = mysql_query("SELECT `playcount` FROM `last_fm_charts` WHERE artist LIKE '$name'"); 
				$count =	mysql_fetch_row($getcount);
				echo $name."<br>";
				$counter=$count[0];
				if(isset($counter) and $counter!="") {
					$getuser_add = mysql_query("SELECT `user` FROM `last_fm_charts` WHERE artist LIKE '$name'"); 
					$users_gotten =	mysql_fetch_row($getuser_add);
					$user_db=$users_gotten[0];
					$counter_insert=$counter+$playcount;
					$user_insert=$user_db."&&".$user_in;
					$update = "UPDATE last_fm_charts SET user = '$user_insert', playcount ='$counter_insert'  where artist = '$name'";
					$updaten = mysql_query($update);  
				}
				else {
					$eintrag = "INSERT INTO last_fm_charts (playcount, artist, user) VALUES ('$playcount', '$name', '$user_in')"; 
    				$eintragen = mysql_query($eintrag);
				}
			}
			}
			unset($out);
		} 
		$d++;
		sleep(1);
	}  
	$getplace = mysql_query("SELECT `artist` FROM `last_fm_charts` ORDER BY playcount DESC "); 
	$i=0;
	while($getplaces = mysql_fetch_row($getplace)){
		$places[$i]=$getplaces[0];
		$i++;
	}
	$i=0;	 
	$place=1;		
	foreach($places as $artist_name){
		if($i<60) {
		$getartist = mysql_query("SELECT `playcount` FROM `last_fm_charts` WHERE artist LIKE '$artist_name'"); 
		$artist =	mysql_fetch_row($getartist);
		$count=$artist[0];
		if($place==1) {$count_max=$count;}
		$getuser = mysql_query("SELECT `user` FROM `last_fm_charts` WHERE artist LIKE '$artist_name'"); 
		$users= mysql_fetch_row($getuser);
		$users_names=$users[0];
		$user =  str_replace("&&", ", ",$users_names);
		$cont= $place.". ".$artist_name." (".$count.") Gehört von ".$user;
		if($i>0) {
			$content[$i]=$content[$i-1].''.PHP_EOL.''.$cont;
		}	
		else {
			$content[$i]=$cont;
		}
		$place++;
		$i++;
	}
	}
	$output=urlencode($content[$i-1]);
	$getid = mysql_query("SELECT `telegram-id` FROM `last_fm`"); 
	while($id_db = mysql_fetch_row($getid)){
		$url = 'https://api.telegram.org/bot'.$bot_id.'/sendMessage?chat_id='.$id_db[0].'&text='.$output; 
		$result = file_get_contents($url);	
	}
	
	
	
	
	   				
?>
