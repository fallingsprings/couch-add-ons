# HTML5 Input Types Add-On #

The HTML 5 Input Types Add-On is still in development, but mostly working. It provides native Couch support for the HTML5 input types, including:

- type="search"
- type="tel"
- type="email"
- type="url"
- type="number"
- type="range"
- type="date"
- type="time"

This add-on not only renders the input tags, but also adds automatic server-side validation of the input values. Since automatic validation can sometimes interfere with what you want to accomplish, it can disabled by setting the parameter `validate='0'` on any of these input types. The parameter `validate='0'` will turn off any automatic validation for the tag but won't affect any custom Couch validation.

For example, the following line won't validate the email address, but the input will still be required:

```
#!php
<cms:input type='email' required='1' validate='0' />
```

### Installing the Add-On ###

Download and unzip the html5-input-types.zip file. Place the unzipped folder in your couch/addons/ folder. Register the add-on by adding a line of code to couch/addons/kfunctions.php. 

```
#!php
require_once( K_COUCH_DIR.'addons/html5-input-types/html5-input-types.php' );
```

