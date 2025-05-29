<?php
/**
 * Plugin Name: Sports Odds Display
 * Description: Display sports betting odds from The Odds API
 * Version: 1.0
 * Author: Mo Gadaphy
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include enhanced settings
require_once plugin_dir_path(__FILE__) . 'enhanced-settings.php';

// Register Elementor widgets if Elementor is active
function register_sports_odds_widgets() {
    // Ensure Elementor is loaded
    if (!did_action('elementor/loaded')) {
        return;
    }

    // Include the main odds widget file
    require_once plugin_dir_path(__FILE__) . 'elementor-widget.php';

    // Include the hot games widget file
    require_once plugin_dir_path(__FILE__) . 'sports-hot-games-widget.php';

    // Register the widgets
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Sports_Odds_Elementor_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Sports_Hot_Games_Elementor_Widget());
}
add_action('elementor/widgets/widgets_registered', 'register_sports_odds_widgets');


class SportsOddsDisplay {
    
    private $api_key;
    private $api_base_url = 'https://api.the-odds-api.com/v4/sports';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('sports_odds', array($this, 'display_odds_shortcode'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
    }
    
    public function init() {
        $this->api_key = get_option('odds_api_key', '');
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('sports-odds-style', plugin_dir_url(__FILE__) . 'style.css');
        wp_enqueue_style('enhanced-odds-style', plugin_dir_url(__FILE__) . 'enhanced-style.css');
        wp_enqueue_script('sports-odds-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.0', true);
        wp_localize_script('sports-odds-script', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('odds_nonce')
        ));
    }
    
    // Admin menu for API key configuration
    public function add_admin_menu() {
        add_options_page(
            'Sports Odds Settings',
            'Sports Odds',
            'manage_options',
            'sports_odds',
            array($this, 'options_page')
        );
    }
    
    public function settings_init() {
        register_setting('sports_odds', 'odds_api_key');
        add_settings_section(
            'sports_odds_section',
            'API Configuration',
            null,
            'sports_odds'
        );
        add_settings_field(
            'odds_api_key',
            'The Odds API Key',
            array($this, 'api_key_render'),
            'sports_odds',
            'sports_odds_section');
    }
    
    public function api_key_render() {
        $options = get_option('odds_api_key');
        ?>
        <input type='text' name='odds_api_key' value='<?php echo $options; ?>' style='width: 400px;'>
        <p class="description">Enter your API key from <a href="https://the-odds-api.com" target="_blank">The Odds API</a></p>
        <?php
    }
    
    public function options_page() {
        ?>
        <form action='options.php' method='post'>
            <h2>Sports Odds Settings</h2>
            <?php
            settings_fields('sports_odds');
            do_settings_sections('sports_odds');
            submit_button();
            ?>
        </form>
        <?php
    }
    
    // Fetch odds from API
    private function fetch_odds($sport = 'soccer_epl', $regions = 'us,uk,eu', $markets = 'h2h,spreads,totals') {
        if (empty($this->api_key)) {
            error_log('Sports Odds Display: API key is empty in fetch_odds');
            return array('error' => 'API key not configured');
        }
        
        $transient_key = 'sports_odds_' . md5($sport . $regions . $markets);
        $cached_data = get_transient($transient_key);
        
        if ($cached_data !== false) {
            return $cached_data;
        }
        
        $url = $this->api_base_url . '/' . $sport . '/odds/';
        $args = array(
            'api_key' => $this->api_key,
            'regions' => $regions,
            'markets' => $markets,
            'oddsFormat' => 'decimal',
            'dateFormat' => 'iso'
        );
        
        $request_url = $url . '?' . http_build_query($args);
        $response = wp_remote_get($request_url, array('timeout' => 30));
        
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('Sports Odds Display: API request failed for ' . $sport . ' - ' . $error_message);
            return array('error' => 'Failed to fetch data: ' . $error_message);
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_message = 'API returned status code ' . $response_code;
            error_log('Sports Odds Display: ' . $error_message . ' for ' . $sport);
            return array('error' => $error_message);
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('Sports Odds Display: Invalid JSON response from API for ' . $sport);
            return array('error' => 'Invalid JSON response');
        }

        // Check for API-specific errors
        if (isset($data['error'])) {
            error_log('Sports Odds Display: API returned error for ' . $sport . ' - ' . $data['error']);
            return array('error' => $data['error']);
        }

        // Check if we got any data
        if (empty($data)) {
            error_log('Sports Odds Display: No data returned for ' . $sport);
            return array('error' => 'No odds data available for ' . $sport);
        }
        
        // Cache for 10 minutes
        set_transient($transient_key, $data, 10 * MINUTE_IN_SECONDS);
        
        return $data;
    }
    
    // Shortcode handler
    public function display_odds_shortcode($atts) {
        $atts = shortcode_atts(array(
            'sport' => 'soccer_epl',
            'regions' => 'us,uk,eu',
            'markets' => 'h2h',
            'limit' => '10',
            'style' => 'cards',
            'bookmakers' => ''
        ), $atts);
        
        // Convert comma-separated bookmakers string to an array
        $bookmakers_array = !empty($atts['bookmakers']) ? array_map('trim', explode(',', $atts['bookmakers'])) : array();
        
        $odds_data = $this->fetch_odds($atts['sport'], $atts['regions'], $atts['markets']);
        
        // Check for error before rendering
        if (isset($odds_data['error'])) {
            // Return the error message directly
            return '<div class="odds-error">Error: ' . esc_html($odds_data['error']) . '</div>';
        }
        
        if (empty($odds_data)) {
            return '<div class="odds-no-data">No odds data available</div>';
        }
        
        // Pass the bookmakers array to the rendering function
        $atts['bookmakers'] = $bookmakers_array;
        return $this->render_odds_html($odds_data, $atts);
    }
    
    // Render odds as HTML
    private function render_odds_html($odds_data, $atts) {
        $limit = intval($atts['limit']);
        $style = $atts['style'];
        $allowed_bookmakers = isset($atts['bookmakers']) && is_array($atts['bookmakers']) ? $atts['bookmakers'] : array();
        $filter_bookmakers = !empty($allowed_bookmakers);
        
        // Get enhanced settings if available
        $enhanced_settings = get_option('enhanced_odds_settings', array(
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'success_color' => '#28a745',
            'border_radius' => '12px',
            'currency' => 'XAF',
            'locale' => 'en',
            'timezone' => 'Africa/Douala',
            'show_live_indicator' => true
        ));
        
        ob_start();
        ?>
        <style>
        .sports-odds-container .match-tab.active {
            background: <?php echo esc_attr($enhanced_settings['primary_color']); ?> !important;
            border-color: <?php echo esc_attr($enhanced_settings['primary_color']); ?> !important;
        }
        .sports-odds-container .match-card {
            border-radius: <?php echo esc_attr($enhanced_settings['border_radius']); ?> !important;
        }
        .sports-odds-container .odd-item:hover {
            border-color: <?php echo esc_attr($enhanced_settings['primary_color']); ?> !important;
        }
        .sports-odds-container .live-indicator {
            background: <?php echo esc_attr($enhanced_settings['success_color']); ?> !important;
        }
        </style>
        
        <div class="sports-odds-container style-<?php echo esc_attr($style); ?>" 
             data-currency="<?php echo esc_attr($enhanced_settings['currency']); ?>"
             data-locale="<?php echo esc_attr($enhanced_settings['locale']); ?>"
             data-timezone="<?php echo esc_attr($enhanced_settings['timezone']); ?>">
            <div class="odds-header">
                <h3><?php echo $enhanced_settings['locale'] === 'fr' ? 'Conseils VIP du Jour' : 'Today\'s VIP Tips'; ?></h3>
                <div class="match-tabs">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <span class="match-tab <?php echo $i === 1 ? 'active' : ''; ?>"><?php echo $enhanced_settings['locale'] === 'fr' ? 'Match ' : 'Match '; ?><?php echo $i; ?></span>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="odds-matches">
                <?php 
                $count = 0;
                foreach ($odds_data as $match): 
                    if ($count >= $limit) break;
                    $count++;
                    
                    // Check if match is live
                    $is_live = strtotime($match['commence_time']) <= time() && 
                              strtotime($match['commence_time']) + 7200 >= time();
                ?>
                    <div class="match-card">
                        <div class="match-info">
                            <?php if ($is_live && $enhanced_settings['show_live_indicator']): ?>
                                <span class="live-indicator">LIVE</span>
                            <?php endif; ?>
                            <div class="teams">
                                <span class="home-team"><?php echo esc_html($match['home_team']); ?></span>
                                <span class="vs">vs</span>
                                <span class="away-team"><?php echo esc_html($match['away_team']); ?></span>
                            </div>
                            <div class="match-time">
                                <?php 
                                $date = new DateTime($match['commence_time']);
                                $date->setTimezone(new DateTimeZone($enhanced_settings['timezone']));
                                echo $date->format('M j, Y H:i'); 
                                ?>
                            </div>
                        </div>
                        
                        <div class="bookmaker-odds">
                            <?php foreach ($match['bookmakers'] as $bookmaker): ?>
                                <?php 
                                // Filter bookmakers if allowed list is provided
                                if ($filter_bookmakers && !in_array($bookmaker['title'], $allowed_bookmakers)) {
                                    continue; 
                                }
                                ?>
                                <div class="bookmaker">
                                    <div class="bookmaker-name"><?php echo esc_html($bookmaker['title']); ?></div>
                                    <div class="odds-row">
                                        <?php foreach ($bookmaker['markets'][0]['outcomes'] as $outcome): ?>
                                            <div class="odd-item">
                                                <span class="outcome-name"><?php echo esc_html($outcome['name']); ?></span>
                                                <span class="odd-value"><?php echo number_format($outcome['price'], 2); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="odds-footer">
                <p><?php echo $enhanced_settings['locale'] === 'fr' ? 
                    'Nos choix quotidiens sont basés sur une analyse statistique approfondie par Intelligence Artificielle, la forme des équipes et des informations internes. Nous nous efforçons de fournir les prédictions les plus précises et informées possibles.' : 
                    'Our daily picks are based on in-depth Artificial Intelligence statistical analysis, team form, and insider information. We strive to provide the most accurate and informed predictions possible.'; ?></p>
                <div class="scorido-branding">
                    <strong>Scorido Team</strong>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize the plugin
new SportsOddsDisplay();

// AJAX handler for live updates
add_action('wp_ajax_refresh_odds', 'handle_refresh_odds');
add_action('wp_ajax_nopriv_refresh_odds', 'handle_refresh_odds');

function handle_refresh_odds() {
    check_ajax_referer('odds_nonce', 'nonce');
    
    $sport = sanitize_text_field($_POST['sport']);
    $regions = sanitize_text_field($_POST['regions']);
    $markets = sanitize_text_field($_POST['markets']);
    
    $odds_display = new SportsOddsDisplay();
    $odds_data = $odds_display->fetch_odds($sport, $regions, $markets);
    
    wp_send_json_success($odds_data);
}

/**
 * Fetches odds data for multiple sports/leagues.
 * @param array $leagues Array of sport keys (e.g., ['soccer_epl', 'soccer_uefa_champs_league']).
 * @param string $regions Comma-separated regions (e.g., 'us,uk,eu').
 * @param string $markets Comma-separated markets (e.g., 'h2h,spreads').
 * @return array An array containing combined odds data or an error.
 */
function fetch_odds_for_leagues($leagues, $regions, $markets) {
    $odds_display = new SportsOddsDisplay();
    $combined_data = array();
    $errors = array();

    // Create a transient key based on the parameters
    $transient_key = 'sports_odds_leagues_' . md5(implode(',', $leagues) . $regions . $markets);
    $cached_data = get_transient($transient_key);

    if ($cached_data !== false) {
        return $cached_data;
    }

    // Get API key directly from options
    $api_key = get_option('odds_api_key', '');
    if (empty($api_key)) {
        return array('error' => 'API key not configured. Please configure your API key in the plugin settings.');
    }

    // Set the API key for the odds display instance
    $odds_display->api_key = $api_key;

    foreach ($leagues as $sport) {
        try {
            // Call the existing fetch_odds method for each sport
            $sport_data = $odds_display->fetch_odds($sport, $regions, $markets);

            if (isset($sport_data['error'])) {
                $errors[] = 'Error fetching data for ' . $sport . ': ' . $sport_data['error'];
                continue; // Skip this sport but continue with others
            }

            if (!empty($sport_data) && is_array($sport_data)) {
                // Add sport identifier to each match
                foreach ($sport_data as &$match) {
                    $match['sport'] = $sport;
                }
                // Merge data from different sports
                $combined_data = array_merge($combined_data, $sport_data);
            } else {
                $errors[] = 'No data available for ' . $sport;
            }
        } catch (Exception $e) {
            $errors[] = 'Exception while fetching ' . $sport . ': ' . $e->getMessage();
        }
    }

    // If we have some data but also some errors, include both
    if (!empty($combined_data) && !empty($errors)) {
        $result = array(
            'data' => $combined_data,
            'warnings' => $errors
        );
        set_transient($transient_key, $result, 10 * MINUTE_IN_SECONDS);
        return $result;
    }

    // If we have no data at all, return the errors
    if (empty($combined_data)) {
        $error_message = !empty($errors) ? implode('; ', $errors) : 'No odds data available for the selected leagues.';
        return array('error' => $error_message);
    }

    // Sort combined data by commencement time
    usort($combined_data, function($a, $b) {
        return strtotime($a['commence_time']) - strtotime($b['commence_time']);
    });

    // Cache the combined data for 10 minutes
    set_transient($transient_key, $combined_data, 10 * MINUTE_IN_SECONDS);

    return $combined_data;
}

/**
 * AJAX handler for refreshing hot games.
 */
add_action('wp_ajax_refresh_hot_games', 'handle_refresh_hot_games');
add_action('wp_ajax_nopriv_refresh_hot_games', 'handle_refresh_hot_games');

function handle_refresh_hot_games() {
    check_ajax_referer('odds_nonce', 'nonce'); // Reuse the existing nonce for simplicity
    
    $leagues = isset($_POST['leagues']) ? (array) $_POST['leagues'] : array();
    $regions = isset($_POST['regions']) ? sanitize_text_field($_POST['regions']) : 'uk,eu'; // Default regions
    $markets = isset($_POST['markets']) ? sanitize_text_field($_POST['markets']) : 'h2h'; // Default market
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 5; // Default limit
    $bookmakers = isset($_POST['bookmakers']) ? (array) $_POST['bookmakers'] : array();

    $hot_games_data = fetch_odds_for_leagues($leagues, $regions, $markets);

    // Apply limit and bookmaker filtering similar to the widget render method
    if (!empty($hot_games_data) && !isset($hot_games_data['error'])) {
         // Filter by selected bookmakers
        $filter_bookmakers = !empty($bookmakers);

        if ($filter_bookmakers) {
            $filtered_data = [];
            foreach ($hot_games_data as $match) {
                $match_bookmakers = [];
                foreach ($match['bookmakers'] as $bookmaker) {
                    if (in_array($bookmaker['title'], $bookmakers)) {
                        $match_bookmakers[] = $bookmaker;
                    }
                }
                // Only keep match if it has at least one selected bookmaker
                if (!empty($match_bookmakers)) {
                    $match['bookmakers'] = $match_bookmakers;
                    $filtered_data[] = $match;
                }
            }
            $hot_games_data = $filtered_data;
        }

        // Apply the limit
        $hot_games_data = array_slice($hot_games_data, 0, $limit);
    }

    // Render the HTML for the hot games list (similar to the widget render method's loop)
    ob_start();
    // Get enhanced settings for localization and timezone within the AJAX handler
    $enhanced_settings = get_option('enhanced_odds_settings', array(
        'locale' => 'en',
        'timezone' => 'Africa/Douala',
        'currency' => 'XAF'
    ));

    if (!empty($hot_games_data) && !isset($hot_games_data['error'])) {
        foreach ($hot_games_data as $match) {
            // Reuse the rendering logic from the widget's render method
            ?>
            <div class="match-card hot-game-match">
                <div class="match-info">
                    <div class="teams">
                        <span class="home-team"><?php echo esc_html($match['home_team']); ?></span>
                        <span class="vs">vs</span>
                        <span class="away-team"><?php echo esc_html($match['away_team']); ?></span>
                    </div>
                    <div class="match-time">
                        <?php
                        $date = new DateTime($match['commence_time']);
                        $date->setTimezone(new DateTimeZone($enhanced_settings['timezone']));
                        echo $date->format('M j, Y H:i');
                        ?>
                    </div>
                </div>

                <?php if (!empty($match['bookmakers'])) : ?>
                <div class="bookmaker-odds">
                    <?php foreach ($match['bookmakers'] as $bookmaker): ?>
                        <div class="bookmaker">
                            <div class="bookmaker-name"><?php echo esc_html($bookmaker['title']); ?></div>
                            <div class="odds-row">
                                <?php
                                if (isset($bookmaker['markets'][0]['outcomes'])):
                                    foreach ($bookmaker['markets'][0]['outcomes'] as $outcome): ?>
                                        <div class="odd-item">
                                            <span class="outcome-name"><?php echo esc_html($outcome['name']); ?></span>
                                            <span class="odd-value"><?php echo number_format($outcome['price'], 2); ?></span>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php
        }
    } else {
        echo '<div class="odds-no-data">' . __('No hot games available at the moment.', 'textdomain') . '</div>';
    }
    $html = ob_get_clean();

    if (isset($hot_games_data['error'])) {
         wp_send_json_error(array('message' => 'Failed to refresh hot games: ' . $hot_games_data['error']));
    } else {
        wp_send_json_success(array('html' => $html));
    }
}
?>