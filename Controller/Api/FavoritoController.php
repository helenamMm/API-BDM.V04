<?php
class FavoritoController extends BaseController
{
  public function manageFavoritoAction(){
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

        if (!isset($inputData['id_publicacion']) || !isset($inputData['correo'])) {
            throw new Exception("Invalid input.");
        }

        $operacion = intval($inputData['operacion']);
        $id_publicacion = $inputData['id_publicacion'];
        $correo = $inputData['correo'];

        $FavoritoModel = new FavoritoModel();

        switch ($operacion) {
            case 1: // Agregar favorito
                try {
                  //echo "estoy dentro";
                    $result = $FavoritoModel->manageFavorito($operacion, $correo, $id_publicacion);
                    
                    if ($result) {
                        $responseData = json_encode(["message" => "Favorito agregado exitosamente."]);
                    } else {
                        throw new Exception("Fallo al insertar favorito.");
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

            case 2: // Eliminar favorito
                try {
                    $result = $FavoritoModel->manageFavorito($operacion, $correo, $id_publicacion);

                    if ($result) {
                        $responseData = json_encode(["message" => "Favorito eliminado exitosamente."]);
                    } else {
                        throw new Exception("Fallo al eliminar favorito.");
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
        
        $favoritoModel = new FavoritoModel();
        $arrFavorito = $favoritoModel->getFavorito($correo);
        $responseData = json_encode(["favoritos" => $arrFavorito], JSON_UNESCAPED_UNICODE); //MUCHOS REGISTROS  
 
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
