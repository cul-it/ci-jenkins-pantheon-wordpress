<section class="staff-profile" aria-label="Staff profile" >
  <?php

  $image = get_field('photo');

  if( !empty($image) ) { ?>

    <img class="staff-photo" src="<?php echo $image['url'];?>" alt="<?php echo $image['alt']; ?>">

  <?php } else { ?>

    <img class="staff-photo" src="<?php echo get_template_directory_uri(); ?>/images/staff/no-photo-profile.png">

  <?php } ?>

  <h2>
    <?php
      $first_name = get_field('first_name');
      $last_name = get_field('last_name');
      $degree = get_field('degree');
      echo $first_name . ' ' . $last_name, ( $degree ? ', ' . $degree : '' ) . '</span>';
    ?>
  </h2>

  <h3>
    <?php
    $title = get_field('title');
    $staff_type = get_field('staff_type');
    echo $title , ( $staff_type ? ', ' . $staff_type : '' );
    ?>
  </h3>

  <p class="staff-email"><a href="mailto:<?php echo the_field('email');?>"><?php echo the_field('email');?></a></p>

  <?php if ( !empty(get_field('phone')) ) { ?>
  <p class="staff-phone"><?php the_field('phone');?></p>
  <?php } ?>

  <?php if ( !empty(get_field('office_location')) ) { ?>
  <p class="staff-office-location"><strong>Office location:</strong> <?php the_field('office_location');?></p>
  <?php } ?>

  <p class="staff-social">
  <?php if ( !empty(get_field('orcid_id')) ) { ?>
  <a class="staff-orcid-id" href="<?php the_field('orcid_id');?>"><img id="orcid-id-logo" src="https://orcid.org/sites/default/files/images/orcid_16x16.png" width='20' height='20' alt="ORCID iD icon"/></a>
  <?php } ?>

  <?php if ( !empty(get_field('linkedin_profile')) ) { ?>
  <a class="staff-linedin" href="<?php the_field('linkedin_profile');?>"><span class="fa fa-linkedin" aria-hidden="true" aria-label="Linkedin profile"></span></a>
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
          jQuery("#<?php echo the_field('consultation'); ?>").LibCalMySched({iid: 973, lid: 0, gid: 0, uid: 18275, width: 560, height: 680, title: 'Make an Appointment', domain: 'https://api3.libcal.com'});
      });
    </script>

    <p>
      <a href="#" id="<?php echo the_field('consultation'); ?>" class="btn-graphic">
        Book a Consultation
      </a>
    </p>

  <?php } ?>


  </div>

</section>
