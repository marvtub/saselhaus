<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
    }
endif;


add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

// Enque custom function Programm-Page // ****************************************
function maweb_hook_javascript() {
    if (is_page ('516')) { 
      ?>
          <script type="text/javascript">
//  PROGRAMM //////////////////////////////////////////////////////////////////


jQuery(document).ready(function(){
var position = document.querySelector(".va_section")
var template = document.querySelector(".va_section").parentElement.innerHTML



// Get JSON Objects
let json_obj = document.querySelector("#sf_json > code").innerText
json_obj = JSON.parse(json_obj);
var count = 0;
json_obj.forEach(el => {
     count++;
   //  Insert Section // Edit SalesForce Fields HERE
   console.log(count + "WHAT UP=?")
   position.insertAdjacentHTML('afterend', '<div id="temp' +count +'">'+populateTemp("#", el.Start__c, el.Type, el.Name, el.Beschreibung__c, el.Normaler_Preis__c, el.BildURL__c, template) + '</div>' )

  
});

//hide Template
position.classList.add("hidden")

});

// Functions
// Insert template
// Function to populate template
function populateTemp(img = "#", date, category, title, descr, price, link, template){

//Hilfsvariablen
let img_replace = 'https://sasel-haus.local/wp-content/uploads/2021/04/Img1Sasel-haus-min.jpg'
let temp = template;
let date_temp = new Date(date);
let dateTime = date_temp.toLocaleDateString() + ", " + date_temp.getHours() + ":" + date_temp.getMinutes()
let description = ""

if(descr != null){
description = truncate(descr, 120)

} 
//check if img is available 
if(link != null){
    temp = temp.replaceAll(img_replace, link)

}
temp = temp.replace("{{DATE}}", dateTime)
temp = temp.replace("{{CATEGORY}}", category)
temp = temp.replace("{{PRICE}}", 'ab ' + price)
temp = temp.replace("{{TITLE}}", title)
temp = temp.replace("{{DESCR}}", description)
temp = temp.replace("http://URL", link)


return temp
}

// Function to shorten Description
function truncate(str, n){
return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
};

// END Functions



// Get JSON Object PROGRAMM //////////////////////////////////////////////////////////////////          </script>
      <?php
    }
  }
  add_action('wp_head', 'maweb_hook_javascript');

  // END CUSTOM JS PROGRAMM-PAGE /////////////////