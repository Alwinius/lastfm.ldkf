<!doctype html>
<html>
	<head>
		<link rel="icon" href="favicon.png">
		<link href="https://msn.ldkf.de/css/bootstrap.min.css" rel="stylesheet">
    	<link href="https://msn.ldkf.de/css/main.css" rel="stylesheet">
    	<link href="https://msn.ldkf.de/css/bootstrap-theme.min.css" rel="stylesheet">
		<title>Startseite</title>
		<style>
			.overlay-content{
				vertical-align: middle;	
				margin: 0px auto;	
				z-index: 2;
				position: relative;
				position: relative;
				top: 50%;
				-webkit-transform: translateY(-50%);
				-ms-transform: translateY(-50%);
				transform: translateY(-50%);				
			}
				html, body {
			width: 100%;
			height: 100%;
			margin: 0;
			padding: 0;
			overflow: hidden;
		}

		#hintergrund {
			min-width: 100%;
			min-height: 100%;
			position: absolute;
			z-index: 0;
		}
		.middle{
			width:410px; 
			padding:30px; 
			background-color: rgba(0, 0, 0, 0.6);
			border-radius: 6px;
		}
		</style>
	</head>
	<body style="">
		<div>
			<img id="hintergrund" src="pic/bg.jpg"/>
		</div>
		<div id="content" class="main-content" role="main" style="height:100%">
				<div class="overlay-content">
					<section class="tracklist-section">
						<table class="chartlist chartlist--no-index chartlist--no-duration chartlist--no-countbar chartlist--no-image chartlist--no-download">
    						<tbody>
								<div class="container middle">
								<div>
      							<form class="form-signin" method="post" action="lastfm.php">
        								<label for="input_username" class="sr-only">Username</label>
		  								<input id="id_username" style="margin-bottom:5px;" autocomplete="on" type="text" name="username" class="form-control" placeholder="<?php
        								if(isset($_COOKIE["login"])){
											$val_name=$_COOKIE["login"];
											echo $val_name.'" value="'.$val_name;
										}
										else{
											echo 'LastFm Benutzername';											
										}
        								?>" required>
        								<select name="method" class="form-control" style="margin-bottom:5px;">
        								  <!-- <option value="1">LDKF-Bot zu Telegram hinzufügen</option>-->
        									<option selected value="2">Gescrobbelte Lieder anzeigen</option>
        									<option value="5">Lieblingslieder anzeigen</option>
        									<option value="6">Top K&uuml;nstler anzeigen</option>
        									<option value="7">Top Titel anzeigen</option>
											<option value="3">Mitglied der LDKF-last.fm-Gruppe werden</option>
										</select>
										<input type="hidden" name="start" value="1">
        								<button class="btn btn-lg btn-primary btn-block" type="submit">Los geht's!</button>
        								<?php
      	     							if(isset($error) and $error!="") {
												echo '<h3 style="color:red">'.$error.'</h3>';      	
      									}
      								?>
      							</form><br>
      							<form class="form-signin" method="post" action="lastfm.php" style="padding-top:10px;">
        								<button class="btn btn-lg btn-primary btn-block">LDKF-Bot zu Telegram hinzufügen</button>
        								<input type="hidden" name="method" value="1">
        							</form>
        							<form class="form-signin" method="post" action="lastfm.php" style="margin-top:10px;">
										<button class="btn btn-lg btn-primary btn-block">Gruppeninformationen anzeigen</button>
										<input type="hidden" name="method" value="4">
        								<?php
      	     							if(isset($error) and $error!="") {
												echo '<h3 style="color:red">'.$error.'</h3>';      	
      									}
      								?>
      							</form>
      							</div>
								</div>
	         			</tbody>
						</table>
					</section>
			  </div>
		</div>      
	</body>
</html> 