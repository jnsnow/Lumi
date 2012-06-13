#!/usr/bin/env php
<?php

require( "Classes/User.php" );
require( "Classes/Persistence.php" );
require( "Classes/Settings.php" );
require( "Classes/Message.php" );
require( "Classes/Server.php" );
require( "Classes/Core.php" );
require( "Functions/Util.php" );

$settings = new Settings();
$settings->Address = "irc2.trimex.us";
$settings->Port = 9034;
$server = new Server( $settings );


while ($rc = $server->Join() === false) {
  //usleep( 2000 );
  sleep( 1 );
}


$core = new Core();
while (1) {
  if ($server->Read( )) {
    _log( $server->Message->raw, "->" );
    if (is_callable("Core::_" . $server->Message->cmd)) {
      $core->{"_".$server->Message->cmd}( $server );
    }
  }
}

?>
