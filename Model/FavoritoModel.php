<?php
class FavoritoModel extends Database
{
  public function manageFavorito($operacion, $correo, $id_publicacion)
    {
     // echo "estoy dentro2";
        $query = "CALL manage_favoritos(?, ?, ?);";
        $params = [
        ["i", $operacion],
        ["s", $correo], 
        ["i", $id_publicacion]
        ];
        //print($operacion);
        return $this->insert($query, $params);  
    }

  public function getFavorito($correo){
    $query = "CALL list_favoritos(?)";
    $params = [
        ["s", $correo] 
    ];
    return $this->selectMpFilasMasivas($query, $params); //aqui puede haber error 
  }
  public function getTotalFavoritos($correo) {
    $query = "SELECT total_favoritos(?) AS total";
    $params = [
        ["s", $correo]
    ];

   return $this->select($query, $params);
}
}
?>

