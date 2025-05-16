<?php
include "Model/Database.php";
class UserModel extends Database
{
    
    public function getUser($correo)
    {
        $query = "CALL traer_datos_usuario(?)";
        $params = ["s", $correo];
        return $this->select($query, $params);
    }

    public function manage_usuario(
    $operacion, 
    $correo, 
    $nombre,
    $apellido, 
    $nombre_usuario, 
    $foto_perfil, 
    $contra)
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

    public function verifyUser($correo, $contra){
        $query = "CALL verificar_usuario(?,?)";
        
        $params = [
        ["s", $correo], 
        ["s", $contra]
        ];

        return $this->selectMultipleParams($query, $params);
    }

    public function manageContacto($operacion, $correo, $contacto, $bloquedo){
        $query = "CALL manage_contactos(?,?,?,?)";
        
        $params = [
        ["i", $operacion],
        ["s", $correo], 
        ["s", $contacto],
        ["i", $bloquedo]
        ];

        return $this->insert($query, $params);  
    }

    public function getContacto($correo){
        $query = "CALL list_contactos(?)";
        
        $params = [
        ["s", $correo]
        ];

        return $this->selectMpFilasMasivas($query, $params); 
    }
}
?>