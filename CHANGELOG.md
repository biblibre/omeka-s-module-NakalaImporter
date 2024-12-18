# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.2] - 2024-12-18

The Nakala API exposes items from newest to oldest, so now we manipulate the sorting so that the import receives the response from the last page (of the API) first and adds the items in reverse order to integrate them in their “natural” order.

## [0.1.1] - 2024-12-17

Now when import an item set, Nakala metadatas is now included on item set creation.

## [0.1.0] - 2024-12-12

Initial release v0.1.0

Import resources directly from Nakala API
- Item set creation with API response informations
- Mapping one to one with records and automatic link with IIIF media