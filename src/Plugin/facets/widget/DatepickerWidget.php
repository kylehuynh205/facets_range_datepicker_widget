<?php

namespace Drupal\facets_range_datepicker_widget\Plugin\facets\widget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\facets\FacetInterface;
use Drupal\facets\Widget\WidgetPluginBase;

/**
 * The datepicker widget.
 *
 * @FacetsWidget(
 *   id = "datepicker",
 *   label = @Translation("Datepicker"),
 *   description = @Translation("A widget that shows a datepicker."),
 * )
 */
class DatepickerWidget extends WidgetPluginBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    $build = parent::build($facet);

    $results = $facet->getResults();
    if (empty($results)) {
      return $build;
    }
    ksort($results);

    $active = $facet->getActiveItems();
    $default = '';
    if (isset($active[0]['min'])) {
      $default = date('Y-m-d', $active[0]['min']);
    }

    $build['#items'] = [];
    $build['#items']['min'] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'input',
        '#attributes' => [
          'placeholder' => 'From',
          'type' => 'number',
          'class' => ['facet-datepicker'],
          'id' => $facet->id() . '-min',
          'name' => $facet->id() . '-min',
          'data-type' => 'datepicker-min',
          'value' => $default,
        ],
      ],
    ];

    $url = array_shift($results)->getUrl()->toString();
    $build['#attached']['drupalSettings']['facets']['datepicker'][$facet->id()] = [
      'url' => $url,
    ];

    $build['#attached']['library'][] = 'facets_range_datepicker_widget/datepicker';

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, FacetInterface $facet) {
    $form += parent::buildConfigurationForm($form, $form_state, $facet);

    $message = $this->t('To achieve the standard behavior of a datepicker, you need to enable the facet setting below <em>"Datepicker"</em>.');
    $form['warning'] = [
      '#markup' => '<div class="messages messages--warning">' . $message . '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function isPropertyRequired($name, $type) {
    if (($name === 'datepicker') && $type === 'processors') {
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

}
