<?php

require_once 'Zend/Filter/Interface.php';

class Recordshelf_Filter_TwitterScreenName implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return str_replace('@', '', $value);
    }
}