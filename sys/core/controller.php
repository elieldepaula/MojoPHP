<?php

if ( ! defined('BASE_PATH')) exit('Acesso negado!');

/**
 * Esta � classe base para os controllers da aplica��o, ela herda a classe
 * MJ_Object que traz as op��es de de registro de objetos e recupera��o
 * das inst�ncias dos mesmos.
 * 
 * @package Mojo*PHP
 * @author Eliel de Paula <elieldepaula@gmail.com
 * @since 26/02/2012
 * 
 */

class MJ_Controller extends MJ_Object {
    
    function __construct(){
        parent::__construct();
    }
    
}