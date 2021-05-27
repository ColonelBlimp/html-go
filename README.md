# html-go


# Landing Pages
HTML-go has four editable landing pages for **posts**, **categories** and **tags**. The fourth
landing page is the **main index** or **home** page.
### Home Page (main index)
The data file is located at ``content/common/pages/index.md`` and is
listed in two indexes: ``slugIndex`` and ``pageIndex`` under the key ``index``.
### Category Index Page
The data file is located at ``content/common/landing/category/index.md`` and
is listed in one index: ``slugIndex`` under the key ``category/index``.
### Post Index Page
The data file is located at ``content/common/landing/posts/index.md`` and is
lised in one index: ``slugIndex`` under the key ``post/index``. Generally,
this index page is use when there is a static front page; if the 'blog' link is
enabled it will point to this page.
### Tag Index Page
This data file is located at ``content/common/landing/tags/index.md`` and
is listed in one index: ``slugIndex`` under the key ``tag/index``.

# Routing
Routes are defined in ``system/core/routes.php``. For the main site,
*special case* routes are explicity defined (i.e. landing pages where the requested
URI does not match the index key. See [above](#landing-pages)). The last route
definition is a *catch-all* route.

Content is retrieve using the requested URI, if the requested URI matches content
it is rendered, otherwise the *not found* page is rendered.
