#!/usr/bin/env php
<?php

define( "NAME", "Lumi IRC Skeleton" );
define( "VERSION", "0.1" );

require( "Classes/User.php" );
require( "Classes/Persistence.php" );
require( "Classes/Settings.php" );
require( "Classes/Message.php" );
require( "Classes/Server.php" );
require( "Classes/Core.php" );
require( "Classes/Lumi.php" );
require( "Functions/Util.php" );


if (($argc > 1) && ($argv[1] == '--portable')) {
  $root = './';
} else {
  $home = getenv("HOME");
  $root = $home."/.config/lumi/"; 
}
echo NAME." ".VERSION.", loading configurations from '".$root."'.\n";

date_default_timezone_set('UTC');
$Lumi = new Lumi();

// Load configuration file ...
/* Temp Block */ {
  $settings = new Settings();
  $settings->Address( "localhost" );
  $settings->Port( 6667 );
  $Lumi->addServer( new Server( $settings ) );
}

// Include user-scripts here...
// Initialize, Reply, Onjoin, Proactive 
// Multiple dirs/scripts, or hooks?

/* ---------------------------------------------------- */

while (true) {

  $Lumi->connectionMaintenance();

  $Lumi->getMessages();

}

?>
