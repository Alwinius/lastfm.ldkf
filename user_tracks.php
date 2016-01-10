<?php
	$i=0;
	$playing=0;
	$page_n=$page+1;
	$page_l=$page-1;
	echo '
		<div style="margin-left:30px;">
			<table>
   			<tbody>';
					foreach($tracks as $track){
						$album_name="";
						$artist_decode= $track->artist;
						$love= $track->loved;
						$album_decode= $track->album;
						$track_name= $track->name;
						$url= $track->url;
						$image_decode= $track->image;
						if(isset($track->date)) {
							$date_decode= $track->date;
						}
						else {
							$date_decode="wird gerade gehört";
							//var_dump($track);
						}
						$artist_array = get_object_vars($artist_decode);
						$album_array = get_object_vars($album_decode);
						$url_array = get_object_vars($album_decode);
						if($date_decode!="wird gerade gehört") {
							$date_array = get_object_vars($date_decode); 
						}
						$image_array = get_object_vars($image_decode[0]);
						$artist_name=$artist_array['name'];
						$album_name=$album_array['#text'];
						if($date_decode!="wird gerade gehört") {
							$date_uts=$date_array['uts']+3600;
						}
						$images=$image_array['#text'];
						if(!isset($images) or $images=="") {
							$getimage = mysql_query("SELECT `name` FROM `last_fm_covers` WHERE artist LIKE '$artist_name'"); 
							$getimages = mysql_fetch_row($getimage);
							if(isset($getimages[0]) and $getimages[0]!="") {							
								$image="covers/".$getimages[0].".png"; 
							}
							else {
								$image="pic/empty.png";
							}
						}
						else {
							$image_db =  str_replace(".png", "",$images);
							$image_db =  str_replace("http://img2-ak.lst.fm/i/u/34s/", "",$image_db);
							$getimage = mysql_query("SELECT `id` FROM `last_fm_covers` WHERE name LIKE '$image_db'"); 
							$getimages = mysql_fetch_row($getimage);
							$getimage_row=$getimages[0];
							if(!isset($getimage_row) or $getimage_row=="") {
								$pfad="covers/".$image_db.".png";
								copy($images, $pfad);
								$eintrag = "INSERT INTO last_fm_covers (name) VALUES ('$image_db')"; 
    							$eintragen = mysql_query($eintrag);
							}
							$image="covers/".$image_db.".png"; 
						}
						if($date_decode!="wird gerade gehört") {
							$gmdate = gmdate("H:i", $date_uts);
							$ch_m_in=gmdate("d", $date_uts);
							$show_date=0;
							if(!isset($check_date) or $check_date=="" ) {
								$ch_m=$ch_m_in;
								$show_date=1;
							}
							else {
								$ch_m=gmdate("d", $check_date);
							}
							if($ch_m_in!=$ch_m or $show_date==1){
								$date_eng=gmdate("l, j. F Y", $date_uts);
								$date_eng =  str_replace("Monday", "Montag",$date_eng);
								$date_eng =  str_replace("Tuesday", "Dienstag",$date_eng);
								$date_eng =  str_replace("Wednesday", "Mittwoch",$date_eng);
								$date_eng =  str_replace("Thursday", "Donnerstag",$date_eng);
								$date_eng =  str_replace("Friday", "Freitag",$date_eng);
								$date_eng =  str_replace("Saturday", "Samstag",$date_eng);
								$date_eng =  str_replace("Sunday", "Sonntag",$date_eng);
								$date_eng =  str_replace("January", "Januar",$date_eng);
								$date_eng =  str_replace("February", "Februar",$date_eng);
								$date_eng =  str_replace("March", "M&auml;rz",$date_eng);
								$date_eng =  str_replace("May", "Mai",$date_eng);
								$date_eng =  str_replace("June", "Juni",$date_eng);
								$date_eng =  str_replace("July", "Juli",$date_eng);
								$date_eng =  str_replace("October", "Oktober",$date_eng);
								$date_eng =  str_replace("December", "Dezember",$date_eng);
								echo'
									<tr>
										<td colspan="5" style="'; if($show_date!=1 or $playing==1){ echo'padding-top:18px;'; } echo' padding-bottom:7px; font-size:15pt;">
											'.$date_eng.'
										</td>
									</tr>
								';								
							}
						}
						$lyric_band = strtolower(preg_replace ( '/[^a-z0-9]/i', '', $artist_name )); 
						$lyric_name = strtolower(preg_replace ( '/[^a-z0-9]/i', '', $track_name)); 
						
						$url="http://www.azlyrics.com/lyrics/".$lyric_band."/".$lyric_name.".html"	;				
						echo'
								<tr frame="hsides" class="" style="'; if($date_decode=="wird gerade gehört") { echo'background-color: #F2F5A9;';} elseif($i==0) { echo'background-color: #F2F2F2;';} if(( (isset($ch_m_in) and isset($ch_m)) and $ch_m_in!=$ch_m) or (isset($show_date) and $show_date==1)) {echo' border-top: 1px solid #D2D2D2; ';} echo'">
									<td class="list">
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
      	         	     				<a href="https://www.last.fm/de/user/'.$username.'/library/music/'. urlencode($artist_name).'/'. urlencode($album_name).'" title="'.$artist_name.' - '.$album_name.'" target="_blank"><img src="'.$image.'"></a>
        									</span>
 	  	 								</span>              		
      	   	       		</td>
      	   	       		<td class="list" style="padding-left:10px;">
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
         	   							<img width="18px" height="18px;" src="'; 
         	   							
         	   								if($love==1) {
														echo "pic/love.png";
         	   								}
         	   								else {
         	   									echo "pic/nolove.png";
         	   								}
         	   								if($date_decode=="wird gerade gehört") {
         	   									$gmdate=$date_decode;
         	   									$date_uts="now";
         	   								}
         	   							echo '">
        									</span>
 	  	 								</span>              		
      	   	       		</td>
 	        	   				<td class="chartlist-ellipsis-wrap list" style="padding-left:10px; padding-right:4px; min-width:600px;">
   	         	    			<span class="chartlist-ellipsis-wrap">
      	         	     			<span class="chartlist-artists">
         	   							<a href="https://www.last.fm/de/user/'.$username.'/library/music/'. urlencode($artist_name).'" title="'.$artist_name.'" target="_blank">'.$artist_name.'</a>
        									</span>
											<span class="artist-name-spacer"> — </span>
											<a href="'.$url.'" title="'.$artist_name.'-'.$track_name.'" target="_blank" class="link-block-target">                                                         
    											'.$track_name.'
  	  										</a>
 	  	 								</span>
									</td>
									<td class="list_image">
   	         	    			<span class="">
      	         	     			<span class="chartlist-image">
												<label style="padding:1px; margin:0;" data-toggle="modal" data-target="#'.$date_uts.'" onclick="loadDoc()">
													<div class="lyric" style="border-radius: 3px;"></div>
												</label>
         	   						</span>
 	  	 								</span>  
 	  	 									<div class="modal fade" id="'.$date_uts.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 										<div class="modal-dialog" role="document">
    										<div class="modal-content">
      										<div class="modal-header" style="padding-top:5px; padding-bottom:20px; padding-right:10px;">
        											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      										</div>
      										<div class="modal-body" id="'.$date_uts.'_text">
      										 <a href="'.$url.'" target="_blank">hier klicken</a>
      										</div>
      									</div>
  										</div>
									</div>            		
      	   	       		</td>
         	   	         <td class="list" style="padding-right:2px;">';
         	   	         if($date_decode=="wird gerade gehört") {
         	   					echo '
         	   						<figure style="float:left; padding-right:8px;">
  												<img src="pic/test.gif" width="15px" height="20px">
  											</figure>
										';
         	   				}
         	   	         echo '<span title="'.$date_uts.'" style="vertical-align:bottom; padding-right:3px;">'.$gmdate.'</span>
               	   	   </td>
								</tr>
           			';
           			if($i==0){$i++;}
           			else {$i--;}
           			if($date_decode!="wird gerade gehört") {
           				$check_date=$date_uts;
           				$playing=0;
           			}
           			else {
							$playing=1;           			
           			}
           		} 
           		echo '
           	</tbody>
			</table>
		</div>
	';
?>