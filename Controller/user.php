<?php

  include ".././Conexion/config.php";  
  include ".././Conexion/utils.php";
  include "./base.php";

  $dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET'){
  
    if(isset($_GET['userid'])){
      
      //Mostrar datos de un usuario en especifico por userid

      $sql = $dbConn->prepare("SELECT * FROM user where userid=:userid");
      $sql->bindValue(':userid', $_GET['userid']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      
      $data = [
        "user" => $sql->fetch(PDO::FETCH_ASSOC)
      ];

      $nodata = [
        "user" => "no hay datos con el userid ".$_GET['userid']
      ];
      
      if($data['user'] == ''){
        echo json_encode($nodata);
      }else{
        echo json_encode($data);
      }         
      exit();

	  }elseif(isset($_GET['correodocumento'])){

      //Mostrar un usuario en especifico por correo o documento

      $sql = $dbConn->prepare("SELECT * FROM user where (documento = :correodocumento OR correo = :correodocumento)");
      $sql->bindValue(':correodocumento', $_GET['correodocumento']);
      $sql->execute();
      header("HTTP/1.1 200 OK");

      $data = [
        "user" => $sql->fetch(PDO::FETCH_ASSOC)
      ];

      $nodata = [
        "user" => "No hay datos con el documento o correo ".$_GET['correodocumento']
      ];

      if($data['user'] == ''){
        echo json_encode($nodata);
      }else{
        echo json_encode($data);
      }         
      exit();
    }
    else {
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM user");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      $data = [
        "user" => $sql->fetchAll()
      ];
      echo json_encode($data);
      exit();
	}
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    
    if(isset($_POST['nombres']) && isset($_POST['apellidos']) && isset($_POST['documento']) && isset($_POST['correo'])){

      $sql = "INSERT INTO user
            (userid, nombres, apellidos, saldo, documento, correo)
            VALUES
            (CONCAT(:correo,:documento), :nombres, :apellidos, 0, :documento, :correo)";
      $statement = $dbConn->prepare($sql);    
      bindAllValues($statement, $input);        
      
      $sqlexistedoc = $dbConn->prepare("SELECT * FROM user where documento = :documento");
      $sqlexistedoc->bindValue(':documento', $input['documento']);
      $sqlexistedoc->execute();
      $existedoc = $sqlexistedoc->fetch(PDO::FETCH_ASSOC);

      if(isset($existedoc['userid'])){
        $data = [
          "user" => 'El documento '.$input['documento'].', ya se encuentra registrado en la base de datos.'
        ];
        echo json_encode($data);
        exit();
      }

      $sqlexistecorreo = $dbConn->prepare("SELECT * FROM user where correo = :correo");
      $sqlexistecorreo->bindValue(':correo', $input['correo']);
      $sqlexistecorreo->execute();
      $existecorreo = $sqlexistecorreo->fetch(PDO::FETCH_ASSOC);

      if(isset($existecorreo['userid'])){
        $data = [
          "user" => 'El correo '.$input['correo'].', ya se encuentra registrado en la base de datos.'
        ];
        echo json_encode($data);
        exit();
      }
      
      $statement->execute();

      $sqlinsertado = $dbConn->prepare("SELECT * FROM user where userid = CONCAT(:correo,:documento)");
      $sqlinsertado->bindValue(':correo', $input['correo']);
      $sqlinsertado->bindValue(':documento', $input['documento']);
      $sqlinsertado->execute();
      $insert = $sqlinsertado->fetch(PDO::FETCH_ASSOC);

      $postId = $insert;

      if($postId)
      {
            
        $data = [
          "user" => $postId
        ];
        echo json_encode($data);
        header("HTTP/1.1 200 OK");
        exit();
    }
  }else{
         
    $error = [
      "user" => 'Los datos que esta enviando no son los correctos'
    ];
    echo json_encode($error);
    header("HTTP/1.1 200 OK");
    exit();
  }
}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'PATCH')
{
  $input = $_GET;

  if(isset($input['transfervalor']) && isset($input['transferuser']) && isset($input['receiveuser'])){   

    $sqluserexisteTrans = $dbConn->prepare("SELECT * FROM user where userid = :userid");
    $sqluserexisteTrans->bindValue(':userid', $input['transferuser']);
    $sqluserexisteTrans->execute();
    $existetrans = $sqluserexisteTrans->fetch(PDO::FETCH_ASSOC);    

    if($existetrans == ''){
            
        $error = [
          "recharge" => 'El usuario '.$input['transferuser'].' que esta recargando no existe en la base de datos'
        ];
        echo json_encode($error);
        header("HTTP/1.1 202 error");
        exit();

    }

    $sqluserexisteRece = $dbConn->prepare("SELECT * FROM user where userid = :userid");
    $sqluserexisteRece->bindValue(':userid', $input['receiveuser']);
    $sqluserexisteRece->execute();
    $existeRece = $sqluserexisteRece->fetch(PDO::FETCH_ASSOC);    

    if($existeRece == ''){
            
        $error = [
          "recharge" => 'El usuario '.$input['receiveuser'].' al que desea recargar no existe en la base de datos'
        ];
        echo json_encode($error);
        header("HTTP/1.1 202 error");
        exit();

    }  

    $valor = $input['transfervalor'];    
    $postIdTrans = $input['transferuser']; 
    $postIdRece = $input['receiveuser']; 
         
    $sqlsaldoTrans = $dbConn->prepare("SELECT saldo FROM user where userid = '$postIdTrans'");
    $sqlsaldoTrans->execute();
    $saldoactualFinalTrans = $sqlsaldoTrans->fetch(PDO::FETCH_ASSOC);
    $saldoactualTrans = ROUND($saldoactualFinalTrans['saldo']);

    if($saldoactualTrans < $input['transfervalor']){
      $error = [
        "transfer"  => 'el valor que desea tranferir es inferior al que tiene actual, por favor recargar',
        "saldo"     => 'Su cuenta actual tiene $'.$saldoactualTrans
      ];
      header("HTTP/1.1 202 error");
      echo json_encode($error);
      exit();
    }

    $sqlsaldoactualRece = $dbConn->prepare("SELECT saldo FROM user where userid = '$postIdRece'");
    $sqlsaldoactualRece->execute();
    $saldoactualFinalRece = $sqlsaldoactualRece->fetch(PDO::FETCH_ASSOC);
    $saldoactualRece = ROUND($saldoactualFinalRece['saldo']);

    $sqlinsert = "INSERT INTO transfer
    (transfervalor, transferuser, receiveuser)
    VALUES
    (:transfervalor, :transferuser, :receiveuser)";
    $statement = $dbConn->prepare($sqlinsert);    
    bindAllValues($statement, $input);   
    $statement->execute();
    
    $statement = $dbConn->prepare("UPDATE user SET saldo = $saldoactualTrans - :valor WHERE userid = '$postIdTrans'");    
    $statement->bindValue(':valor', $valor);     
    $statement->execute();

    $statement = $dbConn->prepare("UPDATE user SET saldo = $saldoactualRece + :valor WHERE userid = '$postIdRece'");    
    $statement->bindValue(':valor', $valor);     
    $statement->execute();

    $data = [
      "transfer" => 'Se transfieren $'.$valor.' del usuario'.$postIdTrans.' al usuario '.$postIdRece
    ];
    header("HTTP/1.1 200 OK");
    echo json_encode($data);
    exit();

  }else{
    $error = [
      "transfer" => 'Los datos que esta enviando no son los correctos'
    ];
    header("HTTP/1.1 202 error");
    echo json_encode($error);
    exit();
  }
 
  exit();

}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;    

    if(isset($input['valor']) && isset($input['userid'])){

      $sqluserexiste = $dbConn->prepare("SELECT * FROM user where userid = :userid");
      $sqluserexiste->bindValue(':userid', $input['userid']);
      $sqluserexiste->execute();
      $existe = $sqluserexiste->fetch(PDO::FETCH_ASSOC);    
    
      if($existe == ''){
              
          $error = [
            "recharge" => 'El usuario '.$input['userid'].' al que desea recargar no existe en la base de datos'
          ];
          echo json_encode($error);
          header("HTTP/1.1 202 error");
          exit();

      }
        

      $valor = $input['valor'];    
      $postId = $input['userid']; 

      $sqlinsert = "INSERT INTO recharge
      (rechargevalor, rechargeuserid)
      VALUES
      (:valor, :userid)";
      $statement = $dbConn->prepare($sqlinsert);    
      bindAllValues($statement, $input);   
      $statement->execute();
          
      $sqlsaldoactual = $dbConn->prepare("SELECT saldo FROM user where userid = '$postId'");
      $sqlsaldoactual->execute();
      $saldoactualFinal = $sqlsaldoactual->fetch(PDO::FETCH_ASSOC);
      $saldoactual = ROUND($saldoactualFinal['saldo']);
      
      $statement = $dbConn->prepare("UPDATE user SET saldo = $saldoactual + :valor WHERE userid = '$postId'");    
      $statement->bindValue(':valor', $valor);     
      $statement->execute();

      $data = [
        "recharge" => 'Se recargan $'.$valor.' al usuario '.$postId
      ];
      header("HTTP/1.1 200 OK");
      echo json_encode($data);
      exit();

    }else{
      $error = [
        "recharge" => 'Los datos que esta enviando no son los correctos'
      ];
      header("HTTP/1.1 202 error");
      echo json_encode($error);
      exit();
    }
   
    exit();
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>