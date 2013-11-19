<?php
/**
 * Controller pro nastavování preferencí dbconnect
 * Class configController
 */
class configController extends JController{
  public function config(){
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
  }
}