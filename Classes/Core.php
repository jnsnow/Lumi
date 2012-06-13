/* A set of functions that define a set of
   default responses for server messages. */
class Core {
  
  public function _PING( &$server ) {
    $server->Send( "PONG :" . $server->Message->msg );
  }
  
  function _PRIVMSG( &$server ) {
    
    $m['chan'] = $m[0];				//A little bit of additional parsing (while we're here).
    if (substr($m['msg'],0,1) == "\x01") {	//this is for CTCP commands. 
      $tok = explode(' ',trim($m['msg'],"\x01"));
      if ($tok[0] == "PING") { irc_ctcpreply($m['nick'],"PING $tok[1]"); }
      elseif ($tok[0] == "VERSION") { irc_ctcpreply($m['nick'],"VERSION $_version"); }
      elseif ($tok[0] == "TIME") { irc_ctcpreply($m['nick'],"TIME ".date("D M d H:i:s Y")); }
    }
    elseif (irc_trigger('users',$m['msg'])) { print_r($s[$k]['names']); }
    
  }
  
  function _353( &$server ) {
    
    foreach (explode(' ',$m['msg']) as $thisnick) {
      preg_match('/(?:[\+&@%~])?([^!]+)(?:!(.+)@([^@]+))?/',$thisnick,$matches);
      $s[$k]['namestmp'][$m['2']][] = $matches[1];
    }
  }
  
  function _366( &$server) {
    $s[$k]['names'][$m['1']] = $s[$k]['namestmp'][$m['1']];
    $s[$k]['namestmp'][$m['1']] = NULL;
  }
  
  function _JOIN( &$server ) {
    
    $s[$k]['names'][$m['msg']][] = $m['nick'];
    
  }
  
  function _KICK( &$server ) {
    
    $key = array_search($m['1'],$s[$k]['names'][$m['0']]); 
    unset($s[$k]['names'][$m['0']][$key]);
    
  }
  
  function _PART( &$server ) {
    
    $key = array_search($m['nick'],$s[$k]['names'][$m['0']]); 
    unset($s[$k]['names'][$m['0']][$key]);
    
  }
  
  function _NICK( &$server ) {
    
    foreach ($s[$k]['names'] as $key => $channel) {
      $key2 = array_search($m['nick'],$channel);
      unset($s[$k]['names'][$key][$key2]);
      $s[$k]['names'][$key][] = $m['msg'];
    }
  }
  
  function _QUIT( &$server ) {
    
    foreach ($s[$k]['names'] as $key => $channel) {
      $key2 = array_search($m['nick'],$channel);
      unset($s[$k]['names'][$key][$key2]);
    }
  }
  
  // Nick is taken
  function _433( &$server ) {
    
    if ($ss['anick'] == $ss['nick']) { irc_nick($ss['altnick']); }
    else { irc_nick($ss['anick'].'_'); }
    
  }
  
  function _ERROR( &$server ) {

    _log("Removing $ss[name] from rotation due to ERROR signal"); 
    quitServer();
  }
  
  function _TOPIC( &$server ) {
    
    $m['chan'] = $m[1];
    
  }
  
  function _004( &$server ) {
    
    foreach ($ss['channels'] as $channel => $pass) { irc_join($channel); }
    
    //This is the auto-perform block, done in response to RAW 004, 
    //which should mean we are connected and clear to do our thing.
    eval($auto_code_str);
    
  }
  
};
