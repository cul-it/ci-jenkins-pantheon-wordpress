<?php

namespace RebelCode\Wpra\Categories\Templates;

use Dhii\Output\TemplateInterface;
use RebelCode\Wpra\Core\Data\Collections\CollectionInterface;

/**
 * Decorates another template to add a "category" context option to filter the items collection by categories.
 *
 * @since 1.3.3
 */
class CategoriesFeedTemplate implements TemplateInterface
{
    /**
     * The inner template.
     *
     * @since 1.3.3
     *
     * @var TemplateInterface
     */
    protected $inner;

    /**
     * The collection of feed items.
     *
     * @since 1.3.3
     *
     * @var CollectionInterface
     */
    protected $itemsCollection;

    /**
     * Constructor.
     *
     * @since 1.3.3
     *
     * @param TemplateInterface   $inner           The inner template.
     * @param CollectionInterface $itemsCollection The collection of feed items.
     */
    public function __construct($inner, $itemsCollection)
    {
        $this->inner = $inner;
        $this->itemsCollection = $itemsCollection;
    }

    /**
     * @inheritdoc
     *
     * @since 1.3.3
     */
    public function render($ctx = null)
    {
        // Make sure the context is an array
        $arrCtx = (is_array($ctx) || is_object($ctx)) ? (array) $ctx : $ctx;

        // Get the categories
        {
            $category = isset($arrCtx['category'])
                ? $arrCtx['category']
                : [];

            $categories = is_string($category)
                ? explode(',', $category)
                : $category;

            $categories = array_map('trim', $categories);
        }

        // If no categories given, render the original template with the original context
        {
            if (!is_array($categories) || empty($categories)) {
                return $this->inner->render($ctx);
            }
        }

        // Filter the items collection with the given categories
        {
            // Use the items collection in the context, if given.
            // Otherwise use this template's collection
            $items = (!isset($arrCtx['items']) || !($arrCtx['items'] instanceof CollectionInterface))
                ? $this->itemsCollection
                : $arrCtx['items'];

            $newItems = $items->filter([
                'tax_query' => [
                    [
                        'taxonomy' => 'wprss_category',
                        'field' => 'slug',
                        'terms' => $categories,
                        'operator' => 'IN',
                    ],
                ],
            ]);

            $arrCtx['items'] = $newItems;
        }

        // Render using the inner template
        return $this->inner->render($arrCtx);
    }
}
