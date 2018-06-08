<?php
//ini_set('display_errors', 0);

$KAFE_URI = SimpleSAML_Module::getModuleURL('kafedsacl');
$KAFE_URI = str_replace('http://', 'https://', $KAFE_URI);

if (!array_key_exists('header', $this->data)) {
    $this->data['header'] = 'selectidp';
}
$this->data['header'] = $this->t($this->data['header']);
$this->data['autofocus'] = 'dropdownlist';

// 요청 SP entityid 확인
parse_str(parse_url($this->data['return'])['query'], $output);
$authId = $output['AuthID'];

$state = SimpleSAML_Auth_State::loadState($authId, 'saml:sp:sso');
$sp_entityid = $state['SPMetadata']['entityid'];

$idplist = Array();

foreach ($this->data['idplist'] as $idpentry) {
    $idp_entityid = $idpentry['entityid'];

    if(sspmod_kafedsacl_DSAcl::acl($sp_entityid, $idp_entityid)) {
        continue;
    }

    if (!empty($idpentry['name'])) {
        $this->includeInlineTranslation('idpname_'.$idpentry['entityid'], $idpentry['name']);
    } elseif (!empty($idpentry['OrganizationDisplayName'])) {
        $this->includeInlineTranslation('idpname_'.$idpentry['entityid'], $idpentry['OrganizationDisplayName']);
    }
    if (!empty($idpentry['description'])) {
        $this->includeInlineTranslation('idpdesc_'.$idpentry['entityid'], $idpentry['description']);
    }

    array_push($idplist, $idpentry);
}

$this->data['idplist'] = $idplist;
?>

<html>
<head>
<title>KAFE discovery service</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="viewport" content="width=device-width, user-scalable=no">
<?php sspmod_kafedsacl_DSAcl::getCss(); ?>
</head>

<body>

<div id="layout-header">
    <div id="header">
        <h1 id="logo"><a href="#">LOGIN <span>to <?php echo $state['SPMetadata']['description']['en'] ?></span></a></h1>

        <a href="#why" class="why btn-why">Why am I here?</a>
        <div id="why">
            <div class="group">
                <h2>Why am I here?</h2>
                <div class="contents">
                    You tried to access <strong> <?php echo $state['SPMetadata']['description']['en'] ?></strong>. This service brings you to your home organization.
                    <div class="paragraph">
                        <p>Your organisation could be:</p>
                        <ul>
                            <li>University</li>
                            <li>Research institution</li>
                            <li>Research Support Organization</li>
                            <li>KAFE Virtual Home Organization</li>
                        </ul>
                    </div>

                    <div class="paragraph">
                        <p>
                            <strong>How to log in</strong>
                        </p>
                        <ol>
                            <li>Select your organization and you will be taken to the login page of your home organization.</li>
                            <li>Successful login will redirect you to the service orginally trying to access.</li>
                        </ol>
                    </div>

                    <div class="paragraph">
                        <p>What if I don’t see my organization in the list?</p>
                        <ul>
                            <li>Your organization may not be the member of the Korean Access Federation.</li>
                            <li>Your organization is not eligible to use  <?php echo $state['SPMetadata']['description']['en'] ?>.</li>
                        </ul>
                        For more information, please contact us at coreen@kreonet.net 
                    </div>
                </div>
                <a href="" class="close" data-ref="close"><img src="<?php echo $KAFE_URI;?>/images/custom2/login_cds_close.png" alt="delete"></a>
            </div>
        </div>
    </div>
</div>

<div id="layout-container">
    <div id="container">
        <div id="container-body">
            <div id="contents">
                <div class="searchform">
                    <input type="text" name="keyword" title="Search for an institution" placeholder="Search for an institution" style="outline: none;">
                    <input type="image" src="<?php echo $KAFE_URI;?>/images/custom2/search.gif" alt="search">
                </div>

                <p class="login-ing">
                    <input type="checkbox" id="remembercheckboxdisplay" value="<?php if($this->data['rememberenabled']) echo "true"; else echo "false"; ?>" id="rememberForSession">
                    <label for="rememberForSession">Remember selection</label>
                    <a href="" class="icon-tip">
                        <img src="<?php echo $KAFE_URI;?>/images/custom2/icon_tip.gif" alt="question_icon">
                        <span>Remember selection for this web browser session.</span>
                    </a>
                </p>

<?php
    #print_r($sp_entityid);
?>

                <h2 class="title-line">Previously chosen</h2>

                <ul class="account-list IdPList">
<?php

$GLOBALS['__t'] = $this;
usort($this->data['idplist'], function ($idpentry1, $idpentry2) {
return strcmp(
    $GLOBALS['__t']->t('idpname_'.$idpentry1['entityid']),
    $GLOBALS['__t']->t('idpname_'.$idpentry2['entityid'])
);
});
unset($GLOBALS['__t']);

//logo: $idpentry['UIInfo']['Logo'];

foreach ($this->data['idplist'] as $idpentry) {
    $IdPName = htmlspecialchars($this->t('idpname_'.$idpentry['entityid']));
    $IdPKey = htmlspecialchars($idpentry['entityid']);
    
    $logo = '/images/custom/login_cds_no.gif';
    // TODO Change logo url refer metadata 

    if (isset($this->data['preferredidp']) && $idpentry['entityid'] == $this->data['preferredidp']) {
?>
                    <li>
                        <a href="#" data-idpkey="<?php echo $IdPKey;?>" data-idpname="<?php echo $IdPName;?>">
                            <p class="subject">
                                <img src="<?php echo htmlspecialchars($idpentry['UIInfo']['Logo'][0]['url']);?>" alt="<?php echo $IdPName ?>">
                                <?php echo $IdPName;?>
                            </p>
                        </a>
                    </li>
<?php 
    }
} 
?>
                </ul>
                <h2 class="title-line">Identity Providers with access</h2>

                <ul class="account-list IdPList">
<?php
$GLOBALS['__t'] = $this;
usort($this->data['idplist'], function ($idpentry1, $idpentry2) {
return strcmp(
    $GLOBALS['__t']->t('idpname_'.$idpentry1['entityid']),
    $GLOBALS['__t']->t('idpname_'.$idpentry2['entityid'])
);
});
unset($GLOBALS['__t']);

//logo: $idpentry['UIInfo']['Logo'];

foreach ($this->data['idplist'] as $idpentry) {

    $IdPName = htmlspecialchars($this->t('idpname_'.$idpentry['entityid']));
    $IdPKey = htmlspecialchars($idpentry['entityid']);

    $logo = '/images/custom/login_cds_no.gif';
    // TODO Change logo url refer metadata 
?>
                    <li>
                        <a href="#" data-idpkey="<?php echo $IdPKey;?>" data-idpname="<?php echo $IdPName;?>">
                            <p class="subject">
                                <img src="<?php echo htmlspecialchars($idpentry['UIInfo']['Logo'][0]['url']);?>" alt="<?php echo $IdPName?>">
                                <?php echo $IdPName;?>
                            </p>
                        </a>
                    </li>
<?php } ?> 
                </ul>
            </div>
        </div>
    </div>
</div>

<form id="IdPForm" method="get" action="<?php echo $this->data['urlpattern']; ?>" style='display: none;'>
    <input type="hidden" name="entityID" value="<?php echo htmlspecialchars($this->data['entityID']); ?>"/>
    <input type="hidden" name="return" value="<?php echo htmlspecialchars($this->data['return']); ?>"/>
    <input type="hidden" name="returnIDParam"
           value="<?php echo htmlspecialchars($this->data['returnIDParam']); ?>"/>
    <select id="dropdownlist" name="idpentityid">
        <?php
        $GLOBALS['__t'] = $this;
        usort($this->data['idplist'], function ($idpentry1, $idpentry2) {
            return strcmp(
                $GLOBALS['__t']->t('idpname_'.$idpentry1['entityid']),
                $GLOBALS['__t']->t('idpname_'.$idpentry2['entityid'])
            );
        });
        unset($GLOBALS['__t']);

        foreach ($this->data['idplist'] as $idpentry) {
            echo '<option value="'.htmlspecialchars($idpentry['entityid']).'"';
            if (isset($this->data['preferredidp']) && $idpentry['entityid'] == $this->data['preferredidp']) {
                echo ' selected="selected"';
            }
            echo '>'.htmlspecialchars($this->t('idpname_'.$idpentry['entityid'])).'</option>';
        }
        ?>
    </select>
    <button class="btn" type="submit"><?php echo $this->t('select'); ?></button>
    <?php
    if ($this->data['rememberenabled']) {
        echo('<br/><input type="checkbox" name="remember" value="1" id="rembercheckbox"/>'.$this->t('remember'));
    }
    ?>
</form>

<div id="layout-footer">
    <div id="footer">
        <p class="kafe">
            <img src="<?php echo $KAFE_URI;?>/images/custom2/kafe.png" alt="KAFE">
            Korean Access FEderation
        </p>

        <ul id="fnb">
            <li><a href="https://coreen.kreonet.net/privacy_policy">PRIVACY POLICY</a></li>
            <li><a href="https://coreen.kreonet.net/user_agreement">TERMS OF USE</a></li>
        </ul>
    </div>
</div>

<script type="text/javascript" src="<?php echo $KAFE_URI;?>/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="<?php echo $KAFE_URI;?>/js/common.js"></script>
</body>
</html>
