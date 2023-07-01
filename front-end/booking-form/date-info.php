<div class="date-info">
    <?php if(is_singular("hb_villa")){ ?>
        <div class="available-days-info">
            <span class="available-days-square">
                square
            </span>
            <?php _e('Available','hbook-admin') ?>
        </div>
        <div class="booked-days-info">
            <span class="booked-days-square">
                square
            </span>
            <?php _e('Booked','hbook-admin') ?>
        </div>
    <?php } ?>
    <div class="clear-dates-wrapper">
        <a href="#" class="clear-dates-btn"><?php _e('Clear Dates','hbook-admin') ?></a>
    </div>
</div>