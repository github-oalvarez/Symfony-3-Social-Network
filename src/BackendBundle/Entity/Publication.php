<?php

namespace BackendBundle\Entity;
 
class Publication {
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* text **************************************************************************/ 
    private $text;
    public function setText($text) { $this->text = $text; return $this; }
    public function getText() { return $this->text; }
/*********************************************************************************/    
/* document **********************************************************************/ 
    private $document;
    public function setDocument($document) { $this->document = $document; return $this; } 
    public function getDocument() { return $this->document; }
/*********************************************************************************/    
/* image *************************************************************************/ 
    private $image;
    public function setImage($image) {  $this->image = $image; return $this; }
    public function getImage() { return $this->image; }
/*********************************************************************************/    
/* status ************************************************************************/ 
    private $status;
    public function setStatus($status) { $this->status = $status; return $this; } 
    public function getStatus() { return $this->status; } 
/*********************************************************************************/    
/* createdAt *********************************************************************/ 
    private $createdAt;
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; return $this; } 
    public function getCreatedAt() { return $this->createdAt; } 
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

