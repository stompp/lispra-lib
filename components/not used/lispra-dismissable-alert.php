<?php function create_lispra_dismissable_alert() { ?>
                <div class="hidden" lispra-dismissable-alert role="alert">
                    <button type="button" class="btn close"  hides-parent aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                    <span lispra-dismissable-alert-content></span>
                </div>
<?php } ?>
