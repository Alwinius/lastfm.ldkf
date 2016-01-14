<?php

include "db_connect.php";
$getplace = mysql_query("SELECT `artist` FROM `last_fm_charts` ORDER BY playcount DESC ");
$i = 0;
while ($getplaces = mysql_fetch_row($getplace)) {
    $places[$i] = $getplaces[0];
    $i++;
}
$getmembers = mysql_query("SELECT `username` FROM `ldkf_lastfm`");
$l = 0;
while ($members = mysql_fetch_row($getmembers)) {
    $member[$l] = $members[0];
    $l++;
}

echo '
 	
	<div class="member">
	 <p style="margin-bottom:7px;"><b>Mitglieder dieser Gruppe:</b></p><div style="padding-left:15px;">';
foreach ($member as $member_name) {
    echo '<form class="form_member" method="post" action="lastfm.php">
		<input type="hidden" name="username" value="' . $member_name . '">
		<input type="hidden" name="method" value="2">
		<button type="submit" class="userButton">' . $member_name . '</button></form>';
}
echo'</div>
	</div>
 	<table lhs style="min-width:600px; border-top:2px solid; border-left:2px solid;">
 		<tbody>
 			<tr>
				<td class="list table_head" style="padding-left:10px;">
					Platz
				</td>
				<td class="list table_head" style="padding-left:8px;">
					K&uuml;nstler
			</td>
			<td class="list table_head">
				Insgesamt geh&ouml;rt
			</td> 
			<td class="list table_head">
				In der letzten Woche gehört von
			</td> 	
		</tr>
 	';
$i = 0;
$place = 1;
foreach ($places as $artist_name) {
    $getartist = mysql_query("SELECT `playcount` FROM `last_fm_charts` WHERE artist LIKE '$artist_name'");
    $artist = mysql_fetch_row($getartist);
    $count = $artist[0];
    if ($place == 1) {
        $count_max = $count;
    }
    $getuser = mysql_query("SELECT `user` FROM `last_fm_charts` WHERE artist LIKE '$artist_name'");
    $users = mysql_fetch_row($getuser);
    $users_names = $users[0];
    $user = str_replace("&&", ", ", $users_names);
    if (substr_count($user, ', ') > 3) {
        $teile = explode(",", $user, 5);
        $teil = str_replace(", ", '</li><li style="padding-left:15px;">', $teile[4]);

        $user = '<ul class="nav navbar-nav">
        			<li class="dropdown">
          			<a href="#" class="dropdown-toggle" style="padding:0px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          		' . $teile[0] . ', ' . $teile[1] . ', ' . $teile[2] . ', ' . $teile[3] . ' ...
				<span class="caret"></span>
									</a>
         								<ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; color:white;">
           								<li style="padding-left:15px;">' . $teil . '
           								</li>
									</ul>
								</li> 
									</ul>';
    }

    if ($count > 1) {
        echo'
			<tr class="" style="';
        if ($i == 0) {
            echo'background-color: #F2F2F2;';
        } echo'">
				<td class="list" style="padding-left:15px;">
   	        	<span class="">
      	   		<span class="chartlist-image">
         				' . $place . '
        				</span>
 	  	 			</span>         		
      	   </td>
    			<td class="list" style="padding-right:5px; padding-left:8px;">
   	        	<span class="chartlist-ellipsis-wrap">
      	   		<span class="chartlist-artists">
         				<a href="http://www.last.fm/de/music/' . urlencode($artist_name) . '" title="' . $artist_name . '" target="_blank">' . $artist_name . '</a>
        				</span>
 	  	 			</span>	
      	   </td>';
        $m = 0;
        $st = 40 * $count / $count_max;
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
         	<td class="list" style="padding-right:3px;">
    				<span>' . $user . '</span>
            </td>
			</tr>';
        if ($i == 0) {
            $i++;
        } else {
            $i--;
        }
    }
    $place++;
}
echo '
 		</tbody>
	</table>

	';
?>
