<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Aka\Curl\Helper;

/**
 * Description of PostFields
 *
 * @author Alex
 */
class PostFields {

    /**
     * Post fileds containers
     * 
     * @var array 
     */
    private $fieldsCotainer = array();

    public function __construct(){}

    /**
     * Sets POST param
     * 
     * @param type $key
     * @param type $value
     * @return \Aka\Curl\Helper\PostFields
     */
    public function setParam($key, $value){
        if(trim($key) === ''){
            //TODO should through an exeption here
            return $this;
        }
        
        $this->fieldsCotainer[$key] = urldecode($value);
        
        return $this;
    }

    /**
     * Gets the URL-encoded query string
     *  
     * @return string 
     */
    public function getQuery() {
        $fieldsString = '';
        foreach ($this->fieldsCotainer as $key => $value) {
            $fieldsString .= $key . '=' . $value . '&';
        }
       // return rtrim($fieldsString, '&');
        
        return http_build_query($this->fieldsCotainer);
    }
    
    public function size(){
        return count($this->fieldsCotainer);
    }

}
