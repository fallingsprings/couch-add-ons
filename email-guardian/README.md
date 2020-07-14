# Email Guardian
## Obfuscate All Email Addresses Automagically

Simply wrap any code with the _email_guardian_ tag to find and obfuscate email addresses.

    <cms:email_guardian>
    	<cms:show my_blog_post>
    </cms:email_guardian>

All plain text email addresses and mailto links will be obfuscated using a cipher, then decoded with a JavaScript routine to hide them from spambots while displaying them normally on the page.

You can wrap an entire page with the tag or use multiple tags to wrap specific content.

### Parameters:

**no_script_message** - With Javascript disabled, this message will be displayed instead of the expected content. The default is 'JavaScript required to see this email address.'

**create_links** -  For free floating plaintext email addresses, the Email Guardian helpfully converts the address to a clickable mailto link. To turn off this feature and simply obfuscate text email addresses without converting them, set create_links="0".

### Installing the Add-On

To use this tag, unzip and add the folder to your 'couch/addons' directory. Initiate the tag in 'couch/addons/k_functions.php'.

	require_once( K_COUCH_DIR.'addons/email-guardian/email-guardian.php' );
