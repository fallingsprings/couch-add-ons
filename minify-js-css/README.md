# Minify Tag
The minify tag will combine and minimize CSS or JS files. It concatenates and minimizes files into a single output file, and renders a `script` or `link` tag to call it, adding a timestamp for cache control.

    <cms:minify 'css' into='css/style.min.css'>
        css/bootstrap.min.css
        css/bootstrap-theme.min.css
        calendar/theme/style.css
        css/custom.css
    </cms:minify>

    <cms:minify 'js' into='js/script.min.js'>
        js/jquery.min.js
        js/bootstrap.min.js
        js/bootstrap-plugin.js
        calendar/js/script.js
        js/custom.js
    </cms:minify>

_All file names should be relative to the site's root_.

The minified file only gets updated when the last modification date of any included file is newer than the current output file, i.e. when you make changes. So it won't create a new file with every single page load. Delete the output file in order to force update. 

If you don't specify an output file, the tag renders the output inline with a `style` or `script` tag.


## Installation:
To use the tag, unzip the attached folder into `/couch/addons/` and enable it in your `kfunctions.php` file:

    require_once( K_COUCH_DIR.'addons/minify-js-css/minify.php' );
