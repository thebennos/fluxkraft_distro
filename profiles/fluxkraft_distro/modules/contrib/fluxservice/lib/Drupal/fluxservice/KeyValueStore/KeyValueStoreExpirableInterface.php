<?php

/**
 * @file
 * Contains KeyValueStoreExpirableInterface.
 */

namespace Drupal\fluxservice\KeyValueStore;

/**
 * Defines the interface for expiring data in a key/value store.
 */
interface KeyValueStoreExpirableInterface extends KeyValueStoreInterface {

  /**
   * Saves a value for a given key with a time to live.
   *
   * @param string $key
   *   The key of the data to store.
   * @param mixed $value
   *   The data to store.
   * @param int $expire
   *   The time to live for items, in seconds.
   */
  function setWithExpire($key, $value, $expire);

  /**
   * Sets a value for a given key with a time to live if it does not yet exist.
   *
   * @param string $key
   *   The key of the data to store.
   * @param mixed $value
   *   The data to store.
   * @param int $expire
   *   The time to live for items, in seconds.
   *
   * @return bool
   *   TRUE if the data was set, or FALSE if it already existed.
   */
  function setWithExpireIfNotExists($key, $value, $expire);

  /**
   * Saves an array of values with a time to live.
   *
   * @param array $data
   *   An array of data to store.
   * @param int $expire
   *   The time to live for items, in seconds.
   */
  function setMultipleWithExpire(array $data, $expire);

}
