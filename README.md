## Installation

```bash
cp modules/kafetheme/config-ds-acl.php config/
vi config/config.php
```

set configuration

```php
...
'theme.use' => 'kafetheme:kafe',
...
```

## Usage

change `YOUR_SSP/templates/selectidp-dropdown.php` file.

```php
$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'kafedsacl:selectidp-dropdown.php');
$t->data = $this->data;
$t->show();
```
