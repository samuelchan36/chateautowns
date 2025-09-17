<?php   
// This software code is made available "AS IS" without warranties of any
// kind.  You may copy, display, modify and redistribute the software
// code either by itself or as incorporated into your code; provided that
// you do not remove any proprietary notices.  Your use of this software
// code is at your own risk and you waive any claim against Amazon
// Digital Services, Inc. or its affiliates with respect to your use of
// this software code. (c) 2006 Amazon Digital Services, Inc. or its
// affiliates.

// Notes:
// - This relies on HTTP_Request from pear.php.net, but the latest version
//   has a bug; see note below on how to fix it (one-character change).
// - A real implementation would stream data in and out of S3; this
//   proof-of-concept stores complete files/responses on the PHP server
//   before passing them on to Amazon or to the web browser.
// - Because of the above fact, large files will require increasing PHP's
//   various settings governing the size of uploaded files.

/**
 * A PHP5 class for interfacing with the Amazon S3 API
 *
 * This is a modification and extension of the Storage3 class built by apokalyptik (apokalyptik@apokalyptik.com) and sponsored by Ookles, Inc.
 * Detials about the original class can be found here: http://freshmeat.net/projects/storage3
*/

// grab this with "pear install Crypt_HMAC"
require_once 'Crypt/HMAC.php';

// grab this with "pear install --onlyreqdeps HTTP_Request"
require_once 'HTTP/Request.php';

// Note that version HTTP_Request 1.3.0 has a BUG in it!  Change line

// 765 from:
//            (HTTP_REQUEST_METHOD_POST != $this->_method && empty($this->_postData) && empty($this->_postFiles))) {
// to:
//            (HTTP_REQUEST_METHOD_POST == $this->_method && empty($this->_postData) && empty($this->_postFiles))) {
// Without this change PUTs with non-empty content-type will fail!

class s3{

var $serviceUrl;
var $accessKeyId;
var $secretKey;
var $responseString;
var $responseCode;
var $parsed_xml;

/**
 * Constructor
 *
 * Takes ($accessKeyId, $secretKey, $serviceUrl)
 *
 * - [str] $accessKeyId: Your AWS Access Key Id
 * - [str] $secretKey: Your AWS Secret Access Key
 * - [str] $serviceUrl: OPTIONAL: defaults: http://s3.amazonaws.com/
 *
*/
function __construct($accessKeyId, $secretKey, $serviceUrl="http://s3.amazonaws.com/") {
	$this->serviceUrl=$serviceUrl;
	$this->accessKeyId=$accessKeyId;
	$this->secretKey=$secretKey;
}

/**
 * createBucket -- creates a bucket.
 *
 * Takes ($bucket, $acl)
 *
 * - [str] $bucket: the bucket you wish to create
 * - [str] $acl: the access control policy (OPTIONAL: defaults to 'private')
*/
function createBucket($bucket, $acl = 'private') {
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "PUT\n\n\n$httpDate\nx-amz-acl:$acl\n/$bucket";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl . $bucket);
	$req->setMethod("PUT");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->addHeader("x-amz-acl", $acl);
	$req->sendRequest();
	$this->responseCode=$req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * deleteBucket -- Deletes an empty bucket.
 *
 * Takes ($bucket)
 *
 * - [str] $bucket: the bucket you wish to delete
*/
function deleteBucket($bucket) {
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "DELETE\n\n\n$httpDate\n/$bucket";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl . $bucket);
	$req->setMethod("DELETE");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 204) {
		return true;
	} else {
		return false;
	}
}

/**
 * listBuckets -- Lists all buckets.
*/
function listBuckets() {
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign="GET\n\n\n$httpDate\n/";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl);
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if($this->responseCode == 200){
		return true;
	}
	else{
		return false;
	}
}

/**
 * listKeys -- Lists keys in a bucket.
 *
 * Takes ($bucket [,$marker, $prefix, $delimeter, $maxKeys]) -- $marker, $prefix, $delimeter, $maxKeys are independently optional
 *
 * - [str] $bucket: the bucket whose keys are to be listed
 * - [str] $marker: keys returned will occur lexicographically after $marker (OPTIONAL: defaults to false)
 * - [str] $prefix: keys returned will start with $prefix (OPTIONAL: defaults to false)
 * - [str] $delimeter: keys returned will be of the form "$prefix[some string]$delimeter" (OPTIONAL: defaults to false)
 * - [str] $maxKeys: number of keys to be returned (OPTIONAL: defaults to 1000 - maximum allowed by service)
*/
function listKeys($bucket, $marker=FALSE, $prefix=FALSE, $delimiter=FALSE, $maxKeys) {
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "GET\n\n\n$httpDate\n/$bucket";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl.$bucket."?max-keys={$maxKeys}&marker={$marker}&prefix={$prefix}&delimiter={$delimeter}");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if($this->responseCode == 200){
		return true;
	} else {
		return false;
	}
}

/**
 * getBucketACL -- Gets bucket access control policy.
 *
 * Takes ($bucket)
 *
 * - [str] $bucket: the bucket whose acl you want
*/
function getBucketACL($bucket){
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "GET\n\n\n$httpDate\n/$bucket/?acl";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl.$bucket.'/?acl');
	$req->setMethod("GET");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * setBucketACL -- Sets bucket access control policy to one of Amazon S3 canned policies.
 *
 * Takes ($bucket, $acl)
 *
 * - [str] $bucket: the bucket whose acl is to be set
 * - [str] $acl: one of the Amazon S3 canned access policies
*/
function setBucketACL($bucket, $acl){
	$httpDate = gmdate(DATE_RFC822);
    $stringToSign = "PUT\n\n\n{$httpDate}\nx-amz-acl:$acl\n/$bucket/?acl";
    $signature = $this->constructSig($stringToSign);
    $req =& new HTTP_Request($this->serviceUrl.$bucket.'/?acl');
    $req->setMethod("PUT");
    $req->addHeader("Date", $httpDate);
    $req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->addHeader("x-amz-acl", $acl);
    $req->sendRequest();
	$this->responseCode=$req->getResponseCode();
    $this->responseString=$req->getResponseBody();
	$this->parsed_xml=simplexml_load_string($this->responseString);
    if ($this->responseCode == 200) {
    	return true;
    } else {
        return false;
    }
}

/**
 * grantLoggingPermission -- allows logs to be written to the bucket.
 *
 * Takes ($bucket)
 *
 * - [str] $bucket
*/
function grantLoggingPermission($bucket){
	$httpDate = gmdate(DATE_RFC822);
    $stringToSign = "PUT\n\ntext/plain\n{$httpDate}\n/$bucket/?acl";
    $signature = $this->constructSig($stringToSign);
    $req =& new HTTP_Request($this->serviceUrl.$bucket.'/?acl');
    $req->setMethod("PUT");
	//The body below is meant in part as an example of how to grant specific groups certain permissions. In practice, use the getBucketACL method to obtain
	//the owner Id and display name. The <ID> and <DisplayName> elements will have to be filled in accordingly here. These can be obtained by using the Get
	//Object ACL or Get Bucket ACL method on an existing object or bucket respectively.Note that this was written with See the the idea that a new bucket
	//first be created with the sole purpose of housing logs. As is, this method will overwrite the ACP for an existing bucket. See the Developer Guide for
	//more details on granting permissions.
	$body = "<?xml version='1.0' encoding='UTF-8'?>
			<AccessControlPolicy xmlns='http://s3.amazonaws.com/doc/2006-03-01/'>
    			<Owner>
        			<ID></ID>
        			<DisplayName></DisplayName>
    			</Owner>
				<AccessControlList>
					<Grant>
						<Grantee xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:type='CanonicalUser'>
							<ID></ID>
							<DisplayName></DisplayName>
						</Grantee>
						<Permission>FULL_CONTROL</Permission>
					</Grant>
					<Grant>
						<Grantee xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:type='Group'>
							<URI>http://acs.amazonaws.com/groups/s3/LogDelivery</URI>
						</Grantee>
						<Permission>WRITE</Permission>
					</Grant>
					<Grant>
						<Grantee xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:type='Group'>
							<URI>http://acs.amazonaws.com/groups/s3/LogDelivery</URI>
						</Grantee>
						<Permission>READ_ACP</Permission>
				</Grant>
			</AccessControlList>
		</AccessControlPolicy>";
	$req->setBody($body);
    $req->addHeader("Date", $httpDate);
	$req->addHeader("Content-Type", "text/plain");
    $req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
    $req->sendRequest();
	$this->responseCode=$req->getResponseCode();
    $this->responseString=$req->getResponseBody();
	$this->parsed_xml=simplexml_load_string($this->responseString);
    if ($this->responseCode == 200) {
    	return true;
    } else {
        return false;
    }
}

/**
 * getLoggingStatus -- gets a bucket's logging status (is logging enabled?).
 *
 * Takes ($bucket)
 *
 * - [str] $bucket
*/
function getLoggingStatus($bucket){
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "GET\n\n\n$httpDate\n/$bucket?logging";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl.$bucket.'?logging');
	$req->setMethod("GET");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * enableLogging -- Turns logging feature on/off for a given bucket.
 *
 * Takes ($bucket, $targetBucket, $targetPrefix, $switch)
 *
 * - [str] $bucket: Bucket for which logging is to be turned on.
 * - [str] $targetBucket: Bucket to which logs will be sent.
 * - [str] $targetPrefix: Prefix for key of each log entry.
 * - [bool] $switch: True to turn logging on, False to turn it off.
*/
function enableLogging($bucket, $targetBucket, $targetPrefix, $switch) {
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "PUT\n\ntext/xml\n{$httpDate}\n/$bucket?logging";
	$signature = $this->constructSig($stringToSign);
	$req = & new HTTP_Request($this->serviceUrl.$bucket.'?logging');
	$req->setMethod("PUT");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Content-Type", "text/xml");
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	if($switch){
		$body = "<?xml version='1.0' encoding='UTF-8'?>
					<BucketLoggingStatus xmlns='http://s3.amazonaws.com/doc/2006-03-01/'>
						<LoggingEnabled>
							<TargetBucket>$targetBucket</TargetBucket>
							<TargetPrefix>$targetPrefix</TargetPrefix>
						</LoggingEnabled>
					</BucketLoggingStatus>";
	} else {
		$body =	"<?xml version='1.0' encoding='UTF-8'?>
					<BucketLoggingStatus xmlns='http://s3.amazonaws.com/doc/2006-03-01/'>
					</BucketLoggingStatus>";
	}
	$req->setBody($body);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * putObject -- Writes a file to a bucket.
 *
 * Takes ($bucket, $key, $filePath, $contentType, $contentLength [,$acl, $metadataArray, $md5])
 *
 * - [str] $bucket: the bucket into which file will be written
 * - [str] $key: key of written file
 * - [str] $contentType: file content type
 * - [str] $contentLength: file content length
 * - [str] $acl: access control policy of file (OPTIONAL: defaults to 'private')
 * - [str] $metadataArray: associative array containing user-defined metadata (name=>value) (OPTIONAL)
 * - [bool] $md5: the MD5 hash of the object (OPTIONAL)
*/
function putObject($bucket, $key, $filePath, $contentType, $contentLength, $acl, $metadataArray, $md5){
	sort($metadataArray);
	$resource = "$bucket/$key";
	$resource = urlencode($resource);
	$req = & new HTTP_Request($this->serviceUrl.$resource);
	$req->setMethod("PUT");
	$httpDate = gmdate(DATE_RFC822);
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Content-Type", $contentType);
	$req->addHeader("Content-Length", $contentLength);
	$req->addHeader("x-amz-acl", $acl);
	if($md5){
		$MD5 = $this->hex2b64(md5_file($filePath));
		$req->addHeader("Content-MD5", $MD5);
	}
	$req->setBody(file_get_contents($filePath));
	$stringToSign="PUT\n$MD5\n$contentType\n$httpDate\nx-amz-acl:$acl\n";
	$metadataArray  = array();
	foreach($metadataArray as $current){
		if($current!=""){
			$stringToSign.="x-amz-meta-$current\n";
			$header = substr($current,0,strpos($current,':'));
			$meta = substr($current,strpos($current,':')+1,strlen($current));
			$req->addHeader("x-amz-meta-$header", $meta);
		}
	}
	$stringToSign.="/$resource";
	$signature = $this->constructSig($stringToSign);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
//	die2($req);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * deleteObject -- Deletes an object.
 *
 * Takes ($bucket, $key)
 *
 * - [str] $bucket: the bucket from which file will be deleted
 * - [str] $key: key of file to be deleted
*/
function deleteObject($bucket, $key) {
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "DELETE\n\n\n$httpDate\n/$bucket/$key";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl.$bucket.'/'.$key);
	$req->setMethod("DELETE");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 204) {
		return true;
	} else {
		return false;
	}
}

/**
 * getObjectACL -- gets an objects access control policy.
 *
 * Takes ($bucket, $key)
 *
 * - [str] $bucket
 * - [str] $key
*/
function getObjectACL($bucket, $key){
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "GET\n\n\n$httpDate\n/$bucket/$key?acl";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl.$bucket.'/'.$key.'?acl');
	$req->setMethod("GET");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * setObjectACL -- sets objects access control policy to one of Amazon S3 canned policies.
 *
 * Takes ($bucket, $key, $acl)
 *
 * - [str] $bucket
 * - [str] $key
 * - [str] $acl -- One of canned access control policies.
*/
function setObjectACL($bucket, $key, $acl){
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "PUT\n\n\n$httpDate\nx-amz-acl:$acl\n/$bucket/$key?acl";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl.$bucket.'/'.$key.'?acl');
	$req->setMethod("PUT");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->addHeader("x-amz-acl", $acl);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * getMetadata -- Gets the metadata associated with an object.
 *
 * Takes ($bucket, $key)
 *
 * - [str] $bucket
 * - [str] $key
*/
function getMetadata($bucket, $key){
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "HEAD\n\n\n$httpDate\n/$bucket/$key";
	$signature = $this->constructSig($stringToSign);
	$req =& new HTTP_Request($this->serviceUrl.$bucket.'/'.$key);
	$req->setMethod("HEAD");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	$this->headers = $req->getResponseHeader();
	$this->parsed_xml = simplexml_load_string($this->responseString);
	if ($this->responseCode == 200) {
		return true;
	} else {
		return false;
	}
}

/**
 * getObjectAsString -- Returns object as a string.
 *
 * Takes ($bucket, $key)
 *
 * - [str] $bucket
 * - [str] $key
*/
function getObjectAsString($bucket, $key) {
	$httpDate = gmdate(DATE_RFC822);
	$stringToSign = "GET\n\n\n{$httpDate}\n/$bucket/$key";
	$signature = $this->constructSig($stringToSign);
	$req = & new HTTP_Request($this->serviceUrl.$bucket.'/'.$key);
	$req->setMethod("GET");
	$req->addHeader("Date", $httpDate);
	$req->addHeader("Authorization", "AWS " . $this->accessKeyId . ":" . $signature);
	$req->sendRequest();
	$this->responseCode = $req->getResponseCode();
	$this->responseString = $req->getResponseBody();
	if ($this->responseCode == 200) {
		return true;
	} else {
	$this->parsed_xml = simplexml_load_string($this->responseString);
		return false;
	}
}

/**
 * queryStringGet -- returns a signed URL to get object
 *
 * Takes ($bucket, $key, $expires)
 *
 * - [str] $bucket
 * - [str] $key
 * - [str] $expires - signed URL with expire after $expires seconds
*/
function queryStringGet($bucket, $key, $expires){
	$expires = time() + $expires;
	$stringToSign = "GET\n\n\n$expires\n/$bucket/$key";
	$signature = urlencode($this->constructSig($stringToSign));
	$queryString = "<a href='http://s3.amazonaws.com/$bucket/$key?AWSAccessKeyId=$this->accessKeyId&Expires=$expires&Signature=$signature'>$bucket/$key</a>";
	return $queryString;
}

function hex2b64($str) {
	$raw = '';
	for ($i=0; $i < strlen($str); $i+=2) {
		$raw .= chr(hexdec(substr($str, $i, 2)));
	}
	return base64_encode($raw);
}

function constructSig($str) {
	$hasher =& new Crypt_HMAC($this->secretKey, "sha1");
	$signature = $this->hex2b64($hasher->hash($str));
	return($signature);
}

	}
?>