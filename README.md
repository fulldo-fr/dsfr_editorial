# DSFR Editorial

Content sections (paragraphs) rendered with the components of the
[Système de design de l'État](https://www.systeme-de-design.gouv.fr/)
(DSFR, the French government design system), and a text filter that gives
tables written in the editor the DSFR markup.

Built for site builders and webmasters of French public-sector Drupal sites:
each section is a paragraph type with plain-language help texts, rendered by
the matching DSFR component, with the accessibility rules of the RGAA applied
by default rather than left to the editor.

## Sections

| Paragraph type     | DSFR component                  | Accessibility built in                                   |
|--------------------|---------------------------------|----------------------------------------------------------|
| `dsfr_text`        | (formatted text)                | —                                                        |
| `dsfr_accordions`  | accordion group + accordion     | Question heading level follows the section title (9.1)   |
| `dsfr_tabs`        | tabs + tab + tab panel          | Stable ids derived from the paragraph id                 |
| `dsfr_alert`       | alert                           | Type conveyed by color **and** icon (3.1)                |
| `dsfr_callout`     | callout (+ button)              | —                                                        |
| `dsfr_highlight`   | highlight                       | —                                                        |
| `dsfr_quote`       | quote                           | —                                                        |
| `dsfr_table`       | table                           | Section title becomes the table caption (5.4)            |
| `dsfr_media`       | content media + transcription   | Detailed description for informative images (1.6)        |

`dsfr_item` is the building block of accordions and tabs; it is not meant to
be added to a page directly.

## Text filter: DSFR table markup

Wraps every table in the four DSFR table containers (styling and horizontal
scrolling on small screens) and adds the `scope` of header cells: `col` in
the header row, `row` for a header cell starting a body row (RGAA 5.7). It
applies to any text field, not only to the table section.

Enable it on your text formats, **after** "Limit allowed HTML and correct
faulty HTML", and allow `<table> <caption> <thead> <tbody> <tfoot> <tr>
<th rowspan colspan> <td rowspan colspan>`.

Until a text format does so, table sections stay empty: the status report
shows a warning, and the help text of the table field tells editors to ask
their administrator.

## Requirements

- Drupal 10.3 or 11.
- [Paragraphs](https://www.drupal.org/project/paragraphs) and Entity
  Reference Revisions.
- Core Media and Media Library, with an `image` media type (provided by the
  Standard profile).
- The [UI Suite DSFR](https://www.drupal.org/project/ui_suite_dsfr) theme, or a
  sub-theme of it, as the front-end theme: the templates use its components.

## Installation

Install as any contributed module. The paragraph types and their fields are
provided as optional configuration: they are created when their dependencies
exist. Then add the section types to the paragraph reference field of your
content types.

## Templates

Each section has its own template (`paragraph--dsfr-*.html.twig`). A theme
overrides one by providing a file with the same name.

## Legal notice

The DSFR terms of use reserve the design system, and the identity of the
French State, to websites of the French public sphere. Check those terms
before using it on any other website. This module provides markup and
templates only; it does not ship the DSFR itself, the Marianne typeface, or
any distinctive sign of the French State.

## Maintainers

- FullDo — https://fulldo.fr
