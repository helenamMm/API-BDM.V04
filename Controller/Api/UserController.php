<?php
class UserController extends BaseController
{
    public function manageUserAction()
    {
        
        //echo "estoy dentro";
        $strErrorDesc = '';
        $responseData = '';

        try {
            $requestMethod = $_SERVER["REQUEST_METHOD"];
            $inputData = json_decode(file_get_contents("php://input"), true);

            if (strtoupper($requestMethod) != 'POST') {
                throw new Exception("Method not supported.");
            }

            if (!isset($inputData['operacion'])) {
                throw new Exception("Se necesita un numero de operacion.");
            }

            $operacion = intval($inputData['operacion']);

            if (!isset($inputData['correo']) || !isset($inputData['nombre']) || 
                        !isset($inputData['apellido']) || !isset($inputData['nombre_usuario']) ||
                        !isset($inputData['foto_perfil']) || !isset($inputData['contra'])) {
                        throw new Exception("Missing required parameters for user insertion.");
                    }

            $correo = $inputData['correo'];
            $nombre = $inputData['nombre'];
            $apellido = $inputData['apellido'];
            $nombre_usuario = $inputData['nombre_usuario'];
            $foto_perfil = base64_decode($inputData['foto_perfil']); 
            $contra = $inputData['contra'];

            $userModel = new UserModel();

            switch ($operacion) {
                case 1: 
                  try { 
                    $result = $userModel->manage_usuario($operacion, $correo, $nombre, $apellido, $nombre_usuario, $foto_perfil, $contra);

                    if ($result) {
                        $responseData = json_encode(["message" => "Usuario agregado exitosamente."]);
                    } else {
                        throw new Exception("Fallo al insertar usuario.");
                    }
                    } catch (Exception $e) {
                        $strErrorDesc = $e->getMessage();
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                        }
                    
                        if (!$strErrorDesc) {
                        $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 201 OK']);
                        } else {
                        $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
                        }   
                    break;

                case 2: 
                //updetear info de usuario    
                    try{
                        $result = $userModel->manage_usuario($operacion, $correo, $nombre, $apellido, $nombre_usuario, $foto_perfil, $contra);
                   
                        if ($result) {
                        $arrUsers = $userModel->getUser($correo);
                        $responseData = json_encode($arrUsers);
                        } else {
                        throw new Exception("Fallo al actualizar usuario.");
                        }
                    } catch (Error $e) {
                    $strErrorDesc = $e->getMessage();
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }
            
                    if (!$strErrorDesc) {
                        $this->sendOutput(
                        $responseData,
                        array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                        );
                    } else {
                        $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
                    }
                    break;

                case 3:
                    try{
                        $result = $userModel->manage_usuario($operacion, $correo, $nombre, $apellido, $nombre_usuario, $foto_perfil, $contra);
                        
                        if ($result) {
                            $responseData = json_encode(["message" => "Usuario eliminado exitosamente."]);
                        } else {
                            throw new Exception("Fallo al eliminar usuario.");
                        }
                    } catch (Error $e) {
                        $strErrorDesc = $e->getMessage();
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }
                
                    if (!$strErrorDesc) {
                        $this->sendOutput(
                            $responseData,
                            array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                        );
                    } else {
                        $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
                    }
                    
                    
                    break;
                default:
                    throw new Exception("Invalid operation type.");
            }

        } catch (Exception $e) {
            $strErrorDesc = $e->getMessage();
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    }

    public function listUserAction(){
        //echo "estoy dentro de listUserAction";
        $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);

        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }

        if (!isset($inputData['correo'])) {
            throw new Exception("Invalid input.");
        }

        $correo = $inputData['correo'];

        $userModel = new UserModel();
        $arrUsers = $userModel->getUser($correo);
        $responseData = json_encode($arrUsers);
        //$responseData = json_encode(["users" => $arrUsers], JSON_UNESCAPED_UNICODE); MUCHOS REGISTROS 
        //print_r($arrUsers);

        
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
        }
    
    } 

    public function verifyAction()
    {
        $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);
    
        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }
    
        if (!isset($inputData['correo']) || !isset($inputData['contra'])) {
            throw new Exception("Invalid input.");
        }
        $correo = $inputData['correo'];
        $contra = $inputData['contra'];

        $userModel = new UserModel();
        $result = $userModel->verifyUser($correo, $contra);
        if ($result['is_valid'] === 1) {
            $responseData = json_encode(["message" => "Credenciales válidas."]);
            $statusHeader = 'HTTP/1.1 200 OK'; 
        } else {
            $responseData = json_encode(["message" => "Credenciales no válidas."]);
            $statusHeader = 'HTTP/1.1 401 Unauthorized'; 
        }
        }catch (Exception $e) {
            $strErrorDesc = $e->getMessage();
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        
            if (!$strErrorDesc) {
                $this->sendOutput($responseData, ['Content-Type: application/json', $statusHeader]);
            } else {
                $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
            }
    
    }

    public function manageContactosAction(){
        $strErrorDesc = '';
        $responseData = '';
    
        try {
            $requestMethod = $_SERVER["REQUEST_METHOD"];
            $inputData = json_decode(file_get_contents("php://input"), true);
    
            if (strtoupper($requestMethod) != 'POST') {
                throw new Exception("Method not supported.");
            }
    
            if (!isset($inputData['operacion'])) {
                throw new Exception("Se necesita un número de operación.");
            }
    
            if (!isset($inputData['correo']) || !isset($inputData['contacto'])) {
                throw new Exception("Invalid input.");
            }
    
            $operacion = intval($inputData['operacion']);
            $correo = $inputData['correo'];
            $contacto = $inputData['contacto'];

            if(isset($inputData['bloqueado']) && !empty($inputData['bloqueado'])){
                //ando aqui dentro de bloqueado
                $bloqueado = $inputData['bloqueado'];
            }
            else{
                $bloqueado = NULL;
            }
                        
    
            $userModel = new UserModel();
    
            switch ($operacion) {
                case 1: // Agregar contacto
                    try {
                      //echo "estoy dentro";
                        $result = $userModel->manageContacto($operacion, $correo, $contacto, $bloqueado);
                        
                        if ($result) {
                            $responseData = json_encode(["message" => "Contacto agregado exitosamente."]);
                        } else {
                            throw new Exception("Fallo al insertar contacto.");
                        }
                    } catch (Exception $e) {
                        $strErrorDesc = $e->getMessage();
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }
    
                    if (!$strErrorDesc) {
                        $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 201 Created']);
                    } else {
                        $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
                    }
                    break;
    
                case 2: // Eliminar contacto
                    try {
                        $result = $userModel->manageContacto($operacion, $correo, $contacto, $bloqueado);
    
                        if ($result) {
                            $responseData = json_encode(["message" => "Contacto eliminado exitosamente."]);
                        } else {
                            throw new Exception("Fallo al eliminar contacto.");
                        }
                    } catch (Exception $e) {
                        $strErrorDesc = $e->getMessage();
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }
    
                    if (!$strErrorDesc) {
                        $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
                    } else {
                        $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
                    }
                    break;
                case 3: //bloquear contacto
                    try {
                        $result = $userModel->manageContacto($operacion, $correo, $contacto, $bloqueado);
                        $arrContactos = $userModel->getContacto($correo);
                        $responseData = json_encode(["contactos" => $arrContactos], JSON_UNESCAPED_UNICODE); //MUCHOS REGISTROS  
                    } catch (Exception $e) {
                        $strErrorDesc = $e->getMessage();
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }
    
                    if (!$strErrorDesc) {
                        $this->sendOutput(
                            $responseData,
                            array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                        );
                    } else {
                        $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
                    }
                    break;
    
                default:
                    throw new Exception("Invalid operation type.");
            }
        } catch (Exception $e) {
            $strErrorDesc = $e->getMessage();
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
        }
    }

    public function listFavoritoAction(){
        $strErrorDesc = '';
            $responseData = '';
            try {
            $requestMethod = $_SERVER["REQUEST_METHOD"];
            $inputData = json_decode(file_get_contents("php://input"), true);
    
            if (strtoupper($requestMethod) != 'POST') {
                throw new Exception("Method not supported.");
            }
    
            if (!isset($inputData['correo']) ) {
                throw new Exception("Invalid input.");
            }
    
            $correo = $inputData['correo'];
            
            $userModel = new UserModel();
            $arrContactos = $userModel->getContacto($correo);
            $responseData = json_encode(["contactos" => $arrContactos], JSON_UNESCAPED_UNICODE); //MUCHOS REGISTROS  
     
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        
            if (!$strErrorDesc) {
                $this->sendOutput(
                    $responseData,
                    array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                );
            } else {
                $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
            }
      }
}
?>
