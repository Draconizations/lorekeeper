# Fulmn's Lorekeeper Extensions
Hi! This repo houses all my **public** lorekeeper extensions. To view any of them in particular, either change the branch using the menu at the top left (above the file viewer), or use any of the links below.

This branch only serves as an index and to provide any additional info that I cannot add to the extensions themselves.

### Support & Bug Reports
You *can* open an issue on this repository, but I'll respond much quicker in the lorekeeper support server. My discord username is `fulmine`, though I used to go by as `Fulmn` in the LK community!

## List of Extensions
> [!NOTE]
> If an extension branch is not listed here, it is a work in progress and **should not** be used yet.

### Multiple Trait Subtypes
![Static Badge](https://img.shields.io/badge/version-v3.0.0/develop-blue) ![Static Badge](https://img.shields.io/badge/status-stable-green)
![Static Badge](https://img.shields.io/badge/supported-yes!-green)

Allows a trait to be associated with multiple subtypes. Doesn't restrict trait assignment, this is purely for aesthetic and clarity reasons.

Useful if you have traits that aren't restricted to one subtype. One use case is if you use the subtype trait index and want users to quickly see all traits they can pick for a particular subtype.

> [!NOTE]
> This extension has been merged into core develop as of December 17th, 2025 (v3.1). If you're on latest develop, you should have this already.

- [wiki page](http://wiki.lorekeeper.me/index.php?title=Extensions:Multiple_Trait_Subtypes)
- [v3.0.0 branch](https://github.com/Draconizations/lorekeeper/tree/extension/multiple-trait-subtypes)

### World Expanded - Unified
![Static Badge](https://img.shields.io/badge/version-v3.0.0-blue) ![Static Badge](https://img.shields.io/badge/status-stable-green)
![Static Badge](https://img.shields.io/badge/supported-yes!-green)

Built on top of Uri's [v3 version of world-expanded](https://github.com/preimpression/lorekeeper/tree/v3/world-expansion). This version consolidates the blade files for each world expansion page type into one. Category/type pages, as well as the entry pages themselves, now all use the same blade file. This *should* enable you to make layout changes much easier.

The base layout did change a bit as a result of me porting this over from my own LK instance, but it is (should be?) a sensible default.

- [v3.0.0 branch](https://github.com/Draconizations/lorekeeper/tree/v3/world-expansion-unified)

### Genetic Data Images
![Static Badge](https://img.shields.io/badge/version-v2.0.0-blue) ![Static Badge](https://img.shields.io/badge/status-stable-green)
![Static Badge](https://img.shields.io/badge/supported-yes!-green)

Built on top of Pure09's [Character Genetic Data](https://github.com/deep-ci/lorekeeper/tree/ext/v2/character-genetic-data). Uses the same lorekeeper version to make merging easier.

Adds an image gallery to display what different genome combinations would look like. Admins can add new images and associate it with a genome. Users can view these images both on the genetics index, a big image gallery and as dedicated gallery pages for each gene group.

- [v2.0.0 branch](https://github.com/Draconizations/lorekeeper/tree/extension/genetic-data-images)

## License
All extension in this repository are licensed under MIT unless specified otherwise!
