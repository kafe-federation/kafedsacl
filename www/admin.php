<?php
//$KAFE_URI = "/simplesaml/module.php/kafedsacl";
$KAFE_URI = SimpleSAML_Module::getModuleURL('kafedsacl');
$KAFE_URI = str_replace('http://', 'https://', $KAFE_URI);


$session = SimpleSAML_Session::getSessionFromRequest();
SimpleSAML\Utils\Auth::requireAdmin();

$aclconfig = SimpleSAML_Configuration::getConfig('config-ds-acl.php');
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Discovery Service ACL Admin</title>

    <link rel="stylesheet" href="<?php echo $KAFE_URI;?>/libs/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $KAFE_URI;?>/style.css">
    <link rel="stylesheet" href="<?php echo $KAFE_URI;?>/css/admin.css">

    <script src="<?php echo $KAFE_URI;?>/libs/jquery-3.2.1.slim.min.js"></script>
    <script src="<?php echo $KAFE_URI;?>/libs/popper.min.js"></script>
    <script src="<?php echo $KAFE_URI;?>/libs/bootstrap.min.js"></script>
</head>
<body>
<div id="layout-header">
    <div id="header">
        <h1 id="logo"><a>ACL Config <span>for KAFE</span></a></h1>
    </div>
</div>

<div id="layout-container">
    <div id="container">
        <div id="container-body"><div id="contents">
<?php if($aclconfig->getValue('type') != 'mysql') { ?>
            <h2>Change config for mysql</h2>
<?php } ?>

<?php 
if($aclconfig->getValue('type') == 'mysql') { 
    $mysql = $aclconfig->getValue('config');
    $conn = new mysqli($mysql['host'].':'.$mysql['port'], $mysql['user'], $mysql['password'], $mysql['database']);

    if(mysqli_connect_errno()) {
?>
            <h2>MySQL Connection Failed</h2>
<?php 
    } else { 
        $sql = "CREATE TABLE IF NOT EXISTS `acl_list` (`sp_entity` varchar(128) NOT NULL DEFAULT '',`type` varchar(12) NOT NULL DEFAULT '',`aclList` longtext NOT NULL, PRIMARY KEY (`sp_entity`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
        $conn->query($sql);

        $result = $conn->query('SELECT sp_entity, type, aclList FROM acl_list;');

        if(isset($_GET['entity_id'])) {
            if(isset($_POST["sp_entity"]) && isset($_POST["type"]) && isset($_POST["aclList"])) {
                if(strlen($_POST["sp_entity"]) > 0) {
                    if($_GET['entity_id'] == 'new') {
                        $update_sql = 'INSERT INTO acl_list SET sp_entity="'.mysqli_escape_string($conn, $_POST["sp_entity"]).'", type="'.mysqli_escape_string($conn, $_POST['type']).'", aclList="'.mysqli_escape_string($conn, $_POST['aclList']).'";';
                    } else {
                        $update_sql = 'UPDATE acl_list SET sp_entity="'.mysqli_escape_string($conn, $_POST["sp_entity"]).'", type="'.mysqli_escape_string($conn, $_POST['type']).'", aclList="'.mysqli_escape_string($conn, $_POST['aclList']).'" WHERE sp_entity="'.mysqli_escape_string($conn, $_GET['entity_id']).'";';
                    }
                    $conn->query($update_sql);

                    header("Location: /simplesaml/module.php/kafedsacl/admin.php"); 
                    exit;
                }
            } else if(isset($_POST['ACTION'])) {
                if($_POST['ACTION'] == 'DELETE') {
                    $delete_sql = 'DELETE FROM acl_list WHERE sp_entity="'.mysqli_escape_string($conn, $_GET['entity_id']).'";';
                    $conn->query($delete_sql);
                    header("Location: /simplesaml/module.php/kafedsacl/admin.php"); 
                }
            }

            $conf = Array();
            $conf['aclType'] = 'blacklist';
            $conf['aclList'] = '';

            if(is_null($result) == False) {
                $aclconfig = Array();
                while($row = $result->fetch_row()) {
                    $aclconfig[$row[0]] = Array(
                        'aclType' => $row[1],
                        'aclList' => $row[2]
                    );
                }
            
                if(isset($aclconfig[$_GET['entity_id']])) {
                    $conf = $aclconfig[$_GET['entity_id']];
                }
            }
?>
            <div>
                <a href="/simplesaml/module.php/kafedsacl/admin.php" class="btn btn-dark text-light" style="margin-right: 12px; margin-bottom: 15px;">&laquo;</a>
                <div class="title-line" style="margin-top: 0; display: inline-block!important;">IdP ACL Config</h2>
            </div>
            <hr />
            <form action="/simplesaml/module.php/kafedsacl/admin.php?entity_id=<?php echo $_GET['entity_id']; ?>" method="POST">
                <div class="form-group">
                    <label for="sp-entity">SP Entity ID</label>
                    <input type="text" class="form-control" id="sp-entity" placeholder="https://testssp.kreonet.net/sp/simplesamlphp" value="<?php echo $_GET['entity_id'] == 'new' ? '' : $_GET['entity_id']; ?>" name="sp_entity">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                        <select class="form-control" id="type" name="type">
                        <option value="blacklist" <?php if($conf["aclType"] == "blacklist") echo "selected"; ?>>Black List</option>
                        <option value="whitelist" <?php if($conf["aclType"] == "whitelist") echo "selected"; ?>>White List</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="acllist">List</label>
                    <textarea class="form-control" id="acllist" name="aclList" placeholder="https://testssp.kreonet.net/sp/simplesamlphp,https://testssp.kreonet.net/sp/simplesamlphp" rows="5"><?php echo $conf["aclList"]; ?></textarea>
                </div>

                <button type="submit" class="btn btn-dark btn-block">Save</button>
            </form>

            <form action="/simplesaml/module.php/kafedsacl/admin.php?entity_id=<?php echo $_GET['entity_id']; ?>" method="POST">
                <input style="display: none;" name="ACTION" value="DELETE"/>
                <div style="width: 100%; margin-top: 8px; text-align: right;">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>

<?php
              
        } else {
?>
            <div>
                <div class="title-line" style="margin-top: 0; display: inline-block;">ACL List</div>
                <a href="/simplesaml/module.php/kafedsacl/admin.php?entity_id=new" class="btn btn-dark text-light" style="float: right;">+</a>
            </div>

            <hr/>

            <ul class="account-list IdPList">
<?php
            if(is_null($result) == False) {
                while($row = $result->fetch_row()) {
?>
                <li>
                    <a href="?entity_id=<?php echo urlencode($row[0]); ?>" >
                        <p class="subject">
                            <?php echo $row[0]; ?>
                        </p>
                    </a>
                </li>
<?php
                }
            }
?>
            </ul>

            <script type="text/javascript" src="<?php echo $KAFE_URI;?>/js/acl-admin.js"></script>
<?php
        }
    }
 
    $conn->close(); 
} 
?>
        </div></div>
    </div>
</div>

</body>
</html>
