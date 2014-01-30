<?php
##################
#  CyberSource Hosted Order Page library.  Inserts fields into the
#  checkout form for posting data to the CyberSource Hosted Order
#  Page.



function php_hmacsha1($data, $key) {
  $klen = strlen($key);
  $blen = 64;
  $ipad = str_pad("", $blen, chr(0x36));
  $opad = str_pad("", $blen, chr(0x5c));

  if ($klen <= $blen) {
    while (strlen($key) < $blen) {
      $key .= "\0";
    }				#zero-fill to blocksize
  } else {
    $key = cybs_sha1($key);	#if longer, pre-hash key
  }
  $key = str_pad($key, strlen($ipad) + strlen($data), "\0");
  return cybs_sha1(($key ^ $opad) . cybs_sha1($key ^ $ipad . $data));
}

# calculates SHA-1 digest of the input string
# cleaned up from John Allen's "SHA in 8 lines of perl5"
# at http://www.cypherspace.org/~adam/rsa/sha.html
#
# returns the hash in a (binary) string

function cybs_sha1($in) {
  return pack ("H*", sha1 ($in));
  $indx = 0;
  $chunk = "";

  $A = array(1732584193, 4023233417, 2562383102,  271733878, 3285377520);
  $K = array(1518500249, 1859775393, 2400959708, 3395469782);
  $a = $b = $c = $d = $e = 0;
  $l = $p = $r = $t = 0;

  do{
    $chunk = substr($in, $l, 64);
    $r = strlen($chunk);
    $l += $r;

    if ($r<64 && !$p++) {
      $r++;
      $chunk .= "\x80";
    }
    $chunk .= "\0\0\0\0";
    while (strlen($chunk) % 4 > 0) { 
      $chunk .= "\0";
    }
    $len = strlen($chunk) / 4;
    if ($len > 16) $len = 16;
    $fmt = "N" . $len;
    $W = array_values(unpack($fmt, $chunk));
    if ($r < 57 ) { 
      while (count($W) < 15) {
	array_push($W, "\0");
      }
      $W[15] = $l*8;
    }

    for ($i = 16; $i <= 79; $i++) {
      $v1 = d($W, $i-3);
      $v2 = d($W, $i-8);
      $v3 = d($W, $i-14);
      $v4 = d($W, $i-16);
      array_push($W, L($v1 ^ $v2 ^ $v3 ^ $v4, 1));
    }

    list($a,$b,$c,$d,$e)=$A;

    for ($i = 0; $i<=79; $i++) {
      $t0 = 0;
      switch(intval($i/20)) {
	case 1:
	case 3:
	$t0 = F1($b, $c, $d);
	break;
	case 2:
	$t0 = F2($b, $c, $d);
	break;
      default:
	$t0 = F0($b, $c, $d);
	break;
      }
      $t = M($t0 + $e  + d($W, $i) + d($K, $i/20) + L($a, 5));
      $e = $d;
      $d = $c;
      $c = L($b,30);
      $b = $a;
      $a = $t;
    }

    $A[0] = M($A[0] + $a);
    $A[1] = M($A[1] + $b);
    $A[2] = M($A[2] + $c);
    $A[3] = M($A[3] + $d);
    $A[4] = M($A[4] + $e);

  }while ($r>56);
  $v = pack("N*", $A[0], $A[1], $A[2], $A[3], $A[4]);
  return $v;
}

#### Ancillary routines used by sha1

function dd($x) {
  if (defined($x)) return $x;
  return 0;
}

function d($arr, $x) {
  if ($x < count($arr)) return $arr[$x];
  return 0;
}

function F0($b, $c, $d) {
  return $b & ($c ^ $d) ^ $d;
}

function F1($b, $c, $d) {
  return $b ^ $c ^ $d;
}

function F2($b, $c, $d) {
  return ($b | $c) & $d | $b & $c;
}

# ($num)
function M($x) {
  $m = 1+~0;
  if ($m == 0) return $x;
  return($x - $m * intval($x/$m));
}

# ($string, $count)
function L($x, $n) { 
  return ( ($x<<$n) | ((pow(2, $n) - 1) & ($x>>(32-$n))) );
}

####
#### end of HMAC SHA1 implementation #####




####
#### HOP functions
#### Copyright 2003, CyberSource Corporation.  All rights reserved.
####

function getmicrotime(){ 
  list($usec, $sec) = explode(" ",microtime());
  $usec = (int)((float)$usec * 1000);
  while (strlen($usec) < 3) { $usec = "0" . $usec; }
  return $sec . $usec;
}


function hopHash($data, $key) {
    return base64_encode(php_hmacsha1($data, $key));
}

function getMerchantID() { return  "v6981885"; }
function getPublicKey()  { return "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCfZ6U7aRcOgUbMLK0T35ekuYwaPPZyb11AOuSHfs65Yegc9AtwQQY4S0Ho3wASBKPgMvweUjZLdGTWcZJ2B5utuJILuCL8h6im/OjrC6xHQ5wPDCt8gDEU20qRJDKZE97FalBRqy6NEo5KnBYYwCYAx8cdmYWlvMWup6wO3cjcuQIDAQAB"; }
function getPrivateKey() { return "MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAJ9npTtpFw6BRswsrRPfl6S5jBo89nJvXUA65Id+zrlh6Bz0C3BBBjhLQejfABIEo+Ay/B5SNkt0ZNZxknYHm624kgu4IvyHqKb86OsLrEdDnA8MK3yAMRTbSpEkMpkT3sVqUFGrLo0SjkqcFhjAJgDHxx2ZhaW8xa6nrA7dyNy5AgMBAAECgYBhKGIsoPQdsiv9vGlNyKV8EF/VijzIKRqUUJEECQVlNsisBQ+HvwpUe4JTtsArO0rLFyhWLtPLD8l6b0TOzfpTZPT3QXUz89k0Lu5VqTXt+JmFsVSmzoQ71gZnEzC0KYY9yvT8n7UOaXvX2xT+EBh++kBYRrnrKpawKA6wJHIxAQJBAM3WMU+jlXoeSyaifAdTSrGnQwijR6rftYPSK4mmiJHKTj/CnE4n/bd/XqY6NBFGWqduDUR0AhDny/1FI7aBiFkCQQDGQKYXIOH8hIUluMuM4jES+1AUxQGCq2hWhtQyX5DbHxOSb3YnvQfqMOlxNDSvCWOZHQ4BZOqHxE8549GRj2thAkBvEF33HMsw41ulcxL9WOH6zceLx56KAr+Ma0D2uxcBo2lpWfbA7S8nbeCCTPF/SLjtkqIyB2599LTK6h2FAouxAkEAubUpYBTvW6mPR8yY77maGpvLYglA5vRFYngeIamfeYLcxJkgZw0NcvSqV959U1FKOM0cp3Wus8rSI/oh/2YHQQJAap0C64vXCMDkRuDx/SGrnt20EjxAzf6yiX0ILt+DisRD/xswSebuQLsEnhlKstXakYNnf8mAl5cdS82fxZOfhg=="; }
function getSerialNumber() { return "1866084389730176045314"; }

#### HOP integration function
function InsertSignature($amount, $currency) {
  if(!isset($amount)){ $amount = "0.00"; }
  if(!isset($currency)){ $currency = "usd"; }
  $merchantID = getMerchantID();
  $timestamp = getmicrotime();
  $data = $merchantID . $amount . $currency . $timestamp;
  $pub = getPublicKey();
  $serialNumber = getSerialNumber();
  $pub_digest = hopHash($data, $pub);

  echo('<input type="hidden" name="amount" value="' . $amount . '">' . "\n");
  echo('<input type="hidden" name="currency" value="' . $currency . '">' . "\n");
  echo('<input type="hidden" name="orderPage_timestamp" value="' . $timestamp . '">' . "\n");
  echo('<input type="hidden" name="merchantID" value="' . $merchantID . '">' . "\n");
  echo('<input type="hidden" name="orderPage_signaturePublic" value="' . $pub_digest . '">' . "\n");
  echo('<input type="hidden" name="orderPage_version" value="4">' . "\n");
  echo('<input type="hidden" name="orderPage_serialNumber" value="' . $serialNumber . '">' . "\n");
}

function InsertSubscriptionSignature($subscriptionAmount, 
    $subscriptionStartDate, 
    $subscriptionFrequency, 
    $subscriptionNumberOfPayments,
    $subscriptionAutomaticRenew){
  if(!isset($subscriptionFrequency)){ return; }
  if(!isset($subscriptionAmount)){ $subscriptionAmount = "0.00"; }
  if(!isset($subscriptionStartDate)){ $subscriptionStartDate = "00000000"; }
  if(!isset($subscriptionNumberOfPayments)){ $subscriptionNumberOfPayments = "0"; }
  if(!isset($subscriptionAutomaticRenew)){ $subscriptionAutomaticRenew = "true"; }
  $data = $subscriptionAmount . $subscriptionStartDate . $subscriptionFrequency . $subscriptionNumberOfPayments . $subscriptionAutomaticRenew;
  $pub = getPublicKey();
  $pub_digest = hopHash($data, $pub);
  echo('<input type="hidden" name="recurringSubscriptionInfo_amount" value="' . $subscriptionAmount . '">' . "\n");
  echo('<input type="hidden" name="recurringSubscriptionInfo_numberOfPayments" value="' . $subscriptionNumberOfPayments . '">' . "\n");
  echo('<input type="hidden" name="recurringSubscriptionInfo_frequency" value="' . $subscriptionFrequency . '">' . "\n");
  echo('<input type="hidden" name="recurringSubscriptionInfo_automaticRenew" value="' . $subscriptionAutomaticRenew . '">' . "\n");
  echo('<input type="hidden" name="recurringSubscriptionInfo_startDate" value="' . $subscriptionStartDate . '">' . "\n");
  echo('<input type="hidden" name="recurringSubscriptionInfo_signaturePublic" value="' . $pub_digest . '">' . "\n");
}
function InsertSubscriptionIDSignature($subscriptionID){
  if(!isset($subscriptionID)){ return; }
  $pub = getPublicKey();
  $pub_digest = hopHash($subscriptionID, $pub);
  echo('<input type="hidden" name="paySubscriptionCreateReply_subscriptionID" value="' . $subscriptionID . '">' . "\n");
  echo('<input type="hidden" name="paySubscriptionCreateReply_subscriptionIDPublicSignature" value="' . $pub_digest . '">' . "\n");
}

function VerifySignature($data, $signature) {
    $pub = getPublicKey();
    $pub_digest = hopHash($data, $pub);
    return strcmp($pub_digest, $signature) == 0;
}


?>
