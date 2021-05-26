# html-go

# Landing Pages
HTML-go has four editable landing pages for **posts**, **categories** and **tags**. The fourth
landing page is the **main index** or **home** page.
### Home Page (main index)
This data file is located at

    content/common/pages/index.md

This data file is listed in two indexes: ``slugIndex`` and ``pageIndex`` with the key ``index``
### Category Index Page
This page is located at

    content/common/landing/category/index.md
    
This data file is listed in one index: ``slugIndex`` with the key ``category/index``
### Post Index Page
This page is located at

    content/common/landing/posts/index.md

This data file is lised in one index: ``slugIndex`` with the key ``post/index``. This
page is generally use when there is a static front page. If the 'blog' link is enabled it will
point to this page.
### Tag Index Page
This page is located at

    content/common/landing/tags/index.md
