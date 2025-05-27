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
            'sports_odds_section'
        );
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
            return array('error' => 'Failed to fetch data: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array('error' => 'Invalid JSON response');
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
            'style' => 'cards'
        ), $atts);
        
        $odds_data = $this->fetch_odds($atts['sport'], $atts['regions'], $atts['markets']);
        
        if (isset($odds_data['error'])) {
            return '<div class="odds-error">Error: ' . $odds_data['error'] . '</div>';
        }
        
        if (empty($odds_data)) {
            return '<div class="odds-no-data">No odds data available</div>';
        }
        
        return $this->render_odds_html($odds_data, $atts);
    }
    
    // Render odds as HTML
    private function render_odds_html($odds_data, $atts) {
        $limit = intval($atts['limit']);
        $style = $atts['style'];
        
        ob_start();
        ?>
        <div class="sports-odds-container style-<?php echo esc_attr($style); ?>">
            <div class="odds-header">
                <h3>Today's VIP Tips</h3>
                <div class="match-tabs">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <span class="match-tab <?php echo $i === 1 ? 'active' : ''; ?>">Match <?php echo $i; ?></span>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="odds-matches">
                <?php 
                $count = 0;
                foreach ($odds_data as $match): 
                    if ($count >= $limit) break;
                    $count++;
                ?>
                    <div class="match-card">
                        <div class="match-info">
                            <div class="teams">
                                <span class="home-team"><?php echo esc_html($match['home_team']); ?></span>
                                <span class="vs">vs</span>
                                <span class="away-team"><?php echo esc_html($match['away_team']); ?></span>
                            </div>
                            <div class="match-time">
                                <?php echo date('M j, Y H:i', strtotime($match['commence_time'])); ?>
                            </div>
                        </div>
                        
                        <div class="bookmaker-odds">
                            <?php foreach ($match['bookmakers'] as $bookmaker): ?>
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
                <p>Our daily picks are based on in-depth Artificial Intelligence statistical analysis, team form, and insider information. We strive to provide the most accurate and informed predictions possible.</p>
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
?>