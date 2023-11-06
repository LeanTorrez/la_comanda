<?php
class Trabajador{
    
    public $id;
    public $nombre;
    public $email;
    public $clave;
    
    public function SetDatos($nombre, $email, $clave){
        $this->nombre = $nombre;
        $this->email = $email;
        $this->clave = $clave;
    }
}