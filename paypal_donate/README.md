#PayPal Donate Button

The paypal_donate tag is a lot like the [paypal_button](http://docs.couchcms.com/tags-reference/paypal_button.html) tag , but a little simpler. Unlike the paypal_button tag, it does not require a clonable template or external variables. All necessary information is passed through parameters of the tag. It can be used together with the [paypal_processor](http://docs.couchcms.com/tags-reference/paypal_processor.html) tag . See [Core Concepts - PayPal](http://docs.couchcms.com/concepts/paypal.html) for a detailed discussion.

##Parameters
- image
- processor
- purpose
- reference
- amount
- custom

###image
This parameter is used to set the image used as the button.
You can either choose to use an image of your own or you may use one of the buttons made available by PayPal. Alternatively, you can use an ordinary submit button and style it with CSS.

####Using a CSS button -

Provide the image parameter with the string "css:My Custom Text". This will create a submit button with the value "My Custom Text" and the class name "pp_submit" which can be used to apply your own styles to the button.

	<cms:paypal_button image="css:Donate" />

####Using your own image -

	<cms:paypal_button image="<cms:show k_site_link />/images/my_button.gif" />

####Using PayPal provided images -

To use these images, set the image parameter to a number ranging from 0 to 6.

	<cms:paypal_button image='3' />

If not specified, the first image is the default. The numbers represent a range of PayPal donate button images.

###processor

The generated button provides PayPal with a link to the page that will process the IPN sent by it. By default this will be the link of the page the button is located on. If you have placed the paypal_processor tag, that handles the IPN, on some other page, set that page's link as this parameter.

###purpose

Set this to the name of the organization, fund, or campaign. If not set, the donor can fill in a text field on the PayPal site.

###reference

An optional field for specifying donation details.
	<cms:paypal_donate purpose='My Great Cause' reference='Spring Gala' />

###amount

You can specify an amount or leave it blank to allow donors to enter an amount on the PayPal website.

###custom

This is a pass-through variable for your own purposes. Donors won't see it. 

##Variables

This tag is self-closing and does not set any variables of its own.

