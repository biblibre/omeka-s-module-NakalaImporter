Introduction
============

How does it work ?
------------------

This Omeka S module allows to import resources from Nakala API.
Select collection(s) you want, and the module will import the items and their media.

Where is the configuration
--------------------------

After configuring the module with your API key (in `local.config.php` file) on the admin side you'll find a form allowing you to choose which Nakala collections to retrieve according to your rights `Nakala documentation`_.

.. note::
   Extract of `local.config.php` file:

   .. code-block:: php

      'nakala-importer' => [
          'api_key' => 'your-nakala-api-key',
      ],

.. _Nakala documentation: https://documentation.huma-num.fr/nakala/#tableau-recapitulatif-des-droits-par-role

.. toctree::
   :maxdepth: 2
   :caption: Contents

   configuration
   features
   tutorials