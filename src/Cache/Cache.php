<?php

namespace Drupal\digitalconvergence_utilities\Cache;

use Drupal\Core\Cache\Cache as CacheBase;
use Drupal\Core\Cache\CacheableDependencyInterface;

/**
 * Utility methods for cache.
 */
class Cache extends CacheBase {

  /**
   * Builds a common expiration given a cacheable dependency object.
   *
   * @param object $object
   *   An object to be cached which requires an expiration calculation. If the
   *   object is not a CacheableDependencyInterface, this method will always
   *   return 0.
   *
   * @return int
   *   The expiration value.
   */
  public static function buildExpiration($object) {
    if (!($object instanceof CacheableDependencyInterface)) {
      return 0;
    }

    $max_age = $object->getCacheMaxAge();
    if ($max_age == static::PERMANENT || $max_age == 0) {
      return $max_age;
    }

    $time_now = \Drupal::time()->getCurrentTime();
    return $time_now + $max_age;
  }

}
