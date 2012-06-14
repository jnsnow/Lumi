<?php /*DEL*/

class Persistence {
  

  public function __construct( $Delay = NULL,
			       $Tries = NULL,
			       $Timeout = NULL ) {
    
    if ($Delay) $this->Delay( $Nick );
    if ($Tries) $this->Tries( $AltNick );
    if ($Timeout) $this->Timeout( $Username );

  }			      

  /********** SETTERS/GETTERS *********/

  public function Delay( $new = NULL ) { 
    if ($new) $this->Delay = $new;
    return $this->Delay;
  }

  public function Tries( $new = NULL ) { 
    if ($new) $this->Tries = $new;
    return $this->Tries;
  }

  public function Timeout( $new = NULL ) { 
    if ($new) $this->Timeout = $new;
    return $this->Timeout;
  }

  /********** DATA MEMBERS ***********/

  private $Delay = 30;
  private $Tries = 5;
  private $Timeout = 30;
  
};
