<?php function create_lispra_list($lispra_component_type = null, $attrs = null) { ?>
    <?php if (!is_null($lispra_component_type)) : ?>
        <ul class="list-group" lispra-component="<?php echo $lispra_component_type; ?>" <?php echo array_to_attrs_string($attrs); ?> ></ul>
    <?php endif ?>
<?php } ?>
