<?php
/**
 * Plugin Name:       Multi Gallery for Posts and cutom posts
 * Plugin URI:        https://atozlearnings.blogspot.com/
 * Description:       This plugin will enable more images for each post types. you can change settings from media->Multi Gallery
 * Version:           1.1.2
 * Requires at least: 5.2
 * Requires PHP:      5.2
 * Author:            Muthe Ashok
 */
if ( ! class_exists( 'mig_Img_uploader' ) ) {
class mig_Img_uploader {
  public function __construct() {
  }
 /*
  * Initialize the class and start calling our hooks and filters
  * @since 1.0.0
 */
 public function init() {
   add_action( 'add_meta_boxes', array($this, 'custom_post_multi_image_meta_box') );
   add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style') );
   add_action( 'save_post', array($this,'mig_uploadedImages'), 10, 2  );
   add_action( 'admin_menu', array($this, 'load_sub_menu') );
   add_action( 'wp_ajax_nopriv_mig_gallery_form_response', array($this,'mig_gallery_form_response' ) );
   add_action( 'wp_ajax_mig_gallery_form_response', array($this,'mig_gallery_form_response' ) );
   add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'mig_add_action_links') );
 }
  public function load_custom_wp_admin_style(){
   wp_enqueue_script('media_js', plugins_url('/js/media_query.js',__FILE__ ));
   $params = array ( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
  wp_localize_script( 'media_js', 'params', $params ); 
  wp_enqueue_style( 'custom_wp_admin_css', plugins_url('/styles/style.css', __FILE__) );
 }
function mig_add_action_links ( $links ) {
 $mylinks = array(
 '<a href="' . admin_url( 'upload.php?page=multi_galery' ) . '">Settings</a>',
 );
return array_merge( $links, $mylinks );
}
 public function load_sub_menu(){
     add_media_page( 'Multi Gallery', 'Multi Gallery', 'read', 'multi_galery', array($this, 'gallery_submenu_settings'));
 }
 public function gallery_submenu_settings(){
    ?>
    <div class="mig_Title"><h2>Multi Gallery Settings</h2></div>
    <div class="mig_content">
    <?php
      $args       = array(
          'public' => true,
      );
    $post_types = get_post_types( $args, 'objects' );
    $gallery_post_types = get_option('multi_gallery_for_posttypes', true );
    ?>
    <?php foreach ( $post_types as $post_type_obj ):
          if(is_array($gallery_post_types))
         $checked = in_array ( esc_attr( $post_type_obj->name ), $gallery_post_types ,true );
        $checked = ($checked == 1) ? 'checked' : '' ;
        $labels = get_post_type_labels( $post_type_obj );
        echo '<label><input type="checkbox" value="'.esc_attr( $post_type_obj->name ).'" name="posttypes[]" id="'.esc_attr( $post_type_obj->name ).'" '.$checked.' />'.esc_attr( $labels->name ).'</label><br />'; 
        ?>
      <?php endforeach; ?>
      <p class="submit">
        <input type="submit" id="submit" value="Save Settings" class="button button-primary"></p>
</div><div class="msg_success"></div>
<?php
 }
 function custom_post_multi_image_meta_box(){
      //on which post types should the box appear?
    $post_types = get_option('multi_gallery_for_posttypes', true );
    foreach($post_types as $pt){
        add_meta_box('custom_postimage_meta_box',__( 'More Featured Images', 'yourdomain'),array($this,'custom_postimage_meta_box_func'),$pt,'side','low');
    }
}
public function custom_postimage_meta_box_func($post){
  ?>

  <div class="gallery">
    <?php  $images = get_post_meta( $post->ID, 'multi_image_gallery', true ); 
            if($images){
            foreach ($images as $id) {
              ?>
               <div class="single_img"> <?php
              echo wp_get_attachment_image($id, array(75,75));
              ?>
               <a href="javascript:;" id="close" data-value="<?php echo $id; ?>"><span class="close">X</span> </a> </div> <?php
            }
          }
    ?>
  </div>
  
  <span class="dashicons dashicons-plus mig_upload_btn" id="btnImage">Add more images</span>
  <input type="hidden" name="img_ids" id="img_ids" value="<?php echo ($images) ? implode(',', $images) : ''; ?>">
  <?php
}
public function mig_uploadedImages($post_id, $post){
    $imggallery =  array_map('intval', explode(',',  $_POST['img_ids']));
   // print_r( $_POST['img_ids']);
   $imggallery = ($imggallery[0] == 0) ? '' : $imggallery;
     update_post_meta( $post_id, 'multi_image_gallery', $imggallery);
}
public function mig_gallery_form_response(){
   update_option('multi_gallery_for_posttypes', array_map('sanitize_text_field', $_POST['postTypes']));
}

}
$mig_Img_uploader = new mig_Img_uploader();
$mig_Img_uploader -> init();
}