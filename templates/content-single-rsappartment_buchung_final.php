<?php
/*
* Indiebooking - die Buchungssoftware fuer Ihre Homepage!
 * Copyright (C) 2016  ReWa Soft GmbH
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */
?>
<?php
/* @var $coupon RS_IB_Model_Gutschein */
/* @var $appartment RS_IB_Model_Appartment */
/* @var $appartmentOption RS_IB_Model_Appartmentoption */
/* @var $buchungsKopfModel RS_IB_Model_Buchungskopf */
?>
<article id="post-<?php echo esc_attr(get_the_ID()); ?>" <?php post_class(); ?>>
    <?php //$ajax_nonce = wp_create_nonce( "my-special-string" );?>
    <?php
        global $RSBP_DATABASE;
        $postId                     = get_the_ID();
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($postId);
        $buchungsKopfId         	= $buchung->getBuchungKopfId();
        $buchungsKopfTbl        	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $buchungsKopfModel      	= $buchungsKopfTbl->loadBooking($buchungsKopfId, false);
//         $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
//         $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
    ?>
    <input type="hidden" id="appartmentPostId" name="appartmentPostId" value="">
    <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php echo esc_attr($postId);?>">
	<header class="entry-header">
		<?php if ( is_single() ) : ?>
		<h1 class="entry-title"><?php the_title(); //<-- gibt den Titel aus?></h1>
		<?php else : ?>
		<h1 class="entry-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
		<?php endif; // is_single() ?>
		<div class="entry-meta">
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<h2>
		<?php
		if ($buchungsKopfModel->getBuchung_status() != "rs_ib-requested") {
			_e("Thanks for Booking.", 'indiebooking');
		} else {
			_e("Thanks for inquiry.", 'indiebooking');
		}
		?>
		<br />
		<?php
			_e("Your BookingId is #", 'indiebooking');
			echo $postId;
		?>
		<br />
		<?php
			_e("You will get an Mail in the next 30 Minutes.", 'indiebooking');
		?>
		</h2>
	</div><!-- .entry-content -->
	<?php endif; ?>
	<footer class="entry-meta">
		<?php if ( comments_open() && ! is_single() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">' .
				        __( 'Leave a comment', 'indiebooking' ) .'</span>',
				        __( 'One comment so far', 'indiebooking' ), __( 'View all % comments', 'indiebooking' ) ); ?>
			</div><!-- .comments-link -->
		<?php endif; // comments_open() ?>
		<?php if ( is_single() && get_the_author_meta( 'description' ) && is_multi_author() ) : ?>
			<?php get_template_part( 'author-bio' ); ?>
		<?php endif; ?>
	</footer><!-- .entry-meta -->
</article><!-- #post -->