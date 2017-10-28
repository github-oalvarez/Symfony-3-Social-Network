<?php

namespace BackendBundle\Entity;
 
class Like {
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* publication *******************************************************************/
    private $publication; 
    public function setPublication(\BackendBundle\Entity\Publication $publication = null) { $this->publication = $publication; return $this; } 
    public function getPublication() { return $this->publication; } 
/*********************************************************************************/    
/* user **************************************************************************/
    private $user; 
    public function setUser(\BackendBundle\Entity\User $user = null) { $this->user = $user; return $this; } 
    public function getUser() { return $this->user; }
/*********************************************************************************/  
}

