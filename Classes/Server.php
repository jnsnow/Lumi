
Class Server { 

  public function __construct( $settings ) {
    $this->State( 'New' );
    $this->Settings = $settings;
    $this->createSocket();
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
	echo "!! Invalid connection state !!\n";
      }
    return $this->State;
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


  public function getSocket() {

    return $this->socket;

  }


  public function Join( ) {

    $ip_addr = gethostbyname( $this->Settings->Address() );
    
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
    }

    if ( @socket_connect( $this->socket, $ip_addr, 
			  $this->Settings->Port() ) === FALSE ) {      
      if (socket_last_error($this->socket) == SOCKET_EINPROGRESS) {
	$this->State( "Dial" );
	return FALSE;
      } else if (socket_last_error($this->socket) == SOCKET_EALREADY) {
	/*
	$this->JoinReply();
	$this->State( "On" );
	slog( "Successfully connected." );
	return TRUE;
	*/
	// Waiting, Waiting, Waiting ...
	return FALSE;
      } else {
	$this->State( "Sleep" );
	$this->SleepTimer = time();
	slog( "Failed to connect: ".
	      socket_strerror(socket_last_error($this->socket)) );
	return FALSE;
      }
    } else { // socket_connect returned TRUE.
      $this->JoinReply();
      $this->State( "On" );
      slog( "Successfully connected." );
      return TRUE;
    }
  }

  /* This completes our connection phase. */
  private function JoinReply() {

    $this->Nick( $this->Settings->My->Nick() );
    $this->Send( implode( " ", array( "USER",
				      $this->Settings->My->Username(),
				      $this->Settings->My->Hostname(),
				      $this->Settings->Address,
				      ":".$this->Settings->My->Realname())));

  }

  /* Grab data from the socket */
  public function Read( ) {
    
    $raw = socket_read( $this->socket, 512, PHP_NORMAL_READ );
    if ($this->buffer) $raw = $this->buffer.$raw;

    if (substr($raw,-1) != "\n") {
      $this->buffer = $raw;
      return false;
    }

    $this->buffer = "";
    $raw = trim( $raw );

    unset( $this->Message );
    $this->Message = new Message( trim($raw) );

    return true;

  }

  /* Jam data into the pipes. */
  public function Send( $msg ) {
    
    echo "-> $msg\r\n";
    socket_write( $this->socket, $msg."\r\n" );

  }

  /* Change our nickname */
  public function Nick( $newNick ) {

    $this->Send( "NICK $newNick" );
    
  }

  /************ DATA MEMBERS **************/

  // Required
  private $Settings;
  private $State;

  // Generated
  public $Message = NULL;
  private $buffer = NULL;
  private $socket = NULL;
  private $sleepTimer = NULL;
  private $Attempts = 0;

}
