<?php
/**********************

Created by Ákos Nikházy

use it as you please

I used this script on my website, so
I can open bit.ly URLs, that was
blocked by workplace firewall.


**********************/

ob_start();

$url = '';

function oneLine($in)
{
	return preg_replace('/^\s+|\n|\r|\t|\s+$/m', '', $in);
	// return $in; //ez akkor kell ha tesztelésből látni akarjuk a formázott kódot
}
function errorCodes($code)
{
	$http_status_codes = array(100 => 'Informational: Continue',
							   101 => 'Informational: Switching Protocols',
							   102 => 'Informational: Processing',
							   200 => 'Successful: OK',
							   201 => 'Successful: Created',
							   202 => 'Successful: Accepted',
							   203 => 'Successful: Non-Authoritative Information',
							   204 => 'Successful: No Content',
							   205 => 'Successful: Reset Content',
							   206 => 'Successful: Partial Content',
							   207 => 'Successful: Multi-Status',
							   208 => 'Successful: Already Reported',
							   226 => 'Successful: IM Used',
							   300 => 'Redirection: Multiple Choices',
							   301 => 'Redirection: Moved Permanently',
							   302 => 'Redirection: Found',
							   303 => 'Redirection: See Other',
							   304 => 'Redirection: Not Modified',
							   305 => 'Redirection: Use Proxy',
							   306 => 'Redirection: Switch Proxy',
							   307 => 'Redirection: Temporary Redirect',
							   308 => 'Redirection: Permanent Redirect',
							   400 => 'Client Error: Bad Request',
							   401 => 'Client Error: Unauthorized',
							   402 => 'Client Error: Payment Required',
							   403 => 'Client Error: Forbidden',
							   404 => 'Client Error: Not Found',
							   405 => 'Client Error: Method Not Allowed',
							   406 => 'Client Error: Not Acceptable',
							   407 => 'Client Error: Proxy Authentication Required',
							   408 => 'Client Error: Request Timeout',
							   409 => 'Client Error: Conflict',
							   410 => 'Client Error: Gone',
							   411 => 'Client Error: Length Required',
							   412 => 'Client Error: Precondition Failed',
							   413 => 'Client Error: Request Entity Too Large',
							   414 => 'Client Error: Request-URI Too Long',
							   415 => 'Client Error: Unsupported Media Type',
							   416 => 'Client Error: Requested Range Not Satisfiable',
							   417 => 'Client Error: Expectation Failed',
							   418 => 'Client Error: I\'m a teapot',
							   419 => 'Client Error: Authentication Timeout',
							   420 => 'Client Error: Enhance Your Calm',
							   422 => 'Client Error: Unprocessable Entity',
							   423 => 'Client Error: Locked',
							   424 => 'Client Error: Failed Dependency',
							   425 => 'Client Error: Unordered Collection',
							   426 => 'Client Error: Upgrade Required',
							   428 => 'Client Error: Precondition Required',
							   429 => 'Client Error: Too Many Requests',
							   431 => 'Client Error: Request Header Fields Too Large',
							   444 => 'Client Error: No Response',
							   449 => 'Client Error: Retry With',
							   450 => 'Client Error: Blocked by Windows Parental Controls',
							   451 => 'Client Error: Redirect',
							   494 => 'Client Error: Request Header Too Large',
							   495 => 'Client Error: Cert Error',
							   496 => 'Client Error: No Cert',
							   497 => 'Client Error: HTTP to HTTPS',
							   499 => 'Client Error: Client Closed Request',
							   500 => 'Server Error: Internal Server Error',
							   501 => 'Server Error: Not Implemented',
							   502 => 'Server Error: Bad Gateway',
							   503 => 'Server Error: Service Unavailable',
							   504 => 'Server Error: Gateway Timeout',
							   505 => 'Server Error: HTTP Version Not Supported',
							   506 => 'Server Error: Variant Also Negotiates',
							   507 => 'Server Error: Insufficient Storage',
							   508 => 'Server Error: Loop Detected',
							   509 => 'Server Error: Bandwidth Limit Exceeded',
							   510 => 'Server Error: Not Extended',
							   511 => 'Server Error: Network Authentication Required',
							   598 => 'Server Error: Network read timeout error',
							   599 => 'Server Error: Network connect timeout error'
							  );

	return $http_status_codes[$code];
}

function is_url($uri)
{
    
	if(preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$uri))
		return $uri;
    
    return false;
	
}

function get_html_title($html)
{
    preg_match("/\<title.*\>(.*)\<\/title\>/isU", $html, $matches);
    return $matches[1];
}

if(isset($_GET['url']))
{

	if(is_url($_GET['url']))
	{


		$cr = curl_init($_GET['url']); 
		
			curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cr, CURLOPT_FOLLOWLOCATION, 3);     
		
		$result = curl_exec($cr); 
		$info = curl_getinfo($cr);
		
		$url = $info["url"];
		$tags = get_meta_tags($_GET['url']);
		

	}
}
?>

<!doctype html>
<html lang="en">

	<head>
		<title>Check Short URL</title>
		
		<meta property="og:title" content="Open Short URL">
		<meta property="og:description" content="Short URL expander. Open any short URL. Good if your proxy blocks these or your want to see what lies behind a short URL before you visit it!">
		<meta property="og:image" content="https://yzahk.in/open-short-url/images/header.jpg">
		
		<base target="_blank">
	</head>
	<body>
		<h1>Check Short URL</h1>
		
		<?php 
			if(isset($_GET['url']) && is_url($_GET['url']))
			{
		?>
		
		<div id="openurl">
			
			<p>The <?php echo $_GET['url']; ?> goes to  <a href="<?php echo $url; ?>"><?php echo $url; ?></a> address.</p>

			<p>Check if this site is safe on <a href="https://transparencyreport.google.com/safe-browsing/search?url=<?php echo $url; ?>">Google</a>.</p>

			<p>Its HTML Title is <?php echo get_html_title($result); ?></p>

			<p>Its description says <?php echo $tags['description'];?></p>

			<p>It responded with http code <?php echo $info['http_code']; ?> meaning <?php echo errorCodes($info['http_code']); ?></p>

		</div>
		
		<?php } ?>
		
		<div id="content">
			
			<form method="GET">
				<label>Short URL: <input type="url" name="url" placeholder="http://stg.here"></label> <input type="submit">
			</form>
		
		</div>

		
	</body>
</html>
<?php

$output = ob_get_contents();

ob_end_clean();

echo oneLine( $output);

?>
