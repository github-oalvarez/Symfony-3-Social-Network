<?php

namespace BackendBundle\Entity;
 
class Like {
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* publication *******************************************************************/
    private $publication; 
    /* 
     * No usamos la función __toString() al no tener que listar las publicaciones
     */
    public function setPublication(\BackendBundle\Entity\Publication $publication = null) { $this->publication = $publication; return $this; } 
    public function getPublication() { return $this->publication; } 
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

