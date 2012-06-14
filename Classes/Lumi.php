<?php /*DEL*/

class Lumi {
  
  public function __construct() {
    $this->core = new Core();
  }

  public function connectionMaintenance() {
    
    for ( $k = 0; $k < $this->numServers; $k++ ) {
      
      $s = &$this->servers[$k];
      
      switch ($s->State()) {
      case "New":
      case "Dial":
      case "Sleep":
      case "Dead":
	$rc = $s->Connect();
        if ($rc === true) {
	  $this->sockets[$k] = &$this->servers[$k]->getSocket();
	  $this->numSockets = count( $this->sockets );
	} else {
	  unset ($this->sockets[$k]);
	  $this->numSockets = count( $this->sockets );
	}
      break;
      case "Off":
      case "On":
	//Nothing.                                                                 
	break;
      default:
	dlog( "We should never, ever, ever get here: ".
	      "Invalid state encountered in the connection block." );
      }
    }

  }

  public function getMessages() {

    if ($this->numSockets < 1) return;

    $rc = @socket_select( $select = $this->sockets,
			  $write = NULL,
			  $except = NULL,
			  0, 200000 );


    if ($rc === 0) return;
    else if ($rc === FALSE) {
      dlog( "socket_select returned FALSE." );
      return;
    }
    

    for ($k = 0; $k < $this->numServers; $k++) {
      $s = &$this->servers[$k];
      if ($s->Read()) {
	if (is_callable("Core::_" . $s->Message->cmd)) {
	  $this->core->{"_".$s->Message->cmd}( $s );
	}
      }
    }

  }

  public function addServer( &$server ) {
    $this->servers[] = $server;
    $this->numServers++;
    dlog( "Adding server; numServers: $this->numServers" );
  }

  private $servers = array();
  private $sockets = array();
  private $numServers = 0;
  private $numSockets = 0;
  private $core = NULL;

};
