<?php

/**
 * @file
 * Provides tokens for entity properties which have no token yet.
 */

/**
 * Defines the types of properties to be added as token.
 *
 * @return
 *   An array mapping token types to the usual (entity) type names.
 */
function entity_token_types() {
  $return = entity_token_types_chained();
  return $return + drupal_map_assoc(array('text', 'integer', 'decimal', 'duration', 'boolean', 'uri'));
}

/**
 * Defines a list of token types that need to be chained.
 *
 * @return
 *   If a (token) type is given, whether the given type needs to be chained.
 *   Else a full list of token types to be chained as returned by
 *   entity_token_token_types().
 */
function entity_token_types_chained($type = NULL) {
  // This functions gets called rather often when replacing tokens, thus
  // we statically cache $types using the advanced drupal static pattern.
  static $drupal_static_fast;
  if (!isset($drupal_static_fast)) {
    $drupal_static_fast['types'] = &drupal_static(__FUNCTION__, array());
  }
  $types = &$drupal_static_fast['types'];

  if (!$types) {
    // Add entities.
    foreach (entity_get_info() as $entity_type => $info) {
      if ($token_type = isset($info['token type']) ? $info['token type'] : $entity_type) {
        $types[$token_type] = $entity_type;
      }
    }
    // Add 'date' and 'site' tokens.
    $types['date'] = 'date';
    $types['site'] = 'site';
    // Add a 'struct' type.
    $types['struct'] = 'struct';
  }

  if (isset($type)) {
    return isset($types[$type]) || entity_property_list_extract_type($type);
  }
  return $types;
}

/**
 * Gets the right token type for a given property info array.
 */
function _entity_token_map_to_token_type($property_info) {
  $lookup = &drupal_static(__FUNCTION__);

  if (!$lookup) {
    // Initialize a lookup array mapping property types to token types.
    $lookup = array_flip(entity_token_types());
  }

  $type = isset($property_info['type']) ? $property_info['type'] : 'text';
  // Just use the type 'struct' for all structures.
  if (!empty($property_info['property info'])) {
    $type = 'struct';
  }

  if ($item_type = entity_property_list_extract_type($type)) {
    return isset($lookup[$item_type]) ? "list<$lookup[$item_type]>" : FALSE;
  }
  return isset($lookup[$type]) ? $lookup[$type] : FALSE;
}

/**
 * Implements hook_token_info_alter().
 */
function entity_token_token_info_alter(&$info) {
  $entity_info = entity_get_info();
  $token_types = entity_token_types_chained();

  // Loop over all chain-able token types, as those may contain further tokens,
  // e.g. entity types or 'site'.
  foreach ($token_types as $token_type => $type) {
    // Just add all properties regardless whether it's in a bundle, but only if
    // there is no token of the property yet.
    foreach (entity_get_all_property_info($type) as $name => $property) {
      $name = str_replace('_', '-', $name);
      $property += array('type' => 'text', 'description' => $property['label']);
      $property_token_type = _entity_token_map_to_token_type($property);

      if (!isset($info['tokens'][$token_type][$name]) && $property_token_type) {

        $info['tokens'][$token_type][$name] = array(
          'name' => $property['label'],
          'description' => $property['description'],
          'type' => $property_token_type,
          // Mark the token so we know we have to provide the value afterwards.
          'entity-token' => TRUE,
        );
      }
      if ($property_token_type == 'struct' && !empty($property['property info'])) {
        $info['tokens'][$token_type][$name]['dynamic'] = TRUE;
        $help = array();
        foreach ($property['property info'] as $key => $property_info) {
          $help[] = $key . ' (' . $property_info['label'] . ')';
        }
        $info['tokens'][$token_type][$name]['description'] .= ' ' . t('The following properties may be appended to the token: @keys',
          array('@keys' => implode(', ', $help))
        );
      }
    }
  }

  // Make sure all chain-able token types we support are registered.
  foreach ($token_types as $token_type => $type) {

    if (!empty($info['tokens'][$token_type]) && !isset($info['types'][$token_type])) {
      if (isset($entity_info[$type])) {
        $info['types'][$token_type] = array(
          'name' => $entity_info[$type]['label'],
          'description' => t('Tokens related to the "@name" entities.', array('@name' => $entity_info[$type]['label'])),
          'needs-data' => $token_type,
        );
      }
      else {
        $info['types'][$token_type] = array(
          'name' => drupal_strtoupper($token_type),
          'description' => t('@name tokens.', array('@name' => drupal_strtoupper($token_type))),
          'needs-data' => $token_type,
        );
      }
    }
    if (!empty($info['tokens'][$token_type]) && !isset($info['types']["list<$token_type>"]) && $token_type != 'site') {
      if (isset($entity_info[$type])) {
        $info['types']["list<$token_type>"] = array(
          'name' => t('List of @entities', array('@entities' => isset($entity_info[$type]['plural label']) ? $entity_info[$type]['plural label'] : $entity_info[$type]['label'] . 's')),
          'description' => t('Tokens related to the "@name" entities.', array('@name' => $entity_info[$type]['label'])),
          'needs-data' => "list<$token_type>",
        );
      }
      else {
        $info['types']["list<$token_type>"] = array(
          'name' => t('List of @type values', array('@type' => $token_type)),
          'description' => t('Tokens for lists of @t