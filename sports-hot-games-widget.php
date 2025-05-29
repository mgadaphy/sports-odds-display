<?php
/**
 * Elementor Widget for Upcoming Hot Games Display
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sports_Hot_Games_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sports_hot_games_widget';
    }

    public function get_title() {
        return __('Sports Hot Games', 'textdomain');
    }

    public function get_icon() {
        return 'eicon-fire'; // Using a fire icon for "hot games"
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Hot Games Settings', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'leagues',
            [
                'label' => __('Select Leagues', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    // Soccer Leagues
                    'soccer_epl' => __('English Premier League', 'textdomain'),
                    'soccer_spain_la_liga' => __('Spanish La Liga', 'textdomain'),
                    'soccer_germany_bundesliga' => __('German Bundesliga', 'textdomain'),
                    'soccer_italy_serie_a' => __('Italian Serie A', 'textdomain'),
                    'soccer_france_ligue_one' => __('French Ligue 1', 'textdomain'),
                    'soccer_mls' => __('Major League Soccer (MLS)', 'textdomain'),
                    'soccer_uefa_champs_league' => __('UEFA Champions League', 'textdomain'),
                    'soccer_uefa_europa_league' => __('UEFA Europa League', 'textdomain'),
                    'soccer_africa_cup_nations' => __('Africa Cup of Nations', 'textdomain'),
                    'soccer_cameroon_league' => __('Cameroon Elite One', 'textdomain'),
                    
                    // American Sports
                    'americanfootball_nfl' => __('NFL', 'textdomain'),
                    'basketball_nba' => __('NBA', 'textdomain'),
                    'baseball_mlb' => __('MLB', 'textdomain'),
                    
                    // Other Sports
                    'tennis_atp' => __('ATP Tennis', 'textdomain'),
                    'tennis_wta' => __('WTA Tennis', 'textdomain'),
                    'golf_pga_championship' => __('PGA Championship', 'textdomain'),
                ],
                'default' => ['soccer_epl', 'soccer_uefa_champs_league', 'soccer_mls'], // Updated default leagues
                'description' => __('Select the leagues to highlight in the Hot Games section.', 'textdomain'),
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => __('Number of Matches to Display', 'textdomain'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 10,
                'step' => 1,
                'default' => 5,
                'description' => __('Maximum number of hot games to show.', 'textdomain'),
            ]
        );

         $this->add_control(
            'bookmakers',
            [
                'label' => __('Bookmakers to Display', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => [
                    '1xBet' => __('1xBet', 'textdomain'),
                    'Betwinner' => __('Betwinner', 'textdomain'),
                    'Betway' => __('Betway', 'textdomain'), // Example of another common bookmaker
                    'Pinnacle' => __('Pinnacle', 'textdomain'),
                    // Add more bookmakers as needed
                ],
                'default' => ['1xBet', 'Betwinner'], // Default to 1xBet and Betwinner
                'description' => __('Select the bookmakers whose odds you want to display for hot games.', 'textdomain'),
            ]
        );

        $this->add_control(
            'see_more_link',
            [
                'label' => __('"See More" Link URL', 'textdomain'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-site.com/full-odds-page', 'textdomain'),
                'description' => __('URL of the page where users can see all odds.', 'textdomain'),
            ]
        );

        $this->end_controls_section();

        // Auto-Refresh Section
        $this->start_controls_section(
            'refresh_section',
            [
                'label' => __('Auto Refresh', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'auto_refresh_interval',
            [
                'label' => __('Refresh Interval (minutes)', 'textdomain'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 1,
                'default' => 5,
                'description' => __('Set to 0 to disable auto-refresh.', 'textdomain'),
            ]
        );

        $this->end_controls_section();

        // Style Section (Basic)
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

         $this->add_control(
            'dark_mode',
            [
                'label' => __('Dark Mode', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'textdomain'),
                'label_off' => __('No', 'textdomain'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        // More detailed style controls will be added in a later step

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $leagues = is_array($settings['leagues']) ? $settings['leagues'] : array();
        $limit = intval($settings['limit']);
        $bookmakers = is_array($settings['bookmakers']) ? implode(',', $settings['bookmakers']) : '';
        $see_more_url = $settings['see_more_link']['url'];
        $dark_mode_class = $settings['dark_mode'] === 'yes' ? 'dark-mode' : '';
        $refresh_interval = intval($settings['auto_refresh_interval']);

        // --- Data Fetching (To be implemented/refined) ---
        // We will need a way to fetch data for multiple leagues efficiently
        // For now, we'll just indicate where the content will go
        // $hot_games_data = ... fetch data based on $leagues, $limit, $bookmakers ...
        // --- End Data Fetching ---

        // --- Data Fetching ---
        // Use the new function to fetch data for the selected leagues
        // Default regions and markets for hot games for now
        $regions = 'uk,eu'; // Default regions - can be made configurable later
        $markets = 'h2h'; // Default market - can be made configurable later

        $hot_games_data = fetch_odds_for_leagues($leagues, $regions, $markets);

        // Handle fetching errors
        if (isset($hot_games_data['error'])) {
            echo '<div class="odds-error">Error fetching hot games: ' . esc_html($hot_games_data['error']) . '</div>';
            return; // Stop rendering if there's an error
        }

        // Apply limit and bookmaker filtering after fetching and combining data
        if (!empty($hot_games_data)) {
             // Filter by selected bookmakers
            $allowed_bookmakers = is_array($settings['bookmakers']) ? $settings['bookmakers'] : array();
            $filter_bookmakers = !empty($allowed_bookmakers);

            if ($filter_bookmakers) {
                $filtered_data = [];
                foreach ($hot_games_data as $match) {
                    $match_bookmakers = [];
                    foreach ($match['bookmakers'] as $bookmaker) {
                        if (in_array($bookmaker['title'], $allowed_bookmakers)) {
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

        // --- End Data Fetching and Filtering ---

        ?>
        <div class="sports-hot-games-container sports-odds-container <?php echo esc_attr($dark_mode_class); ?>"
            data-leagues="<?php echo esc_attr(implode(',', $leagues)); ?>"
            data-bookmakers="<?php echo esc_attr(implode(',', is_array($settings['bookmakers']) ? $settings['bookmakers'] : [])); ?>"
            data-limit="<?php echo esc_attr($limit); ?>"
            data-refresh-interval="<?php echo esc_attr($refresh_interval); ?>">
            <div class="hot-games-header odds-header">
                <h3><?php echo __('Upcoming Hot Games', 'textdomain'); ?></h3>
            </div>
            <div class="hot-games-list odds-matches">
                <?php
                // --- Rendering Loop (To be implemented) ---
                // Get enhanced settings for localization and timezone
                $enhanced_settings = get_option('enhanced_odds_settings', array(
                    'locale' => 'en',
                    'timezone' => 'Africa/Douala',
                    'currency' => 'XAF' // Although currency display is not strictly needed for hot games, include for completeness if we decide to show prices
                ));

                if (!empty($hot_games_data)) {
                    // Loop through the fetched and filtered hot games data
                    foreach ($hot_games_data as $match) {
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
                                            // Assuming only h2h for hot games for simplicity initially
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
                // --- End Rendering Loop ---

                ?>
            </div>

            <?php if (!empty($see_more_url)) : ?>
                <div class="see-more-link" style="text-align: center; margin-top: 20px;">
                    <a href="<?php echo esc_url($see_more_url); ?>" class="elementor-button elementor-button-link elementor-size-sm">
                        <?php echo __('See More Full Odds', 'textdomain'); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
        <?php
    }

    // Placeholder for potential AJAX refresh for hot games (if needed)
    // public function refresh_hot_games() { ... }

} 