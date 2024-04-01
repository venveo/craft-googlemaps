---
description:
---

# Define Keywords Event

This event is triggered when saving an [Address field](/address-field/).

:::warning Native Craft Event
This event is native to Craft. For further details, consult the Craft documentation:
- [`EVENT_DEFINE_KEYWORDS`](https://docs.craftcms.com/api/v4/craft-base-field.html#event-define-keywords)
- [`DefineFieldKeywordsEvent`](https://docs.craftcms.com/api/v4/craft-events-definefieldkeywordsevent.html)
:::

## Properties

| Property   |        Type        | Description                                 |
|:-----------|:------------------:|:--------------------------------------------|
| `value`    |      _mixed_       | The Address field's value to be indexed.    |
| `element`  | `ElementInterface` | The element whose field is being indexed.   |
| `keywords` |      _string_      | Custom compiled keywords for Address field. |
| `handled`  |       _bool_       | Whether the event is handled.               |

## Example

```php
use craft\events\DefineFieldKeywordsEvent;
use doublesecretagency\googlemaps\fields\AddressField;
use yii\base\Event;

Event::on(
    AddressField::class,
    AddressField::EVENT_DEFINE_KEYWORDS,
    function (DefineFieldKeywordsEvent $event) {

        // Set custom keywords
        $event->keywords = 'my custom keywords';

        // Mark event as handled
        $event->handled = true;

    }
);
```
