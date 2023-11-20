<?php
class Archivos{
    /**
     * @param funcion
     * Tipo array, que especifica la clase y metodo que sera invocado en el call_user_func
     * @param columnas
     * nombre de las columnas que seran escritas en el primer renglon del csv
     * @return string
     * retorna un string con la ruta del archivo
     */
    public static function BaseDatosCSV(array $funcion,array $columnas){
        error_reporting(E_ERROR | E_PARSE);
        //llamado al callback
        $lista = call_user_func($funcion);
        //creo un archivo temporal csv, este mismo me devuelve un string con la ruta de la misma
        $name = tempnam(sys_get_temp_dir(), 'csv');
        $archivo = fopen($name, 'w');
        fputcsv($archivo,$columnas);
        foreach($lista as $obj){
            fputcsv($archivo,$obj->CrearArray());
        } 
        fclose($archivo);
        
        error_reporting(E_ALL);
        //retorno la ruta del archivo temporal
        return $name;
    }

    /**
     * @param stream
     * el archivo que es pasado por Slim
     * @param funcion
     * callable que es la funcion que instanciara las diferentes clases
     * @return bool
     * retorna true si fue exitoso, false si ocurrio un error
     */
    public static function CSVBaseDatos($stream, array $funcion){
        $retorno = false;
        if(!empty($stream)){
            $retorno = true;
            //uso getStream(), el mismo lee el csv y lo retorna como string
            $lista = explode("\n",$stream->getStream());
            for($i = 1;$i < count($lista);$i++){
                //me fijo si la lista esta vacio, este caso seria en la ultima linea del csv
                if(!empty($lista[$i])){
                    //separo los diferentes partes del string csv
                    $parametros = explode(",",$lista[$i]);
                    //llamo al callback que instancia la clase con sus parametros
                    $obj = call_user_func($funcion, $parametros);
                    //en caso de error al insertar el objeto devuelve 0
                    if($obj->Insertar() === 0){
                        $retorno = false;
                        break;
                    }
                }
            }
        }
        return $retorno;
    }
    
}