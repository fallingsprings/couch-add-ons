# Character Counter
## Add Character Counters to Fields in the Admin Panel

This custom tag allows you to attach character counters of different kinds to any field in the Couch Admin Panel. The tag can set any number of counters with several different types available. They can be a useful tool for SEO and meta tags, or anywhere the editor needs to be aware of the constraints on a text region.

The tag itself is essentially a container for the javascript code that creates the counters. It is meant to be added to the 'cms:script' tag nested inside the 'cms:config_form_view' tag in your template. It can share the space with other custom javascript.

    <cms:config_form_view>
    	<cms:script>
    		alert('Hello World!');
        	<cms:character_counter>
            	{ editable:'my_editable' }        
        	</cms:character_counter>
        </cms:script>
    </cms:config_form_view>

Parameters for counters are nested inside of the 'cms:character_counter' tag. The configuration above adds a simple counter beneath the 'my_editable' field, showing the number of characters in the field. If it's part of a repeatable region, add the name of the repeatable to the declaration.

    { repeatable:'my_repeatable', editable:'my_editable' }    

You can also add a **min** or **max** limit to the counter. This won't actually limit the number of characters, but the counter will turn red when it's out of range. Use the **enforce_max** parameter to actively limit the number of characters allowed.

    <cms:character_counter>
        { repeatable:'my_repeatable', editable:'my_editable' }, 
        { editable:'countdown_style', max:140 }, 
        { editable:'strict_counter', max:140, enforce_max:true } 
    </cms:character_counter>
        	
Notice that multiple counters are set in the same tag. You can set any number of counters at the same time. _Don't forget the comma between different counters_.

By default, the counter becomes a countdown counter when a 'max' limit is specified, counting down from the 'max' value. You can change that with the 'type' parameter.

    { editable:'not_countdown', max:140, type:'up' } 
    
Additionally, you can choose to show the min and/or max value alongside the counter, or add a label.

    <cms:character_counter>
        { repeatable:'my_repeatable', editable:'my_editable' }, 
        { editable:'twitter_style', max:140 },
        { editable:'super_fancy', min:35, max:500, type:'up', show:'both', label:'Character Count:' } 
    </cms:character_counter>

###Older Versions of Couch

Older versions of Couch don't have v2.0's custom admin features, but as mentioned, the tag is just a container for the script. For older versions of Couch you can use a message-type editable region to deliver the script.

    <!-- CHARACTER COUNTER -->
    <cms:editable name='char_counter' type='message' order="100">
    	<script type="text/javascript">
        	<cms:character_counter>
        	   { editable:'meta_description', max:159 }
        	</cms:character_counter>
    	</script>
    </cms:editable>

###On the Front End

You can even use this script on the front end of your website if you happen to need a character counter for a field. Just place it inside a script tag at the bottom of your template. For your front end fields, use the 'field' parameter instead of 'editable' to target the id of the field you want to target. You may need additional css to style the counter on the front_end. the character counter's id is the target id with '_counter' appended.

    <script type="text/javascript">
        <cms:character_counter>
            { field:'my_form_field', max:180 }
        </cms:character_counter>
    </script>

###Parameters:

**repeatable** - name of the repeatable region

**editable** -  name of the editable region

**max** -  maximum count

**min** -  minimum count

**enforce_max** - strictly enforces the _max_ parameter, clipping off any characters above the maximum limit. Give it any true value.

**type** - either 'up' or 'down'
- When the 'max' parameter is set, the default is a countdown counter.	
- You can't use a countdown type without setting the 'max' parameter.
	
**show** - an option to show the max and/or min count along with the counter. Valid options include 'max', 'min', 'both', 'min max', 'max min', 'MiniMax', or 'Maxine has a really min cat'.

**label** - applies a text label to the counter.
- Pro-tip: use html to dress up your label: 

	{ editable:'lets-go-crazy', label:'<strong style="color:purple;">Character Count:</strong>' }

###Installing the Add-On

To use this tag, unzip and add the folder to your 'couch/addons' directory. Initiate the tag in 'couch/addons/k_functions.php'.

	require_once( K_COUCH_DIR.'addons/character-counter/character-counter.php' );
