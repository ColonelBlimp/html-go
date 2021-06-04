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
### Blog Index Page
The data file is located at ``content/common/landing/posts/index.md`` and is
lised in one index: ``slugIndex`` under the key ``blog/index``. Generally,
this index page is use if the 'blog' link is enabled it will point to this page.
### Tag Index Page
This data file is located at ``content/common/landing/tags/index.md`` and
is listed in one index: ``slugIndex`` under the key ``tag/index``.

# Routing
There is no complex router for html-go which analizes a request and routes it
accordingly. Rather, html-go uses a indexing system whereby all the content is indexed
and the URI used as the key. Apart from a few special cases such as landing pages,
the requested URI is passed to the indexing system to check if it exists,
otherwise the *not found* page is rendered.

# Content
Content files are in JSON format as JSON is handled natively by PHP and conversion
between JSON object and ``stdClass`` is also handled natively. The minimum
required for a valid content file is:

    {
        "title": "some title",
        "description": "some description",
        "body": "The content."
    }

### Menus

# Templating

### Context Variables

<table>
 <thead>
  <tr>
   <th>Variable</th>
   <th>Config Option</th>
   <th>Comments</th>
  </tr>
 </thead>
 <tbody>
  <tr>
   <td>{{ site.language }}</td>
   <td>site.language</td>
   <td>Default is "en"</td>
  </tr>
 </tbody>
</table>