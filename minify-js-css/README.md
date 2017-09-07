#Minify Tag
This tag will combine and minimize CSS or JS files. It concatenates and minimizes files into a single output file, and renders a _script_ or _link_ tag to call it.

    <cms:minify 'css' into='css/style.min.css'>
        css/bootstrap.min.css
        css/bootstrap-theme.min.css
        css/custom.css
    </cms:minify>

    <cms:minify 'js' into='js/script.min.js'>
        js/jquery.min.js
        js/bootstrap.min.js
        js/bootstrap-plugin.js
        js/custom.js
    </cms:minify>


##Parameters

###filetype
css or js

###into
The single file that will be served to the page. Should be _relative to the site's root_.

The tag combines and minimizes all files in the list in the order given, storing the result in an external file. It renders a _script_ or _link_ tag that includes a timestamp for cache control. 

The minified file only gets updated when the last modification date of included files is newer than the current output file, i.e. when you make changes, so it doesn't have to create a new file with every single page load. Delete the output file in order to force update. 

If you don't specify an output file, the tag renders the output inline with a _style_ or _script_ tag.


##Installation:
To use the tag, unzip the attached folder into _/couch/addons/_ and enable it in your _kfunctions.php_ file:

    require_once( K_COUCH_DIR.'addons/minify-js-css/minify.php' );