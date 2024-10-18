<?php

class Customer extends CustomerCore
{
    public $firstname;
    public $lastname;
    public $email;
    public $passwd;
    public $active = 1;  // Activar al cliente automáticamente

    public static $definition = array(
        'table' => 'customer',
        'primary' => 'id_customer',
        'fields' => array(
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 512),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 512),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255),
            'passwd' => array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 255),
            'active' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true, 'default' => 1),
        ),
    );


}
