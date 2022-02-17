# extendable-theme
Extendify WP theme updates
## PM@
https://github.com/users/tradesouthwest/projects/1/views/1

## Theme Updates for custom checkout and plugin Access

== Setup from zip ==
- Extract .zip to temp folder
- Add files from appropriate folders to appropriate directories

== Folder Structures ==
The following folders and/or files must be added to the theme folder.

Folder(assuming root of theme)  Files

style.css (all modifications to checkout styles)
functions.php (added hooks and actions)
`    /* =================================
    * START CHANGES BY LARRY @CODEABLE 
    * ================================= */
    about Line 385  
    to end
    /* =================================
    * ENDS  CHANGES BY LARRY @CODEABLE 
    * ================================= */`
woocmmerce/checkout/form-checkout.php
woocmmerce/checkout/review-order.php
woocmmerce/checkout/thank-you.php  
woocmmerce/checkout/form-billing.php


assets/images/*
*Additional Images for checkout payment details logos are in theme `assets/image/`
