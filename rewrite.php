#!/usr/bin/env php
<?php

 //Init over, here's the Main Block. Here we go!
while (1) {

  //Sub-block #1: Retrieving user input from the terminal.
  if ($line = (trim(fgets(STDIN)))) {
    $pcs = explode(' ',$line);
    if (substr($pcs[0],0,1) == "'") { raw(substr($line,1)); }
    else { elog('-- Terminal command not recognized. To send raw text, prefix the statement with a single leading quote.'); }
  }


  //Sub-Block #2: Connection Maintenance and Active-Send Dynamic Code Block
  //If we've got no server blocks at all ... 
  if (count($s) < 1) { echo '.'; sleep(1); continue; } 

  foreach ($s as $k => $ss) {
    //If a server is active, but has no socket, Join the server/Open the socket.
    if (($ss['status'] === NULL) || 
	(($ss['status'] == 'sleep') && (time() >= $ss['sleep']))) { 
      joinServer(); 
      //states 'off', 'active', and 'dialing' are the other possibilities.
    } 

    if ($ss['dead'] === TRUE) { 
      // If a socket has died (as declared by irc_quit, called by hopServer()),
      // reset it.
      elog("-- Dead Socket Code Activated");
      set('dead',FALSE);
      fclose($s[$k]['socket']);
      continue;
    }

    //Active-Send Dynamic Code Block.
    eval($send_code_str);

  }

  if (count($sockets) <1 ) { 
    // If we've got no open sockets, return to the beginning of our loop.
    // No need to continue.
    echo '.';
    sleep(1);
    continue;
  } 

  //Sub-Block #3: Stream Select and reading from our sockets.
  $bstream = stream_select($rfds = $sockets, $write = NULL, $except = NULL, $timeout = 0, $utimeout = 200000);
  if ($bstream === FALSE) { elog("-- stream_select() failed... I'm bailing!"); die("stream_select() failed... bailing big time!\n"); }
  elseif ($bstream > 0) {
    foreach ($rfds as $active) {
      $k = array_search($active,$sockets);
      $ss = $s[$k];
     
      while ($server->Read()) {
	if (callable($server->Message->cmd)) call_user_func("Core::_".$server->Message->cmd);
	
        //The dynamic 'remote' code block.
        eval($remote_code_str);

      } //while Read()
      if (feof($active)) { elog("-- EOF while reading stream!"); hopServer(); }
    } //foreach (cycling through the streams that stream_select returned)
  } //stream_select (end of Sub-Block #3.
} //while (1) (end of main loop.)

?>
