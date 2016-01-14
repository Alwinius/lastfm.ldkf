<?php

/*
  object(stdClass)#10 (7) {
  ["name"]=> string(18) "Machinae Supremacy"
  ["playcount"]=> string(3) "712"
  ["mbid"]=> string(36) "22cbe400-9906-4c47-973c-b7122b23840a"
  ["url"]=> string(43) "http://www.last.fm/music/Machinae+Supremacy"
  ["streamable"]=> string(1) "0"
  ["image"]=> array(5) {
  [0]=> object(stdClass)#11 (2) {
  ["#text"]=> string(66) "http://img2-ak.lst.fm/i/u/34s/09cea4cfdbb044dfa98522fcd7fa9cc1.png"
  ["size"]=> string(5) "small"
  }
  [1]=> object(stdClass)#12 (2) {
  ["#text"]=> string(66) "http://img2-ak.lst.fm/i/u/64s/09cea4cfdbb044dfa98522fcd7fa9cc1.png"
  ["size"]=> string(6) "medium"
  }
  [2]=> object(stdClass)#13 (2) {
  ["#text"]=> string(67) "http://img2-ak.lst.fm/i/u/174s/09cea4cfdbb044dfa98522fcd7fa9cc1.png"
  ["size"]=> string(5) "large"
  }
  [3]=> object(stdClass)#14 (2) {
  ["#text"]=> string(70) "http://img2-ak.lst.fm/i/u/300x300/09cea4cfdbb044dfa98522fcd7fa9cc1.png"
  ["size"]=> string(10) "extralarge"
  }
  [4]=> object(stdClass)#15 (2) {
  ["#text"]=> string(62) "http://img2-ak.lst.fm/i/u/09cea4cfdbb044dfa98522fcd7fa9cc1.png"
  ["size"]=> string(4) "mega"
  }
  }
  ["@attr"]=> object(stdClass)#16 (1) {
  ["rank"]=> string(1) "1"
  }
  }

 */



$i = 0;
$page_n = $page + 1;
$page_l = $page - 1;
$place = (($page_in - 1) * $limit_in) + 1;
$counter_cont = 1;
echo '
		<div style="margin-left:30px;">
			<table>
   			<tbody>';
foreach ($tracks as $track) {
    $track_name = $track->name;
    $count = $track->playcount;
    $artist_name = $track->artist->name;
    //ar_dump($artist);
    //$artist_name=$artist['name'];
    $url = $track->url;
    $image_decode = $track->image;
    $image_array = get_object_vars($image_decode[0]);
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
    if ($counter_cont == 1) {
        $count_max = $count;
    }
    echo'
							<tr class="" style="';
    if ($i == 0) {
        echo'background-color: #F2F2F2;';
    } echo'">
								<td class="list">
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
         	   							<img src="' . $image . '">
        									</span>
 	  	 								</span>              		
      	   	       		</td>
				
				<td class="list" style="padding-left:15px;">
   	        	<span class="">
      	   		<span class="chartlist-image">
         				' . $place . '
        				</span>
 	  	 			</span>         		
      	   </td>
    			<td class="list" style="padding-right:5px; padding-left:8px; min-width:400px;">
   	        	<span class="chartlist-ellipsis-wrap">
      	         	     			<span class="chartlist-artists">
         	   							<a href="https://www.last.fm/de/user/' . $username . '/library/music/' . urlencode($artist_name) . '" title="' . $artist_name . '" target="_blank">' . $artist_name . '</a>
        									</span>
											<span class="artist-name-spacer"> — </span>
											<a href="' . $url . '" title="' . $artist_name . '-' . $track_name . '" target="_blank" class="link-block-target">                                                         
    											' . $track_name . '
  	  										</a>
 	  	 								</span>
      	   </td>';
    $m = 0;
    $st = (60 / $page_in) * $count / $count_max;
    echo'      	   
      	   <td class="list" style="padding-right:8px; min-width:200px;"><div class="';
    if ($st > strlen($count) * 2) {
        echo'textunter';
    } echo '">
    				';
    while ($m < $st) {
        echo '<img style="';
        if ($m == 0) {
            echo 'border-top-left-radius:3px; border-bottom-left-radius:3px; ';
        }
        if ($m + 1 >= $st) {
            echo 'border-top-right-radius:3px; border-bottom-right-radius:3px';
        }


        echo'" src="pic/count.png" height:15px;>';
        $m++;
    }
    echo '<span';
    if ($st > strlen($count) * 2) {
        
    } else {
        echo' style="padding-left:5px;"';
    } echo '>' . $count . '</span></div>
            </td>
         </tr>';

    if ($i == 0) {
        $i++;
    } else {
        $i--;
    }
    $place++;
    $counter_cont++;
}


echo '
           	</tbody>
			</table>
		</div>
	';
?>