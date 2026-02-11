<?php

class ValidadorMesa {

    public static function ValidarAltaMesa($estado)
    {
        if (!isset($estado) || trim($estado) === '') {
            throw new Exception("El estado es obligatorio");
        }

        if ($estado !== 'cerrada') {
            throw new Exception("Para dar de alta una mesa, el estado debe ser 'cerrada'");
        }

        return true;
    }
    
}