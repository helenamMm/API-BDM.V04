<?php

class PublicacionModel extends Database
{
    public function managePublicacion($operacion, $id_publicacion, $nombre_publicacion, $nombre_tema, $descripcion, 
    $correo, $num_likes, $tipo, $imagen, $video)
    {
        $query = "CALL manage_publicaciones(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $params = [
        ["i", $operacion],
        ["i", $id_publicacion],
        ["s", $nombre_publicacion],        
        ["i", $nombre_tema],       
        ["s", $descripcion],
        ["s", $correo],
        ["i", $num_likes],
        ["s", $tipo],       
        ["b", $imagen], 
        ["s", $video]        
        ];
        //print($operacion);
        return $this->insert($query, $params);  
    }

  public function getPublicaciones($operacion, $id_publicacion, $correo){
    $query = "CALL list_publicaciones(?, ?, ?)";
    $params = [
        ["i", $operacion],
        ["i", $id_publicacion],
        ["s", $correo] 
    ];
    if($operacion == 1 || $operacion == 3 ){
        return $this->selectMpFilasMasivas($query, $params);
    }
    else {
        //echo "estoy aqui dentro de dos";
        return $this->selectMultipleParams($query, $params);
    }
    
  }

}
?>
