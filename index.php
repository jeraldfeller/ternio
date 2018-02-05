<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/session.php';

$privateKey = ($userData['hasShared'] == true ? $userData['keys']['private'][array_rand($userData['keys']['private'])] : '????????????');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Main</title>

	<link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/x-icon" href="" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <style>
        .margin-top-24{
            margin-top: 24px;
        }
        .margin-top-12{
            margin-top: 12px;
        }
        .margin-bottom-12{
            margin-bottom: 12px;
        }
        .margin-bottom-24{
            margin-bottom: 24px;
        }
        .display-none{
            display: none;
        }

    </style>

</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
	        <div class="dashlogo"></div>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">Main</a></li>
                <li><a href="points-board.php">Current Tokens Board</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container-fluid currentpointsmargin" style="margin-top: 10%;">
    <div class="row">
        <div class="col-md-12 text-center dash-currentpoint">
            <h2>Current Tokens: <?php echo $userData['points']; ?></h2>
        </div>
    </div>
    <div class="row margin-top-24">
        <div class="col-md-4 col-md-offset-4 currentpoints-bluewrap">
            <div class="form-group">
                <label for="firstName">Public Code</label>
                <input type="text" class="form-control" value="<?php echo $userData['keys']['public']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="firstName">Confirmation Code</label>
                <input type="text" class="form-control private-key-display" value="<?php echo $privateKey; ?>" readonly>
            </div>
        </div>
    </div>
    <?php if($userData['hasShared'] == false) { ?>
    <div class="row share-container">
        <div class="col-md-4 col-md-offset-4">
            <div class="well well-sm sociallikewrap">
                <h3 class="text-center">Reveal Your Private Key</h3>
                <h4 class="text-center sociallike-bottomrevealtext">Share on one of the following social networks to reveal your private key:</h4>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                            <a style="cursor: pointer;" class="share-fb"><img src="assets/images/social/fb.png"></a>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                            <a style="cursor: pointer;" class="share-twitter" ><img src="assets/images/social/twitter.png"></a>

                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                            <a style="cursor: pointer;" class="share-reddit" ><img src="assets/images/social/reddit.png"></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
   <?php } ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="assets/ajax/app.js"></script>

<script>
    // FB
    $(document).ready(function(){
        $('.share-fb').click(function(){
            FB.ui(
                {
                    method: 'share',
                    href: 'https://airdrop.ternio.io'
                }, function(response){
                    hasShared({userId: <?php echo $userData['id']; ?>, socialMedia: 'twitter'}).done(function(response){
                        if(response.success == true){
                            setTimeout(function(){
                                $('.private-key-display').val(response.privateKey);
                                $('.share-container').addClass('display-none');
                            }, 1000);
                        }
                    });
                });
        }) ;

        // Twitter
        $url = 'http://airdrop.ternio.io';
        $text = '#Ternio is giving away 100k a day in airdrops.  https://airdrop.ternio.io #Airdrop #cryptocurrency #blockchain #TokenSale';
        $intent = 'https://twitter.com/intent/tweet?text='+encodeURIComponent($text)+'&url='+encodeURIComponent($url);
        $('.share-twitter').attr('href', $intent);

        // Reddit
        $url = 'https://airdrop.ternio.io';
        $title = 'They are giving away 100k a day in airdrops!';
        $link = 'https://www.reddit.com/submit?url='+$url+'&title='+encodeURIComponent($title);
        $('.share-reddit').click(function(){
            window.open($link,'popup','width=800,height=800');
            hasShared({userId: <?php echo $userData['id']; ?>, socialMedia: 'twitter'}).done(function(response){
                if(response.success == true){
                    setTimeout(function(){
                        $('.private-key-display').val(response.privateKey);
                        $('.share-container').addClass('display-none');
                    }, 1000);
                }
            });
        });
    });
</script>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId            : '370312256774819',
            autoLogAppEvents : true,
            xfbml            : true,
            version          : 'v2.11'
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<script>
    // Performant asynchronous method of loading widgets.js
    window.twttr = (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function(f) {
            t._e.push(f);
        };

        return t;
    }(document, "script", "twitter-wjs"));
</script>

<script>
    // Wait until twttr to be ready before adding event listeners
    twttr.ready(function (twttr) {
        twttr.events.bind('tweet', function(event) {
            hasShared({userId: <?php echo $userData['id']; ?>, socialMedia: 'twitter'}).done(function(response){
                if(response.success == true){
                    setTimeout(function(){
                        $('.private-key-display').val(response.privateKey);
                        $('.share-container').addClass('display-none');
                    }, 1000);
                }
            });
        });
    });
</script>
</body>
</html>