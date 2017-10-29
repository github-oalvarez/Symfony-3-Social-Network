<?php

namespace BackendBundle\Entity;

class Following{
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* followed **********************************************************************/ 
    /* 
     * No usamos la función __toString() al no tener que listar las followed
     */
    private $followed; 
    public function setFollowed(\BackendBundle\Entity\User $followed = null) { $this->followed = $followed; return $this; } 
    public function getFollowed() { return $this->followed; } 
/*********************************************************************************/    
/* user **************************************************************************/ 
    /* 
     * Añadiremos la función __toString(){ return $this->name;  } dentro de 
     * src\BackendBundle\Entity\User.php, y permite 
     * listar los campos cuando referenciemos la tabla
     */
    private $user; 
    public function setUser(\BackendBundle\Entity\User $user = null) { $this->user = $user; return $this; } 
    public function getUser() { return $this->user; }
/*********************************************************************************/    
}

