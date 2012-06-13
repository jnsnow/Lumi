<?php

echo "Loaded Functions!\n";

/*
 joinServer($k = NULL)
 raw($str,$k = NULL)
 hopServer($k = NULL)
 quitServer($k = NULL)
 destroyServer($k = NULL)
 irc_quit($msg = '', $k = NULL)
 rehashActiveSockets()
 elog($str,$k = NULL)
 set($key,$value,$k = NULL)
 irc_nick($nick,$k = NULL)
 strip($str)
 ts()
 irc_msg($dest,$msg,$k = NULL)
 irc_notice($dest,$msg,$k = NULL)
 irc_ctcp($dest,$msg,$k = NULL)
 irc_ctcpreply($dest,$msg,$k = NULL)
 irc_join($msg,$k = NULL)
 irc_tries($k)
 irc_delay($k)
 irc_timeout($k)
*/


function joinServer($k = NULL) { 
  if ($k === NULL) { global $k; }
  global $s, $sockets;
  set('sleep','',$k); //reset the sleep timer.

  set('attempts',($s[$k]['attempts'] + 1));
  if ($s[$k]['attempts'] > irc_tries($k)) {
    elog("-- Max attempts reached, giving up.");
    set('status','off');
    set('attempts','0');
    return FALSE;
  }

  $fulladdr = $s[$k]['address'].":".$s[$k]['port'];
  if ($s[$k]['ssl']) { $fulladdr = 'ssl://' . $fulladdr; }
  elog('-- Attempt '.$s[$k]['attempts'].'/'.irc_tries($k).' Trying '.$fulladdr.' ... ');

  set('status','dialing');
  $socket = @stream_socket_client($fulladdr,$errno,$errstr,irc_timeout($k));
  if ((!$socket) || ($errno > 0)) {
    elog("-- $errstr ($errno)",$k);
    set('status',NULL);
    return FALSE;
  }
  elog("-- ... OK!");
  set('attempts',0);

  rehashActiveSockets();
  /* Already re-implemented */
  // FuckingConnect();
  return TRUE;
}

function raw($str,$k = NULL) {
  if ($k === NULL) { global $k; }
  global $s;
  if (feof($s[$k]['socket'])) { elog("-- Could not write '$str' to socket. Declaring socket dead."); hopServer($k); return FALSE; }
  fwrite($s[$k]['socket'], "$str\r\n");
  elog("-> $str",$k);
}

function hopServer($k = NULL) {
  if ($k === NULL) { global $k; }
  irc_quit("I want to ride my bicycle!",$k);
  set('status','sleep',$k);
  set('sleep',(time() + irc_delay($k)),$k);
  elog("-- Giving this connection a rest for ".irc_delay($k)." seconds.");
}

function quitServer($k = NULL) {
  if ($k === NULL) { global $k; }
  irc_quit("Peacin' Out!");
  set('status','off',$k);	//not technically needed, since irc_quit uses 'off' by default, but it's here for posterity.
}

function destroyServer($k = NULL) {
  if ($k === NULL) { global $k; }
  global $s;
  irc_quit("Peacin' Out! Permanently!");
  unset ($s[$k]);
}

function irc_quit($msg = '', $k = NULL) {
  //This function shouldn't be used to terminate connections directly, rather, you shouuld use a wrapper function like hopServer() or quitServer().
  if ($k === NULL) { global $k; }
  global $s;
  if (feof($s[$k]['socket'])) { elog("-- Disconnected!"); }
  else { 
    elog("-- Closing Connection (".$s[$k]['name'].")",$k);
    raw("QUIT :".$msg);
  }
  //Instead of closing the socket now, we're going to mark it to be closed during the connection maintenance sub-block.
  set('dead',TRUE,$k);
  //fclose($s[$k]['socket']);
  
  //The following line sets the socket that was closed in a state that will prevent it from being reopened. If this is not desired, use set('status',[active|sleep|off][,$k])
  set('status','off',$k);
  rehashActiveSockets();
}

function rehashActiveSockets() {
  //elog("-- Rehashing which sockets are active, hang on!");
  global $s, $sockets;
  $sockets = array();
  foreach ($s as $k => $ss) {
    if ($ss['status'] == 'active') { $sockets[$k] = $ss['socket']; }
  }
  //print_r($sockets);
}

function irc_nick($nick,$k = NULL) {
  if ($k === NULL) { global $k; }
  global $s;
  set('anick',$nick);
  raw("NICK $nick");
}

function irc_msg($dest,$msg,$k = NULL) {
  if ($k === NULL) { global $k; }
  raw("PRIVMSG $dest :$msg",$k);
}

function irc_notice($dest,$msg,$k = NULL) {
  if ($k === NULL) { global $k; }
  raw("NOTICE $dest :$msg",$k);
}

function irc_ctcp($dest,$msg,$k = NULL) {
  if ($k === NULL) { global $k; }
  irc_msg($dest,"\x01".$msg."\x01",$k);
}

function irc_ctcpreply($dest,$msg,$k = NULL) {
  if ($k === NULL) { global $k; }
  irc_notice($dest,"\x01".$msg."\x01",$k);
}

function irc_join($msg,$pass,$k = NULL) {
  if ($k === NULL) { global $k; }
  raw("JOIN $msg $pass",$k);
}

function irc_tries($k) {
  global $s;
  if ($s[$k]['tries']) { return $s[$k]['tries']; }
  else { global $s_tries; return $s_tries; }
}

function irc_delay($k) {
  global $s;
  if ($s[$k]['delay']) { return $s[$k]['delay']; }
  else { global $s_delay; return $s_delay; }
}

function irc_timeout($k) {
  global $s;
  if ($s[$k]['timeout']) { return $s[$k]['timeout']; }
  else { global $s_timeout; return $s_timeout; }
}

function irc_trigger($str,$msg) {
  $l = strlen($str) + 1;
  if ((substr($msg,0,$l) == '.'.$str) || (substr($msg,0,$l) == '!'.$str)) { return TRUE; }
}

function irc_isinchan($nick,$chan,$k = NULL) {
  global $s;
  if ($k === NULL) { global $k; }
  if (array_search($nick,$s[$k]['names'][$chan]) !== FALSE) { return TRUE; }
}

function irc_get_k( $server ) {
  global $s;
  foreach ($s as $key => $ss) {
    if ($ss['name'] == $server) return $k;
  }
}

?>
