
class Message { 
 
  public function __construct( $msg = NULL ) {

    $this->raw = $msg;
    
    // Expand into an array of tokens.
    $tokens = explode( ' ', $msg );
    
    // If the first token is prefixed with a colon,
    // Then the first word is the source of the msg.
    if ( substr( $tokens[0], 0, 1 ) == ':' ) {
      $this->src = substr( $tokens[0], 1 );

      // If the source of the message looks like a person, parse further.
      $matches = array();
      if (preg_match( '/([^!]+)!(.+)@([^@]+)/', $this->src, $matches)) {
	$this->nick = $matches[1];
	$this->ident = $matches[2];
	$this->host = $matches[3];
      }

      array_shift( $tokens );
    }

    // If the first token was NOT prefixed with a colon,
    // Then the first token is the Command.
    $this->cmd = $tokens[0];
    array_shift( $tokens );

    // At this point, the remaining tokens are positionals,
    // With exception of the last token, which is the message.
    foreach ( $tokens as $token ) {
      if (substr($token, 0, 1) == ':') {
	// The message is, at this point, the rest of the message.
	$this->msg = substr( implode( ' ', $tokens ), 1 );
	break;
      }
      else {
	array_push( $this->argv, $token );
	array_shift( $tokens );
      }
    }

  }
 
  public function Dump() {
    foreach ($this as $key => $value) {
      print_r("$key => ");
      print_r($value);
      print_r("\n");
    }
  }

  public $raw;
  public $src;
  public $cmd;
  public $argv = Array();
  public $msg;
  
  public $nick;
  public $ident;
  public $host;
  
  /* New Fields */
  public $dest;        /* Channel it was sent to, User it was sent to (me) */
  public $reply;       /* Who we should send back to [convenience field] */
  
};
