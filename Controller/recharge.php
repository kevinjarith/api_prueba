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
      $sql = $dbConn->prepare("SELECT r.rechargevalor recarga, r.rechargefecha fecharecarga, CONCAT(u.nombres,' ',u.apellidos) usuario, u.saldo, u.correo
      FROM recharge r
      JOIN user u ON u.userid = r.rechargeuserid");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      $data = [
        "rechargehistory" => $sql->fetchAll()
      ];
      echo json_encode($data);
      exit();
}

// Crear un nuevo post para traer las recargas por fecha
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;    

    if(isset($_POST['fechainicio']) && isset($_POST['fechafinal'])){
      
      $sql = $dbConn->prepare("SELECT r.rechargevalor recarga, r.rechargefecha fecharecarga, CONCAT(u.nombres,' ',u.apellidos) usuario, u.saldo, u.correo
      FROM recharge r
      JOIN user u ON u.userid = r.rechargeuserid
      WHERE DATE(r.rechargefecha) BETWEEN :fechaincio AND :fechafinal");
      $sql->bindValue(':fechaincio', $input['fechainicio']);
      $sql->bindValue(':fechafinal', $input['fechafinal']);
      $sql->execute();
      $data = $sql->fetchAll(PDO::FETCH_ASSOC);

      $data = [
        "rechargefecha" => $data
      ];
      echo json_encode($data);
      exit();

    }else{
      $error = [
        "rechargefecha" => 'Los datos que esta enviando no son los correctos'
      ];
      echo json_encode($error);
      exit();
    }
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>