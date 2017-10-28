<?php

namespace BackendBundle\Entity;

class Following{
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* followed **********************************************************************/ 
    private $followed; 
    public function setFollowed(\BackendBundle\Entity\User $followed = null) { $this->followed = $followed; return $this; } 
    public function getFollowed() { return $this->followed; } 
/*********************************************************************************/    
/* user **************************************************************************/ 
    private $user; 
    public function setUser(\BackendBundle\Entity\User $user = null) { $this->user = $user; return $this; } 
    public function getUser() { return $this->user; }
/*********************************************************************************/    
}

