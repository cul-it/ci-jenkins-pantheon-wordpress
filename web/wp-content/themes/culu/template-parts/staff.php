<?php
  $first_name = get_field('first_name');
  $last_name = get_field('last_name'); ?>

<section class="staff-profile" aria-label="<?php echo $first_name ?> <?php echo $last_name ?> staff profile" >

  <?php

  $image = get_field('photo');

  if( !empty($image) ) { ?>

    <img class="staff-photo" src="<?php echo $image['url'];?>" alt="<?php echo $image['alt']; ?>">

  <?php } else { ?>

    <img class="staff-photo" src="<?php echo get_template_directory_uri(); ?>/images/staff/no-photo-profile.png" alt="">

  <?php } ?>

  <h3>
    <?php
      $degree = get_field('degree');
      echo $first_name . ' ' . $last_name, ( $degree ? ', ' . $degree : '' ) . '</span>';
    ?>
  </h3>

  <h4>
    <?php
    $title = get_field('title');
    $staff_type = get_field('staff_type');
    echo $title , ( $staff_type ? ', ' . $staff_type : '' );
    ?>
  </h4>

  <p class="staff-email"><a href="mailto:<?php echo the_field('email');?>"><?php echo the_field('email');?></a></p>

  <?php if ( !empty(get_field('phone')) ) { ?>
  <p class="staff-phone"><?php the_field('phone');?></p>
  <?php } ?>

  <?php if ( !empty(get_field('office_location')) ) { ?>
  <p class="staff-office-location"><strong>Office location:</strong> <?php the_field('office_location');?></p>
  <?php } ?>

  <?php if ( !empty(get_field('faculty_bio')) ) { ?>
  <p class="faculty-bio"><a href="<?php the_field('faculty_bio'); ?>" aria-label="<?php echo $first_name . ' '. $last_name .  ' faculty bio'; ?>"><strong>Professional Biography</strong></a></p>
  <?php } ?>
  </p>

  <p class="staff-social">
  <?php if ( !empty(get_field('orcid_id')) ) { ?>
  <a class="staff-orcid-id" href="<?php the_field('orcid_id');?>"><img id="orcid-id-logo" src="https://orcid.org/sites/default/files/images/orcid_16x16.png" width='20' height='20' alt="ORCID iD icon"/></a>
  <?php } ?>

  <?php if ( !empty(get_field('linkedin_profile')) ) { ?>
  <a class="staff-linedin" href="<?php the_field('linkedin_profile');?>"><i class="fab fa-linkedin-in" aria-hidden="true" aria-label="Linkedin profile"></i></a>
  <?php } ?>
  </p>
  
  <?php if ( !empty(get_field('areas_of_expertise')) ) { ?>
  <p class="staff-expertise"><strong>Areas of Expertise:</strong> <?php the_field('areas_of_expertise');?></p>
  <?php } ?>

  <?php if ( !empty(get_field('liaison_areas')) ) { ?>
  <p class="staff-liaison"><strong>Liaison Areas: </strong><?php the_field('liaison_areas');?></p>
  <?php } ?>



  <?php if ( !empty(get_field('consultation')) ) { ?>
    <script>
      jQuery.getScript("https://api3.libcal.com/js/myscheduler.min.js", function() {
          jQuery("#<?php echo the_field('consultation'); ?>").LibCalMySched({iid: 973, lid: 0, gid: 0, uid: <?php echo str_replace("mysched_","", get_field('consultation')); ?>, width: 560, height: 680, title: 'Make an Appointment', domain: 'https://api3.libcal.com'});
      });
    </script>

    <p>
      <a href="#" id="<?php echo the_field('consultation'); ?>" class="btn-graphic">
        Book a Consultation
      </a>
    </p>

  <?php } ?>


  </section>
