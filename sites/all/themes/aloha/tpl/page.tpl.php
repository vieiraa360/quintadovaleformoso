<?php
/**
 * @file
 * Aloha theme
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template normally located in the
 * modules/system directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 * - $hide_site_name: TRUE if the site name has been toggled off on the theme
 *   settings page. If hidden, the "element-invisible" class is added to make
 *   the site name visually hidden, but still accessible.
 * - $hide_site_slogan: TRUE if the site slogan has been toggled off on the
 *   theme settings page. If hidden, the "element-invisible" class is added to
 *   make the site slogan visually hidden, but still accessible.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['header']: Items for the header region.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see html.tpl.php
 */
?>


<div id="page" <?php print $page_css; ?>>
  <?php if(isset($page['show_skins_menu']) && $page['show_skins_menu']):?>
    <?php print $page['show_skins_menu'];?>
  <?php endif;?>

  <?php if($headline = render($page['headline'])): ?>
    <section id="headline" class="headline animate-on-scroll">
      <div class="container">
        <?php print $headline; ?>
      </div>
    </section>
  <?php endif;?>

  <header id="header" class="header section animate-on-scroll">
    <div class="container">
      <div class="navbar-header">
        <?php if ($logo): ?>
         <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
           <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
         </a>
       <?php endif; ?>

       <?php if ($site_name || $site_slogan): ?>
         <div id="name-and-slogan">
           <?php if ($site_name): ?>
             <?php if ($title): ?>
               <div id="site-name">
                <strong>
                  <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
                </strong>
              </div>
            <?php else: /* Use h1 when the content title is empty */ ?>
             <h1 id="site-name">
              <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"><span><?php print $site_name; ?></span></a>
            </h1>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($site_slogan): ?>
         <div id="site-slogan"><?php print $site_slogan; ?>
         </div>
       <?php endif; ?>
     </div>
   <?php endif; ?>
   <a id="menu-toggle" class="navbar-toggle" href="#menu-toggle">
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
  </a>
</div>
<?php print render($page['header']); ?>

<?php if($main_menu = render($page['main_menu'])): ?>

  <nav class="collapse navbar-collapse width" id="main-menu-inner">
    <div class="container-inner">
      <span class="btn-close"><i class="fa fa-times"></i></span>
      <?php print $main_menu; ?>
    </div>
  </nav>
<?php endif;?>
</div>
</header>

<?php if($slideshow = render($page['slideshow'])): ?>
  <section id="slideshow" class="slideshow section animate-on-scroll">
    <div class="container-fluid">
      <?php print $slideshow;?>
    </div>
  </section>
<?php endif;?>

<?php if ($booking = render($page['booking'])): ?>
  <section id="booking" class="section animate-on-scroll">
    <div class="container">
      <?php print $booking; ?>
    </div>
  </section>
<?php endif; ?>

<?php if ($messages): ?>
  <section id="messages" class="message section animate-on-scroll">
    <div class="container">
      <?php print $messages; ?>
    </div>
  </section>
<?php endif;?>

<?php if ($title && !$is_front): ?>
  <section id="title-wrapper" class="wrapper animate-on-scroll">
    <div class="container">
      <?php print render($title_prefix); ?>
      <h1 class="title" id="page-title"><span><?php print $title; ?></span></h1>
      <?php print render($title_suffix); ?>
    </div>
  </section>
<?php endif; ?>

<section id="main" class="main section">
  <div class="container">
    <div class="row">
      <div class="col-lg-<?php print $regions_width['content']?> col-md-<?php print $regions_width['content']?> col-sm-12 col-xs-12">
        <div id="content" class="column animate-on-scroll">
          <div class="section">
            <?php if ($page['highlighted']): ?><div id="highlighted"><?php print render($page['highlighted']); ?></div><?php endif; ?>
            <?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>
            <?php print render($page['help']); ?>
            <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
            <?php if ($title && $is_front): ?>
              <?php print render($title_prefix); ?>
              <h1 class="title" id="page-title"><span><?php print $title; ?></span></h1>
              <?php print render($title_suffix); ?>
            <?php endif; ?>
            <?php if ($content_action = render($page['content_action'])) print $content_action; ?>
            <?php print render($page['content']); ?>
          </div>
        </div>
      </div>
      <?php if ($regions_width['sidebar_first']): ?>
        <aside id="sidebar-first" class="sidebar col-lg-<?php print $regions_width['sidebar_first']?> col-md-<?php print $regions_width['sidebar_first']?> col-sm-12 col-xs-12 animate-on-scroll">
          <div class="section">
            <?php print render($page['sidebar_first']); ?>
          </div>
        </aside>
      <?php endif; ?>
      <?php if ($page['sidebar_second']): ?>
        <aside id="sidebar-second" class="sidebar col-lg-<?php print $regions_width['sidebar_second']?> col-md-<?php print $regions_width['sidebar_second']?> col-sm-12 col-xs-12 animate-on-scroll">
          <div class="section">
            <?php print render($page['sidebar_second']); ?>
          </div>
        </aside>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if($panel_first = render($page['panel_first'])): ?>
  <section id="panel-first" class="section bg-grey animate-on-scroll">
    <div class="container">
      <?php print $panel_first;?>
    </div>
  </section>
<?php endif; ?>

<?php if($panel_second = render($page['panel_second'])): ?>
  <section id="panel-second" class="section animate-on-scroll">
    <div class="container">
      <?php print $panel_second;?>
    </div>
  </section>
<?php endif; ?>

<?php if($panel_third = render($page['panel_third'])): ?>
  <section id="panel-third" class="section about-pro-brand animate-on-scroll">
    <div class="container">
      <?php print $panel_third;?>
    </div>
  </section>
<?php endif; ?>

<?php if($panel_fourth = render($page['panel_fourth'])): ?>
  <section id="panel-fourth" class="section animate-on-scroll">
    <div class="container">
      <?php print $panel_fourth;?>
    </div>
  </section>
<?php endif; ?>

<?php if($panel_fifth = render($page['panel_fifth'])): ?>
  <section id="panel-fifth" class="section bg-grey animate-on-scroll">
    <div class="container">
      <?php print $panel_fifth;?>
    </div>
  </section>
<?php endif; ?>

<?php if($gmap = render($page['gmap'])): ?>
  <section id="gmap" class="section">
    <?php print $gmap; ?>
  </section>
<?php endif; ?>

<?php if($panel_footer = render($page['panel_footer'])): ?>
  <section id="panel-footer" class="section bg-black animate-on-scroll">
    <div class="container">
      <?php print $panel_footer;?>
    </div>
  </section>
<?php endif; ?>

<?php if($footer = render($page['footer'])): ?>
  <footer id="footer" class="section bg-black animate-on-scroll">
    <div class="container">
      <?php print $footer; ?>
      <!--?php print $feed_icons; ?-->
    </div>
  </footer>
<?php endif;?>
<a title="<?php print t('Back to Top')?>" class="btn-btt" href="#Top"></a>
</div>
