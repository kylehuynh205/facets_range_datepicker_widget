<?php

namespace Drupal\facets_range_datepicker_widget\Plugin\facets\widget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\facets\FacetInterface;

/**
 * The Datepicker widget.
 *
 * @FacetsWidget(
 *   id = "range_datepicker",
 *   label = @Translation("Range Datepicker"),
 *   description = @Translation("A widget that shows a datepicker slider."),
 * )
 */
class RangeDatepickerWidget extends DatepickerWidget {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    $build = parent::build($facet);

    $results = $facet->getResults();
    if (empty($facet->getResults())) {
      return $build;
    }

    $active = $facet->getActiveItems();
    $default = '';
    if (isset($active[0]['max'])) {
      $default = date('Y-m-d', $active[0]['max']);
     }

    $build['#items']['max'] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'input',
        '#attributes' => [
          'type' => 'number',
          'class' => ['facet-datepicker'],
          'id' => $facet->id() . '-max',
          'name' => $facet->id() . '-max',
          'data-type' => 'datepicker-max',
          'value' => $default,
        ],
      ],
    ];

    $build['#items']['refine'] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'button',
        '#attributes' => [
          'type' => 'number',
          'class' => ['facet-datepicker-submit'],
          'id' => $facet->id() . '-submit',
          'name' => $facet->id() . '-submit',
          'data-type' => 'datepicker-submit',

        ],
        '#value' => "Refine",
      ],
    ];

    $url = array_shift($results)->getUrl()->toString();
    $build['#attached']['drupalSettings']['facets']['datepicker'][$facet->id()] = [
      'url' => $url,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function isPropertyRequired($name, $type) {
    if ($name === 'range_datepicker' && $type === 'processors') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getQueryType() {
    return 'range';
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, FacetInterface $facet) {
    $form += parent::buildConfigurationForm($form, $form_state, $facet);

    $message = $this->t('To achieve the standard behavior of a Range datepicker, you need to enable the facet setting below <em>"Range Datepicker"</em>.');
    $form['warning'] = [
      '#markup' => '<div class="messages messages--warning">' . $message . '</div>',
    ];

    return $form;
  }

}
