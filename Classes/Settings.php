
class Settings {
  
  public function __construct() {
    $this->Persistence = new Persistence();
    $this->My = new User();
  }


  /* Read-only Getters */
  public function Persistence() {
    return $this->Persistence;
  }

  public function My() {
    return $this->My;
  }

  public function Chanlist() {
    return $this->ChanList;
  }

  /* Read/Write Getters */
  public function Name( $new = NULL ) {
    if ($new) $this->Name = $new;
    else return $this->Name;
  }

  public function Address( $new = NULL ) {
    if ($new) $this->Address = $new;
    else return $this->Address;
  }

  public function Port( $new = NULL ) {
    if ($new) $this->Port = $new;
    else return $this->Port;
  }

  public function SSL( $new = NULL ) {
    if ($new) $this->SSL = $new;
    else return $this->SSL;
  }

  /**** Data Members ****/
  // Encapsulated Objects
  private $Persistence = NULL;
  private $My = NULL;
  // Regular Fields
  private $Name =     "Default Connection";
  private $Address =  "localhost";
  private $Port =     "6667";
  private $SSL =      FALSE;
  private $ChanList = array( "#lumi" );
  
};
