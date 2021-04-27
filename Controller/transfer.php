<?php

  include ".././Conexion/config.php";  
  include ".././Conexion/utils.php";
  include "./base.php";

  $dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
      //Mostrar lista de tranferencias realizadas
      $sql = $dbConn->prepare("SELECT ROUND(t.transfervalor) valortransferencia, t.transferfecha fechatransferencia, CONCAT(ut.nombres,' ', ut.apellidos) usuariotransfiere, ROUND(ut.saldo) saldotransfiere, ut.correo correotransfiere, CONCAT(ur.nombres,' ',ur.apellidos) usuariorecibe, ROUND(ur.saldo) saldorecibe, ur.correo correorecibe
      FROM transfer t
      JOIN user ut ON ut.userid = t.transferuser
      JOIN user ur ON ur.userid = t.receiveuser");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      $data = [
        "transferhistory" => $sql->fetchAll()
      ];
      echo json_encode($data);
      exit();
}

// Crear un nuevo post para traer las tranferencias por fecha
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;    
    
    if(isset($_POST['fechainicio']) && isset($_POST['fechafinal'])){
      
      $sql = $dbConn->prepare("SELECT ROUND(t.transfervalor) valortransferencia, t.transferfecha fechatransferencia, CONCAT(ut.nombres,' ', ut.apellidos) usuariotransfiere, ROUND(ut.saldo) saldotransfiere, ut.correo correotransfiere, CONCAT(ur.nombres,' ',ur.apellidos) usuariorecibe, ROUND(ur.saldo) saldorecibe, ur.correo correorecibe
      FROM transfer t
      JOIN user ut ON ut.userid = t.transferuser
      JOIN user ur ON ur.userid = t.receiveuser
      WHERE DATE(t.transferfecha) BETWEEN :fechaincio AND :fechafinal");
      $sql->bindValue(':fechaincio', $input['fechainicio']);
      $sql->bindValue(':fechafinal', $input['fechafinal']);
      $sql->execute();
      $data = $sql->fetchAll(PDO::FETCH_ASSOC);
  
      $data = [
        "transferfecha" => $data
      ];
      echo json_encode($data);
      exit();
    }else{
      $error = [
        "transferfecha" => 'Los datos que esta enviando no son los correctos'
      ];
      echo json_encode($error);
      exit();
    }
    
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>