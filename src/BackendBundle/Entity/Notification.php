<?php

namespace BackendBundle\Entity;

class Notification { 
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* mtype *************************************************************************/ 
    private $type; 
    public function setType($type) { $this->type = $type; return $this; } 
    public function getType() { return $this->type; } 
/*********************************************************************************/    
/* typeId ************************************************************************/ 
    private $typeId; 
    public function setTypeId($typeId) { $this->typeId = $typeId; return $this; } 
    public function getTypeId() { return $this->typeId; } 
/*********************************************************************************/    
/* readed ************************************************************************/ 
    private $readed; 
    public function setReaded($readed) { $this->readed = $readed; return $this; } 
    public function getReaded() { return $this->readed; } 
/*********************************************************************************/    
/* createdAt *********************************************************************/ 
    private $createdAt; 
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; return $this; } 
    public function getCreatedAt() { return $this->createdAt; } 
/*********************************************************************************/    
/* extra *************************************************************************/ 
    private $extra; 
    public function setExtra($extra) { $this->extra = $extra; return $this; } 
    public function getExtra() { return $this->extra; } 
/*********************************************************************************/    
/* user **************************************************************************/ 
    private $user; 
    /* 
     * Añadiremos la función __toString(){ return $this->name;  } dentro de 
     * src\BackendBundle\Entity\User.php, y permite 
     * listar los campos cuando referenciemos la tabla
     */
    public function setUser(\BackendBundle\Entity\User $user = null) { $this->user = $user; return $this; } 
    public function getUser() { return $this->user; }
/*********************************************************************************/  
}

