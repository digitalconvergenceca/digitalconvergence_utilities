<?php

namespace Drupal\digitalconvergence_utilities\Render;

/**
 * Provides lazy builder placeholder element generation.
 */
trait PlaceholderGeneratorTrait {

  /**
   * The placeholder generator render service.
   *
   * @var \Drupal\Core\Render\PlaceholderGeneratorInterface
   */
  protected $placeholderGenerator;

  /**
   * Returns the placeholder generator render service.
   *
   * @return \Drupal\Core\Render\PlaceholderGeneratorInterface
   *   The placeholder generator render service.
   */
  protected function placeholderGenerator() {
    if (!isset($this->placeholderGenerator)) {
      $this->placeholderGenerator = \Drupal::service('render_placeholder_generator');
    }
    return $this->placeholderGenerator;
  }

  /**
   * Generates a placeholder render element.
   *
   * @param array $element
   *   The lazy builder render element.
   *
   * @return array
   *   The renderable element array.
   */
  protected function generateRenderPlaceholder(array $element) {
    $element += ['#create_placeholder' => TRUE];
    return $this->placeholderGenerator()->createPlaceholder($element);
  }

}
