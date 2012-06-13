#!/usr/bin/env php
<?php

/* Hack to get in-line instance creation to work. */
function Event( $cmd, $fnc ) { return new Event( $cmd, $fnc ); }

class Event { 
  
  public function __construct( $cmd, $fnc ) {
    $this->cmd = $cmd;
    $this->fnc = $fnc;
  }
  
  private $cmd;
  private $fnc;
  
};


class EventTable {
  
  function __construct() {
    /* These are not strictly needed, perhaps. */
    array_push( $this->events, Event( "PING", "Core::PING" ));
    array_push( $this->events, Event( "PRIVMSG", "Core::PRIVMSG" ));
    array_push( $this->events, Event( "ERROR", "Core::ERROR" ));
    array_push( $this->events, Event( "TOPIC", "Core::TOPIC" ));
    array_push( $this->events, Event( "JOIN", "Core::JOIN" ));
    array_push( $this->events, Event( "KICK", "Core::KICK" ));
    array_push( $this->events, Event( "PART", "Core::PART" ));
    array_push( $this->events, Event( "NICK", "Core::NICK" ));
    array_push( $this->events, Event( "QUIT", "Core::QUIT" ));

    /* Aliased Numerics */
    array_push( $this->events, Event( "433", "Core::NickInUse" ));
    array_push( $this->events, Event( "004", "Core::AutoInit" ));
    array_push( $this->events, Event( "353", "Core::NickThing1" ));
    array_push( $this->events, Event( "366", "Core::NickThing2" ));

  }

  private $events = array();

}

class Core {
  
  public function PING( $server, $argv ) {
    echo "Hey :)\n";
    //$server->Send( "PONG :" . $argv );
  }

  public function PING

}
    

call_user_func_array( "Core::PING", array( NULL, NULL ));

$table = new EventTable();



/*    
PRIVMSG
 -CTCP
  -PING
  -VERSION
  -TIME
 -CHANNEL_MSG
 -PRIVATE_MSG
*/