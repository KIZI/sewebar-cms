<?php 
  defined('_JEXEC') or die('Restricted access');
                      
  $returnArr=array(
                 'name'=>@$this->user->name,
                 'username'=>@$this->user->username,
                 'email'=>@$this->user->email,
                 'id'=>@$this->user->id
               );
  echo json_encode($returnArr);             

?>

