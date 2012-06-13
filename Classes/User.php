
class User { 

  public function __construct( 
    $Nick = NULL, $AltNick = NULL, $Username = NULL,
    $Hostname = NULL, $Realname = NULL ) {
    
    if ($Nick) $this->Nick( $Nick );
    if ($AltNick) $this->AltNick( $AltNick );
    if ($Username) $this->Username( $Username );
    if ($Hostname) $this->Hostname( $Hostname );
    if ($Realname) $this->Realname( $Realname );

  }			      

  /********** SETTERS/GETTERS *********/

  public function Nick( $new = NULL ) { 
    if ($new) $this->Nick = $new;
    return $this->Nick;
  }

  public function AltNick( $new = NULL ) { 
    if ($new) $this->AltNick = $new;
    return $this->AltNick;
  }

  public function Username( $new = NULL ) { 
    if ($new) $this->Username = $new;
    return $this->Username;
  }

  public function Hostname( $new = NULL ) { 
    if ($new) $this->Hostname = $new;
    return $this->Hostname;
  }

  public function Realname( $new = NULL ) { 
    if ($new) $this->Realname = $new;
    return $this->Realname;
  }

  /********** DATA MEMBERS ***********/

  private $Nick = "Lumi";
  private $AltNick = "Snow";
  private $Username = "Lumi"; // Ignored by server
  private $Hostname = "localhost"; // Ignored by server
  private $Realname = "Lumi";
  
};
