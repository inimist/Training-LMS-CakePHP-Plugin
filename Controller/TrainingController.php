<?php
/**
 * Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('TrainingAppController', 'Training.Controller');
App::uses('Sanitize', 'Utility');
/**
 * Training Controller
 *
 * @package Training
 * @subpackage training.controllers
 *
 * @property Training $Log
 * @property SessionComponent  $Paginator
 * @property PaginatorComponent $RequestHandler
 * @property PhpExcelComponent  $PhpExcel
 * @property FileUploadComponent  $FileUpload
 */
class TrainingController extends TrainingAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'FileUpload.FileUpload');

  public $helpers = array('TinyMCE.TinyMCE');


  function beforeFilter(){
    /* defaults to:
    'jpg' => array('image/jpeg', 'image/pjpeg'),
    'jpeg' => array('image/jpeg', 'image/pjpeg'),
    'gif' => array('image/gif'),
    'png' => array('image/png','image/x-png'),*/
   
    $this->FileUpload->allowedTypes(array(
      'jpg' => array('image/jpeg','image/pjpeg'),
      'gif',
      'png' => array('image/png','image/x-png'),
      'pdf' => array('application/pdf'),
      'txt' => 'text/plain',
      'doc', 'docx', 'xlsx', 'xls'
    ));

    $this->FileUpload->uploadDir('files');
    $this->FileUpload->fileModel('LogEntry');
    $this->FileUpload->fileVar('attachment');
    $this->FileUpload->fileNameFunction('sha1');
    parent::beforeFilter();
  }

//----------------Managing Logs Starts ----------------------//
/**
 * index method
 *
 * @return void
 */
	public function dashboard() {
    
	}
}
