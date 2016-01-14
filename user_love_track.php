<?php

$i = 0;
$page_n = $page + 1;
$page_l = $page - 1;
echo '
		<div style="margin-left:30px;">
			<table>
   			<tbody>';
foreach ($tracks as $track) {
    $artist_decode = $track->artist;
    //$love= 1;
    //$album_decode= $track->album;
    $album_name = "";
    $track_name = $track->name;
    $url = $track->url;
    $image_decode = $track->image;
    $date_decode = $track->date;
    $artist_array = get_object_vars($artist_decode);
    //$album_array = get_object_vars($album_decode);
    //$url_array = get_object_vars($album_decode);
    $date_array = get_object_vars($date_decode);
    $image_array = get_object_vars($image_decode[0]);
    $artist_name = $artist_array['name'];
    if ($artist_name == "Royal Rebublic") {
        $artist_name = "Royal Republic";
    }
    //$album_name=$album_array['#text'];
    $date_uts = $date_array['uts'];
    $lyric_band = str_replace(" ", "", $artist_name);
    $lyric_band = str_replace(" ", "", $lyric_band);
    $lyric_band = str_replace("'", "", $lyric_band);
    $lyric_band = str_replace("(", "", $lyric_band);
    $lyric_band = str_replace(")", "", $lyric_band);
    $lyric_band = str_replace("&", "", $lyric_band);
    $lyric_band = str_replace(".", "", $lyric_band);
    $lyric_band = str_replace(",", "", $lyric_band);
    $lyric_band = str_replace("$", "s", $lyric_band);
    $lyric_band = str_replace("/", "s", $lyric_band);
    $lyric_name = str_replace(" ", "", $track_name);
    $lyric_name = str_replace(")", "", $lyric_name);
    $lyric_name = str_replace("(", "", $lyric_name);
    $lyric_name = str_replace("'", "", $lyric_name);
    $lyric_name = str_replace("&", "", $lyric_name);
    $lyric_name = str_replace(".", "", $lyric_name);
    $lyric_name = str_replace(",", "", $lyric_name);
    $lyric_name = str_replace("$", "s", $lyric_name);
    $lyric_name = str_replace("/", "s", $lyric_name);
    $images = $image_array['#text'];
    if (!isset($images) or $images == "") {
        $getimage = mysql_query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name'");
        $getimages = mysql_fetch_row($getimage);
        if (isset($getimages[0]) and $getimages[0] != "") {
            $image = "covers/" . $getimages[0] . ".png";
        } else {
            $image = "pic/empty.png";
        }
    } else {
        $image_db = str_replace(".png", "", $images);
        $image_db = str_replace("http://img2-ak.lst.fm/i/u/34s/", "", $image_db);
        $getimage = mysql_query("SELECT `id` FROM `last_fm_covers` WHERE name LIKE '$image_db'");
        $getimages = mysql_fetch_row($getimage);
        $getimage_row = $getimages[0];
        if (!isset($getimage_row) or $getimage_row == "") {
            $pfad = "covers/" . $image_db . ".png";
            copy($images, $pfad);
            $eintrag = "INSERT INTO last_fm_covers (name) VALUES ('$image_db')";
            $eintragen = mysql_query($eintrag);
        }
        $image = "covers/" . $image_db . ".png";
    }
    $gmdate = gmdate("H:i", $date_uts);
    $ch_m_in = gmdate("d", $date_uts);
    $show_date = 0;
    if (!isset($check_date) or $check_date == "") {
        $ch_m = $ch_m_in;
        $show_date = 1;
    } else {
        $ch_m = gmdate("d", $check_date);
    }
    if ($ch_m_in != $ch_m or $show_date == 1) {
        $date_eng = gmdate("l, j. F Y", $date_uts);
        $date_eng = str_replace("Monday", "Montag", $date_eng);
        $date_eng = str_replace("Tuesday", "Dienstag", $date_eng);
        $date_eng = str_replace("Wednesday", "Mittwoch", $date_eng);
        $date_eng = str_replace("Thursday", "Donnerstag", $date_eng);
        $date_eng = str_replace("Friday", "Freitag", $date_eng);
        $date_eng = str_replace("Saturday", "Samstag", $date_eng);
        $date_eng = str_replace("Sunday", "Sonntag", $date_eng);
        $date_eng = str_replace("January", "Januar", $date_eng);
        $date_eng = str_replace("February", "Februar", $date_eng);
        $date_eng = str_replace("March", "M&auml;rz", $date_eng);
        $date_eng = str_replace("May", "Mai", $date_eng);
        $date_eng = str_replace("May", "Juni", $date_eng);
        $date_eng = str_replace("May", "Juli", $date_eng);
        $date_eng = str_replace("October", "Oktober", $date_eng);
        $date_eng = str_replace("December", "Dezember", $date_eng);
        echo'
								<tr>
									<td colspan="4" style="';
        if ($show_date != 1) {
            echo' padding-top:18px;';
        }echo' padding-bottom:7px; font-size:15pt;">
										' . $date_eng . '
									</td>
								</tr>
							';
    }
    echo'
								<tr frame="hsides" class="" style="';
    if ($i == 0) {
        echo'background-color: #F2F2F2;';
    } if ($ch_m_in != $ch_m or $show_date == 1) {
        echo' border-top: 1px solid #D8D8D8; ';
    } echo'">
									<td class="list">
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
         	   							<a href="https://www.last.fm/de/user/' . $username . '/library/music/' . urlencode($artist_name) . '/' . urlencode($album_name) . '" title="' . $artist_name . ' - ' . $album_name . '" target="_blank"><img src="' . $image . '"></a>
        									</span>
 	  	 								</span>              		
      	   	       		</td>
      	   	       		<td class="list" style="padding-left:10px;>
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
         	   							<img width="18px" height="18px;" src="';
    //	if($love==1) {
    echo "pic/love.png";
    //	}
    //	else {
    //		echo "pic/nolove.png";
    //	}
    echo '">
        									</span>
 	  	 								</span>              		
      	   	       		</td>
 	        	   				<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; min-width:600px;">
   	         	    			<span class="chartlist-ellipsis-wrap">
      	         	     			<span class="chartlist-artists">
         	   							<a href="https://www.last.fm/de/user/' . $username . '/library/music/' . urlencode($artist_name) . '" title="' . $artist_name . '" target="_blank">' . $artist_name . '</a>
        									</span>
											<span class="artist-name-spacer"> — </span>
											<a href="' . $url . '" title="' . $artist_name . '-' . $track_name . '" target="_blank" class="link-block-target">                                                         
    											' . $track_name . '
  	  										</a>
 	  	 								</span>
									</td>
									<td class="list_image">
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
         	   							<a href="http://www.azlyrics.com/lyrics/' . strtolower($lyric_band) . '/' . strtolower($lyric_name) . '.html" target="_blank" title="Lyrics"zz><div class="lyric" style="border-radius: 2px;"></div></a>
        									</span>
 	  	 								</span>              		
      	   	       		</td>
         	   	         <td class="list" style="padding-right:2px;">
            	   	     		<span title="' . $date_uts . '">' . $gmdate . '</span>
               	   	   </td>
								</tr>
           			';
    if ($i == 0) {
        $i++;
    } else {
        $i--;
    }
    $check_date = $date_uts;
}
echo '
           	</tbody>
			</table>
		</div>
	';
?>