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

### attributes
Any additional parameters after the output file name will pass through to the rendered tag.

	<cms:minify 'css' into='css/style.min.css' media="only screen and (min-width: 800px)" >
	
If you wish to add an attribute to _inline_ js or css, you must explicitly declare the empty output filename in it's proper order.

	<cms:minify 'js' as='' defer='defer'>

### Usage
The `<cms:minify>` tag combines and minimizes all files in the list in the order given, storing the result in an external file. It renders a `<script>` or `<link>` tag that includes a timestamp for cache control.

The minimized file only gets updated when the last modification date of included files is newer than the current output file, i.e. when you make changes. So it doesn't create a new file with every single page load. Delete the output file in order to force update. 

If you don't specify an output file, the tag renders the output inline with a `<style>` or `<script>` tag.

    <cms:minify 'css'>
        css/bootstrap.css
        css/bootstrap-theme.css
        css/custom.css
    </cms:minify>

Remember that relative urls in your CSS or JS files (like fonts or background images) will now be relative to the new, minimized file. Or to the web page if rendered inline.

_Note:_ In order to write the new ouput file, your server must have allow_url_fopen enabled.  If you get an empty output file - and you haven't made any mistakes with your file list - add `allow_url_fopen=1` to your php.ini file, or contact your host for help.
 
## Installation:
To use the tag, unzip the attached folder into `/couch/addons/` and enable it in your `kfunctions.php` file:

    require_once( K_COUCH_DIR.'addons/minify-js-css/minify.php' );
