<?php
/**
 * Scheduler for automated post generation
 * 
 * Note: Cron-related functionality has been moved to WP_Blog_Agent_Cron
 * This class maintains backward compatibility
 */
class WP_Blog_Agent_Scheduler {
    
    public function __construct() {
        // Backward compatibility - these hooks are now registered in WP_Blog_Agent_Cron
        // Keeping them here ensures existing integrations continue to work
    }
    
    /**
     * Update schedule frequency
     * Delegates to cron module
     * 
     * @param string $frequency Frequency: hourly, twicedaily, daily, weekly, none
     */
    public static function update_schedule($frequency) {
        WP_Blog_Agent_Cron::schedule_post_generation($frequency);
    }
}
