<?php

namespace Drupal\digitalconvergence_utilities\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;

/**
 * Utility Drush commands for developers and other operational needs.
 */
class EntityOperationCommands extends DrushCommands {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a new instance of EntityOperationCommands.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Performs a complete purge of a particular entity type's entities.
   *
   * @param string $entity_type_id
   *   The entity type ID (e.g. node).
   * @param string $bundle_name
   *   (optional) The name of a particular bundle to purge.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drush\Exceptions\UserAbortException
   *
   * @command digitalconvergence:utilities:entity-ops-delete
   */
  public function deleteAll($entity_type_id, $bundle_name = NULL) {
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    $storage_handler = $this->entityTypeManager->getStorage($entity_type_id);
    $query = $storage_handler->getQuery();
    if ($entity_type->hasKey('bundle') && !empty($bundle_name)) {
      $query->condition($entity_type->getKey('bundle'), $bundle_name);
    }
    $query->accessCheck(FALSE);
    $results = $query->execute();
    if (empty($results)) {
      $this->logger()->notice(dt('There were no entities found.'));
      return;
    }

    $confirm = $this->io()->confirm(dt('Are you sure you want to delete all entities (count: :count)?', [
      ':count' => count($results),
    ]));
    if (!$confirm) {
      throw new UserAbortException();
    }

    $entities = $storage_handler->loadMultiple($results);
    $storage_handler->delete($entities);

    $this->output()->writeln('Deletion complete.');
  }

}
