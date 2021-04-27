<?php

  //Funcion para obtener los parametros por la url
  function getParams($input){
      $filterParams = [];
      foreach($input as $param => $value){
              $filterParams[] = "$param=:$param";
      }
      return implode(",", $filterParams);
  }

  //Funcion para obtenere los valores desde el body form-data
  function bindAllValues($statement, $params){
  foreach($params as $param => $value){
      $statement->bindValue(':'.$param, $value);
  }        
  return $statement;
  }
?>