<?php
include "Model/Database.php";
class UserModel extends Database
{
    public function getUsers($limit)
    {
        return $this->select("SELECT * FROM Usuarios LIMIT ?", ["i", $limit]);
    }

    public function getUser($correo)
    {
        $query = "CALL traer_datos_usuario(?)";
        $params = ["s", $correo];
        return $this->select($query, $params);
    }

    public function manage_usuario($operacion, $correo, $nombre, $apellido, $nombre_usuario, $foto_perfil, $contra)
    {

        //echo "existo";
        $query = "CALL manage_usuario(?, ?, ?, ?, ?, ?, ?);";
        $params = [
        ["i", $operacion],
        ["s", $correo],        
        ["s", $nombre],       
        ["s", $apellido],
        ["s", $nombre_usuario],       
        ["b", $foto_perfil], 
        ["s", $contra]        
        ];
        //print($operacion);
        return $this->insert($query, $params);
                
       
    }

    public function modifyUser($correo, $nombre, $apellido, $contra, $foto_perfil)
    {
        $query = "UPDATE usuarios
        SET 
            nombre = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE nombre END,
            apellido = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE apellido END,
            contra = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE contra END,
            foto_perfil = CASE WHEN ? IS NOT NULL AND ? != '' THEN ? ELSE foto_perfil END
        WHERE correo = ?;";
    
    $params = [
        ["s", $nombre],  
        ["s", $nombre],  
        ["s", $nombre],  
        ["s", $apellido],
        ["s", $apellido],
        ["s", $apellido],
        ["s", $contra],
        ["s", $contra],
        ["s", $contra],
        ["s", $foto_perfil],
        ["s", $foto_perfil],
        ["s", $foto_perfil],
        ["s", $correo],  
    ];

        return $this->insert($query, $params);
    }

    public function verifyUser($correo, $contra)
    {
        $query = "CALL verificar_usuario(?,?)";
        
        $params = [
        ["s", $correo], 
        ["s", $contra]
        ];

        return $this->selectMultipleParams($query, $params);
    }
}
?>