<?php

class ValidadorUsuario {


    public static function ValidarUsuarioNuevo($usuario) {
        if (!isset($usuario->usuario) || !is_string($usuario->usuario) || strlen($usuario->usuario) > 20) {
            return false;
        }

        if (!isset($usuario->clave) || !is_string($usuario->clave)) {
            return false;
        }

        $tiposValidos = ['cocinero', 'cervecero', 'bartender','socio','cliente','mozo'];
        if (!isset($usuario->tipo) || !in_array(strtolower($usuario->tipo), $tiposValidos)) {
            return false;
        }

        $estadosValidos = ['activo', 'suspendido', 'de baja'];
        if (!isset($usuario->estado) || !in_array(strtolower($usuario->estado), $estadosValidos)) {
            return false;
        }

        if (!self::esFechaValida($usuario->fechaAlta)) {
            return false;
        }

        return true;
    }


    public static function ValidarCamposModificables($usuario)
    {
        $tiposValidos = ['cocinero', 'cervecero', 'bartender', 'socio', 'cliente', 'mozo'];
        $estadosValidos = ['activo', 'suspendido', 'de baja'];

        if (isset($usuario->tipo) && !in_array(strtolower($usuario->tipo), $tiposValidos)) {
            return false;
        }

        if (isset($usuario->estado) && !in_array(strtolower($usuario->estado), $estadosValidos)) {
            return false;
        }

        if (isset($usuario->fechaAlta) && !self::esFechaValida($usuario->fechaAlta)) {
            return false;
        }

        if (isset($usuario->fechaBaja) && !self::esFechaValida($usuario->fechaBaja)) {
            return false;
        }

        
        $modificables = ['tipo', 'estado', 'fechaAlta', 'fechaBaja'];
        $camposPresentes = array_intersect($modificables, array_keys((array)$usuario));
        if (empty($camposPresentes)) {
            return false; 
        }

        return true;
    }


    private static function esFechaValida($fecha)
    {
        if ($fecha instanceof \DateTime) {
            $fecha = $fecha->format('Y-m-d');
        }
        else
        {
            $formatosValidos = ['m/d/Y','d/m/Y','Y/m/d','d-m-Y','Y-m-d','Y-d-m'];
            var_dump("Entra a obtenerfecha");
            foreach ($formatosValidos as $formato) {
                $d = \DateTime::createFromFormat($formato, $fecha);
                if ($d && $d->format($formato) === $fecha) {
                    var_dump("La fecha es: " . $d->format('Y-m-d'));
                    return $d->format('Y-m-d');
                }
            }
        }
        return false;
    }

 



}