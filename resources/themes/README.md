# Cyca's themes

Cyca supports themes, and this is the directory where they should sit.

Each directory contains a theme, which consists of, at least, a ```theme.js```
file, which is tailwind's configuration used to compile theme's CSS, and a
```theme.css``` file, where you will include base stylesheet:

```css
@import "../../css/app.css";
```

Please read [tailwind's documentation](https://tailwindcss.com/docs/configuration)
for more informations.

Your theme can contain more resources than just tailwind's configuration. For 
instance, custom fonts and icons.

Themes processing includings copying files from your theme's ```resources```
directory to Cyca's public directory. Please be sure to use a ```resources```
directory inside your theme if it needs such resources.

For instance, if your theme makes use of a custom font file, you can create a
```fonts``` folder inside the ```resources``` folder, and then put your font in
the ```fonts``` folder.

Please note that if you want Cyca to use custom icons, you should comply to the
following rules:

- Icons must be SVG
- All icons must be in the same file
- Icons identifiers must match the ones used in the _cyca-dark_ theme
- This file must be named "icons.svg"
- This file must be in a ```resources/images``` folder

These rules exist to ensure consistency across themes.

If you made a theme and want to share with others, you can make a 
[pull request](https://github.com/RichardDern/Cyca/pulls). Your theme will be
tested and integrated to Cyca's repository.