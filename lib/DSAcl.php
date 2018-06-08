<?php

class sspmod_kafedsacl_DSAcl {

    public static function getModuleURI() {
        $URI = SimpleSAML_Module::getModuleURL('kafedsacl');
        $URI = str_replace('http://', 'https://', $URI);
        return $URI;
    }

    public static function getCss() {
        $URI = sspmod_kafedsacl_DSAcl::getModuleURI();
        echo '<link rel="shortcut icon" href="'.$URI.'/images/favicon.ico">';
        echo '<link type="text/css" rel="stylesheet" href="'.$URI.'/style.css"/>';
    }

    public static function getJs() {
        $URI = sspmod_kafedsacl_DSAcl::getModuleURI();

    }

    public static function acl($sp, $idp) {
        $aclconfig = SimpleSAML_Configuration::getConfig('config-ds-acl.php');
        if($aclconfig->getValue('type') == 'text') {
            $aclconfig = $aclconfig->getValue('data');
        } else if($aclconfig->getValue('type') == 'mysql') {
            $mysql = $aclconfig->getValue('config');

            $conn = new mysqli($mysql['host'].':'.$mysql['port'], $mysql['user'], $mysql['password'], $mysql['database']);

            if(mysqli_connect_errno()) {
                return False;
            }

            $table = isset($mysql['table']) ? $mysql['table'] : 'acl_list';
            $result = $conn->query('SELECT sp_entity, type, idp_list as aclList FROM '.$table);
            if($result == Null) {
                return False;
            }

            $aclconfig = Array();

            while($row = $result->fetch_row()) {
                $aclconfig[$row[0]] = Array(
                    'aclType' => $row[1],
                    'aclList' => explode(",", preg_replace('/\s+/', '', $row[2])) 
                );
            }

            $conn->close(); 
        }

        if(array_key_exists($sp, $aclconfig) == False) {
            return False;
        }

        $baseAcl = $aclconfig[$sp];

        $aclType = $baseAcl['aclType'];
        $aclList = $baseAcl['aclList'];
        $aclOptional = Array();
        if(array_key_exists('optional', $baseAcl)) {
            $aclOptional = $baseAcl['optional'];
        }

        if(in_array($idp, $aclOptional)) {
            return False;
        }

        if($aclType == 'blacklist') {
            return in_array($idp, $aclList);
        }

        if($aclType == 'whitelist') {
            return in_array($idp, $aclList) == False;
        }

        return False;
    }

}
