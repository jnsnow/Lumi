<?php /*DEL*/

Class Server { 

  public function __construct( $settings ) {
    $this->State( 'New' );
    $this->Settings = $settings;
    //$this->createSocket();
    $this->SleepTimer = 0;
  }

  private function createSocket( ) {    

    $this->socket = @socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
    if ($this->socket === FALSE) {
      slog( "Failed to open socket." );
      ilog( "Reason: " . socket_strerror( socket_last_error( $this->socket ) ) );
      return FALSE;
    }
    
    $rc = @socket_set_nonblock( $this->socket );
    if ($rc === FALSE) {
      slog( "Failed to set non-blocking mode on the socket." );
      return FALSE;
    }
    return TRUE; 
  }

  public function Connect() {

    if (time() <= $this->SleepTimer) return false;

    // This is not a refresh attempt, this is a new connection. */
    if ($this->State() != "Dial") {
      $maxTries = &$this->Settings->Persistence()->Tries();
      if ( ++$this->Attempts > $maxTries ) {
	slog( "Max attempts exceeded ($maxTries), giving up." );
	$this->State( "Off" );
	$this->Attempts = 0;
	return false;
      }
      slog( "Attempt #$this->Attempts/$maxTries" );

      switch ($this->State()) {
      case "Dead":
      case "New":
      case "Sleep":
	if (!$this->socket) {
	  $rc = $this->createSocket();
	  if ($rc === false) return false;
	}
        break;
      }
    }

    $ip_addr = gethostbyname( $this->Settings->Address() );
    slog( "Trying to connect to " . $ip_addr . " // " . $this->Settings->Address() );


    if ( @socket_connect( $this->socket, $ip_addr, 
			  $this->Settings->Port() ) === FALSE ) {      
      if (socket_last_error($this->socket) == SOCKET_EINPROGRESS) {
	$this->State( "Dial" );
	return FALSE;
      } else if (socket_last_error($this->socket) == SOCKET_EALREADY) {
	// This essentially means we are still dialing.
	return FALSE;
      } else {
	slog( "Failed to connect: ".
	      socket_strerror(socket_last_error($this->socket)) );
	$this->Sleep();
	return FALSE;
      }
    } else { // socket_connect returned TRUE.
      $this->ConnectReply();
      $this->State( "On" );
      $this->Settings->Persistence()->Tries( 0 );
      slog( "Successfully connected." );
      return TRUE;
    }
  }

  /* This completes our connection phase. */
  private function ConnectReply() {

    $S = &$this->Settings;
    $My = &$this->Settings->My();
    $this->Send( "CAP LS" );
    $this->Nick( $My->Nick() );
    $this->Send( implode( " ", array( "USER", $My->Username(), $My->Hostname(),
				      $S->Address(), ":".$My->Realname())));
    $this->Send( "CAP END" );
  }

  /* Grab data from the socket */
  public function Read() {
    
    $raw = @socket_read( $this->socket, 512, PHP_NORMAL_READ );
    if ($raw === FALSE) {
      slog( "Failed to read; ". socket_strerror( socket_last_error( $this->socket ) ) );
      $this->Close();
      return false;
    }

    if ($this->buffer) $raw = $this->buffer.$raw;

    if (substr($raw,-1) != "\n") {
      $this->buffer = $raw;
      return false;
    }

    _log( $raw = trim( $raw ), "<-" );
    $this->buffer = "";
    unset( $this->Message );
    $this->Message = new Message( trim($raw) );
    return true;

  }

  /* Jam data into the pipes. */
  public function Send( $msg ) {
    
    _log( "$msg", "->" );
    $rc = @socket_write( $this->socket, $msg."\r\n" );
    if ($rc === false) {
      slog( "Failed to write; ". socket_strerror( socket_last_error( $this->socket ) ) );
      $this->Close();
    }

  }

  public function Close() {
    slog( "Closing Socket" );
    socket_close( $this->socket );
    $this->State( "Dead" );
    $this->socket = NULL;
  }

  public function Sleep() {
    slog( "Waiting..." );
    socket_close( $this->socket );
    $this->socket = NULL;
    $this->State( "Sleep" );
    $this->SleepTimer = time() + $this->Settings->Persistence()->Delay();
  }











  /* COMMON MESSAGES */

  public function Notice( $dest, $msg ) {
    $this->Send( "NOTICE $dest :$msg" );
  }

  public function Msg( $dest, $msg ) {
    $this->Send( "PRIVMSG $dest :$msg" );
  }

  public function Ctcp( $dest, $msg ) {
    $this->Msg( $dest, "\x01".$msg."\x01" );
  }

  public function CtcpReply( $dest, $msg ) {
    $this->Notice( $dest, "\x01".$msg."\x01" );
  }

  public function Nick( $newNick ) {
    $this->Send( "NICK $newNick" );
    $this->ANick( $newNick ); 
  }

  public function Join( $chan, $pass = "" ) {
    $this->Send( "JOIN $chan :$pass" );
  }



  /************ SETTERS/GETTERS ***********/

  public function getSocket() {
    return $this->socket;
  }
    
  public function State( $new = NULL ) { 
    if ($new) switch ($new) {
      case "New":
      case "Dial":
      case "Sleep":
      case "Dead":
      case "Off":
      case "On":
	$this->State = $new;
        break;
      default:
	_dlog( "!! Invalid connection state !!" );
      }
    return $this->State;
  }

  public function ANick( $new = NULL ) {
    if ($new) $this->ANick = $new;
    return $this->ANick;
  }

  /************ DATA MEMBERS **************/

  // Required
  private $Settings;
  private $State = "New";

  // Generated
  public $Message = NULL;
  private $buffer = NULL;
  private $socket = NULL;
  private $sleepTimer = NULL;
  private $Attempts = 0;
  private $ANick = NULL; // our ACTIVE nick.

}
