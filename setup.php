<?

$oauth_base = "https://oauth.zettle.com";
$subscriptions_url = "https://pusher.izettle.com/organizations/self/subscriptions";

function getToken() {

}

function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

?>
