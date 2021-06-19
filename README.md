# HTML-go
HTML-go is a databaseless, flat-file blogging platform, which is very flexible, simple and fast.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ColonelBlimp/html-go-func/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/ColonelBlimp/html-go-func/?branch=main) [![Build Status](https://scrutinizer-ci.com/g/ColonelBlimp/html-go-func/badges/build.png?b=main)](https://scrutinizer-ci.com/g/ColonelBlimp/html-go-func/build-status/main) [![Code Intelligence Status](https://scrutinizer-ci.com/g/ColonelBlimp/html-go-func/badges/code-intelligence.svg?b=main)](https://scrutinizer-ci.com/code-intelligence) [![Maintainability](https://api.codeclimate.com/v1/badges/b59227a05de955a954b5/maintainability)](https://codeclimate.com/github/ColonelBlimp/html-go-func/maintainability) [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=ColonelBlimp_html-go-func&metric=alert_status)](https://sonarcloud.io/dashboard?id=ColonelBlimp_html-go-func)

# Features

- Simple, fast and flexible
- Supports PHP 8+
- Multiple template systems supported: Twig, Smarty and PHP
- Categorization and multiple tags
- Content format in JSON
- GitHub flavoured Markdown
- Content summary through front matter or manual splitting
- Multiple menus defined through front matter
- Nested static pages

# Landing Pages
HTML-go has four editable landing pages for **blog**, **category**, **tag** and the **index** or
**home** page which at the root of all the other static pages.
All of these pages are listed in the `slugIndex` and the `pageIndex` and are considered
to be pages by the html-go.
### Home Page (main index)
The data file is located at `content/common/pages/index.md` and is
listed in two indexes: `slugIndex` and `pageIndex` under the key `/`.
### Category Index Page
The data file is located at `content/common/landing/category/index.md` and
is listed in two indexes: `slugIndex` and `pageIndex` under the key `category`.
### Blog Index Page
The data file is located at `content/common/landing/blog/index.md` and is
lised in one index: `slugIndex` under the key `blog`. Generally,
this index page is use if the 'blog' link is enabled which should link to this page.
### Tag Index Page
This data file is located at `content/common/landing/tags/index.md` and
is listed in one index: `slugIndex` and `pageIndex` under the key `tag`.

# Indexing
The indexing system is at the core of HTML-go. All content is listed in one or
more indexes.  The main index is called the `slugIndex` and list all **posts**,
**categories**, **pages** and **tags**.

There are some *special* composite indexes for category to posts and tag to posts.

# Routing
There is no complex router for html-go. Rather, HTML-go uses a indexing system
whereby all the content is indexed with its unique URI used as the index key. Apart from
a few special cases such as landing pages, the requested URI is passed to the
indexing system to check if it exists, if it does it is loaded and rendered.
Otherwise the *not found* page is rendered.

# Content
Content files are in JSON format as JSON is handled natively by PHP and conversion
between JSON object, `stdClass` and an `array` is also handled natively.
The minimum required for a valid content file is:

    {
        "title": "some title",
        "description": "some description",
        "body": "The content of this article."
    }

## Summary
HTML-go automatically generates a summary of the content.

### Front Matter Summary
If you require the summary to be something different to the opening text of the
article, you can add the following to the front matter:

    {
        "title": "some title",
        "description": "some description",
        "summary": "Now for something completely different."
        "body": "The content of this article."
    }

### Manual Summary
If the `summary` variable is not defined in the front matter, HTML-go will search
the `body` for the divider marker `<!--more-->` and split the text.  For example:

    {
        "title": "some title",
        "description": "some description",
        "body": "The content<!--more--> of this article."
    }

The above will give a `summary` of *"The content"* and the `body` *"The content of the article."*

### Menus
Menus entries are valid for *pages* only. A single content page can be listed in as many menus
as required. Defined menus are available on the `content.menus.[menu_name]` object
within the template context.

For example, below is a sample home page with the page listed in two menus:
**main** and **footer**. The **name** for the menu link is *Home* in both menus
and the position (weight) is the first entry. The actual link will be the same
for both menus and is defined by the system, in this case `/`

    {
        "title" : "Our Website",
        "description" : "Welcome to our website",
        "menus": {
            "main": {
                "name": "Home",
                "weight": 1
            },
            "footer": {
                "name": "Home",
                "weight": 1
            }
        },
        "body" : "Welcome to our new website."
    }

The above menus can be accessed by the following **Twig** code:

    {{ content.menus.main }}

and

    {{ content.menus.footer }}

#### Twig Code Sample
    {% if content.menus.main is defined %}
    {% for main in content.menus.main %}
            <a href="{{ content.site.url }}{%if main.key starts with '/'%}{{ main.key }}{% else %}/{{ main.key }}{% endif %}">{{ main.name }}</a>
    {% endfor %}
    {% endif %}

### Sections
Content is identified by its *section*. There are currently four sections **page** for static pages,
**tag** for tags, **category** for categories and **post** for posts.

# Templating

### Context Variables

|Twig |Smarty |PHP |Config | Comments|
|--- | --- | --- | --- | ---|
|`{{ site.language }}`|?|?|site.language | Default is "en"|

# Technical Data

## Content Object
The content object is a `stdClass` and has the following public properties:


