<?php

namespace Drupal\facets_range_datepicker_widget\Plugin\facets\processor;

use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\PreQueryProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;
use Drupal\facets\Processor\BuildProcessorInterface;

/**
 * Provides a processor that adds all values for selected date.
 *
 * @FacetsProcessor(
 *   id = "datepicker",
 *   label = @Translation("Datepicker"),
 *   description = @Translation("Add results for selected day."),
 *   stages = {
 *     "pre_query" = 60,
 *     "build" = 20
 *   }
 * )
 */
class DatepickerProcessor extends ProcessorPluginBase implements PreQueryProcessorInterface, BuildProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function preQuery(FacetInterface $facet) {
    $active_items = $facet->getActiveItems();
    if ($facet->getWidget()['type'] === 'datepicker') {
      array_walk($active_items, function(&$item) {
        if (preg_match('/\(min:((?:-)?[\d\.]+)/i', $item, $matches)) {
          $item = ['min' => $matches[1], (string) ($matches[1] + 86399)];
        }
        else {
          $item = NULL;
        }
      });
    }
    $facet->setActiveItems($active_items);
  }

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet, array $results) {
    /** @var \Drupal\facets\Plugin\facets\processor\UrlProcessorHandler $url_processor_handler */
    $url_processor_handler = $facet->getProcessors()['url_processor_handler'];
    $url_processor = $url_processor_handler->getProcessor();
    $active_filters = $url_processor->getActiveFilters();

    if (isset($active_filters[''])) {
      unset($active_filters['']);
    }

    /** @var \Drupal\facets\Result\ResultInterface[] $results */
    foreach ($results as &$result) {
      $new_active_filters = $active_filters;
      unset($new_active_filters[$facet->id()]);
      if ($facet->getWidget()['type'] !== 'datepicker') {
        continue;
      }
      // Add one generic query filter with the min placeholder.
      $new_active_filters[$facet->id()][] = '(min:__datepicker_min__)';
      $url = \Drupal::service('facets.utility.url_generator')->getUrl($new_active_filters, FALSE);
      $result->setUrl($url);
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function supportsFacet(FacetInterface $facet) {
    $field_name = $facet->getFieldIdentifier();
    /** @var \Drupal\Core\Field\TypedData\FieldItemDataDefinition $field */
    $field = $facet->getFacetSource()->getDataDefinition($field_name);
    $allowed_types = [
      'datetime',
      'created',
      'changed'
    ];
    if (in_array($field->getFieldDefinition()->getFieldStorageDefinition()->getType(), $allowed_types)) {
      return TRUE;
    }
    return FALSE;
  }

}
