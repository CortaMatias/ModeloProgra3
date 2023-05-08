<?php 
require_once("./AccesoDatos.php");
use PDO;
use ejercicio_bd\AccesoDatos;

 class Usuario {
    public int $id;
    public string $nombre;
    public string $correo;
    public string $clave;
    public int $id_perfil;
    public string $perfil;


    public function toJSON(){
       $alumno = array("nombre" =>$this->nombre,
       "correo" => $this->correo,
       "clave" => $this->clave
        );
        return json_encode($alumno);
    }

    public function GuardarEnArchivo() {
		$retorno = false;	
		
			//ABRO EL ARCHIVO
			$ar = fopen("./archivos/usuarios.json", "a");//A - append()
			//ESCRIBO EN EL ARCHIVO CON FORMATO: CLAVE-VALOR_UNO-VALOR_DOS
			$cant = fwrite($ar, $this->toJSON(). "\r\n");
			if($cant > 0)$retorno =array("exito" => true, "mensaje" => "agregado");						
			else{$retorno=array("exito" => false, "mensaje" => "ERROR");}
			//CIERRO EL ARCHIVO
			fclose($ar);
		
		return $retorno;
	}

    public static function TraerTodosJSON(){
        $retorno = [];			
        $ar = fopen("./archivos/usuarios.json", "r");
		while(!feof($ar)){
            $linea = fgets($ar);
            $usuario_leido = json_decode($linea);
            if(isset($usuario_leido)){
                $usuario = new Usuario();
                $usuario->nombre = $usuario_leido->nombre;
                $usuario->correo = $usuario_leido->correo;
                $usuario->clave = $usuario_leido->clave;
                array_push($retorno, $usuario);
            }
        }		
		return $retorno;
    }

    public function Agregar(){
        
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            
            $consulta =$objetoAccesoDato->retornarConsulta("INSERT INTO usuarios (nombre, correo, clave,id_perfil)"
                                                        . "VALUES(:nombre, :correo, :clave, :id_perfil)");
            
            $consulta->bindValue(':nombre', $this->nombre,  PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':id_perfil', $this->id_perfil,  PDO::PARAM_INT);    
            return $consulta->execute();                 
    }


    public static function TraerTodos()
    {    
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios");        
        
        $consulta->execute();
        
        $consulta->setFetchMode(PDO::FETCH_INTO, new Usuario);                                               

        return $consulta;
    }


}

?>