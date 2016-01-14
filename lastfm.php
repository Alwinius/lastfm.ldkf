<?php
include "db_connect.php";
if (!isset($_GET['token'])) {
    if (!isset($_GET['method'])) {
        $method_in = $_POST['method'];
    } else {
        $method_in = $_GET['method'];
    }
} else {
    $method_in = 3;
}
if (isset($_POST['username'])) {
    $user_in = $_POST['username'];
    if ($method_in == 3) {
        header('Location: http://www.last.fm/api/auth?api_key=830d6e2d4d737d56aa1f94f717a477df&cb=https://lastfm.ldkf.de/lastfm.php');
    }
} else {
    if (isset($_GET['token']) and $_GET['token'] != "") {
        $method_in = 3;
        $token = $_GET['token'];
        $sig = md5("api_key830d6e2d4d737d56aa1f94f717a477dfmethodauth.getSessiontoken" . $token . "1a05eab1f6dba7de78d59a6c94267464");
        $methode = "method=auth.getSession&token=" . $token . "&api_sig=" . $sig;
        $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
        $decode = json_decode($out);
        $info_array = get_object_vars($decode);
        // var_dump($info_array);
        //	$keyy=$info_array['session']->key;
        //	$methode="'method=track.love&track=Deify&artist=Disturbed&api_sig='".$sig."'&sk='".$keyy;
        //	exec("python get.py $methode", $outa);
        // var_dump($outa[0]);

        if (isset($info_array['error'])) {
            $error = 1; //fehler bei übermittlung
        } else {
            $info = get_object_vars($info_array['session']);
            $user_in = $info['name'];
            $getname = mysql_query("SELECT `id` FROM `ldkf_lastfm` WHERE `username` LIKE '$user_in'");
            $namecheck = mysql_fetch_row($getname);
            $user = $namecheck[0];
            if (!isset($user) or $user == "") {
                $eintrag = "INSERT INTO ldkf_lastfm (username) VALUES ('$user_in')";
                $eintragen = mysql_query($eintrag);
                $error = 2; //ERFOLG
            } else {
                $error = 3; //Bereits Mitglied				
            }
        }
    } else {
        if (isset($_GET['method'])) {
            $method = $_GET['method'];
        } elseif ($method_in == 1 or $method_in == 4) {
            if ($method_in == 1) {
                header('Location: https://telegram.me/ldkf_bot');
            }
        } else {
            header('Location: ./');
        }
    }
}

if (isset($_POST['pagein'])) {
    $page_in = $_POST['pagein'];
} else {
    $page_in = 1;
}
if (isset($_POST['limitin'])) {
    $limit_in = $_POST['limitin'];
} elseif ($method_in == 2 or $method_in == 5) {
    $limit_in = 15;
} else {
    $limit_in = 20;
}
if (isset($user_in)) {
    $methode = "method=user.getInfo&user=" . $user_in;
    $out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
    if ($out_user != '{"error":6,"message":"User not found","links":[]}') {
        $decode_Info_User = json_decode($out_user);
        $user_info_forimage_array = get_object_vars($decode_Info_User)['user'];
        $user_name_info = get_object_vars($decode_Info_User)['user']->name;
        $totalTracks = get_object_vars($decode_Info_User)['user']->playcount;
        $starttime = get_object_vars($decode_Info_User)['user']->registered->unixtime;
        $user_info_forimage = get_object_vars($user_info_forimage_array)['image'];
        $userimage = get_object_vars($user_info_forimage[1]);
        $account_image = $userimage['#text'];
        if (!isset($account_image) or $account_image == "") {
            $image = "pic/empty.png";
        } else {
            $image_db = str_replace(".png", "", $account_image);
            $image_db = str_replace("http://img2-ak.lst.fm/i/u/64s/", "", $image_db);
            $getimage = mysql_query("SELECT `id` FROM `last_fm_user_pics` WHERE name LIKE '$image_db'");
            $getimages = mysql_fetch_row($getimage);
            $getimage_row = $getimages[0];
            if (!isset($getimage_row) or $getimage_row == "") {
                $pfad = "user_pics/" . $image_db . ".png";
                copy($account_image, $pfad);
                $eintrag = "INSERT INTO last_fm_user_pics (name) VALUES ('$image_db')";
                $eintragen = mysql_query($eintrag);
            }
            $image = "user_pics/" . $image_db . ".png";
        }
        if ($method_in == 2) {
            if (!isset($_COOKIE['login']) and isset($_POST['start']) and $_POST['start'] == 1) {
                setcookie("login", $user_in, time() + (3600 * 24 * 365));
            }
            $methode = "method=user.getRecentTracks&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
            $decode = json_decode($out);
            $user_info_array = get_object_vars($decode->recenttracks);
            $user_decode = $user_info_array['@attr'];
            $username = $user_decode->user;
            $page = $user_decode->page;
            $perPage = $user_decode->perPage;
            $totalPages = $user_decode->totalPages;
            $tracks = $decode->recenttracks->track;
        }
        if ($method_in == 5) {
            $methode = "method=user.getLovedTracks&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
            if (isset($out)) {
                $decode = json_decode($out);
                $user_info_array_love = get_object_vars($decode->lovedtracks);
                $user = $user_info_array_love['@attr'];
                $tracks = $user_info_array_love['track'];
                $username = $user->user;
                $page = $user->page;
                $perPage = $user->perPage;
                $totalPages = $user->totalPages;
                $totaltracks = $user->total;
            }
        }
        if ($method_in == 6) {
            $methode = "method=user.getTopArtists&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
            if (isset($out)) {
                $decode = json_decode($out);
                $user_info_array_love = get_object_vars($decode->topartists);
                $user = $user_info_array_love['@attr'];
                $tracks = $user_info_array_love['artist'];
                $username = $user->user;
                $page = $user->page;
                $perPage = $user->perPage;
                $totalPages = $user->totalPages;
                $totaltracks = $user->total;
            }
        }
        if ($method_in == 7) {
            $methode = "method=user.getTopTracks&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&" . $methode);
            if (isset($out)) {
                $decode = json_decode($out);
                $user_info_array_love = get_object_vars($decode->toptracks);
                $user = $user_info_array_love['@attr'];
                $tracks = $user_info_array_love['track'];
                $username = $user->user;
                $page = $user->page;
                $perPage = $user->perPage;
                $totalPages = $user->totalPages;
                $totaltracks = $user->total;
            }
        }
    } else {
        $method_in = 0;
    }
}
?>

<!DOCTYPE html>
<meta charset="UTF-8">
<html>
    <head>
        <link rel="icon" href="favicon.png"/>
        <link href="https://msn.ldkf.de/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://msn.ldkf.de/css/bootstrap-theme.min.css" rel="stylesheet" />
        <title><?php if ($method_in == 4 or $method_in == 8 or $method_in == 9) {
    echo "LDKF-Gruppe";
} elseif ($method_in == 2) {
    echo "Musikprofil";
} elseif (isset($user_in)) {
    echo $user_in;
} ?></title>
        <style>
            html, body {
                height: 100%;
            }
            @font-face {
                font-family: 'ubuntu-m';
                src: url('fonts/Ubuntu-R.ttf')
            }
            .option {
                padding-top:4px;
                padding-bottom:4px
            }
            .lyric{ 
                height:24px; 
                width: 24px;
                background: url("pic/lyrics.png") ; 
                background-size: cover;
            }
            .lyric:hover { 
                height:24px; 
                width: 24px;
                background: url("pic/lyrics_over.png") ; 
                background-size: cover; 
            }
            .member{
                position:relative; 
                background-color: #2B7824; 
                color:white; 
                float:left; 
                margin: 0px 40px 20px 0; 
                padding:12px;
                padding-top: 23px;
                padding-bottom: 23px;
                border-bottom-right-radius:6px;
                border-top-right-radius:6px;

            }
            .form_member {
                padding: 0;
                margin: 0;
            }
            .main-content{
                position: relative;
                min-height: 100%;
            }
            .list {
                padding: 2px 0px 2px 0px;
                border-collapse: collapse;
                border-bottom: 1px solid #d2d2d2;
            }	
            .list_image {
                padding-right:10px;
                border-collapse: collapse;
                border-bottom: 1px solid #d2d2d2;
            }	
            .table_head	{
                padding-bottom: 5px;
                padding-top: 5px;				
            }
            .navfooter{
                padding: 0px 5px 0px 5px;
            }
            .main{
                margin-left:40px;

            }
            .textunter span {
                position: absolute;
                color: white;
                left: 2px;
            }
            .textunter{
                position:relative; 
            }
            .footer{
                position:absolute;
                background-color:#222; 
                width:100%;
                min-width:500px;
                color:white;
                padding:10px 1px 10px 100px;
                bottom: 0;
            }
            .userButton {
                border-style: none;
                background-color: rgba(43, 120, 36, 0);
                display:inline-block;
                cursor:pointer;
                color:#ffffff;
                padding:0 0 0 0;
                text-decoration:none;
            }
            .userButton:hover {
                color: #D8D8D8;
                text-decoration: none;
            }
            .userButton:active {
                position:relative;
            }

        </style>
    </head>
    <body style="font-family: ubuntu-m;">
        <div id="content" class="main-content" role="main" style="">
            <nav class="navbar navbar-inverse navbar-static-top">
                <div class="container-fluid" id="navigation" style="display:block;">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="http://ldkf.de" target="_blank">LDKF.de</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li><a href="./">Startseite<span class="sr-only">(current)</span></a></li>
<?php
if ($method_in == 2 or $method_in == 5 or $method_in == 6 or $method_in == 7) {
    $getname = mysql_query("SELECT `id` FROM `ldkf_lastfm` WHERE `username` LIKE '$user_in'");
    $namecheck = mysql_fetch_row($getname);
    $user = $namecheck[0];
    if (isset($user) and $user != "") {
        echo'<li><a href="./lastfm.php?method=4">Gruppe</a></li>
                                                                                <li><a href="http://explr.fm/?username=' . $user_in . '">Explr.fm</a></li>';
    }
    echo '</ul>
                                                                                <ul class="nav navbar-nav navbar-right">
                                                                                        <li class="dropdown" style="width:200px;">
                                                                                        <a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                                                                <img style="border-radius: 18px;" width="36px" src="';
    echo $image;
    echo '">	' . $user_in . '<span class="caret"></span>
                                                                                                        </a>
                                                                                        <ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
                                                                                                <li style="padding-left:15px;">';

    if ($method_in != 2) {
        echo'
                                                                                                        <form class="form_member" method="post" action="lastfm.php">
                                                                                                                                <input type="hidden" name="username" value="' . $user_in . '">
                                                                                                                                <input type="hidden" name="method" value="2">
                                                                                                                                <button type="submit" class="userButton">';
    } echo 'Scrobbles: ' . $totalTracks;
    if ($method_in != 2) {
        echo '</button>
                                                                                                                        </form>';
    }
    echo '
                                                                                                </li>
                                                                                                <li style="padding-left:15px;">';
    if ($method_in != 5) {
        echo'
                                                                                                        <form class="form_member" method="post" action="lastfm.php">
                                                                                                                                <input type="hidden" name="username" value="' . $user_in . '">
                                                                                                                                <input type="hidden" name="method" value="5">
                                                                                                                                <button type="submit" class="userButton">';
    } echo 'Lieblingslieder';
    if ($method_in == 5) {
        echo ': ' . $totaltracks;
    }
    echo'</button>
                                                                                                                        </form>';
    echo '
                                                                                                                </li>
                                                                                                                <li style="padding-left:15px;">';
    if ($method_in != 6) {
        echo'
                                                                                                        <form class="form_member" method="post" action="lastfm.php">
                                                                                                                                <input type="hidden" name="username" value="' . $user_in . '">
                                                                                                                                <input type="hidden" name="method" value="6">
                                                                                                                                <button type="submit" class="userButton">';
    } echo 'Top K&uuml;nstler';
    if ($method_in == 6) {
        echo ': ' . $totaltracks;
    }
    echo'</button>
                                                                                                                        </form>';
    echo '
                                                                                                </li>
                                                                                                <li style="padding-left:15px;">';
    if ($method_in != 7) {
        echo'
                                                                                                        <form class="form_member" method="post" action="lastfm.php">
                                                                                                                                <input type="hidden" name="username" value="' . $user_in . '">
                                                                                                                                <input type="hidden" name="method" value="7">
                                                                                                                                <button type="submit" class="userButton">';
    } echo 'Top Titel';
    if ($method_in == 7) {
        echo ': ' . $totaltracks;
    }
    echo'</button>
                                                                                                                        </form>';
    echo '
                                                                                                </li>
                                                                                                <li style="padding-left:15px;">Scrobbelt seit: ' . gmdate("d.m.Y", $starttime) . '</li>
                                                                                                </ul>
                                                                                </li> 
                                                                        </ul>';
}
if ($method_in == 4) {
    echo'</ul>
                                                                                <ul class="nav navbar-nav navbar-right">
                                                                                <li class="dropdown" style="width:200px;">
                                                                                <a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                                                        <img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
                                                                                                </a>
                                                                                <ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
                                                                                           <li style="padding-left:15px;">Wochencharts</li>
                                                                                   <li style="padding-left:15px;">
                                                                                                        <div>
                                                                                                                        <a class="userButton" href="lastfm.php?method=8">Top Künstler</a>
                                                                                                                </div>
                                                                                                        </li>
                                                                                        <li style="padding-left:15px;">
                                                                                                <div>
                                                                                                                        <a class="userButton" href="lastfm.php?method=8">test</a>
                                                                                                                </div>
                                                                                        </li>
                                                                                        </ul>
                                                                        </li> 
                                                                </ul>';
}
if ($method_in == 8) {
    echo'</ul>
                                                                                <ul class="nav navbar-nav navbar-right">
                                                                                <li class="dropdown" style="width:200px;">
                                                                                <a href="#" class="dropdown-toggle" style="padding-bottom:6px; padding-top:7px;" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                                                        <img style="border-radius: 18px;" width="36px" src="pic/ldkf.png"> last.fm Gruppe<span class="caret"></span>
                                                                                                </a>
                                                                                <ul class="navbar-inverse dropdown-menu" style="border-radius: 6px; width:100%; margin-top:10px; padding-bottom:8px; color:white;">
                                                                                        <li style="padding-left:15px;">
                                                                                                <div>
                                                                                                                        <a class="userButton" href="lastfm.php?method=4">Wochencharts</a>
                                                                                                                </div>
                                                                                        </li>
                                                                                        <li style="padding-left:15px;">Top Künstler</li>
                                                                                        <li style="padding-left:15px;">
                                                                                        <div>
                                                                                                                        <a class="userButton" href="lastfm.php?method=8">test</a>
                                                                                                                </div>
                                                                                        </li>
                                                                                        </ul>
                                                                        </li> 
                                                                </ul>';
}
?>					
                    </div>
                </div>
            </nav>
            <div class="main" style="margin-left:0; padding-bottom:70px;">
                <section class="tracklist-section">
                    <?php
                    switch ($method_in) {
                        case 0:
                            echo '<div style="margin:40px;"><h3>Benutzer "' . $user_in . '" existiert nicht.</h3></div>';
                        case 1:
                            break;
                        case 2:
                            include "user_tracks.php";
                            break;
                        case 3:
                            switch ($error) {
                                case 1:
                                    echo "Es gab einen Fehler, versuche es noch einmal.";
                                    break;
                                case 2:
                                    echo "Du wurdest erfolgreich zu dieser Gruppe hinzugef&uuml;gt.";
                                    break;
                                case 3:
                                    echo "Du bist bereits Mitglied in dieser Gruppe.";
                                    break;
                                default:
                                    echo "";
                                    break;
                            }
                            break;
                        case 4:
                            include "group.php";
                            break;
                        case 5:
                            include "user_love_track.php";
                            break;
                        case 6:
                            include "user_topartist.php";
                            break;
                        case 7:
                            include "user_toptrack.php";
                            break;
                        case 8:
                            include "group.all.php";
                            break;
                        default:
                            break;
                    }
                    ?>
                </section>
            </div>

            <?php
            if ($method_in == 2 or $method_in == 5 or $method_in == 6 or $method_in == 7) {
                echo '
                <div class="nav footer">
                        <table>
                                <tr>
                                        <td class="navfooter" style="color:white;">
                        Seite ' . $page . ' von ' . $totalPages . '
                </td>
                                        <td class="navfooter">
                                ';
                if ($page > 2) {
                    echo '
                                                <form action="?" style="margin:0; padding:0;" method="POST">
                                <input type="hidden" name="username" value=' . $user_in . '>
                                                <input type="hidden" name="method" value="' . $method_in . '">
                                                <input type="hidden" name="limitin" value="' . $limit_in . '">
                                        <input type="hidden" name="pagein" value="1">
                                                        <button type="submit" class="btn btn-primary">
                                                                |<<
                                                        </button>
                                                </form>
                                        ';
                }
                echo '
                                        </td>
                                        <td class="navfooter">
                                ';
                if ($page != 1) {
                    echo '
                                                <form action="?" style="margin:0; padding:0;" method="POST">
                                <input type="hidden" name="username" value=' . $user_in . '>
                                                <input type="hidden" name="method" value="' . $method_in . '">
                                                <input type="hidden" name="limitin" value="' . $limit_in . '">
                                        <input type="hidden" name="pagein" value="' . $page_l . '">
                                                        <button type="submit" class="btn btn-primary">
                                                                <<
                                                        </button>
                                                </form>
                                        ';
                }
                echo'
                                        </td>
                                        <td class="navfooter">
                                ';
                if ($page < $totalPages) {
                    echo'
                        <form action="?" style="margin:0; padding:0;" method="POST">
                                <input type="hidden" name="username" value=' . $user_in . '>
                                                        <input type="hidden" name="method" value="' . $method_in . '">
                                                        <input type="hidden" name="limitin" value="' . $limit_in . '">
                                                <input type="hidden" name="pagein" value="' . $page_n . '">
                                                                <button type="submit" class="btn btn-primary">
                                                                        >>
                                                                </button>
                                                        </form>
                                                ';
                }
                echo'
                                                </td>   
                                                <td class="navfooter">
                                        ';
                if ($page + 1 < $totalPages) {
                    echo'
                        <form action="?" style="margin:0; padding:0;" method="POST">
                                <input type="hidden" name="username" value=' . $user_in . '>
                                                        <input type="hidden" name="method" value="' . $method_in . '">
                                                        <input type="hidden" name="limitin" value="' . $limit_in . '">
                                                <input type="hidden" name="pagein" value="' . $totalPages . '">
                                                                <button type="submit" class="btn btn-primary">
                                                                        >>|
                                                                </button>
                                                        </form>
                                                ';
                }
                echo'
                                                </td>             			
                        <td class="navfooter">
                                <form action="?" style="margin:0; padding:0;" method="POST">
                                                        <select class="" name="limitin" id="myselect" onchange="this.form.submit()" style="color:black">';
                if ($method_in == 6 or $method_in == 7) {
                    echo'
                                                        <option class="option"';
                    if ($perPage == 20) {
                        echo " selected";
                    } echo ' value="20">20 Eintr&auml;ge Pro Seite</option>
                                                        <option class="option"';
                    if ($perPage == 40) {
                        echo " selected";
                    } echo ' value="40">40 Eintr&auml;ge Pro Seite</option>
                                                        <option class="option"';
                    if ($perPage == 60) {
                        echo " selected";
                    } echo ' value="60">60 Eintr&auml;ge Pro Seite</option>';
                } else {
                    echo'
                                                        <option class="option"';
                    if ($perPage == 15) {
                        echo " selected";
                    } echo ' value="15">15 Eintr&auml;ge Pro Seite</option>
                                                        <option class="option"';
                    if ($perPage == 25) {
                        echo " selected";
                    } echo ' value="25">25 Eintr&auml;ge Pro Seite</option>
                                                        <option class="option"';
                    if ($perPage == 35) {
                        echo " selected";
                    } echo ' value="35">35 Eintr&auml;ge Pro Seite</option>';
                }
                echo'</select>
                                                        <input type="hidden" name="username" value=' . $user_in . '>
                                                        <input type="hidden" name="method" value="' . $method_in . '">
                                                        <input type="hidden" name="pagein" value="' . $page . '">
                                                </form>
                                        </td>
                                </tr>
                        </table>
                </div>
                                ';
            }
            ?>
        </div>
        <script type="text/javascript" src="https://msn.ldkf.de/js/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="https://msn.ldkf.de/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('body').on('hidden.bs.modal', '.modal', function () {
                    $(this).removeData('bs.modal');
                });
            });
        </script>
    </body>
</html> 