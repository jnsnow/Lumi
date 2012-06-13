
class Settings {
  
  function __construct() {
    $this->Persistence = new Persistence();
    $this->My = new User();
  }
  
  public $Name = "Default Connection";
  public $Address = "Localhost";
  public $Port = "6667";
  public $SSL =	FALSE;
  public $Persistence = NULL;
  public $My = NULL;
  public $ChanList = array( "#snow" );
  
};
