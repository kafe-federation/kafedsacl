<?php
$config = array(
    'type' => 'text',
    'data' => array(
        'https://testssp.kreonet.net/sp/simplesamlphp' => array(
            'aclType' => 'whitelist',
            'aclList' => array(
                'https://testidp.kreonet.net/idp/simplesamlphp',
                'http://rz-saml.kreonet.net/simplesaml-google/saml2/idp/metadata.php'
            ),
        ),

        'https://saml.proinlab.com/simplesaml/module.php/saml/sp/metadata.php/default-sp' => array(
            'aclType' => 'blacklist',
            'aclList' => array(
                'https://testidp.kreonet.net/idp/simplesamlphp'
            )
        ),
    ),
);
