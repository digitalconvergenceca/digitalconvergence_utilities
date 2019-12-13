<?php

namespace Drupal\digitalconvergence_utilities\Block;

/**
 * A utility trait used in conjunction with the BlockBuilder class.
 */
trait BlockBuilderTrait {

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
  protected function getBlockForRender($block_id, array $configuration = [], array $context_values = []) {
    if ($block = $this->getBlockInstance($block_id, $configuration, $context_values)) {
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
  protected function getBlockInstance($block_id, array $configuration = [], array $context_values = []) {
    return BlockBuilder::get($block_id, $configuration, $context_values);
  }

}
