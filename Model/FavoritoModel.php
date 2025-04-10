<?php
class FavoritoModel extends Database
{
  public function insertFavorito($id_publicacion, $correo)
  {
    $query1 = "INSERT INTO Favoritos(id_usuarios, id_publicacion)
        VALUES(?, ?);";
    $params1 = [
      ['s', $correo],
      ["i", $id_publicacion]
    ];

    $this->insert($query1, $params1); 

    $query2 = "UPDATE publicacion
        SET num_likes = (num_likes + 1)
        WHERE id_publicacion = ?;";
    $params2 = [["i", $id_publicacion]];
    
    $this->insert($query2, $params2);
    
    return true;
    /* $query = "CALL agregar_favoritos(?, ?)";
    $params = [
      ["i", $id_publicacion], 
      ["s", $correo]
    ];
    return $this->insert($query, $params); */
  }

  public function getFavorito($correo)
  {
    $query = "CALL mostrar_favoritos_por_usuarios(?)";
    $params = ["s", $correo];

    return $this->select($query, $params);
  }

  public function deleteFavorito($id_publicacion, $correo)
  {
    $query1 = "UPDATE publicacion
        SET
        num_likes = num_likes - 1
        WHERE id_publicacion = ?;";
    $params1 = [["i", $id_publicacion]];
    
    $this->insert($query1, $params1);

    $query2 = "DELETE FROM Favoritos
    WHERE  id_usuarios = ? AND id_publicacion = ?;";
    $params2 = [
      ['s', $correo],
      ["i", $id_publicacion]
    ];
    $this->insert($query2, $params2);

    return true;
    
    /* $query = "CALL  eliminar_favorito(?, ?)";
    $params = [
      ["i", $id_publicacion], 
      ['s', $correo]
    ];
    return $this->insert($query, $params); */
  }
}
?>

