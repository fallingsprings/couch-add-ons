# Minify Tag
The minify tag will combine and minimize CSS or JS files. It concatenates and minimizes files into a single output file, and renders a `<script>` or `<link>` tag to call it.

    <cms:minify 'css' into='css/style.min.css'>
        css/bootstrap.css
        css/bootstrap-theme.css
        css/custom.css
    </cms:minify>

    <cms:minify 'js' as='js/script.min.js'>
        js/jquery.js
        js/bootstrap.js
        js/bootstrap-plugin.js
        js/custom.js
    </cms:minify>


## Parameters

### filetype
'css' or 'js'

### output_file
The single combined file that will be served to the page. Should be _relative to the site's root_. You can use any parameter name you want for the output file. Both of the above tags work the same.

### Usage
The `<cms:minify>` tag combines and minimizes all files in the list in the order given, storing the result in an external file. It renders a `<script>` or `<link>` tag that includes a timestamp for cache control.

The minimized file only gets updated when the last modification date of included files is newer than the current output file, i.e. when you make changes. So it doesn't create a new file with every single page load. Delete the output file in order to force update. 

If you don't specify an output file, the tag renders the output inline with a `<style>` or `<script>` tag.

Remember that relative urls in your CSS or JS files (like fonts or background images) will now be relative to the new, minimized file. Or to the web page if rendered inline.
 
## Installation:
To use the tag, unzip the attached folder into `/couch/addons/` and enable it in your `kfunctions.php` file:

    require_once( K_COUCH_DIR.'addons/minify-js-css/minify.php' );
