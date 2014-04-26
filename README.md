Dynamic Post Links
==================

WordPress plugin to support smart linking posts, images and uploads using the
`wp_posts.ID` rather than hardcoding URL into `wp_posts.wp_content`.

**Plugin inserts short code by default when using insert media/link to post
in the post editor.**

Supported formats
---

### Short code

Link original upload with id 123:

```html
[image id="123" size="full-size"]
[file id="123"]
```

### Href with ID

Link to post (or attachment) with 123:

```html
<a href="123" title="The title">Any text</a>
```

### Markdown style

Supports opening on `_blank` page (or new tab) is extra, not in original **M&darr;**
specs.

```html
[Any text](123 "title" _blank]
```

Installation
---

[Unzip release][releases] into `wp-content/plugins` or clone `git clone git@github.com:HackingWP/hivelogic-email-encoder.git`
subdirectory and activate in administration.

Enjoy!

[@martin_adamko](http://twitter.com/martin_adamko)

[releases]: https://github.com/HackingWP/dynamic-post-links/releases/latest
