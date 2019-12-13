<?php

namespace Drupal\digitalconvergence_utilities\Block;

/**
 * Utility class for retrieving and building Blocks.
 */
class BlockBuilder {

  /**
   * Retrieves a Block's render array.
   *
   * @param string $block_id
   *   The block plugin ID.
   * @param array $configuration
   *   The configuration to pass to the block.
   * @param array $context_values
   *   The contexts to apply in conjunction with available contexts.
   *
   * @return array
   *   The Block's render array.
   */
  public static function build($block_id, array $configuration = [], array $context_values = []) {
    if ($block = static::get($block_id, $configuration, $context_values)) {
      return $block->build();
    }

    return [];
  }

  /**
   * Retrieves a Block instance.
   *
   * @param string $block_id
   *   The block plugin ID.
   * @param array $configuration
   *   The configuration to pass to the block.
   * @param array $context_values
   *   The contexts to apply in conjunction with available contexts.
   *
   * @return \Drupal\Core\Block\BlockBase
   *   The block. Will return NULL if an exception is caught.
   */
  public static function get($block_id, array $configuration = [], array $context_values = []) {
    try {
      /** @var \Drupal\Core\Block\BlockManagerInterface $block_manager */
      $block_manager = \Drupal::service('plugin.manager.block');
      /** @var \Drupal\Core\Block\BlockBase $block */
      $block = $block_manager->createInstance($block_id, $configuration);
      /** @var \Drupal\Core\Plugin\Context\ContextHandlerInterface $context_handler */
      $context_handler = \Drupal::service('context.handler');
      /** @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface $context_repository */
      $context_repository = \Drupal::service('context.repository');

      $contexts = $context_repository->getAvailableContexts() + $context_values;
      $context_handler->applyContextMapping($block, $contexts);
    }
    catch (\Exception $exception) {
      return NULL;
    }

    return $block;
  }

}
