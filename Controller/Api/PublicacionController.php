<?php
class PublicacionController extends BaseController
{
 
  public function managePublicacionAction()
    {
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

      // Validate and decode input
      if (!isset($inputData['id_publicacion'])||
          !isset($inputData['nombre']) || !isset($inputData['tema_id']) || 
          !isset($inputData['descripcion']) || !isset($inputData['correo']) || 
          !isset($inputData['num_likes'])) {
          throw new Exception("Invalid input.");
        } 

        if (isset($inputData['video']) && !empty($inputData['video'])) {
        $tipo = 'video';
        $video = $inputData['video'];
        $imagen = null;
        } else if (isset($inputData['imagen']) && !empty($inputData['imagen'])) {
        $tipo = 'imagen';
        $imagen = base64_decode($inputData['imagen']); 
        $video = null; 
        } else {
        if ($operacion == 1) {
            throw new Exception("Either 'imagen' or 'video' must be provided for CREATE operation.");
        }
        }
        $id_publicacion = $inputData['id_publicacion'];
        $nombre_publicacion = $inputData['nombre']; //titulo de la publicacion 
        $nombre_tema = $inputData['tema_id']; // tiene que ser un numero 
        $descripcion = $inputData['descripcion'];
        $correo = $inputData['correo'];
        $num_likes = $inputData['num_likes'];
      
        $publicacionModel = new PublicacionModel();

            switch ($operacion) {
                case 1: 
                    try { 
                        $result = $publicacionModel->managePublicacion(
                            $operacion, 
                            $id_publicacion, 
                            $nombre_publicacion, 
                            $nombre_tema, 
                            $descripcion, 
                            $correo, 
                            $num_likes, 
                            $tipo, 
                            $imagen, 
                            $video
                        );
                        
                        if ($result) {
                            $responseData = json_encode(["message" => "publicacion agregado exitosamente."]);
                        } else {
                            throw new Exception("Fallo al insertar publicacion.");
                        }
                    } 
                    catch (Exception $e) {
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
                //updetear publicacion    
                    try{
                        $result = $publicacionModel->managePublicacion($operacion, $id_publicacion, $nombre_publicacion, $nombre_tema, $descripcion, 
                        $correo, $num_likes, $tipo, $imagen, $video);
                        if ($result) {
                        $arrPublicacion = $publicacionModel->getPublicaciones(2, $id_publicacion, NULL);
                        $responseData = json_encode($arrPublicacion);
                        } else {
                        throw new Exception("Fallo al actualizar publicacion.");
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
                        $result = $publicacionModel->managePublicacion($operacion, $id_publicacion, $nombre_publicacion, $nombre_tema, $descripcion, 
                        $correo, $num_likes, $tipo, $imagen, $video);
                        
                        if ($result) {
                            $responseData = json_encode(["message" => "Publicacion eliminado exitosamente."]);
                        } else {
                            throw new Exception("Fallo al eliminar publicacion.");
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

    public function listPublicacionAction(){
        //echo "estoy dentro de listUserAction";
        $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);

        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }

        if (!isset($inputData['operacion']) || !isset($inputData['id_publicacion']) || !isset($inputData['correo']) ) {
            throw new Exception("Invalid input.");
        }

        $operacion = $inputData['operacion'];
        $id_publicacion = $inputData['id_publicacion'];
        $correo = $inputData['correo'];
        
        $publicacionModel = new PublicacionModel();
        $arrPublicacion = $publicacionModel->getPublicaciones($operacion, $id_publicacion, $correo);
        if($operacion == 1 || $operacion == 3){
            $responseData = json_encode(["publicaciones" => $arrPublicacion], JSON_UNESCAPED_UNICODE); //MUCHOS REGISTROS  
        }
        else {
            $responseData = json_encode($arrPublicacion);
        }
 
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
