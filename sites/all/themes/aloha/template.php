<?php
include_once(drupal_get_path('theme', 'aloha') . '/common.inc');

function aloha_theme() {
  $items = array();
  $items['render_panel'] = array(
    "variables" => array(
      'page' => array(),
      'panels_list' => array(),
      'panel_regions_width' => array(),
    ),
    'preprocess functions' => array(
      'aloha_preprocess_render_panel'
    ),
    'template' => 'panel',
    'path' => drupal_get_path('theme', 'aloha') . '/tpl',
  );
  return $items;
}

function aloha_process_html(&$vars) {
  $current_skin = theme_get_setting('skin');
  if (isset($_COOKIE['weebpal_skin'])) {
    $current_skin = $_COOKIE['weebpal_skin'];
  }
  if (!empty($current_skin) && $current_skin != 'default') {
    $vars['classes'] .= " skin-$current_skin";
  }

  $current_background = theme_get_setting('background');
  if (isset($_COOKIE['weebpal_background'])) {
    $current_background = $_COOKIE['weebpal_background'];
  }
  if (!empty($current_background)) {
    $vars['classes'] .= ' ' . $current_background;
  }
}

function aloha_preprocess_page(&$vars) {
  global $theme_key;

  $vars['page_css'] = '';

  $vars['regions_width'] = aloha_regions_width($vars['page']);
  $panel_regions = aloha_panel_regions();
  if (count($panel_regions)) {
    foreach ($panel_regions as $panel_name => $panels_list) {
      $panel_markup = theme("render_panel", array(
        'page' => $vars['page'],
        'panels_list' => $panels_list,
        'regions_width' => $vars['regions_width'],
      ));
      $panel_markup = trim($panel_markup);
      $vars['page'][$panel_name] = empty($panel_markup) ? FALSE : array('content' => array('#markup' => $panel_markup));
    }
  }

  $current_skin = theme_get_setting('skin');
  if (isset($_COOKIE['weebpal_skin'])) {
    $current_skin = $_COOKIE['weebpal_skin'];
  }

  $layout_width = (theme_get_setting('layout_width') == '')
                  ? theme_get_setting('layout_width_default')
                  : theme_get_setting('layout_width');
  $vars['page']['show_skins_menu'] = $show_skins_menu = theme_get_setting('show_skins_menu');

  if($show_skins_menu) {
    $current_layout = theme_get_setting('layout');
    if (isset($_COOKIE['weebpal_layout'])) {
      $current_layout = $_COOKIE['weebpal_layout'];
    }

    if ($current_layout == 'layout-boxed') {
      $vars['page_css'] = 'style="max-width:' . $layout_width . 'px;margin: 0 auto;" class="boxed"';
    }
    $data = array(
      'layout_width' => $layout_width,
      'current_layout' => $current_layout
    );
    $skins_menu = theme_render_template(drupal_get_path('theme', 'aloha') . '/tpl/skins-menu.tpl.php', $data);
    $vars['page']['show_skins_menu'] = $skins_menu;
  }

  $vars['page']['weebpal_skin_classes'] = !empty($current_skin) ? ($current_skin . "-skin") : "";
  if (!empty($current_skin) && $current_skin != 'default' && theme_get_setting("default_logo") && theme_get_setting("toggle_logo")) {
    $vars['logo'] = file_create_url(drupal_get_path('theme', $theme_key)) . "/css/colors/" . $current_skin . "/images/logo.png";
  }

  //////
  $skin = theme_get_setting('skin');
  if (isset($_COOKIE['weebpal_skin'])) {
    $skin = $_COOKIE['weebpal_skin'] == 'default' ? '' : $_COOKIE['weebpal_skin'];
  }
  if (!empty($skin) && file_exists(drupal_get_path('theme', $theme_key) . "/css/colors/" . $skin . "/style.css")) {
    $css = drupal_add_css(drupal_get_path('theme', $theme_key) . "/css/colors/" . $skin . "/style.css", array(
      'group' => CSS_THEME,
    ));
  }
}

function aloha_preprocess_render_panel(&$variables) {
  $page = $variables['page'];
  $panels_list = $variables['panels_list'];
  $regions_width = $variables['regions_width'];
  $variables = array();
  $variables['page'] = array();
  $variables['panel_width'] = $regions_width;
  $variables['panel_classes'] = array();
  $variables['panels_list'] = $panels_list;
  $is_empty = TRUE;
  $panel_keys = array_keys($panels_list);

  foreach ($panels_list as $panel) {
    $variables['page'][$panel] = $page[$panel];
    $panel_width = $regions_width[$panel];
    if (render($page[$panel])) {
      $is_empty = FALSE;
    }
    $classes = array("panel-column");
    $classes[] = "col-lg-$panel_width";
    $classes[] = "col-md-$panel_width";
    $classes[] = "col-sm-12";
    $classes[] = "col-xs-12";
    $classes[] = str_replace("_", "-", $panel);
    $variables['panel_classes'][$panel] = implode(" ", $classes);
  }
  $variables['empty_panel'] = $is_empty;
}

function aloha_css_alter(&$css) {
}

function aloha_preprocess_maintenance_page(&$vars) {
}

function aloha_preprocess_views_view_fields(&$vars) {
  $view = $vars['view'];
  foreach ($vars['fields'] as $id => $field) {
    if(isset($field->handler->field_info) && $field->handler->field_info['type'] === 'image') {
      $prefix = $field->wrapper_prefix;
      if(strpos($prefix, "views-field ") !== false) {
        $parts = explode("views-field ", $prefix);
        $type = str_replace("_", "-", $field->handler->field_info['type']);
        $prefix = implode("views-field views-field-type-" . $type . " ", $parts);
      }
      $vars['fields'][$id]->wrapper_prefix = $prefix;
    }
  }
}

function aloha_node_view_alter(&$build) {
  if ($build['#view_mode'] =='teaser'){
    if ($build['#bundle'] == 'product') {
      unset($build['links']['comment']);
    }
    elseif ($build['#bundle'] == 'article' && isset($build['links']['comment'])) {
      unset($build['links']['comment']);
    }
  }
}

/**
 * Get predefined param.
 */
function _get_predefined_param($param, $pre_array = array(), $suf_array = array()) {
  global $theme_key;
  $theme_data = list_themes();
  $result = isset($theme_data[$theme_key]->info[$param]) ? $theme_data[$theme_key]->info[$param] : array();
  return $pre_array + $result + $suf_array;
}

/**
 * Hook preprocess node.
 */
function aloha_preprocess_node(&$vars) {
  $skins = _get_predefined_param('skins', array('default' => t("Default skin")));
  foreach ($skins as $key => $val) {
    $url = str_replace(base_path(), '', $vars['node_url']);
    if (strpos($url, 'skins/' . $key) !== FALSE && (!isset($_COOKIE['weebpal_skin']) || $_COOKIE['weebpal_skin'] != $key)) {
      setcookie('weebpal_skin', $key, time() + 100000, base_path());
      //header('Location: ' . $vars['node_url']);
      drupal_goto("");
    }
  }

  $vars['aloha_media_field'] = false;
  foreach($vars['content'] as $key => $field) {
    if (isset($field['#field_type']) && isset($field['#weight'])) {
      if ($field['#field_type'] == 'image' || $field['#field_type'] == 'video_embed_field' || $field['#field_type'] == 'youtube') {
        $vars['aloha_media_field'] = drupal_render($field);
        $vars['classes_array'][] = 'real-estast-media-first';
        unset($vars['content'][$key]);
        break;
      }
    }
  }

  $node = $vars['node'];
  if (variable_get('node_submitted_' . $node->type, TRUE)) {
    $user    = t('<span><i class="fa fa-user"></i> !username</span>', array('!username' => $vars['name']));
    $created = t('<span><i class="fa fa-clock-o"></i>  !datetime</span>', array('!datetime' => date('F d, Y', $vars['created'])));
    $comment = t('<span><i class="fa fa-comment"></i> !comment Comments</span>', array('!comment' => $vars['comment_count']));
    $vars['submitted'] = $user . $created . $comment;
  }
}

/**
 * Override theme_form_element_label function
 *
 * @param $variables
 * @return string
 * @throws Exception
 */
function aloha_form_element_label($variables) {
    $element = $variables['element'];
    // This is also used in the installer, pre-database setup.
    $t = get_t();

    // If title and required marker are both empty, output no label.
    if ((!isset($element['#title']) || $element['#title'] === '') && empty($element['#required'])) {
        return '';
    }

    // If the element is required, a required marker is appended to the label.
    $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';

    $title = filter_xss_admin($element['#title']);

    $attributes = array();
    // Style the label as class option to display inline with the element.
    if ($element['#title_display'] == 'after') {
        $attributes['class'] = 'option';
    }
    // Show label only to screen readers to avoid disruption in visual flows.
    elseif ($element['#title_display'] == 'invisible') {
        $attributes['class'] = 'element-invisible';
    }

    // The leading whitespace helps visually separate fields from inline labels.
    return ' <label' . drupal_attributes($attributes) . '>' . $t('!title !required', array('!title' => $title, '!required' => $required)) . "</label>\n";
}

/**
 * Override theme_select function
 *
 * @param $variables
 * @return string
 */
function aloha_select($variables) {
    $element = $variables['element'];
    element_set_attributes($element, array('id', 'name', 'size'));
    _form_set_class($element, array('form-select'));

    return '<select' . drupal_attributes($element['#attributes']) . '>' . _form_select_options($element) . '</select>';
}

/**
 *
 * @param $element
 * @param null $choices
 * @return string
 */
function _form_select_options($element, $choices = NULL) {
    if (!isset($choices)) {
        $choices = $element['#options'];
    }
    // array_key_exists() accommodates the rare event where $element['#value'] is NULL.
    // isset() fails in this situation.
    $value_valid = isset($element['#value']) || array_key_exists('#value', $element);
    $value_is_array = $value_valid && is_array($element['#value']);
    $options = '';
    foreach ($choices as $key => $choice) {
        if (is_array($choice)) {
            $options .= '<optgroup label="' . $key . '">';
            $options .= form_select_options($element, $choice);
            $options .= '</optgroup>';
        }
        elseif (is_object($choice)) {
            $options .= form_select_options($element, $choice->option);
        }
        else {
            $key = (string) $key;
            if ($value_valid && (!$value_is_array && (string) $element['#value'] === $key || ($value_is_array && in_array($key, $element['#value'])))) {
                $selected = ' selected="selected"';
            }
            else {
                $selected = '';
            }
            $choice = check_plain($choice);
            if ($choice == '') $choice = 'Choose an option';
            $options .= '<option value="' . check_plain($key) . '"' . $selected . '>' . $choice . '</option>';
        }
    }
    return $options;
}

/**
 * Implements hook_element_info_alter
 */

function aloha_element_info_alter(&$info) {
  $info['select']['#pre_render'][] = 'aloha_render_select';
}

function aloha_render_select($element) {
  global $language;
  if($language->direction == 1 && in_array('chosen', $element['#attached']['library'][0])) {
    $element['#attributes']['class'][] = 'chosen-select';
    $element['#attributes']['class'][] = 'chosen-rtl';
  }
  return $element;
}