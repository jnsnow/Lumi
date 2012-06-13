/* A set of functions that define a set of
   default responses for server messages. */

class Core {
  
  public function _PING( &$server ) {
    $server->Send( "PONG :" . $server->Message->msg );
  }
  
  public function _PRIVMSG( &$server ) {
    
    $server->Message->chan = $server->Message->argv[0];
    if (substr( $server->Message->msg, 0, 1 ) == "\x01" ) {
      $msg = &$server->Message->msg;
      /* We have received a CTCP message. */
      $tok = explode(' ',trim($msg,"\x01"));
      switch($tok[0]) {
      case "PING":
      case "VERSION":
      case "TIME":
      default:
	//nothing for now.
	break;
      }
    }
  }

  // Server response to NAMES.
  public function _353( &$server ) {
 
    $server->Message->chan = $server->Message->argv[0];
    $msg = &$server->Message->msg;

    foreach (explode(' ',$msg) as $thisnick) {
      preg_match('/(?:[\+&@%~])?([^!]+)(?:!(.+)@([^@]+))?/',
		 $thisnick,$matches);
      
      // $Server->Chanlist_Set( $chan, $matches[1] );

    }
  }
  
  public function _366( &$server) {
    //$s[$k]['names'][$m['1']] = $s[$k]['namestmp'][$m['1']];
    //$s[$k]['namestmp'][$m['1']] = NULL;
  }
  
  public function _JOIN( &$server ) {
    //$s[$k]['names'][$m['msg']][] = $m['nick'];
  }
  
  public function _KICK( &$server ) {
    //$key = array_search($m['1'],$s[$k]['names'][$m['0']]); 
    //unset($s[$k]['names'][$m['0']][$key]);
  }
  
  public function _PART( &$server ) {
    //$key = array_search($m['nick'],$s[$k]['names'][$m['0']]); 
    //unset($s[$k]['names'][$m['0']][$key]);
  }
  
  public function _NICK( &$server ) {
    //foreach ($s[$k]['names'] as $key => $channel) {
    //  $key2 = array_search($m['nick'],$channel);
    //  unset($s[$k]['names'][$key][$key2]);
    //  $s[$k]['names'][$key][] = $m['msg'];
    //}
  }
  
  public function _QUIT( &$server ) {
    //foreach ($s[$k]['names'] as $key => $channel) {
    //  $key2 = array_search($m['nick'],$channel);
    //  unset($s[$k]['names'][$key][$key2]);
    //}
  }
  
  /* Nickname is taken. */
  public function _433( &$server ) {
    //if ($ss['anick'] == $ss['nick']) { irc_nick($ss['altnick']); }
    //else { irc_nick($ss['anick'].'_'); }
  }
  
  public function _ERROR( &$server ) {
    _log("Removing $ss[name] from rotation due to ERROR signal"); 
    //quitServer();
  }
  
  public function _TOPIC( &$server ) {
    $server->Message->chan = $server->Message->argv[1];
    dlog( "Set CHAN to $server->Message->chan in response to TOPIC." );
  }
  
  public function _004( &$server ) {    
    //foreach ($ss['channels'] as $channel => $pass) { irc_join($channel); }
    //This is the auto-perform block, done in response to RAW 004, 
    //which should mean we are connected and clear to do our thing.
    //eval($auto_code_str);
  }
  
};
