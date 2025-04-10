<?php

class PublicacionModel extends Database
{
  public function insertPublicacion($correo, $titulo, $nombre_tema, $foto_portada, $instrucciones, $descripcion, 
  $num_likes, $foto_proceso)
  {
    $query1 = "SELECT id_tema FROM tema WHERE nombre_tema = ?";
    $params1 = ["s", $nombre_tema];
    $id_tema = $this->select($query1, $params1);

    if (!$id_tema) {
        throw new Exception("No se encontro tema.");
    }
    $id_tema = $id_tema[0]['id_tema']; // Assuming `select` returns an associative array.

    $query2 = "SELECT correo FROM usuarios WHERE correo = ?";
    $params2 = ["s", $correo];
    $correoUsuario = $this->select($query2, $params2);

    if (!$correoUsuario) {
        throw new Exception("User not found.");
    }
    $correoUsuario = $correoUsuario[0]['correo'];

    // Step 3: Insert into `publicacion` table
    $query3 = "
        INSERT INTO publicacion 
        (id_usuarios, titulo, id_tema, foto_portada, descripcion, num_likes, fecha_creacion, instrucciones) 
        VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)";
    $params3 = [
        ["s", $correoUsuario], 
        ["s", $titulo], 
        ["i", $id_tema], 
        ["s", $foto_portada], 
        ["s", $descripcion], 
        ["i", $num_likes], 
        ["s", $instrucciones]
    ];
    $insertResult = $this->insert($query3, $params3);

    if (!$insertResult) {
        throw new Exception("Failed to insert into publicacion.");
    }

    // Get the last inserted ID
    $id_publicacion = $this->getLastInsertId();

    // Step 4: Insert into `fotos_publicacion` table
    $query4 = "INSERT INTO fotos_publicacion (imagen, id_publicacion) VALUES (?, ?)";
    $params4 = [
        ["s", $foto_proceso],
        ["i", $id_publicacion]
    ];
    $this->insert($query4, $params4);

    return true;
    
    
    /* $query = "CALL insertar_publicacion(?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
      ["s", $correo], 
      ["s", $titulo], 
      ["s", $nombre_tema], 
      ["s", $foto_portada], 
      ["s", $instrucciones], 
      ["s", $descripcion], 
      ["i", $num_likes], 
      ["s", $foto_proceso]
    ];

    return $this->insert($query, $params); */

  }

  public function getTodasPublicaciones()
  {
      return $this->select("CALL traer_todas_publicaciones()");
  }

  public function getPublicacionesUsuario($correo){
    $query = "CALL traer_publicacion_por_usuario(?)";
    $params = ["s", $correo];
    return $this->select($query, $params);
  }

  public function modifyPublicacion($id_publicacion, $correo, $titulo, $nombre_tema, $foto_portada, $descripcion, $instrucciones, $foto_proceso)
{
    $id_tema = null;
    if (!empty($nombre_tema) || $nombre_tema != "") {
        $query1 = "SELECT id_tema FROM tema WHERE nombre_tema = ?";
        $params1 = ["s", $nombre_tema];
        $id_tema = $this->select($query1, $params1);
        if (!$id_tema) {
            throw new Exception("No se encontro tema.");
        }
        $id_tema = $id_tema[0]['id_tema'];
    }

    $query2 = "UPDATE publicacion AS Pub
           JOIN usuarios AS U ON U.correo = Pub.id_usuarios
           SET 
               Pub.titulo = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE Pub.titulo END,
               Pub.id_tema = CASE WHEN ? IS NOT NULL THEN ? ELSE Pub.id_tema END,
               Pub.foto_portada = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE Pub.foto_portada END,
               Pub.descripcion = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE Pub.descripcion END,
               Pub.instrucciones = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE Pub.instrucciones END
           WHERE U.correo = ? AND Pub.id_publicacion = ?";
    $params2 = [
        ["s", $titulo],          // Title
        ["s", $titulo],          // Title (for the CASE condition)
        ["s", $titulo],          // Title (for the CASE condition)
        ["i", $id_tema],         // Topic ID
        ["i", $id_tema],         // Topic ID (for the CASE condition)
        ["s", $foto_portada],    // Cover photo
        ["s", $foto_portada],    // Cover photo (for the CASE condition)
        ["s", $foto_portada],    // Cover photo (for the CASE condition)
        ["s", $descripcion],     // Description
        ["s", $descripcion],     // Description (for the CASE condition)
        ["s", $descripcion],     // Description (for the CASE condition)
        ["s", $instrucciones],   // Instructions
        ["s", $instrucciones],   // Instructions (for the CASE condition)
        ["s", $instrucciones],   // Instructions (for the CASE condition)
        ["s", $correo],          // User's email
        ["i", $id_publicacion]   // Publication ID
    ];

    $this->insert($query2, $params2);

    if (!empty($foto_proceso) || $foto_proceso != "") {
        $query3 = "UPDATE fotos_publicacion
           JOIN publicacion AS Pub ON fotos_publicacion.id_publicacion = Pub.id_publicacion
           JOIN usuarios AS U ON U.correo = Pub.id_usuarios
           SET fotos_publicacion.imagen = ?
           WHERE U.correo = ? AND Pub.id_publicacion = ?";
        $params3 = [
            ["s", $foto_proceso],  
            ["s", $correo],         
            ["i", $id_publicacion]  
        ];

        $this->insert($query3, $params3);
        return true;
    }
    return true;
}

  
  public function deletePublicacion($id_publicacion, $correo)
  {
    $query1 = " DELETE fp 
            FROM fotos_publicacion fp
            JOIN publicacion p ON p.id_publicacion = fp.id_publicacion
            WHERE p.id_usuarios = ? AND fp.id_publicacion = ?";
    $params1 = [ 
      ["s", $correo], 
      ["i", $id_publicacion]
    ];

    $this->delete($query1, $params1); 

    $query2 = "DELETE FROM publicacion 
        WHERE id_publicacion = ?;";
    $params2 = [["i", $id_publicacion]];

    $this->delete($query2, $params2); 

    return true;



    /* $query = "CALL eliminar_publicacion(?, ?)";
    $params = [
      ["i", $id_publicacion], 
      ["s", $correo]
    ];
    return $this->insert($query, $params); */
  }

  public function orderAscPublicacion()
  {
    $query = " SELECT 
       Pub.id_publicacion, 
       Pub.id_usuarios, 
       Pub.titulo, 
       tema.nombre_tema, 
       Pub.foto_portada, 
       Pub.descripcion, 
       Pub.num_likes, 
       Pub.estado, 
       Pub.fecha_creacion, 
       Pub.instrucciones,
       fotos_publicacion.imagen as foto_proceso
   FROM 
       publicacion AS Pub
   JOIN 
       tema ON tema.id_tema = Pub.id_tema
   JOIN 
       fotos_publicacion ON fotos_publicacion.id_publicacion = Pub.id_publicacion
	ORDER BY tema.nombre_tema;";
    return $this->select($query);
  }

  public function orderDescPublicacion()
  {
    $query = "SELECT 
       Pub.id_publicacion, 
       Pub.id_usuarios, 
       Pub.titulo, 
       tema.nombre_tema, 
       Pub.foto_portada, 
       Pub.descripcion, 
       Pub.num_likes, 
       Pub.estado, 
       Pub.fecha_creacion, 
       Pub.instrucciones,
       fotos_publicacion.imagen as foto_proceso
   FROM 
       publicacion AS Pub
   JOIN 
       tema ON tema.id_tema = Pub.id_tema
   JOIN 
       fotos_publicacion ON fotos_publicacion.id_publicacion = Pub.id_publicacion
	ORDER BY tema.nombre_tema DESC;";
    return $this->select($query);
  }
}
?>
