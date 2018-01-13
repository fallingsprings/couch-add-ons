# PayPal Donate Button

The paypal_donate tag is a lot like the [paypal_button](http://docs.couchcms.com/tags-reference/paypal_button.html) tag which it's based on, but it's a little simpler. Unlike the paypal_button tag, paypal_donate does not require a clonable template or expect any external variables. All necessary information is passed through optional parameters of the tag.


At it's simplest, `<cms:paypal_donate />` can be used anywhere so long as the Paypal credentials are configured in couch/config.php.

This tag can be used together with the [paypal_processor tag](http://docs.couchcms.com/tags-reference/paypal_processor.html). See [Core Concepts - PayPal](http://docs.couchcms.com/concepts/paypal.html) for a detailed discussion.

## Parameters
- purpose
- reference
- amount
- image
- processor
- custom

### purpose

Set this to the name of the organization, fund, or campaign, or the purpose of the donation. The paypal_processor returns this as `pp_item_name`.

### reference

An optional field for specifying donation details. The paypal_processor returns this as `pp_item_number`.


    <cms:paypal_donate purpose='A Great Cause' reference='Spring Gala' />

### amount

Specify an amount or leave it empty to allow donors to enter any amount on the PayPal website.

### image
This parameter is used to set the image used as the button.
You can either choose to use an image of your own or you may use one of the buttons made available by PayPal. Alternatively, you can use an ordinary submit button and style it with CSS.

#### Using your own image -

	<cms:paypal_donate image="<cms:show k_site_link />/images/my_button.gif" />

#### Using PayPal provided images -

To use these images, set the image parameter to a number ranging from 0 to 6.

	<cms:paypal_donate image='3' />

If not specified, '0' is the default. The numbers represent a range of PayPal donate button images.

#### Using a CSS button -

Provide the image parameter with a string in the form of "css:My Custom Text". This will create an html button with the value "My Custom Text". The paypal_donate tag accepts an id and/or classes which will be attached to the button so that you can apply styles; id and class names can be used on image buttons as well.

    <cms:paypal_donate image="css:Donate Now" id="donate-button" class="button" />

### processor

The generated button provides PayPal with a link to the page that will process the IPN sent by it. By default this will be the link of the page the button is located on. If you have placed the paypal_processor tag, that handles the IPN, on some other page, set that page's link as this parameter.

### custom

This is a pass-through variable for your own purposes. Donors won't see it. 

## Variables

This tag is self-closing and does not set any variables of its own.

