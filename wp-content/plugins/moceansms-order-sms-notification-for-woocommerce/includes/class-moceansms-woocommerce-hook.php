<?php

class Moceansms_WooCoommerce_Hook {
    
    public function add_action($hook_actions) {
        foreach ( $hook_actions as $hook ) {
            add_action( $hook['hook'], $hook['function_to_be_called'], $hook['priority'], $hook['accepted_args']);
        }
    }
    
}

?>
