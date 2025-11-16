<?php
/**
 * Plugin Deactivator
 */
class WP_Blog_Agent_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear all scheduled events using cron module
        WP_Blog_Agent_Cron::unschedule_all();
    }
}
