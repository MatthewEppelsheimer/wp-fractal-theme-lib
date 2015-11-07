# Fractal

An object oriented WordPress template engine supporting inheritance with infinite nesting.

## Deprecated ##

This project is no longer being maintained.

Any project using it should replace it with something else, and any theme using either Fractal itself or Fractal patterns in its theme should have said theme be rewritten.

## Instructions for Use

### Setting up a theme for Fractal

Add a `fractal/` directory to your theme file, including at least two files: `fractal.base.html` and `fractal.index.html`.

### Standard template files in the theme

Standard template files like `index.php` and `page.php` are required as always, because of WordPress' template redirect system.  E.g., if we're rendering an archive page for a custom post type "book", WordPress will first try to load `archive-book.php`. If that doesn't exist, it will fall back to `archive.php`. If that doesn't exist, it will fall back to `index.php`. (See [Template Hierarchy on the Codex](http://codex.wordpress.org/Template_Hierarchy)). 

With Fractal, it is recommended to *only* use standard template files to declare their inheritance of Fractal templates that live inside of the the `fractal/` directory. 
