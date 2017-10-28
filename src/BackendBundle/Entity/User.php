<?php

// src\AppBundle\Resources\config\validation.yml

namespace BackendBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, \Serializable {
/* Id de la Tabla ****************************************************************/    
    private $id;
    public function getId() { return $this->id; }
/*********************************************************************************/    
/* role **************************************************************************/  
    private $role;
    public function setRole($role) { $this->role = $role; return $this; }
    public function getRole() { return $this->role; }
    public function getRoles(){ return array('ROLE_USER','ROLE ADMIN'); }
/*********************************************************************************/    
/* email *************************************************************************/ 
    private $email;
    public function setEmail($email) { $this->email = $email; return $this; }
    public function getEmail() { return $this->email; }
    public function getUsername(){  return $this->email;  }
/*********************************************************************************/    
/* name **************************************************************************/ 
    private $name;
    public function setName($name) {$this->name = $name; return $this; }
    public function getName() { return $this->name; }
    /* 
     * la función __toString(){ return $this->name;  } permite 
     * listar los campos cuando referenciemos la tabla
     */
    public function __toString(){ return $this->name;  }
/*********************************************************************************/    
/* surname ***********************************************************************/ 
    private $surname;
    public function setSurname($surname) { $this->surname = $surname; return $this; }
    public function getSurname() { return $this->surname; }
/*********************************************************************************/    
/* password **********************************************************************/
    private $password;
    public function setPassword($password) { $this->password = $password; return $this; }
    public function getPassword() { return $this->password; }
/*********************************************************************************/    
/* nick **************************************************************************/
    private $nick;
    public function setNick($nick) { $this->nick = $nick; return $this; }
    public function getNick() { return $this->nick; }
/*********************************************************************************/    
/* bio ***************************************************************************/
    private $bio;
    public function setBio($bio) { $this->bio = $bio; return $this; }
    public function getBio() { return $this->bio; }
/*********************************************************************************/    
/* active ************************************************************************/
    private $active;
    public function setActive($active) { $this->active = $active; return $this; }
    public function getActive() { return $this->active; }
/*********************************************************************************/    
/* pimage ************************************************************************/
    private $image;
    public function setImage($image) {  $this->image = $image; return $this; } 
    public function getImage() { return $this->image; }
/* INTRODUCIMOS LOS SIGUIENTES MÉTODOS *******************************************/
    public function getSalt(){ return null; }
    public function eraseCredentials(){  }
/*********************************************************************************/
/*
 * INTRODUCIMOS MÉTODOS SERIALIZABLES
 * Facilita la subida y actualización de imágenes en los formularios
*/
  public function serialize(){ return serialize (array( $this->id, $this->email, $this->password )); }
  public function unserialize ($serialized){ list( $this->id, $this->email, $this->password ) = unserialize ($serialized); }
/*********************************************************************************/
}
