# HTML5 Input Types Add-On #

The HTML 5 Input Types Add-On is still in development, but working. It provides native Couch support for the HTML5 input types, including:

- type="search"
- type="tel"
- type="email"
- type="url"
- type="number"
- type="range"
- type="date"
- type="time"

This add-on not only renders the input tags, but also adds automatic server-side validation of the input values. To disable the automatic validation, use the parameter `validate='0'`.

For example:

```
#!php
<cms:input type='email' required='1' validate='0' />
```

### Installing the Add-On ###

Download and unzip the too-many-urls.zip file. Place the unzipped folder in your couch/addons/ folder. Register the add-on by adding a line of code to couch/addons/kfunctions.php. 

```
#!php
require_once( K_COUCH_DIR.'addons/html5-input-types/html5-input-types.php' );
```

