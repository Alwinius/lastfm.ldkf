<?php

//get lyrics from azlyrics.com

$url="http://www.azlyrics.com/lyrics/". strtolower(preg_replace ( '/[^a-z0-9]/i', '', $_GET["artist"]))."/".  strtolower(preg_replace ( '/[^a-z0-9]/i', '', $_GET["song"]).".html");
if(get_headers($url, 1)[0]!="HTTP/1.1 200 OK") {
    print "<h3>Lyrics f&uuml;r diesen Song nicht gefunden.</h3>";
}
else {
    $response=  file_get_contents($url);
    $ly=substr(preg_replace("/^(.*)Sorry about that\. -->(.*)<form id=\"addsong(.*)/s", "$2", $response), 2, -23);
    echo '
 										
    										<div class="modal-content">
      										<div class="modal-header" style="padding-top:5px; padding-bottom:20px; padding-right:10px;">
        											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      										</div>
      										<div class="modal-body">'.$ly.'<br/><br/>Lyrics provided by <a href="http://azlyrics.com">AZLyrics</a></div><div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schlie&szlig;en</button>
      </div></div>
      									
  										';
}
