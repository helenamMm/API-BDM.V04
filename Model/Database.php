<?php
//include "inc/config.php";
class Database
{
    protected $connection = null;
    public function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME, DB_PORT);
    	
            if ( mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");   
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());   
        }			
    }

    public function select($query = "" , $params = []){
        try {
            $stmt = $this->executeStatement( $query , $params );
            $result = $stmt->get_result()->fetch_assoc();  
            $stmt->close();
        
            if ($result && isset($result['foto_perfil'])) {
            $result['foto_perfil'] = base64_encode($result['foto_perfil']);
            }
            /* $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);			
            $stmt->close();
            foreach ($result as &$row) {
                if (isset($row['foto_perfil'])) {
                    $row['foto_perfil'] = base64_encode($row['foto_perfil']);
                }
            } para traer varios registros*/
            return $result;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    public function selectMultipleParams($query = "", $params = []){
        try {
            $stmt = $this->executeMultipleParams($query, $params);
            $result = $stmt->get_result()->fetch_assoc();  
            $stmt->close();
        
            if ($result && isset($result['foto_perfil'])) {
            $result['foto_perfil'] = base64_encode($result['foto_perfil']);
            }
            elseif($result && isset($result['imagen'])){
            $result['imagen'] = base64_encode($result['imagen']);
            }
            return $result;
            }catch (Exception $e){
            throw new Exception($e->getMessage());
            return false;
            }   
    }
    public function selectMpFilasMasivas($query = "", $params = []){//una disculpa por el nombre de esta funcion 
        try {
            $stmt = $this->executeMultipleParams($query, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);			
            $stmt->close();
            foreach ($result as &$row) {
                if (isset($row['foto_perfil'])) {
                    $row['foto_perfil'] = base64_encode($row['foto_perfil']);
                }
                else if(isset($row['imagen']) && !empty($row['imagen'])){
                    $row['imagen'] = base64_encode($row['imagen']);
                }
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return false;
    }

    public function insert($query = "" , $params = []){
        try {
            $stmt = $this->executeMultipleParams($query, $params);          
            $stmt->close();
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return false;
    }
    
    private function executeStatement($query = "" , $params = []){
        try {
            $stmt = $this->connection->prepare( $query );
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            if( $params ) {
                $stmt->bind_param($params[0], $params[1]);
            }
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }	
    }
    public function delete($query = "", $params = []){
        try {
        
        $stmt = $this->executeMultipleParams($query, $params);
        
        // Check if rows were affected
        if ($stmt->affected_rows > 0) {
            return true;  
        } else {
            return false; 
        }
        } catch (Exception $e) {
        // Handle any errors
        throw new Exception($e->getMessage());
        }
    }

    private function executeMultipleParams($query = "", $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
    
            if ($stmt === false) {
                throw new Exception("Unable to prepare statement: " . $this->connection->error);
            }
    
            $types = '';
            $values = [];
            $blobIndexes = [];
    
            // Identify parameter types and collect values
            foreach ($params as $index => $param) {
                $types .= $param[0];
                $values[] = $param[1];
    
                // If it's a BLOB, store its index
                if ($param[0] === "b") {
                    $blobIndexes[] = $index;
                }
            }

           /*   echo "<pre>";
            echo "Types: " . $types . "\n";
            echo "Values: ";
            print_r($values);
            echo "</pre>";  */
    
            // Bind parameters except BLOBs
            $stmt->bind_param($types, ...$values);
    
            // Bind BLOBs separately using send_long_data()
            foreach ($blobIndexes as $index) {
                $stmt->send_long_data($index, $values[$index]);
            }

           /*  echo "<pre>";
            print_r($values);
            echo "</pre>"; */
    
            $stmt->execute();

           /* if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $this->connection->error);
            }  */ //este debuggeador no fall namas que luego te marca error de que se ducplico y namas no 

            return $stmt;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}
?>
