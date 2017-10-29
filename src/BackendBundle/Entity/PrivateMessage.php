<?php

namespace BackendBundle\Entity;

class PrivateMessage {
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* message ***********************************************************************/  
    private $message;
    public function setMessage($message) { $this->message = $message; return $this; } 
    public function getMessage() { return $this->message; } 
/*********************************************************************************/    
/* file **************************************************************************/
    private $file;
    public function setFile($file) { $this->file = $file; return $this; } 
    public function getFile() { return $this->file; }
/*********************************************************************************/    
/* image *************************************************************************/
    private $image;
    public function setImage($image) { $this->image = $image; return $this; } 
    public function getImage() { return $this->image; } 
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
/* emitter ***********************************************************************/
    /* 
     * Añadiremos la función __toString(){ return $this->name;  } dentro de 
     * src\BackendBundle\Entity\User.php, y permite 
     * listar los campos cuando referenciemos la tabla
     */
    private $emitter;
    public function setEmitter(\BackendBundle\Entity\User $emitter = null) { $this->emitter = $emitter; return $this; } 
    public function getEmitter() { return $this->emitter; } 
/*********************************************************************************/    
/* receiver **********************************************************************/
    /* 
     * Añadiremos la función __toString(){ return $this->name;  } dentro de 
     * src\BackendBundle\Entity\User.php, y permite 
     * listar los campos cuando referenciemos la tabla
     */
    private $receiver;
    public function setReceiver(\BackendBundle\Entity\User $receiver = null) { $this->receiver = $receiver; return $this; } 
    public function getReceiver() { return $this->receiver; }
/*********************************************************************************/ 
}

