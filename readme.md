# Cards 0.1.1

Make cards from pages and their settings. Developed by Liam Perlaki.

A card is built from another page, not written a second time by hand: the title, the description and
any setting from the page you point at. The markup comes from a layout you control, so a card can be
anything, a section box, a news teaser, a person.

## How to install an extension

[Download ZIP file](https://github.com/pfadfinder26/yellow-cards/archive/refs/heads/main.zip) and copy it into your `system/extensions` folder. [Learn more about extensions](https://github.com/annaesvensson/yellow-update).

## How to make cards

One card for one page:

    [card /stufen/biber/]

Cards for all subpages of a page:

    [cards /stufen/]

Leave out the location, or write `-`, to use the page you are on. `[cards]` makes a card for every
subpage of the current page.

**Templates:** the second argument picks the layout `card-<template>.html` in your `system/layouts`
folder, the default is `card-default.html`. Write your own templates next to it, one per kind of
card:

    [cards /leitung/ person]

Themes can ship their own version, `includeLayout` prefers `<theme>-card-<template>.html` over
`card-<template>.html`, like with every other layout.

**Filter:** the third argument keeps only the pages whose setting matches, written as
`setting:value`, not case sensitive. The setting on the page may hold a list, `Stufe: gusp, raro`
matches both `stufe:gusp` and `stufe:raro`:

    [cards /leitung/ person stufe:gusp]

Several filters can follow each other, a page has to match all of them:

    [cards /leitung/ person stufe:gusp funktion:gruppenleitung]

A `*` as the value keeps every page that has that setting at all,
`[cards /leitung/ person stufenleitung:*]` collects the leaders of all sections in one row. Use `-`
for the template to keep the default one while filtering, for example `[cards /leitung/ - stufe:gusp]`. A filter with an empty value keeps the pages that do not have that
setting at all, `[cards /leitung/ person stufe:]`, which is how a page groups everything by a
setting: one block per value, one for the rest.

**Options:** any argument without a colon is passed to the template, which decides what it means,
for example `[cards /stufen/ stufe link]`, where the demo template makes the whole card the link to
that page, while a mail address inside it stays clickable. One option is handled
by the extension itself: `unlisted` also takes the pages that are `Status: unlisted`, so a section
that is out of the menu can still appear in an overview.

**Hidden pages:** a page with `Status: unlisted` stays out of the navigation and out of card rows,
but it can still be found. That makes a folder of pages a small database, for example one page per
leader, shown on several other pages as cards.

**Templates get the filters:** the fourth layout argument is the list of options and filters of that
call. A template can show what fits the row it is in, `Rolle-gusp` in a `stufe:gusp` row and `Rolle`
otherwise, see `card-person.html` of the demo site.

## How to write a card template

A card template is a normal layout. It gets the page of the card and the name of the template:

    <?php list($name, $page, $type) = $this->yellow->getLayoutArguments() ?>
    <div class="card">
    <h3><a href="<?php echo $page->getLocation(true) ?>"><?php echo $page->getHtml("title") ?></a></h3>
    <p><?php echo $page->getHtml("description") ?></p>
    </div>

Every setting at the top of the target page is available, `$page->getHtml("stufe")` for
`Stufe: gusp`, and so on. Pages without a `Description` setting get one from the first sentences of
their content, so a card has something to show without extra work. [Learn more about
layouts](https://datenstrom.se/yellow/help/how-to-customise-html-and-css).

The extension does not bring any CSS, the classes in your template are styled by your theme.

Do you have questions? [Get help](https://datenstrom.se/yellow/help/).
