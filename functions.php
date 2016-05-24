<?php
/*
Author: Eddie Machado
URL: http://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, ect.
*/

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'library/bones.php' );

// LOAD WooCommerce custom functions (if you remove this, the theme will break)
require_once( 'library/woocommerce-functions.php' );

// CUSTOMIZE THE WORDPRESS ADMIN
require_once( 'library/admin.php' );

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function bones_ahoy() {

  //Allow editor style.
  add_editor_style();

  // let's get language support going, if you need it
  load_theme_textdomain( 'bonestheme', get_template_directory() . '/library/translation' );

  // Load the Team Profiles custom post type
  require_once( 'library/testimonials-post-type.php' );

  // USE THIS TEMPLATE TO CREATE CUSTOM POST TYPES EASILY
  // require_once( 'library/custom-post-type.php' );

  // launching operation cleanup
  add_action( 'init', 'bones_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'bones_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'bones_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  bones_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'bones_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'bones_filter_ptags_on_images' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'bones_excerpt_more' );

} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );


/************* Create a global Options page using Advanced Custom Fields plugin *************/

if( function_exists('acf_add_options_page') ) {

  acf_add_options_page(array(
    'page_title'  => 'Global Settings',
    'menu_title'  => 'Global Settings',
    'menu_slug'   => 'global-settings',
    'capability'  => 'edit_posts',
    'redirect'    => false
  ));
}

/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
  $content_width = 640;
}

/************* ENVIRONMENT CONFIG OPTIONS *************/

// Check if the current environment is production or not
function is_production(){
  if(ENVIRONMENT == 'production'){
    return true;
  }else{
    return false;
  }
}

// Block search robots from the development, preview and staging sites
function robots_access(){
  if(is_production() && get_option( 'blog_public' ) == '0') update_option( 'blog_public', '1' );
  if(!is_production() && get_option( 'blog_public' ) == '1') update_option( 'blog_public', '0' );
}

add_action( 'init', 'robots_access' );

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'safe2choose-thumb-700', 700, 700, true );
add_image_size( 'safe2choose-thumb-700-500', 700, 500, true );
add_image_size( 'safe2choose-thumb-300-200', 300, 200, true );
add_image_size( 'safe2choose-thumb-300', 300, 100, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 100 sized image,
we would use the function:
<?php the_post_thumbnail( 'safe2choose-thumb-300' ); ?>
for the 600 x 150 image:
<?php the_post_thumbnail( 'safe2choose-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'safe2choose_custom_image_sizes' );

function safe2choose_custom_image_sizes( $sizes ) {
  return array_merge( $sizes, array(
    'safe2choose-thumb-700' => __('700px by 700px'),
    'safe2choose-thumb-700-500' => __('700px by 500px'),
    'safe2choose-thumb-300-200' => __('300px by 200px'),
    'safe2choose-thumb-300' => __('300px by 100px'),
  ) );
}

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

/************* THEME CUSTOMIZE *********************/

/*
  A good tutorial for creating your own Sections, Controls and Settings:
  http://code.tutsplus.com/series/a-guide-to-the-wordpress-theme-customizer--wp-33722

  Good articles on modifying the default options:
  http://natko.com/changing-default-wordpress-theme-customization-api-sections/
  http://code.tutsplus.com/tutorials/digging-into-the-theme-customizer-components--wp-27162

  To do:
  - Create a js for the postmessage transport method
  - Create some sanitize functions to sanitize inputs
  - Create some boilerplate Sections, Controls and Settings
*/
function bones_theme_customizer($wp_customize) {
  // $wp_customize calls go here.
  //
  // Uncomment the below lines to remove the default customize sections
  // $wp_customize->remove_section('title_tagline');
  // $wp_customize->remove_section('colors');
  // $wp_customize->remove_section('background_image');
  // $wp_customize->remove_section('static_front_page');
  // $wp_customize->remove_section('nav');
  // Uncomment the below lines to remove the default controls
  // $wp_customize->remove_control('blogdescription');

  // Uncomment the following to change the default section titles
  // $wp_customize->get_section('colors')->title = __( 'Theme Colors' );
  // $wp_customize->get_section('background_image')->title = __( 'Images' );
}

add_action( 'customize_register', 'bones_theme_customizer' );

/************* ACTIVE PRODUCTS ********************/

// Force Woocommerce product to be published
// wp_update_post( array(
//    'ID' => 220,
//    'post_status' => 'publish',
// ) );


add_filter('gform_register_init_scripts', 'gform_display_weeks', 10, 2);
function gform_display_weeks($form) {

$script = <<<EOT

  (function($){

var vals = {
  en: {
    fields: {
      insertTenWeekReminderAfter: "#gform_page_4_6 .gform_page_fields",
      lmpWeeksLabel: "#field_4_59 > .gfield_label",
      dateSelect: "#input_4_19",
      weeksRadio: "#field_4_59",
      tenWeekWarning: "#field_4_60",
      weeksRadio5: "#choice_4_59_0",
      weeksRadio6: "#choice_4_59_1",
      weeksRadio7: "#choice_4_59_2",
      weeksRadio8: "#choice_4_59_3",
      weeksRadio9: "#choice_4_59_4",
      weeksRadio10: "#choice_4_59_5",
      nextButton: "#gform_next_button_4_21",
      nineWeekWarning: "#field_4_55",
      nineWeekAccept: "#input_4_55",
      nineWeekAcceptYes: "#choice_4_55_1"
    },
    text: {
      weeks: "weeks(s)",
      days: "day(s)",
      and: "and",
      estimation1: "Based on your response, we understand you are ",
      estimation2: " pregnant. Please confirm below.",
      tenWeekReminder: "Remember! Using abortion pills at home is safer within the first 10 weeks of pregnancy."
    }
  },
  es: {
    fields: {
      insertTenWeekReminderAfter: "#gform_page_10_6 .gform_page_fields",
      lmpWeeksLabel: "#field_10_59 > .gfield_label",
      dateSelect: "#input_10_19",
      weeksRadio: "#field_10_59",
      tenWeekWarning: "#field_10_60",
      weeksRadio5: "#choice_10_59_0",
      weeksRadio6: "#choice_10_59_1",
      weeksRadio7: "#choice_10_59_2",
      weeksRadio8: "#choice_10_59_3",
      weeksRadio9: "#choice_10_59_4",
      weeksRadio10: "#choice_10_59_5",
      nextButton: "#gform_next_button_10_21",
      nineWeekWarning: "#field_10_55",
      nineWeekAccept: "#input_10_55",
      nineWeekAcceptYes: "#choice_10_55_1"
    },
    text: {
      weeks: "semana(s)",
      days: "día(s)",
      and: "y",
      estimation1: "Con base en tu respuesta, entendemos que tu embarazo es de ",
      estimation2: ". Por favor, confirma abajo.",
      tenWeekReminder: "¡Recuerda! Usar pastillas abortivas en casa es más seguro durante las 10 primeras semanas de embarazo."
    }
  },
  pl: {
    fields: {
      insertTenWeekReminderAfter: "#gform_page_13_6 .gform_page_fields",
      lmpWeeksLabel: "#field_13_60 > .gfield_label",
      dateSelect: "#input_13_19",
      weeksRadio: "#field_13_60",
      tenWeekWarning: "#field_13_61",
      weeksRadio5: "#choice_13_60_0",
      weeksRadio6: "#choice_13_60_1",
      weeksRadio7: "#choice_13_60_2",
      weeksRadio8: "#choice_13_60_3",
      weeksRadio9: "#choice_13_60_4",
      weeksRadio10: "#choice_13_60_5",
      nextButton: "#gform_next_button_13_21",
      nineWeekWarning: "#field_13_55",
      nineWeekAccept: "#input_13_55",
      nineWeekAcceptYes: "#choice_13_55_1"
    },
    text: {
      weeks: "tygodniu",
      days: "dniu",
      and: "i",
      estimation1: "Według Twojej odpowiedzi rozumiemy, że jesteś ",
      estimation2: ". Proszę potwierdzić, poniżej.",
      tenWeekReminder: "Pamiętaj! Używanie pigułki aborcyjnej w domu jest bezpieczniejsze w ciągu pierwszych 10 tygodniach ciąży."
    }
  },
   fr: {
    fields: {
      insertTenWeekReminderAfter: "#gform_page_22_6 .gform_page_fields",
      lmpWeeksLabel: "#field_22_59 > .gfield_label",
      dateSelect: "#input_22_19",
      weeksRadio: "#field_22_59",
      tenWeekWarning: "#field_22_60",
      weeksRadio5: "#choice_22_59_0",
      weeksRadio6: "#choice_22_59_1",
      weeksRadio7: "#choice_22_59_2",
      weeksRadio8: "#choice_22_59_3",
      weeksRadio9: "#choice_22_59_4",
      weeksRadio10: "#choice_22_59_5",
      nextButton: "#gform_next_button_22_21",
      nineWeekWarning: "#field_22_55",
      nineWeekAccept: "#input_22_55",
      nineWeekAcceptYes: "#choice_22_55_1"
    },
    text: {
      weeks: "semaine(s)",
      days: "jour(s)",
      and: "et",
      estimation1: "D'après votre réponse, nous comprenons que vous êtes enceinte de ",
      estimation2: " . Merci de confirmer ci-dessous.",
      tenWeekReminder: "N'oubliez pas ! L’avortement à domicile à l’aide de la pilule abortive est sans risque pendant les 10 premières semaines de grossesse."
    }
  },
  pt: {
    fields: {
      insertTenWeekReminderAfter: "#gform_page_23_6 .gform_page_fields",
      lmpWeeksLabel: "#field_23_59 > .gfield_label",
      dateSelect: "#input_23_19",
      weeksRadio: "#field_23_59",
      tenWeekWarning: "#field_23_60",
      weeksRadio5: "#choice_23_59_0",
      weeksRadio6: "#choice_23_59_1",
      weeksRadio7: "#choice_23_59_2",
      weeksRadio8: "#choice_23_59_3",
      weeksRadio9: "#choice_23_59_4",
      weeksRadio10: "#choice_23_59_5",
      nextButton: "#gform_next_button_23_21",
      nineWeekWarning: "#field_23_55",
      nineWeekAccept: "#input_23_55",
      nineWeekAcceptYes: "#choice_23_55_1"
    },
    text: {
      weeks: "semana (s)",
      days: "dia (s)",
      and: "e",
      estimation1: "Com base na sua resposta, entendemos que você está grávida de ",
      estimation2: " . Por favor, confirme abaixo.",
      tenWeekReminder: "Aviso! O uso das pílulas abortivas em casa é um procedimento seguro dentro das primeiras 10 semanas de gravidez."
    }
  },
  hi: {
    fields: {
      insertTenWeekReminderAfter: "#gform_page_24_6 .gform_page_fields",
      lmpWeeksLabel: "#field_24_59 > .gfield_label",
      dateSelect: "#input_24_19",
      weeksRadio: "#field_24_59",
      tenWeekWarning: "#field_24_60",
      weeksRadio5: "#choice_24_59_0",
      weeksRadio6: "#choice_24_59_1",
      weeksRadio7: "#choice_24_59_2",
      weeksRadio8: "#choice_24_59_3",
      weeksRadio9: "#choice_24_59_4",
      weeksRadio10: "#choice_24_59_5",
      nextButton: "#gform_next_button_24_21",
      nineWeekWarning: "#field_24_55",
      nineWeekAccept: "#input_24_55",
      nineWeekAcceptYes: "#choice_24_55_1"
    },
    text: {
      weeks: "सप्ताह",
      days: "दिन",
      and: "और",
      estimation1: "आपकी प्रतिक्रिया के आधार पर हम समझते है आपको ",
      estimation2: " का गर्भ है। कृपया नीचे पुष्टि करें।",
      tenWeekReminder: "ध्यान रहे! घर पर गर्भपात गोलियों का उपयोग गर्भावस्था के पहले 10 सप्ताह के भीतर सुरक्षित है।"
    }
  }
};

var getTotalDaysFrom = function(then) {
  var now = new Date();

  var diff = Math.floor(now.getTime() - then.getTime());
  var aDay = 1000 * 60 * 60 * 24;
  return Math.floor(diff/aDay);
}

var getSelectedDate = function(context) {
  var thenArray = $(context).val().split("/");
  return new Date(thenArray[2], thenArray[1]-1, thenArray[0]);
}

var getWeeks = function(totalDays) {
  return Math.floor(totalDays/7);
}

var getDays = function(totalDays) {
  return Math.floor(totalDays - getWeeks(totalDays) * 7);
}

var getWeekText = function(weeks, translatedWeek) {
  return (weeks > 0) ? (weeks + " " + translatedWeek) : "";
}

var getDayText = function(days, translatedDay) {
  return (days > 0) ? (days + " " + translatedDay) : "";
}

var getAndText = function(days, weeks, translatedAnd) {
  return (weeks > 0 && days > 0) ? (" " + translatedAnd + " ") : "";
}

var getEstimatedLMPText = function(totalDays, translatedWeek, translatedDay, translatedAnd) {
    var weeks = getWeeks(totalDays);
    var days = getDays(totalDays);

    var weekText = getWeekText(weeks, translatedWeek);
    var dayText = getDayText(days, translatedDay);
    var andText = getAndText(days, weeks, translatedAnd);
    return weekText + andText + dayText;
}

var setTenWeekReminder = function(reminderText, insertAfterSelector) {
  var divId = "tenWeekReminder";
  var replaceWithText = "<div id=\"" + divId + "\"><h5>" + reminderText + "</h5></div>";
  if($('#' + divId).length) {
    $('#' + divId).replaceWith(replaceWithText);
  } else {
    $( replaceWithText ).insertAfter(insertAfterSelector);
  }
};

var hideTenWeekReminder = function() {
  $("#tenWeekReminder").hide();
}

var updateWeeksLabel = function(newText, labelSelector) {
  $(labelSelector).text(newText);
};

var handleDateChange = function(language, totalDays) {
  var v = vals[language];
  var vt = v["text"];
  var vf = v["fields"];

  if(totalDays > 0) {
    var estimatedLMPText = getEstimatedLMPText(totalDays, vt["weeks"], vt["days"], vt["and"]);
    estimatedLMPText = vt["estimation1"] + estimatedLMPText + vt["estimation2"];
    updateWeeksLabel(estimatedLMPText, vf["lmpWeeksLabel"]);

    $(vf['weeksRadio']).show();

    if(getWeeks(totalDays) <= 8) {
      setTenWeekReminder(vt["tenWeekReminder"], vf["insertTenWeekReminderAfter"]);
      $(vf['nextButton']).show();
    } else {
      hideTenWeekReminder();
      $(vf['nextButton']).hide();
    }

    if(getWeeks(totalDays) <= 9) {
      $(vf['tenWeekWarning']).hide();
    }

    $(vf["nineWeekWarning"]).hide();

    if(getWeeks(totalDays) <= 5) {
      $(vf["weeksRadio5"]).prop("checked", true);

    } else if(getWeeks(totalDays) <= 6) {
      $(vf["weeksRadio6"]).prop("checked", true);

    } else if(getWeeks(totalDays) <= 7) {
      $(vf["weeksRadio7"]).prop("checked", true);

    } else if(getWeeks(totalDays) <= 8) {
      $(vf["weeksRadio8"]).prop("checked", true);

    } else if(getWeeks(totalDays) <= 9) {
      $(vf["weeksRadio9"]).prop("checked", true);

      $(vf["nineWeekWarning"]).show();
    } else {
      $(vf["weeksRadio10"]).prop("checked", true);

      $(vf["tenWeekWarning"]).show();
    }
  }
}

$.each(vals, function(language, v) {
  var vf = v['fields'];

  $(vf['dateSelect']).change(function () {
    var then = getSelectedDate(this);
    var totalDays = getTotalDaysFrom(then);
    handleDateChange(language, totalDays);
  });

  $(vf['weeksRadio']).hide();

  $(vf['nineWeekAccept']).change(function() {
    var nineWeekConfirm = $(vf['nineWeekAcceptYes']).prop("checked");
    if (nineWeekConfirm) {
      $(vf['nextButton']).show();
    } else {
      $(vf['nextButton']).hide();
    }
  });


  $(vf['weeksRadio']).change(function () {
    var fiveClicked =  $(vf['weeksRadio5']).prop('checked');
    var sixClicked = $(vf['weeksRadio6']).prop('checked');
    var sevenClicked = $(vf['weeksRadio7']).prop('checked');
    var eightClicked = $(vf['weeksRadio8']).prop('checked');
    var nineClicked = $(vf['weeksRadio9']).prop('checked');
    var tenClicked = $(vf['weeksRadio10']).prop('checked');

    var totalDays = 7;

    if(fiveClicked) totalDays = totalDays * 5;
    else if (sixClicked) totalDays = totalDays * 6;
    else if (sevenClicked) totalDays = totalDays * 7;
    else if (eightClicked) totalDays = totalDays * 8;
    else if (nineClicked) totalDays = totalDays * 9;
    else totalDays = totalDays * 10;

    handleDateChange(language, totalDays);
  });
});



  })(jQuery);

EOT;

  GFFormDisplay::add_init_script($form['id'], 'gform_display_weeks', GFFormDisplay::ON_PAGE_RENDER, $script);

}


/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function bones_register_sidebars() {
  register_sidebar(array(
    'id' => 'sidebar1',
    'name' => __( 'Sidebar 1', 'bonestheme' ),
    'description' => __( 'The first (primary) sidebar.', 'bonestheme' ),
    'before_widget' => '<div id="%1$s" class="module module--primary cf %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="h3 module__title">',
    'after_title' => '</h2>',
  ));

  register_sidebar(array(
    'id' => 'sidebar2',
    'name' => __( 'Sidebar 2', 'bonestheme' ),
    'description' => __( 'The second (secondary) sidebar.', 'bonestheme' ),
    'before_widget' => '<div id="%1$s" class="module module--primary cf %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="h3 module__title">',
    'after_title' => '</h2>',
  ));

  register_sidebar(array(
    'id' => 'sidebar3',
    'name' => __( 'Sidebar 3', 'bonestheme' ),
    'description' => __( 'The third sidebar.', 'bonestheme' ),
    'before_widget' => '<div id="%1$s" class="module module--primary cf %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="h3 module__title">',
    'after_title' => '</h2>',
  ));

  register_sidebar(array(
    'id' => 'sidebar4',
    'name' => __( 'Sidebar 4', 'bonestheme' ),
    'description' => __( 'The fourth sidebar.', 'bonestheme' ),
    'before_widget' => '<div id="%1$s" class="module module--primary cf %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="h3 module__title">',
    'after_title' => '</h2>',
  ));

  register_sidebar(array(
    'id' => 'sidebar-social',
    'name' => __( 'Social Sidebar', 'bonestheme' ),
    'description' => __( 'The social sidebar.', 'bonestheme' ),
    'before_widget' => '<div id="%1$s" class="module module--primary cf %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="h3 module__title">',
    'after_title' => '</h2>',
  ));

  // register_sidebar(array(
  //  'id' => 'testimonials-widget',
  //  'name' => __( 'Testimonials Widget', 'bonestheme' ),
  //  'description' => __( 'The testimonials widget.', 'bonestheme' ),
  //  'before_widget' => '<div id="%1$s" class="module module--primary cf %2$s">',
  //  'after_widget' => '</div>',
  //  'before_title' => '<h2 class="h3 module__title">',
  //  'after_title' => '</h2>',
  // ));

  /*
  to add more sidebars or widgetized areas, just copy
  and edit the above sidebar code. In order to call
  your new sidebar just use the following code:

  Just change the name to whatever your new
  sidebar's id is, for example:

  register_sidebar(array(
    'id' => 'sidebar2',
    'name' => __( 'Sidebar 2', 'bonestheme' ),
    'description' => __( 'The second (secondary) sidebar.', 'bonestheme' ),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h4 class="widgettitle">',
    'after_title' => '</h4>',
  ));

  To call the sidebar in your template, you can just copy
  the sidebar.php file and rename it to your sidebar's name.
  So using the above example, it would be:
  sidebar-sidebar2.php

  */
} // don't remove this bracket!


/************* COMMENT LAYOUT *********************/

// Comment Layout
function bones_comments( $comment, $args, $depth ) {
   $GLOBALS['comment'] = $comment; ?>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
    <article  class="cf">
      <header class="comment-author vcard">
        <?php
        /*
          this is the new responsive optimized comment image. It used the new HTML5 data-attribute to display comment gravatars on larger screens only. What this means is that on larger posts, mobile sites don't have a ton of requests for comment images. This makes load time incredibly fast! If you'd like to change it back, just replace it with the regular wordpress gravatar call:
          echo get_avatar($comment,$size='32',$default='<path_to_url>' );
        */
        ?>
        <?php // custom gravatar call ?>
        <?php
          // create variable
          $bgauthemail = get_comment_author_email();
        ?>
        <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>?s=40" class="load-gravatar avatar avatar-48 photo" height="40" width="40" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />
        <?php // end custom gravatar call ?>
        <?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'bonestheme' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'bonestheme' ),'  ','') ) ?>
        <time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__( 'F jS, Y', 'bonestheme' )); ?> </a></time>

      </header>
      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert alert-info">
          <p><?php _e( 'Your comment is awaiting moderation.', 'bonestheme' ) ?></p>
        </div>
      <?php endif; ?>
      <section class="comment_content cf">
        <?php comment_text() ?>
      </section>
      <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </article>
  <?php // </li> is added by WordPress automatically ?>
<?php
} // don't remove this bracket!

/*CGH change currency symbol for Mexican pesos*/
add_filter('woocommerce_currency_symbol', 'change_existing_currency_symbol', 10, 2);
 
function change_existing_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'MXN': $currency_symbol = 'MXN $'; break;
     }
     return $currency_symbol;
}

/*CGH add input field type checkbox to the checkout form*/
/**
 * Add the field to the checkout
 */
add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );

function my_custom_checkout_field( $checkout ) {

    echo '<div id="permisson_call">';

    woocommerce_form_field( 'permisson_call', array(
        'type'          => 'checkbox',
        'value'=>'Yes',
        'class'         => array('permisson-class form-row-wide'),
        'label'         => __('I give permission for safe2choose to contact me with important health information and inqueries related to my order.')
        ), $checkout->get_value( 'permisson_call' ));

    echo '</div>';

}
/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['permisson_call'] ) ) {
        update_post_meta( $order_id, 'Permisson_call', sanitize_text_field( 'Yes' ) );
    }
}
/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>Permisson to calls:</strong> ' . get_post_meta( $order->id, 'Permisson_call', true ) . '</p>';
}

/*CGH Conversion tracking code*/
/**
 * Prints the Google Analytics tracking code in the Thank you page.
 * @return void
 */
function wc_ga_conversion_tracking() {
    if ( is_order_received_page() ) {
        ?>
          <!-- Google Code for safe2choose conversiones Conversion Page -->
          <script type="text/javascript">
          /* <![CDATA[ */
          var google_conversion_id = 925000065;
          var google_conversion_language = "en";
          var google_conversion_format = "3";
          var google_conversion_color = "ffffff";
          var google_conversion_label = "hbv7CI-MymUQgcOJuQM";
          var google_remarketing_only = false;
          /* ]]> */
          </script>
          <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
          </script>
          <noscript>
          <div style="display:inline;">
          <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/925000065/?label=hbv7CI-MymUQgcOJuQM&amp;guid=ON&amp;script=0"/>
          </div>
          </noscript>
        <?php
    }
}
add_action( 'wp_footer', 'wc_ga_conversion_tracking' );
/* DON'T DELETE THIS CLOSING TAG */ ?>
