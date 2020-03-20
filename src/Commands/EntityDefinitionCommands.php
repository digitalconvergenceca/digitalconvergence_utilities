<?php

namespace Drupal\digitalconvergence_utilities\Commands;

use Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Commands\DrushCommands;

/**
 * Defines entity definition related utility commands.
 */
class EntityDefinitionCommands extends DrushCommands {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity definition update manager.
   *
   * @var \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface
   */
  protected $entityDefinitionManager;

  /**
   * Creates a new EntityDefinitionCommands instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface $entity_definition_manager
   *   The entity definition update manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityDefinitionUpdateManagerInterface $entity_definition_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityDefinitionManager = $entity_definition_manager;
  }

  /**
   * Rebuilds the stored definition of a given entity.
   *
   * @param string $entity_type
   *   The machine name of the entity type to update (e.g. node).
   *
   * @command digitalconvergence:utilities:entity-definition-update
   */
  public function updateEntityDefinition($entity_type) {
    try {
      $this->entityTypeManager->clearCachedDefinitions();
      $this->entityTypeManager->useCaches(FALSE);
      $entity_definition = $this->entityTypeManager->getDefinition($entity_type);
      $this->entityDefinitionManager->updateEntityType($entity_definition);
      $this->logger()->info(dt('Entity type updated.'));
    }
    catch (\Exception $exception) {
      $this->logger()->critical($exception->getMessage());
    }
  }

}
