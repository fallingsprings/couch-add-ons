# Too Many URLs Tag #
##Unobtrusive Spam Protection for Couch Forms##

This tag is used to stop form spam by limiting the number of URLS allowed in a given field. Since the whole purpose of spam is to spread URLS, no URLs means no spam.

This type of spam protection won't work with every kind of form, but it's excellent with most ordinary contact forms. By limiting URLS, it's possible to eliminate contact form spam without requiring anything from legitimate users.

Since most spammers seem to prefer sending multiple URLs, I've found that allowing just one URL stops most spam while still letting people send a link if they want to.

### Installing the Add-On ###

Download and unzip the too-many-urls.zip file. Place the unzipped folder in your couch/addons/ folder. Register the add-on by adding a line of code to couch/addons/kfunctions.php. 

```
#!php
require_once( K_COUCH_DIR.'addons/too-many-urls/too_many_urls.php' );
```

###Using the Too Many URLs Tag###

This tag is meant to be used in a form's k_success routine. It tests how many urls are in a given field and indicates whether the number exceeds the allowed amount. If there are too many, you interrupt processing and return an error. Otherwise go ahead and process the form. 

For example:

    <cms:if k_success >
        <cms:if "<cms:too_many_urls in='frm_message' allowed='1' />" >
            <p class="error_msg">Sorry. There are too many URLs in your message. If you need to send us a link, please contact us first.</p>
        <cms:else/>
           ...Not spam. Process form...
        </cms:if>
    </cms:if>

If the 'allowed' parameter is not specified, the default is '1'.

More information about using add-ons with Couch is available on the Couch forum.
http://www.couchcms.com/forum/