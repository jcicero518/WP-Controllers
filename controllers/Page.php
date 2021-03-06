<?php

/**
 * Class Page
 */
class Page extends Post {

  /**
   * @var string $post_type
   */
  public static
    $controller_post_type = 'page';

  /**
   * @var PostController $_parent
   * @var PostController[] $_children
   */
  protected
    $_parent,
    $_children;

  /**
   * Returns controllers for an array of posts or get_pages arguments
   * @since 0.7.0
   * @param   array $args     Either an array of WP_Post objects or get_pages arguments
   * @param   array $options  Options for the controllers
   * @return  array
   */
  public static function get_controllers($args = null, $options = array()) {
    if ( empty($args) || isset($args[0]) ) {
      return parent::get_controllers($args);
    }

    $pages = get_pages($args);
    return self::get_controllers($pages, $options);
  }

  /**
   * Returns pages children, if any
   * @return array
   */
  public function children() {
    if ( $this->_children ) return $this->_children;

    $this->_children = self::get_controllers(array(
      'hierarchical'  => false,
      'parent'        => $this->id,
      'post_status'        => 'publish,private'
    ));

    return $this->_children;
  }

  /**
   * Returns page parent controller, if any
   * @return object|null
   */
  public function parent() {
    if ( $this->_parent ) return $this->_parent;
    if ( !$this->parent_id ) return null;

    return $this->_parent = self::get_controller($this->parent_id);
  }

  /**
   * Returns pages of specified template(s)
   * @param array|string $templates
   * @param array $options (optional)
   * @return array
   */
  public static function get_page_templates($templates, $options = array()) {
    $options = array_merge_recursive($options, array(
      'post_type'   => 'page',
      'numberposts' => -1,
      'meta_query'  => array(
        array(
          'key'       => '_wp_page_template',
          'value'     => $templates
        )
      )
    ));

    return parent::get_controllers($options);
  }

  /**
   * Returns parent controller for post in loop
   * @return object
   */
  public static function get_parent() {
    global $post;
    return ( $post->post_parent ) ? self::get_controller($post->post_parent) : null;
  }

}
