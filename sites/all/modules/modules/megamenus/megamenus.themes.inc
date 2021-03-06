<?php

function template_preprocess_megamenus(&$vars) {
  $menu_name = $vars['menu_name'];
  if($vars['section'] == 'backend') {
    $items = megamenus_get_tree($menu_name);
  }
  else {
    $items = menu_tree_all_data($menu_name);
  }
  if (function_exists('i18n_menu_localize_tree')) {
    $items = i18n_menu_localize_tree($items);
  }

  $items_page = menu_tree_page_data($menu_name);
  $trail = megamenus_build_page_trail($items_page);
  $menu_config = megamenus_get_menu_config($menu_name, $items);
  $block_config = megamenus_get_block_config($menu_name);
  megamenus_edit_block_config($block_config);
  $vars['classes_array'][] = "megamenus-" . str_replace('_', '-', $menu_name);
  if($vars['section'] == 'frontend' && $block_config->style != '') {
    drupal_add_css(drupal_get_path("module", "megamenus") . "/stylesheets/presets/" . $block_config->style . ".css", array(
        'basename' => "megamenus-style-" . $block_config->style . ".css",
      ));
    $vars['classes_array'][] = "style-" . $block_config->style;
  }

  $vars['attributes_array']['data-name'] = $menu_name;
  $vars['attributes_array']['data-preset'] = "";
  $vars['attributes_array']['data-animation'] = "";
  $vars['attributes_array']['data-auto-arrow'] = isset($block_config->{'auto-arrow'}) ? $block_config->{'auto-arrow'} : '';
  $vars['attributes_array']['data-direction'] = isset($block_config->{'direction'}) ? $block_config->{'direction'} : '';
  $vars['attributes_array']['data-always-show-submenu'] = isset($block_config->{'always-show-submenu'}) ? $block_config->{'always-show-submenu'} : '';
  $vars['attributes_array']['id'] = "megamenus-" . str_replace('_', '-', $menu_name);
  if($block_config->animation != 'none') {
    $vars['classes_array'][] = "animate";
    $vars['classes_array'][] = isset($block_config->animation) ? $block_config->animation : '';
  }
  
  if(isset($block_config->{'auto-arrow'}) && $block_config->{'auto-arrow'}) {
    $vars['classes_array'][] = "auto-arrow";
  }

  if(isset($block_config->{'direction'}) && $block_config->{'direction'}) {
    $vars['classes_array'][] = $block_config->{'direction'};
  }

  if(isset($block_config->{'always-show-submenu'}) && $block_config->{'always-show-submenu'}) {
    $vars['classes_array'][] = "always-show-submenu";
  }

  $vars['content'] = theme('megamenus_ul', array(
    'menu_config' => $menu_config,
    'menu_items' =>$items,
    'trail' => $trail,
  ));

  $vars['block_config'] = $block_config;
  if($vars['section'] == 'frontend' && $block_config->animation != 'none') {
    $css = array();
    $css[] = '.megamenus.animate .mega > .mega-dropdown-menu, .megamenus.animate.slide .mega > .mega-dropdown-menu > div {';
    $css[] = '  transition-delay: ' . $block_config->delay . 'ms;';
    $css[] = '  -webkit-transition-delay: ' . $block_config->delay . 'ms;';
    $css[] = '  -ms-transition-delay: ' . $block_config->delay . 'ms;';
    $css[] = '  -o-transition-delay: ' . $block_config->delay . 'ms;';
    $css[] = '  transition-duration: ' . $block_config->duration . 'ms;';
    $css[] = '  -webkit-transition-duration: ' . $block_config->duration . 'ms;';
    $css[] = '  -ms-transition-duration: ' . $block_config->duration . 'ms;';
    $css[] = '  -o-transition-duration: ' . $block_config->duration . 'ms;';
    $css[] = '}';
    drupal_add_css(implode("\n", $css), array('type' => 'inline'));
  }
}

function template_preprocess_megamenus_ul(&$vars) {
  $menu_config = $vars['menu_config'];
  $menu_items = $vars['menu_items'];

  $level = 0;
  $lis = array();
  
  # Check exits menu login / Logout
  $past = array();
  foreach ($menu_items as $key => $value) {
    foreach ($menu_config as $item) {
      if ($value['link']['mlid'] == $item->config->mlid && !in_array($value['link']['mlid'], $past)) {
        $past[] = $value['link']['mlid'];
        $lis[] = theme('megamenus_li', array(
          'submenu' => $item->submenu,
          'menu_config' => $item->config,
          'menu_items' => $menu_items,
          'level' => $level + 1,
          'trail' => $vars['trail'],
        ));
      }
    }
  }

/*
  foreach ($menu_config as $item) {

    $lis[] = theme('megamenus_li', array(
      'submenu' => $item->submenu,
      'menu_config' => $item->config,
      'menu_items' => $menu_items,
      'level' => $level + 1,
      'trail' => $vars['trail'],
    ));
  }
  */

  $vars['lis'] = implode("\n", $lis);
  $vars['classes_array'][] = 'nav';
  $vars['classes_array'][] = 'navbar-nav';
  $vars['classes_array'][] = "level-" . $level;
  $vars['classes_array'][] = "items-" . count($lis);

}


function template_preprocess_megamenus_li(&$vars) {
  $submenu = $vars['submenu'];
  $menu_items = $vars['menu_items'];
  $menu_config = $vars['menu_config'];
  $vars['content'] = "";
  $level = $vars['level'];
  $trail = $vars['trail'];
  static $t = 1;
  $t ++;

  foreach ($menu_items as $item) {
    if ($item['link']['mlid'] == $menu_config->mlid && !$item['link']['hidden']) {
      $vars['menu_item'] = $item;
      if(!empty($submenu->rows_cols)) {
        $vars['sub_menu_icon'] = TRUE;
        $vars['content'] = theme('megamenus_submenu', array(
          'rows_cols' => (isset($submenu->rows_cols) ? $submenu->rows_cols : array()),
          'menu_config' => (isset($submenu->config) ? $submenu->config : (new stdClass())),
          'parent_config' => $menu_config,
          'menu_items' => $item,
          'level' => $level,
          'trail' => $vars['trail'],
        ));
      }
    }
  }


  if (isset($menu_config->caption)) {
    $vars['caption'] = $menu_config->caption;
  }
  
  $vars['icon'] = isset($menu_config->icon) ? $menu_config->icon : ($menu_config->icon = '');
  
  if (isset($menu_config->{'a-class'})) {
    $vars['a_class'] = $menu_config->{'a-class'};
  }
  
  $vars['classes_array'][] = 'level-' . $level;
  
  if (!empty($menu_config->{'align-submenu'})) {
    $vars['classes_array'][] = 'submenu-align-' . $menu_config->{'align-submenu'};
  }

  if (!empty($menu_config->hidewsub)) {
    $vars['classes_array'][] = 'sub-hidden-collapse';
  }
  
  if ($menu_config->group) {
    $vars['classes_array'][] = 'submenu-group';    
  }

  if($level == 1) {
    $vars['classes_array'][] = 'dropdown';
  }
  else {
    if (!empty($submenu->rows_cols)) {
      $vars['classes_array'][] = 'dropdown-submenu';
    }
  }
  
  if(isset($trail[$menu_config->mlid])) {
    $vars['classes_array'][] = 'active';
  }
  
  # add other attr
  $vars['attributes_array']['data-hidewsub'] = isset($menu_config->hidewsub) ? $menu_config->hidewsub : '';
  $vars['attributes_array']['data-mlid'] = isset($menu_config->mlid) ? $menu_config->mlid : '';
  $vars['attributes_array']['data-level'] = $level++;
  $vars['attributes_array']['data-align-submenu'] = isset($menu_config->{'align-submenu'}) ? $menu_config->{'align-submenu'} : '';
  $vars['attributes_array']['data-group'] = isset($menu_config->group) ? $menu_config->group : '';
}

function template_preprocess_megamenus_submenu(&$vars) {
  $menu_items = $vars['menu_items'];
  $rows_cols = $vars['rows_cols'];
  $menu_config = $vars['menu_config'];
  $parent_config = $vars['parent_config'];
  $level = $vars['level'];

  $rows = array();
  foreach ($rows_cols as $item) {
    $rows[] = theme('megamenus_row', array(
      'menu_config' => $item,
      'menu_items' => $menu_items,
      'level' => $level,
      'trail' => $vars['trail'],
    ));
  }

  $vars['rows'] = implode("\n", $rows);
  if(isset($menu_config->{'class'}) && $menu_config->{'class'} != "") {
    $vars['classes_array'][] = $menu_config->{'class'};
  }
  if($parent_config->group) {
    $vars['classes_array'][] = 'submenu-group-ct';
  }
  else {
    $vars['classes_array'][] = 'dropdown-menu';
  }

  $vars['width'] = isset($menu_config->width) ? $menu_config->width : '';


  if (isset($menu_config->{'class'})) {
    $vars['data_class'] = $menu_config->{'class'};
  }
  
}

function template_preprocess_megamenus_row(&$vars) {
  $row = $vars['menu_config'];
  $level = $vars['level'];
  $menu_items = $vars['menu_items'];
  $cols = array();
  foreach($row as $col) {
    $cols[] = theme('megamenus_col', array(
      'menu_config' => $col,      
      'menu_items' => $menu_items,
      'level' => $level,
      'trail' => $vars['trail'],
    ));
  }

  $vars['columns'] = implode("\n", $cols);
}

function template_preprocess_megamenus_col(&$vars) {
  $menu_items = $vars['menu_items'];
  $col_config = $vars['menu_config'];

  $level = $vars['level'];
  $result = array();

  if ($col_config->block == '') {
    $result[] = theme('megamenus_subul', array(
      'subcol' => $col_config,
      'menu_items' => $menu_items,
      'level' => $level,
      'trail' => $vars['trail'],
    ));
  }
  else {
    $result[] = theme('megamenus_block', array(
      'block_key' => $col_config->block->block,
      'showblocktitle' => isset($col_config->showblocktitle) ? $col_config->showblocktitle : ($col_config->showblocktitle = ''),
      'level' => $level,
    ));
  }

  $vars['tb_items'] = implode("\n", $result);

  $vars['classes_array'][] = 'col-md-' . (isset($col_config->config->grid) ? $col_config->config->grid : '');
  if(!empty($col_config->class)) {
    $vars['classes_array'][] = $col_config->class;
  }
  if(!empty($col_config->config->class)) {
    $vars['classes_array'][] = $col_config->config->class;
  }  
  if(!empty($col_config->config->hidewcol)) {
    $vars['classes_array'][] = 'hidden-collapse';
  }
  if(isset($col_config->config->{'show-block-title'}) && $col_config->config->{'show-block-title'}) {
    $vars['classes_array'][] = 'show-block-title';
  }

  $vars['attributes_array']['data-class'] = isset($col_config->class) ? $col_config->class : ($col_config->class = '');
  $vars['attributes_array']['data-grid'] = isset($col_config->config->grid) ? $col_config->config->grid : '';
  $vars['attributes_array']['data-hidewcol'] = isset($col_config->config->hidewcol) ? $col_config->config->hidewcol : ($col_config->config->hidewcol = '');
  $vars['attributes_array']['data-id'] = megamenus_get_counter("column");
  $vars['attributes_array']['id'] = megamenus_get_counter("column");
  $vars['attributes_array']['data-block'] = isset($col_config->block->block) ? ($col_config->block->block) : ($col_config->block = new StdClass());
  $vars['attributes_array']['data-show-block-title'] = isset($col_config->config->{'show-block-title'}) ? $col_config->config->{'show-block-title'} : ($col_config->config->{'show-block-title'} = '');
  $vars['attributes_array']['data-class'] = isset($col_config->config->class) ? $col_config->config->class : ($col_config->config->class = '');
}

function template_preprocess_megamenus_subul(&$vars) {
  $items = $vars['subcol']->ul;
  $level = $vars['level'];
  $menu_items = $vars['menu_items'];

  $lis = array();
  foreach ($items as $item) {
    foreach ($item as $key => $value) {
      if ($key == 'submenu') {
        $lis[] = theme('megamenus_li', array(
          'submenu' => $item->submenu,
          'flag' => TRUE,
          'menu_config' => $item->config,
          'menu_items' => $menu_items['below'],
          'level' => $level + 1,
          'li_active' => 0,
          'trail' => $vars['trail'],
        ));
      }
    }
  }

  $vars['lis'] = implode("\n", $lis);
  
  $vars['classes_array'][] = "level-" . $level;
  $vars['classes_array'][] = "items-" . count($items);
  $vars['theme_hook_suggestions'][] = 'megamenus-subul';
}

function template_preprocess_megamenus_block(&$vars) {
  $block = megamenus_load_block($vars['block_key']);
  
  
  if($block) {
    $module = $block->module;
    $delta = $block->delta;
    $content = module_invoke($module, 'block_view', $delta);

    /*
    if(isset($vars['showblocktitle']) && $vars['showblocktitle']) {
      $block->subject = empty($content['subject']) ? $block->title : $content['subject'];
    }*/

    if(empty($content['subject'])) {
      $block->subject = $block->title;
    } else {
      $block->subject = $content['subject'];
    }
    if(empty($content) && $vars['section'] == 'backend') {

      $vars['content'] = megamenus_get_block_title($vars['block_key']);
    }
    elseif($content) {
      $vars['content'] = theme("block", array('elements' => array('content' => $content['content'], '#children' => (is_array($content['content']) ? drupal_render($content['content']) : $content['content']), '#block' => $block)));
    }
    else {
      $vars['content'] = NULL;
    }
    $vars['classes_array'][] = "megamenus-block";
    $vars['attributes_array']['data-type'] = "block";
    $vars['attributes_array']['data-block'] = $vars['block_key'];
  }
  else {
    $vars['content'] = NULL;
  }
}

function template_preprocess_megamenus_backend(&$vars) {
  megamenus_add_css(drupal_get_path('module', 'megamenus') . '/bootstrap/css/bootstrap.css');
  megamenus_add_css(drupal_get_path('module', 'megamenus') . '/bootstrap/css/bootstrap-theme.css');
  megamenus_add_css(drupal_get_path('module', 'megamenus') . '/font-awesome/css/font-awesome.css');
  megamenus_add_css(drupal_get_path('module', 'megamenus') . '/stylesheets/base.css');
  megamenus_add_css(drupal_get_path('module', 'megamenus') . '/stylesheets/backend.css');  
  drupal_add_js(drupal_get_path('module', 'megamenus') . '/bootstrap/js/bootstrap.js');    
  $menu_name = $vars['menu_name'];
  $block_config = megamenus_get_block_config($menu_name);
  megamenus_edit_block_config($block_config);
  $menu_content = megamenus_render_block($menu_name);
  $vars['menu_content'] = $menu_content['content']['#markup'];
  $vars['block_config'] = $block_config;
  $vars['animation_options'] = megamenus_animation_options($block_config);
  $vars['direction_options'] = megamenus_direction_options($block_config);
  $vars['preset_options'] = megamenus_preset_options($block_config);
  $vars['edit_links'] = base_path() . "admin/structure/menu/manage/$menu_name";
  $vars['edit_menu'] = base_path() . "admin/structure/menu/manage/$menu_name/edit";
  $vars['block_options'] = megamenus_get_blocks_options();
}
