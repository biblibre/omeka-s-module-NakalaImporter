# Nakala Importer

This module allows you to import resources from Nakala API webservice.

The complete documentation of NakalaImporter can be found [here](https://biblibre.github.io/omeka-s-module-NakalaImporter).

## Rationale

This plugin offers a new way to import resources:
- mapping one to one with Nakala propertyUri
- automatically linking with medias files in Nakala response data

## Requirements

* Omeka S >= 4.0.0

## Quick start

1. [Add the module to Omeka S](https://omeka.org/s/docs/user-manual/modules/#adding-modules-to-omeka-s)
2. Login to the admin interface, install NakalaImporter module.
3. Define your API key in `local.config.php` file
4. Use it

## Features

This module import resources directly from Nakala API with a mapping one to one (not configurable).

After configuring the module with your API key (in `local.config.php` file) on the admin side you'll find a form allowing you to choose which Nakala collections to retrieve according to your rights [Nakala documentation here](https://documentation.huma-num.fr/nakala/#tableau-recapitulatif-des-droits-par-role).

When you submit the form, you'll find your collections, with the option of importing them by selecting them.

The next submission will allow you to run the task in the background in order to integrate the resources.

You will then be redirected to the summary view with all past imports.

## How to contribute

You can contribute to this module by adding issues directly [here](https://github.com/biblibre/omeka-s-module-NakalaImporter/issues).

## Contributors / Sponsors

Contributors:
* [ThibaudGLT](https://github.com/ThibaudGLT)

NakalaImporter was sponsored by:
* [Université de Bourgogne](#)

## Licence

NakalaImporter is distributed under the GNU General Public License, version 3. The full text of this license is given in the LICENSE file.

Created by [BibLibre](https://www.biblibre.com).