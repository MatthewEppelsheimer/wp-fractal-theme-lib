# Fractal

A WordPress template engine supporting inheritance with infinite nesting.

_This is alpha software._

## Instructions for Use

### Setting up a theme for Fractal

Add a `fractal/` directory to your theme file, including at least two files: `fractal.base.html` and `fractal.index.html`.

### Standard template files in the theme

Standard template files like `index.php` and `page.php` are required as always, because of WordPress' template redirect system.  E.g., if we're rendering an archive page for a custom post type "book", WordPress will first try to load `archive-book.php`. If that doesn't exist, it will fall back to `archive.php`. If that doesn't exist, it will fall back to `index.php`. (See [Template Hierarchy on the Codex](http://codex.wordpress.org/Template_Hierarchy)). 

With Fractal, it is recommended to *only* use standard template files to declare their inheritance of Fractal templates that live inside of the the `fractal/` directory. Here is the standard `index.php` file contents:


```
<?php
/**
 * index.php, the main template file.
 */

fractal_template();

fractal( 'index' );
```

`fractal_template();` ensures the system is initialized. It must be the first line of code in every Fractal template file.

`fractal( '$some_fractal_template' );` must be the last line of code in every Fractal template file. It establishes that this template is an ancestor of $some_fractal_template. Calling it loads and process the ancestor, and eventually handles rending. Note that `fractal( 'index' );` will load the template file `fractal/fractal.index.php`.

### The Base Template

`fractal.base.php` is special: It is the foundational template that all others inherit from. There are three requirements for this file:

1. It must begin with `fractal_template();` (like every template file).
2. It must declare the fractal_block `base`.
3. It must end with `fractal();`. Note that in this case we don't pass an ancestor template to the function.

```
<?php
/**
 * fractal.base.php, the foundational Fractal template.
 */

fractal_template();

fractal_block( 'base', function(){

  // ...

});

fractal();
```
