<?php

namespace Tfg\SesionJadBundle;
/**
 * Description of variableGlobal
 *
 * @author josejavi14
 */
class variableGlobal {
    
  public $count;
   
   public function __construct()
   {
      $this->count = 0;
   }
   
   public function incremento()
   {
       $this->count ++;
       
   }
   
   public function __toString() {
       return $this->count;
   }
   
}

?>
