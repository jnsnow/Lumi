#!/usr/bin/env php
<?php

 $_version = "Snow IRC Skeleton 0.4b"; //Set the version string!

 //Determine where we're loading extensions from ...
 if (($argc > 1) && ($argv[1] == '--portable')) { $root_dir = './'; } 
 else { $root_dir = $_ENV['HOME']."/.config/snow/"; }
 echo $_version.', loading extensions from '.$root_dir."\n"; //Tell the user hello!
 stream_set_blocking(STDIN,0); //For asynchronous user input.
 $inc_dirs = array('init', 'remote', 'auto', 'send'); //These are the sub-directories of the root we're interested in.

 //Find out what we want to include.
 foreach($inc_dirs as $dir) {
  echo $root_dir.$dir."\n";
  if (file_exists($root_dir.$dir)) { $dh = opendir($root_dir.$dir); }
  else { continue; }
  if (!$dh) { continue; }
  while (false !== ($file = readdir($dh))) {
   if (fnmatch('*.php',$file)) { $includes[$dir][] = $root_dir.$dir.'/'.$file; }
  }
  closedir($dh);
 }

 //Let's include the Init stuff now.
 if (is_array($includes['init'])) { foreach ($includes['init'] as $inc) { require($inc); } }

 //Let's read the other blocks into memory now, though.
 $remote_code_str = NULL;
 $auto_code_str = NULL;
 $send_code_str = NULL;
 if (is_array($includes['remote'])) { foreach ($includes['remote'] as $inc) { $remote_code_str .= file_get_contents($inc); } }
 if (is_array($includes['auto'])) { foreach ($includes['auto'] as $inc) { $auto_code_str .= file_get_contents($inc); } }
 if (is_array($includes['send'])) { foreach ($includes['send'] as $inc) { $send_code_str .= file_get_contents($inc); } }
 
 //Init over, here's the Main Block. Here we go!
while (1) {

  //Sub-block #1: Retrieving user input from the terminal.
  if ($line = (trim(fgets(STDIN)))) {
    $pcs = explode(' ',$line);
    if (substr($pcs[0],0,1) == "'") { raw(substr($line,1)); }
    else { elog('-- Terminal command not recognized. To send raw text, prefix the statement with a single leading quote.'); }
  }

  //Sub-Block #2: Connection Maintenance and Active-Send Dynamic Code Block
  if (count($s) < 1) { echo '.'; sleep(1); continue; } //If we've got no server blocks at all ... 
  foreach ($s as $k => $ss) {
    //If a server is active, but has no socket, Join the server/Open the socket.
    if (($ss['status'] === NULL) || (($ss['status'] == 'sleep') && (time() >= $ss['sleep']))) { joinServer(); } //states 'off', 'active', and 'dialing' are the other possibilities.
    if ($ss['dead'] === TRUE) { elog("-- Dead Socket Code Activated"); set('dead',FALSE); fclose($s[$k]['socket']); continue; } //If a socket has died (as declared by irc_quit, called by hopServer()) reset it.
    //Active-Send Dynamic Code Block.
    eval($send_code_str);
  }
  if (count($sockets) <1 ) { echo '.'; sleep(1); continue; } //If we've got no open sockets, return to the beginning of our loop. No need to continue.

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
