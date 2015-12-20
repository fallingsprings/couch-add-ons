# HTML5 Input Types Add-On #

The HTML 5 Input Types Add-On provides native Couch support for the HTML5 input types, including:

- type="email"
- type="url"
- type="tel"
- type="search"
- type="color"
- type="number"
- type="range"
- type="date"
- type="time"

Once the add-on is activated, you can use these types just like you would any of the standard input types.

This add-on also provides support for the HTML5 "required" attribute - but only for these input types - by including the "required" attribute in the rendered tag for browser-based HTML5 validation. 

The add-on not only renders the input tags, but also adds automatic server-side validation to back up the native browser validation. Couch will generate the same errors that you would expect from browser validation. Error messages can be modified in the language files for customization and localization.

Since automatic validation can sometimes interfere with what you want to accomplish, it can be disabled by setting the parameter `validate='0'` on any of these input types. The parameter `validate='0'` will turn off automatic validation for the tag but won't affect any other Couch validation that is present. For example, the following line won't validate the email address, but the input will still be required:

	<cms:input type='email' validate='0' required='1' />

Please be aware that PHP is only cabable of understanding numbers up to 12 digits, so server-side validation may fail with very large numbers or numbers with too many decimal places. But for most ordinary uses you are unlikely to run up against this limitation.


### Installing the Add-On ###
Download and unzip the html5-input-types.zip file. Place the unzipped folder in your couch/addons/ folder. Register the add-on by adding a line of code to couch/addons/kfunctions.php. 

require_once( K_COUCH_DIR.'addons/html5-input-types/html5-input-types.php' );

