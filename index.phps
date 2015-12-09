 <?php
//
// Config
// 

$clientid        = '';    // Spotify App Client ID (see https://developer.spotify.com/my-applications/)
$clientsecret    = '';    // Spotify App Client Secret (see https://developer.spotify.com/my-applications/)
$redirect_uri    = '';    // Spotify App Redirect URI (make sure, the path to this script is inside the list on https://developer.spotify.com/my-applications/)
$state            = 'supersecretkey';    // Has to be set to something

include('config.php');    // has to be removed!!!
//
// Functions
//

function getCode($clientid, $redirect_uri, $state)
{
    header('Location: '
        .'https://accounts.spotify.com/authorize'
        .'?client_id='.$clientid
        .'&response_type=code'
        .'&redirect_uri='.$redirect_uri
        .'&state='.$state
        .'&scope=user-read-private%20playlist-read-private%20playlist-read-collaborative%20playlist-modify-public%20playlist-modify-private'
        .'&show_dialog=true');
}
function getToken($code, $clientid, $clientsecret, $redirect_uri)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,
        'https://accounts.spotify.com/api/token'
        .'?grant_type=authorization_code'
        .'&code='.$code
        .'&redirect_uri='.$redirect_uri
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode($clientid.':'.$clientsecret)));
    return json_decode(curl_exec($ch), true);
}

//
// Logic
//

if (isset($_GET['state']) && $_GET['state'] == $state)
{
    if (isset($_GET['error'])) exit('ERROR: '.$_GET['error']);
    elseif (!isset($_GET['code'])) exit('ERROR');
    else
    {
        $infos = array('refresh_token' => getToken($_GET['code'], $clientid, $clientsecret, $redirect_uri)['refresh_token'],
                    'appkey' => base64_encode($clientid.':'.$clientsecret));
    }
}
else
{
    getCode($clientid, $redirect_uri, $state);
}

//
// HTML
//
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <h1 class="text-center">PhantomBot Spotify Connection</h1>
            </div>
            <hr />
            <div class="row">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="refresh_token" class="col-sm-2 control-label">Refresh Token</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="refresh_token" value="<?php echo $infos['refresh_token'] ?>" />
                                <div class="input-group-addon btn btn-copy" data-clipboard-text="<?php echo $infos['refresh_token'] ?>" title="Copy to Clipboard">
                                    <span class="glyphicon glyphicon-copy" aria-hidden="true" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="appkey" class="col-sm-2 control-label">App Key</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="appkey" value="<?php echo $infos['appkey'] ?>" />
                                <div class="input-group-addon btn btn-copy" data-clipboard-text="<?php echo $infos['appkey'] ?>" title="Copy to Clipboard">
                                    <span class="glyphicon glyphicon-copy" aria-hidden="true" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.min.js"></script>
        <script type="text/javascript">
            ZeroClipboard.config( { swfPath: "https://cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.2.0/ZeroClipboard.swf" } );
            var client = new ZeroClipboard( $('.btn-copy'));
        </script>
    </body>
</html>
