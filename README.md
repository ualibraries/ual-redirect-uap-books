# University of Arizona Libraries redirect urls using JSON file WordPress plugin

This WordPress plugin maps book urls between the old and new versions of the University of Arizona Press website.
URLs in the form of `uapress.arizona.edu/Books/bid2137.htm` are mapped to `uapress.arizona.edu/book/a-doctors-legacy`

## `RedirectURLS.php`

This is a script to create `mapped_uris.json`. It's only of use to University of Arizona Libraries. If you need a different `mapped_uris.json` you would need a script that would create the redirects and output to a new `mapped_uris.json` but keep the same basic structure of the JSON object in in the original json file.
