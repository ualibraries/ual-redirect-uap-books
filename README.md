# UA Library Redirect URL's Using JSON File Word Press Plug-in


This is a wordpress plug-in that maps book uri's from the old UAPress site the the new one. The old format for book uri's was `uapress.arizona.edu/Books/bid2137.htm` and this plugin maps it to `uapress.arizona.edu/book/a-doctors-legacy`. 

## RedirectURLS.php
This is a script to create create `mapped_uris.json`. It's only of use to UA Libraries. If you need a different `mapped_uris.json` you would need a script that would create the redirects and output to a new `mapped_uris.json` but keep the same basic structure of the JSON object in in the original json file.

