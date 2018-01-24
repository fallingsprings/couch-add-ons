# Lazy Load Images, Iframes, Video and Audio

Lazy loading images and other large files is a great way to get a big boost to page speed, while saving yourself and site visitors on unnecessary bandwidth usage. Lazy loading works by initially showing a 1-pixel image while hiding the real image source in a 'data-src' attribute. A script then loads the real source into the 'src' attribute only when it's needed by the viewport. 

    <img src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' data-src='<cms:show my_image />' class='lazyload' alt='' />
    
It's a neat trick, but the non-standard markup makes it a hassle to write and manage the html code. That's where the `<cms:lazy_load>` tag steps in. This tag is a pre-processor that looks through your ordinary, standard markup and modifies image, iframe, audio, and video tags with the necessary changes.

### lazysizes.js
I've chosen to use _lazysizes.js_ (https://github.com/aFarkas/lazysizes) with this module. It's a script that's free from dependencies like jQuery, and it has worked well for my own purposes. The necessary _lazysizes_ scripts are bundled together with this tag, but will have to be added by you to the javascript on the page.

All lazy-load scripts work on the same principle, so this pre-processor can be made to work with other scripts. For images and iframes, the only difference may be the class name that triggers lazy loading (audio and video may be more complicated). This module uses "lazyload" as the trigger. You can either configure the trigger class in your lazy-load script, or change the trigger definition in _lazyload.php_.

    define(LAZYLOAD_TRIGGER, 'lazyload');

### Usage
Include the lazy load script(s) on the web page. The _ls.unveilhooks.min.js_ script is a plugin for `<audio>` or `<video>` tags. It's not needed unless you're using it for those tags.

    <script src="ls.unveilhooks.min.js"></script>
    <script src="lazysizes.min.js"></script>
    
Wrap any portion of your Couch code with the `<cms:lazy_load>` tag to pre-process tags for lazy loading. If you don't want a particular asset to be lazy-loaded, give it a class of _'eager'_ to skip over it.

By default, the tag processes all resource types. You can specify the types of content to target by adding parameters to the tag.

    <cms:lazy_load> //lazyloads images, iframes, audio/video
    ---
    <cms:lazy_load 'image'> //only images
    ---
    <cms:lazy_load 'iframe' 'video'> //only iframes and audio/video

### Images
Big galleries of images are the ideal use case for lazy loading.

Lazy loading works best when the <img> tag or css provides explicit sizes, so the screen can be blocked out correctly on loading. Otherwise, resizing and reflowing of the page content as new images load can give a janky experience. This issue can be challenging because it constrains your design choices.

Using lazysizes.js, this module will also lazy-load responsive images.

### Iframes
This is my favorite use for lazy-loading. I consider it best practice to lazy load any embedded content like YouTube or Google Maps. They are always the biggest drag on page speed, both in analytics and user experience. Lazy loading embedded content makes a huge impact on the snappiness of a page, and this module makes it plug-and-play easy.

I've found that browsers can misbehave if an iframe's 'src' attribute isn't a url. So this module uses an empty file named _iframe-dummy.html_ in your site's root - the equivalent of a 1-pixel image. The tag will create the file in the site's root if it doesn't already exist.

### Video and Audio Tags
The _ls.unveilhooks_ plugin handles video and audio. It doesn't mess with a video or audio tag's sources. Instead, the pre-processor simply sets `preload='none'` to prevent downloading the audio or video file. The script changes the attribute to `preload='auto'` when it enters the viewport. It lazy loads the poster image just like other images.

### CSS Hooks
The _lazysizes_ script provides the class names '_lazyload_,' '_lazyloading_,' and '_lazyloaded_' for you to hook styles to. The following CSS creates a fade-in effect for images and adds a loading gif to iframes and video.

    /* Lazy Loading */
    .lazyload, .lazyloading {
        opacity: 0;
        }
    .lazyloaded {
        opacity: 1;
        -webkit-transition: opacity 600ms;
        transition: opacity 600ms;
        }
    iframe.lazyload, iframe.lazyloading, video.lazyload, video.lazyloading {
        opacity: 1;
        background: url('icons/loading.gif') no-repeat 50% 50%;
        }

There is much more detailed information about using _lazysizes.js_ at https://github.com/aFarkas/lazysizes.

## Installation:
To use the tag, unzip the attached folder into `couch/addons/` and enable it in your `couch/addons/kfunctions.php` file:

    require_once( K_COUCH_DIR.'addons/lazyload/lazyload.php' );
