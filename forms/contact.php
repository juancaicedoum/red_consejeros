<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

//CONFIGURE JSON reponse
$reponse = [
  'top_err' => '',
  'top_succes' => '',
  'to_err' => '',
  'subject_err' => '',
  'message_err' => '',
];

try{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $_SERVER['HTTP_PHP_MAILER_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Port       = $_SERVER['HTTP_PHP_MAILER_PORT'];
    $mail->Username   = $_SERVER['HTTP_PHP_MAILER_USERNAME'];
    $mail->Password   = $_SERVER['HTTP_PHP_MAILER_PASSWORD'];
  }catch(Exception $e){
    $reponse['top_err'] = 'Lo sentimos, presentamos problemas enviando tu email';
    exit(json_encode($reponse));
  }
$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) :
'';

if($contentType == 'application/json'){
  $content = trim(file_get_contents(('php://input')));
  //CONVERT CONTENT INTO PHP ARRAY
  $decoded = json_decode($content, true);
  if(is_array($decoded)){
    //Sanitize Input Data
    foreach($decoded as $name => $value){
      $decoded[$name] = trim(filter_var($value, FILTER_SANITIZE_STRING));
    }
    //ERROR CHECKING
    if(empty($decoded['to'])){
      $reponse['to_err'] = 'Error. Este campo no puede estar vacio.';
    }else if(!filter_var($decoded['to'], FILTER_SANITIZE_STRING)){
      $reponse['to_err'] = 'Error. Este campo debe tener un Email valido.';
    }
    if(empty($decoded['subject'])){
      $reponse['subject_err'] = 'Error. Este campo no puede estar vacio.';
    }
    if(empty($decoded['message'])){
      $reponse['message_err'] = 'Error. Este campo no puede estar vacio.';
    }
    //Can´t send the email if we already have a response to show
    foreach($reponse as $type => $message){
      if(!empty($reponse[$type])){
        exit(json_encode($reponse));
      }
    }
    //ACTUALLY SEND EMAIL
    try{
      $mail->setFrom('reddeconsejerosum@gmail.com');
      $mail->subject = $decoded['subject'];
      $mail->isHTML(true);
      $mail->Body = '<p>'.$decoded['message'].'</p>';
      $mail->addAdress($decoded['to']);

      $mail->send();
    }catch(Exception $e){
      $reponse['top_err'] = 'Lo sentimos, presentamos problemas enviando tu email';
      exit(json_encode($reponse));
    }
    //Success response
    $reponse['top_success'] = 'Listo. El mensaje fue enviado con éxito.';
    exit(json_encode($reponse));
  }
}

$reponse['top_err'] = 'Lo sentimos, presentamos problemas enviando tu email';
exit(json_encode($reponse));