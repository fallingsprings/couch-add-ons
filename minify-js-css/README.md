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

The `<cms:minify>` tag combines and minimizes all files in the list in the order given, storing the result in an external file. It renders a `<script>` or `<link>` tag for the combined file. If you don't specify an output file, the minimized code will be rendered inline.

    <cms:minify 'css'>
        css/bootstrap.css
        css/bootstrap-theme.css
        css/custom.css
    </cms:minify>

The minimized file only gets updated when the last modification date of included files is newer than the current output file, i.e. when you make changes. So it doesn't create a new file with every single page load. Delete the output file in order to force update. 

Remember that relative urls in your CSS or JS files (like fonts or background images) will now be relative to the new, minimized file. Or to the web page if rendered inline.

## Parameters

### filetype
'css' or 'js'

### output_file
The single combined file that will be served to the page. The file path should be relative to the site's root. You can use any parameter name you want for the output file. Both of the above tags work the same. If you don't specify an output file, the minimized code will be rendered inline.

### attributes
Any additional parameters after the output file name will pass through to the rendered tag.

	<cms:minify 'css' into='css/style.min.css' media="only screen and (min-width: 800px)" >
	
If you wish to add an attribute to _inline_ js or css, you must explicitly declare the empty output filename in it's proper order.

	<cms:minify 'js' as='' defer='defer'>
    
## Code Blocks
In addition to external files, you can also include blocks of inline code inside the _<cms:minify>_ tag. Simply mark the beginning and end of each code block with the marker `/*CODE*/` One use case would be for mixing server-side Couch variables into your styles and scripts, which can be a powerful technique for integrating server-side data into your client-side scripts or stylesheets.

    <cms:minify 'js'>
        js/jquery.js
        js/bootstrap.js
        
        /*CODE*/
        var my_template = "<cms:show k_template_title/>";
        alert(my_template);
        /*CODE*/
        
        js/custom.js
        
        /*CODE*/
        alert('Any number and combination of external files and code blocks allowed.');
        /*CODE*/        
    </cms:minify>
    
This method will be most useful when the minified code is rendered inline. Otherwise, the cached external file can't respond dynamically to the conditions of the template.
        
_Pro-tip:_ The above configuration may break the syntax highlighting on your text editor because the editor doesn't know what type of code it's displaying. You can wrap your code blocks with `<script>` or `<style>` tags to get the correct highlighting and other features from your text editor. If the tags are outside of the `/*CODE*/` markers, they will be discarded in processing.

    <cms:minify 'js'>
        js/jquery.js
        js/bootstrap.js
        
        <script>/*CODE*/
            var my_template = <cms:show k_template_title/>
            alert(my_template);
        /*CODE*/</script>
        
        js/custom.js
    </cms:minify>

### Timestamps
Time-stamping your assets to bust browser caching is especially useful for sites with lots of regular users. This tag offers 2 optional methods to timestamp output files for version control. The feature needs to be turned on in the _minify.php_ file. At the top of the file, you'll find these two lines. Uncomment one or the other to turn on versioning.

    //define('MINIFY_TIMESTAMP_QUERYSTRING', 1);
    //define('MINIFY_TIMESTAMP_FILENAME', 1);// ** Requires a rewrite rule in .htaccess **

The first option adds a querystring to the filename: `styles.min.css?1515655661`.

The second choice adds a timestamp to the filename itself: `styles.min.css^1515655661^.css`. The actual filename isn't changed, just the output on the front end. For this method, you have to add a rewrite rule to the .htaccess file in the site's root in order to direct the link to the correct file. This method is borrowed from [@trendoman's `<cms:rel>` tag](https://www.couchcms.com/forum/viewtopic.php?f=8&t=10644).

    RewriteRule ^(.+)\^([\d-]+)\^\.(js|css)$ $1 [L] 

_Note:_ In order to write the new ouput file, your server must have allow_url_fopen enabled.  If you get an empty output file - and you haven't made any mistakes with your file list - add `allow_url_fopen=1` to your php.ini file, or contact your host for help.
 
## Installation:
To use the tag, unzip the attached folder into `/couch/addons/` and enable it in your `kfunctions.php` file:

    require_once( K_COUCH_DIR.'addons/minify-js-css/minify.php' );
