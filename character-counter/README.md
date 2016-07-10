# Character Counter Tag
### Add Character Counters to fields in the Admin Panel

This is a full-featured character counter for the Couch Admin Panel with numerous options. With this custom tag, you can add character counters of different kinds to any field in your templates. 

### Usage

This tag takes advantage of the new Admin Panel features in Couch v2.0. It only works inside of a cms:config_form_view tag.

```
    <cms:config_form_view>
        <cms:character_counter>
            { editable:'my_editable_region' }        
        </cms:character_counter>
    </cms:config_form_view>
```
    
This simplest of all counters just adds a character counter beneath the specified field. If it's in a repeatable, add the repeatable name to the declaration.
    { repeatable:'my_repeatable', editable:'my_editable_region' }        

You can also specify a minimum or maximum character limit. The following will create a twitter-style countdown counter beginning at the max limit. As you can see, multiple counters can all be applied at once. Don't forget the comma between them. 


    <cms:config_form_view>
        <cms:character_counter>
            { editable:'my_editable_region' },        
            { editable:'my_other_editable', max:140 }        
        </cms:character_counter>
    </cms:config_form_view>


By default, if a "max" parameter is set, the tag creates a twitter-style countdown counter. But you can specify the kind of counter you want with the "type" parameter.


    { editable:'my_other_editable', max:140, type:'up' }


Additional parameters are detailed below. Here's a counter that's decked out with all the bells and whistles:

    <cms:config_form_view>
        <cms:character_counter>
        {
            repeatable:'my_repeatable',
            editable:'my_editable',
            min:20,
            max:159,
            type: 'up',
            show:'max min',
            label:'Character Counter:'
        }        
        </cms:character_counter>
    </cms:config_form_view>
    
### Parameters:
##repeatable:## name of the repeatable region
##editable:## name of the editable region
##max:## maximum desired character count
##min:## minimum desired character count
The character counter will turn red when it's out of range. The max and min parameters don't place any actual limits on what can be entered.
##type: either 'up' or 'down'. When the 'max' parameter is set, the default is a countdown counter. You can't use a countdown type without setting the 'max' parameter.
##show:## an option to show the max and/or min count along with the counter. valid options are 'max', 'min', min max', or 'both'.
##label:## applies a text label to the counter. Pro-tip: use html to dress up your label, i.e.: 

    label:'<strong style="color:purple;">Character Count:</strong>'

### Installing the Add-On ###

To use this tag, unzip and add the folder to your couch/addons directory. Initiate the tag in couch/addons/k_functions.php:

    require_once( K_COUCH_DIR.'addons/character-counter/character-counter.php' );

This tag takes advantage of new Admin Panel features, and won't work for Couch versions below 2.0. However, the script itself will work with older versions. The method of implementing it is a little different though.

Before v2.0, you can hack the script into a message-type editable region.

    <cms:editable name='character_counter' type='message' >
    	<script type="text/javascript">
        	var my_counters = [
            	{ editable:'my_editable', max:159 },
            	{ editable:'another_one', max:140, type:'up', show:'max' }
        	];
        	var script = document.createElement("script");
        	script.src = "<cms:show k_admin_link/>addons/character-counter/js/characterCounter.min.js";
        	document.body.appendChild(script);
    	</script>
    </cms:editable>

