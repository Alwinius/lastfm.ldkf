<?php		$out=exec("python /var/www/projekte/last_fm/get.py 'method=user.getTopArtists&user=cerdun&period=7day'");	echo $out; ?>
