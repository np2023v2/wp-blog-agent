<div class="wrap">
    <h1><?php echo esc_html__('Post Series', 'wp-blog-agent'); ?></h1>
    
    <?php
    // Display messages
    if (isset($_GET['created'])) {
        $series_id = intval($_GET['created']);
        echo '<div class="notice notice-success is-dismissible"><p>' . sprintf(esc_html__('Series created successfully! ID: %d', 'wp-blog-agent'), $series_id) . '</p></div>';
    }
    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Series deleted successfully!', 'wp-blog-agent') . '</p></div>';
    }
    if (isset($_GET['added_post'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Post added to series!', 'wp-blog-agent') . '</p></div>';
    }
    if (isset($_GET['removed_post'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Post removed from series!', 'wp-blog-agent') . '</p></div>';
    }
    if (isset($_GET['rewrite_queued'])) {
        $post_id = intval($_GET['rewrite_queued']);
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             sprintf(
                 esc_html__('Post rewrite has been queued! The post will be regenerated asynchronously. %s', 'wp-blog-agent'),
                 '<a href="' . admin_url('admin.php?page=wp-blog-agent-queue') . '">' . esc_html__('View Queue', 'wp-blog-agent') . '</a>'
             ) . 
             '</p></div>';
    }
    if (isset($_GET['queued'])) {
        $queue_count = intval($_GET['queued']);
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             sprintf(
                 _n(
                     '%d topic has been added to the generation queue! Posts will be generated asynchronously.',
                     '%d topics have been added to the generation queue! Posts will be generated asynchronously.',
                     $queue_count,
                     'wp-blog-agent'
                 ),
                 $queue_count
             ) . 
             ' <a href="' . admin_url('admin.php?page=wp-blog-agent-queue') . '">' . esc_html__('View Queue', 'wp-blog-agent') . '</a></p></div>';
    }
    if (isset($_GET['post_generated'])) {
        $post_id = intval($_GET['post_generated']);
        $edit_link = get_edit_post_link($post_id);
        $view_link = get_permalink($post_id);
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             sprintf(
                 esc_html__('Post generated successfully! %s | %s', 'wp-blog-agent'),
                 '<a href="' . esc_url($edit_link) . '">' . esc_html__('Edit Post', 'wp-blog-agent') . '</a>',
                 '<a href="' . esc_url($view_link) . '" target="_blank">' . esc_html__('View Post', 'wp-blog-agent') . '</a>'
             ) . 
             '</p></div>';
    }
    if (isset($_GET['error'])) {
        $error_messages = array(
            'empty_name' => __('Series name cannot be empty', 'wp-blog-agent'),
            'creation_failed' => __('Failed to create series', 'wp-blog-agent'),
            'invalid_id' => __('Invalid series ID', 'wp-blog-agent'),
            'invalid_data' => __('Invalid data provided', 'wp-blog-agent'),
            'empty_topic' => __('Topic cannot be empty', 'wp-blog-agent'),
            'queue_failed' => __('Failed to add to queue', 'wp-blog-agent'),
            'rewrite_failed' => __('Failed to queue post rewrite', 'wp-blog-agent'),
            'post_not_found' => __('Post not found', 'wp-blog-agent')
        );
        $error_key = $_GET['error'];
        $error_message = isset($error_messages[$error_key]) ? $error_messages[$error_key] : esc_html($_GET['error']);
        echo '<div class="notice notice-error is-dismissible"><p>' . $error_message . '</p></div>';
    }
    
    // Check if we're viewing a specific series
    $view_series_id = isset($_GET['view']) ? intval($_GET['view']) : 0;
    
    if ($view_series_id > 0) {
        // Show series detail view
        $series = WP_Blog_Agent_Series::get_series($view_series_id);
        if (!$series) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Series not found', 'wp-blog-agent') . '</p></div>';
            echo '<p><a href="' . admin_url('admin.php?page=wp-blog-agent-series') . '" class="button">' . esc_html__('Back to Series List', 'wp-blog-agent') . '</a></p>';
            return;
        }
        
        $posts = WP_Blog_Agent_Series::get_series_posts($view_series_id);
        $stats = WP_Blog_Agent_Series::get_series_stats($view_series_id);
        ?>
        
        <div class="wp-blog-agent-series-detail">
            <p><a href="<?php echo admin_url('admin.php?page=wp-blog-agent-series'); ?>" class="button">&larr; <?php echo esc_html__('Back to Series List', 'wp-blog-agent'); ?></a></p>
            
            <h2><?php echo esc_html($series->name); ?></h2>
            
            <?php if (!empty($series->description)) : ?>
                <p><?php echo esc_html($series->description); ?></p>
            <?php endif; ?>
            
            <div class="wp-blog-agent-stats">
                <strong><?php echo esc_html__('Total Posts:', 'wp-blog-agent'); ?></strong> <?php echo $stats['total_posts']; ?>
            </div>
            
            <hr>
            
            <h3><?php echo esc_html__('AI Topic Suggestions', 'wp-blog-agent'); ?></h3>
            
            <div id="suggestions-container">
                <?php if ($stats['total_posts'] > 0) : ?>
                    <p><?php echo esc_html__('Click the button below to get AI-powered topic suggestions for the next post in this series.', 'wp-blog-agent'); ?></p>
                    <button type="button" id="get-suggestions-btn" class="button button-primary" data-series-id="<?php echo $view_series_id; ?>">
                        <?php echo esc_html__('Get AI Suggestions', 'wp-blog-agent'); ?>
                    </button>
                    <div id="suggestions-list" style="margin-top: 20px;"></div>
                <?php else : ?>
                    <p><?php echo esc_html__('Add at least one post to this series to get AI topic suggestions.', 'wp-blog-agent'); ?></p>
                <?php endif; ?>
            </div>
            
            <hr>
            
            <h3><?php echo esc_html__('Posts in this Series', 'wp-blog-agent'); ?></h3>
            
            <?php if (!empty($posts)) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Position', 'wp-blog-agent'); ?></th>
                            <th><?php echo esc_html__('Title', 'wp-blog-agent'); ?></th>
                            <th><?php echo esc_html__('Status', 'wp-blog-agent'); ?></th>
                            <th><?php echo esc_html__('Date', 'wp-blog-agent'); ?></th>
                            <th><?php echo esc_html__('Actions', 'wp-blog-agent'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $position = 1;
                        foreach ($posts as $post) : 
                        ?>
                        <tr>
                            <td><?php echo $position++; ?></td>
                            <td>
                                <strong>
                                    <a href="<?php echo get_edit_post_link($post->ID); ?>"><?php echo esc_html($post->post_title); ?></a>
                                </strong>
                            </td>
                            <td><?php echo esc_html(ucfirst($post->post_status)); ?></td>
                            <td><?php echo get_the_date('', $post->ID); ?></td>
                            <td>
                                <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button button-small">
                                    <?php echo esc_html__('Edit', 'wp-blog-agent'); ?>
                                </a>
                                <?php if ($post->post_status === 'publish') : ?>
                                <a href="<?php echo get_permalink($post->ID); ?>" class="button button-small" target="_blank">
                                    <?php echo esc_html__('View', 'wp-blog-agent'); ?>
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=wp_blog_agent_rewrite_post&series_id=' . $view_series_id . '&post_id=' . $post->ID), 'wp_blog_agent_rewrite_post'); ?>" class="button button-small" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to rewrite this post? The existing content will be replaced with new AI-generated content.', 'wp-blog-agent')); ?>');">
                                    <?php echo esc_html__('Rewrite', 'wp-blog-agent'); ?>
                                </a>
                                <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=wp_blog_agent_remove_post_from_series&series_id=' . $view_series_id . '&post_id=' . $post->ID), 'wp_blog_agent_remove_post_from_series'); ?>" class="button button-small" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to remove this post from the series?', 'wp-blog-agent')); ?>');">
                                    <?php echo esc_html__('Remove', 'wp-blog-agent'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php echo esc_html__('No posts in this series yet.', 'wp-blog-agent'); ?></p>
            <?php endif; ?>
            
            <hr>
            
            <h3><?php echo esc_html__('Add Existing Post to Series', 'wp-blog-agent'); ?></h3>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="wp_blog_agent_add_post_to_series">
                <input type="hidden" name="series_id" value="<?php echo $view_series_id; ?>">
                <?php wp_nonce_field('wp_blog_agent_add_post_to_series'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="post_filter"><?php echo esc_html__('Filter Posts', 'wp-blog-agent'); ?></label>
                        </th>
                        <td>
                            <select name="post_filter" id="post_filter" class="regular-text">
                                <option value="all"><?php echo esc_html__('All Posts', 'wp-blog-agent'); ?></option>
                                <option value="ai_generated"><?php echo esc_html__('AI Generated Posts', 'wp-blog-agent'); ?></option>
                                <option value="manual"><?php echo esc_html__('Manual Posts', 'wp-blog-agent'); ?></option>
                            </select>
                            <p class="description"><?php echo esc_html__('Filter which posts to show in the selection below.', 'wp-blog-agent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="post_id"><?php echo esc_html__('Select Post', 'wp-blog-agent'); ?></label>
                        </th>
                        <td>
                            <?php
                            // Get posts not in this series
                            $existing_post_ids = array_map(function($p) { return $p->ID; }, $posts);
                            
                            // Get all posts (not just AI generated)
                            $available_posts_args = array(
                                'post_type' => 'post',
                                'posts_per_page' => -1,
                                'post__not_in' => $existing_post_ids,
                                'orderby' => 'date',
                                'order' => 'DESC'
                            );
                            
                            $all_posts = get_posts($available_posts_args);
                            
                            // Separate posts by type
                            $ai_generated_posts = array();
                            $manual_posts = array();
                            
                            foreach ($all_posts as $post_item) {
                                if (get_post_meta($post_item->ID, '_wp_blog_agent_generated', true)) {
                                    $ai_generated_posts[] = $post_item;
                                } else {
                                    $manual_posts[] = $post_item;
                                }
                            }
                            
                            if (!empty($all_posts)) {
                                echo '<select name="post_id" id="post_id" class="regular-text" required>';
                                echo '<option value="">' . esc_html__('-- Select a post --', 'wp-blog-agent') . '</option>';
                                
                                // AI Generated posts group
                                if (!empty($ai_generated_posts)) {
                                    echo '<optgroup label="' . esc_attr__('AI Generated Posts', 'wp-blog-agent') . '" class="post-group-ai">';
                                    foreach ($ai_generated_posts as $available_post) {
                                        echo '<option value="' . $available_post->ID . '" data-type="ai_generated">' . esc_html($available_post->post_title) . ' (' . get_the_date('', $available_post->ID) . ')</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                
                                // Manual posts group
                                if (!empty($manual_posts)) {
                                    echo '<optgroup label="' . esc_attr__('Manual Posts', 'wp-blog-agent') . '" class="post-group-manual">';
                                    foreach ($manual_posts as $available_post) {
                                        echo '<option value="' . $available_post->ID . '" data-type="manual">' . esc_html($available_post->post_title) . ' (' . get_the_date('', $available_post->ID) . ')</option>';
                                    }
                                    echo '</optgroup>';
                                }
                                
                                echo '</select>';
                            } else {
                                echo '<p>' . esc_html__('No available posts to add. All posts are already in this series.', 'wp-blog-agent') . '</p>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                
                <?php if (!empty($all_posts)) : ?>
                    <?php submit_button(__('Add to Series', 'wp-blog-agent'), 'secondary', 'submit', true); ?>
                <?php endif; ?>
            </form>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Post filter functionality
            $('#post_filter').on('change', function() {
                var filter = $(this).val();
                var $postSelect = $('#post_id');
                var $options = $postSelect.find('option');
                
                // Reset select
                $postSelect.val('');
                
                // Show/hide options based on filter
                if (filter === 'all') {
                    $options.show();
                    $postSelect.find('optgroup').show();
                } else if (filter === 'ai_generated') {
                    $options.hide();
                    $options.filter('[data-type="ai_generated"]').show();
                    $postSelect.find('.post-group-ai').show();
                    $postSelect.find('.post-group-manual').hide();
                    $options.first().show(); // Show "Select a post" option
                } else if (filter === 'manual') {
                    $options.hide();
                    $options.filter('[data-type="manual"]').show();
                    $postSelect.find('.post-group-manual').show();
                    $postSelect.find('.post-group-ai').hide();
                    $options.first().show(); // Show "Select a post" option
                }
            });
            
            $('#get-suggestions-btn').on('click', function() {
                var button = $(this);
                var seriesId = button.data('series-id');
                var container = $('#suggestions-list');
                
                button.prop('disabled', true);
                button.text('<?php echo esc_js(__('Loading...', 'wp-blog-agent')); ?>');
                container.html('<p><?php echo esc_js(__('Generating suggestions...', 'wp-blog-agent')); ?></p>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wp_blog_agent_get_suggestions',
                        series_id: seriesId,
                        nonce: '<?php echo wp_create_nonce('wp_blog_agent_suggestions'); ?>'
                    },
                    success: function(response) {
                        button.prop('disabled', false);
                        button.text('<?php echo esc_js(__('Get AI Suggestions', 'wp-blog-agent')); ?>');
                        
                        if (response.success && response.data.suggestions) {
                            var html = '<div class="wp-blog-agent-suggestions">';
                            html += '<p><strong><?php echo esc_js(__('Suggested Topics:', 'wp-blog-agent')); ?></strong></p>';
                            html += '<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">';
                            html += '<input type="hidden" name="action" value="wp_blog_agent_generate_from_suggestion">';
                            html += '<input type="hidden" name="series_id" value="' + seriesId + '">';
                            html += '<?php echo wp_nonce_field('wp_blog_agent_generate_from_suggestion', '_wpnonce', true, false); ?>';
                            html += '<ul style="list-style: none; padding-left: 0;">';
                            
                            response.data.suggestions.forEach(function(suggestion, index) {
                                html += '<li style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;">';
                                html += '<label style="display: flex; align-items: center; cursor: pointer;">';
                                html += '<input type="checkbox" name="topics[]" value="' + suggestion + '" style="margin-right: 10px;"> ';
                                html += '<span>' + suggestion + '</span>';
                                html += '</label>';
                                html += '</li>';
                            });
                            
                            html += '</ul>';
                            html += '<p style="margin-top: 10px; color: #666; font-style: italic;"><?php echo esc_js(__('Select one or more topics to generate posts simultaneously.', 'wp-blog-agent')); ?></p>';
                            html += '<button type="submit" class="button button-primary"><?php echo esc_js(__('Generate Selected Topics', 'wp-blog-agent')); ?></button>';
                            html += '</form>';
                            html += '</div>';
                            
                            container.html(html);
                            
                            // Add form validation
                            $('#suggestions-list form').on('submit', function(e) {
                                var checkedCount = $(this).find('input[type="checkbox"]:checked').length;
                                if (checkedCount === 0) {
                                    e.preventDefault();
                                    alert('<?php echo esc_js(__('Please select at least one topic to generate.', 'wp-blog-agent')); ?>');
                                    return false;
                                }
                            });
                        } else {
                            var errorMsg = response.data && response.data.message ? response.data.message : '<?php echo esc_js(__('Failed to generate suggestions', 'wp-blog-agent')); ?>';
                            container.html('<p style="color: red;">' + errorMsg + '</p>');
                        }
                    },
                    error: function() {
                        button.prop('disabled', false);
                        button.text('<?php echo esc_js(__('Get AI Suggestions', 'wp-blog-agent')); ?>');
                        container.html('<p style="color: red;"><?php echo esc_js(__('Error occurred while generating suggestions', 'wp-blog-agent')); ?></p>');
                    }
                });
            });
        });
        </script>
        
        <?php
        return;
    }
    ?>
    
    <!-- Series List View -->
    <div class="wp-blog-agent-series-list">
        <h2><?php echo esc_html__('Create New Series', 'wp-blog-agent'); ?></h2>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="wp_blog_agent_create_series">
            <?php wp_nonce_field('wp_blog_agent_create_series'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="series_name"><?php echo esc_html__('Series Name', 'wp-blog-agent'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" name="series_name" id="series_name" class="regular-text" required>
                        <p class="description"><?php echo esc_html__('Give your post series a descriptive name.', 'wp-blog-agent'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="series_description"><?php echo esc_html__('Description', 'wp-blog-agent'); ?></label>
                    </th>
                    <td>
                        <textarea name="series_description" id="series_description" class="large-text" rows="3"></textarea>
                        <p class="description"><?php echo esc_html__('Describe the purpose and theme of this series (optional).', 'wp-blog-agent'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(__('Create Series', 'wp-blog-agent'), 'primary', 'submit', true); ?>
        </form>
        
        <hr>
        
        <h2><?php echo esc_html__('Existing Series', 'wp-blog-agent'); ?></h2>
        
        <?php
        $all_series = WP_Blog_Agent_Series::get_all_series();
        
        if (!empty($all_series)) {
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Name', 'wp-blog-agent'); ?></th>
                        <th><?php echo esc_html__('Description', 'wp-blog-agent'); ?></th>
                        <th><?php echo esc_html__('Posts', 'wp-blog-agent'); ?></th>
                        <th><?php echo esc_html__('Created', 'wp-blog-agent'); ?></th>
                        <th><?php echo esc_html__('Actions', 'wp-blog-agent'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_series as $series) : 
                        $stats = WP_Blog_Agent_Series::get_series_stats($series->id);
                    ?>
                    <tr>
                        <td>
                            <strong>
                                <a href="<?php echo admin_url('admin.php?page=wp-blog-agent-series&view=' . $series->id); ?>">
                                    <?php echo esc_html($series->name); ?>
                                </a>
                            </strong>
                        </td>
                        <td><?php echo esc_html(mb_strimwidth($series->description, 0, 100, '...')); ?></td>
                        <td><?php echo $stats['total_posts']; ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($series->created_at))); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=wp-blog-agent-series&view=' . $series->id); ?>" class="button button-small">
                                <?php echo esc_html__('View', 'wp-blog-agent'); ?>
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-post.php?action=wp_blog_agent_delete_series&series_id=' . $series->id), 'wp_blog_agent_delete_series'); ?>" class="button button-small" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete this series? This will not delete the posts, only the series grouping.', 'wp-blog-agent')); ?>');">
                                <?php echo esc_html__('Delete', 'wp-blog-agent'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<p>' . esc_html__('No series created yet. Create your first series above!', 'wp-blog-agent') . '</p>';
        }
        ?>
    </div>
</div>
