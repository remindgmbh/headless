# REMIND - Headless Extension

This extension provides:
- base content elements
- additional fields for pages and tt_content (e.g. items)
- extended flex form processor
- image processing middleware
- base configuration for form_framework
- a default backend layout


## Installation

Use comoser to install the extension using `composer install remind/headless`. Import typoscript in your provider extension.

Add the following to your site config:

```yaml
headless: 1
```

## Dependencies

Required dependencies are [headless](https://github.com/TYPO3-Headless/headless) and [content-defender](https://github.com/IchHabRecht/content_defender). The latter is used in the default backend layout.



## Backend Layouts

### Default

The default layout consists of 1 column with 2 rows. Besides the main content (colPos = 0) there is also one column for content above the breadcrumbs (colPos = 1).

## TCA

### tt_content

#### tx_headless_item

Field of type inline. Basically `tx_headless_item` acts like tt_content without a `colPos`. Used for `accordion` and `tabs`. See one of these definition on how to use items and override the showitem definition. Nested items are possible as well, however flexform only work for top level items for now.

To add a flexform to an item add the following configuration to `TCA/Overrides/tx_headless_item.php`:

```$GLOBALS['TCA']['tx_headless_item']['columns']['flexform']['config']['ds']['<tt_content CType>'] = '<Path to flexform xml file>';```

The `flexform` column has to be added to `showitem` of the respective tt_content type as well.
For example, to add a flexform to the accordion type the following must contain the flexform column:

```$GLOBALS['TCA']['tt_content']['types']['accordion']['columnsOverrides']['tx_headless_item']['config']['overrideChildTca']['types']['0']['showitem']```

To ouput the flexform data in the frontend, the flexform field has to be added to the content elements typoscript.
Example for modified `Accordion.typoscript`:

```
lib.accordionItems =< lib.items
lib.accordionItems {
    dataProcessing {
        10 {
            fields {
                flexform {
                    dataProcessing {
                        10 = Remind\Headless\DataProcessing\FlexFormProcessor
                        10 {
                            fieldName = flexform
                            as = flexform
                        }
                    }
                }
            }
        }
    }
}

tt_content.accordion =< lib.contentElementWithHeader
tt_content.accordion {
    fields {
        content {
            fields {
                items =< lib.accordionItems
            }
        }
    }
}
```

#### header_layout

Values for text, H1-H6 and hidden.

#### tx_headless_background_color

A background color for all content elements. Only option `none` exists per default. Add options per content element by using `columnOverrides`:

```php
    'columnsOverrides' => [
        'tx_headless_background_color' => [
            'config' => [
                'items' => [
                    1 => [
                        'label' => 'Custom Color',
                        'value' => 'custom',
                    ],
                ],
            ],
        ],
    ],
```



#### tx_headless_background_full_width

Only visible if `tx_headless_background_color` is other than `none`. Used to extend the background color to full width instead of the content container only.

#### tx_headless_space_before_inside

Addition to `space_before`. Space before the content element, but inside the background color. Only available if `tx_headless_background_color` is other than none.

#### tx_headless_space_after_inside

Similar to `space_before_inside`.

### pages

#### tx_headless_footer

The `tx_headless_footer` flexform field contains the footer content. Use the following code to set a flexform:

```php
\Remind\Headless\Utility::setFooterFlexForm('FILE:EXT:provider_extension/Configuration/FlexForms/Footer.xml');
```

#### tx_headless_overview_label

An `tx_headless_overview_label` field is added to the page TCA. The field should be used to customize the label for the overview pages.

### Crop Variants

`ImageProcessingMiddleware` accepts a breakpoint as a query parameter and uses a crop variant by that name if available. Appropriate crop variants have to be created for content elements.

Example for crop variants for breakpoints sm, md and lg for `textpic`:

```php
$GLOBALS['TCA']['tt_content']['types']['textpic']['columnsOverrides']['image']['config']['overrideChildTca']['columns']['crop']['config'] = [
	'cropVariants' => [
		'sm' => [
			// configuration
		],
		'md' => [
			// configuration
		],
		'lg' => [
			// configuration
		],
	],
];
```

Example for crop variants for breakpoints sm, md and lg for `accordion` items:

```php
$GLOBALS['TCA']['tt_content']['types']['accordion']['columnsOverrides']['tx_headless_item']['config']['overrideChildTca']['columns']['image']['config']['overrideChildTca']['columns']['crop']['config'] = [
	'cropVariants' => [
		'sm' => [
			// configuration
		],
		'md' => [
			// configuration
		],
		'lg' => [
			// configuration
		],
	],
];
```


## Content Elements

### accordion

Uses `tx_headless_item`, items consist of text (header, subheader, bodytext, title), a flexform field and images.

### tabs

Uses `tx_headless_item`, items consist of text (header, subheader, bodytext) only.
