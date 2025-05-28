<?php
/**
 * Elementor Widget for Sports Odds Display
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sports_Odds_Elementor_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'sports_odds_widget';
    }
    
    public function get_title() {
        return __('Sports Odds Display', 'textdomain');
    }
    
    public function get_icon() {
        return 'eicon-sports';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Odds Settings', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'sport',
            [
                'label' => __('Sport', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'soccer_epl',
                'options' => [
                    'soccer_epl' => __('English Premier League', 'textdomain'),
                    'soccer_spain_la_liga' => __('Spanish La Liga', 'textdomain'),
                    'soccer_germany_bundesliga' => __('German Bundesliga', 'textdomain'),
                    'soccer_italy_serie_a' => __('Italian Serie A', 'textdomain'),
                    'soccer_france_ligue_one' => __('French Ligue 1', 'textdomain'),
                    'soccer_uefa_champs_league' => __('UEFA Champions League', 'textdomain'),
                    'soccer_fifa_world_cup' => __('FIFA World Cup', 'textdomain'),
                    'americanfootball_nfl' => __('NFL', 'textdomain'),
                    'basketball_nba' => __('NBA', 'textdomain'),
                    'tennis_atp_french_open' => __('French Open Tennis', 'textdomain'),
                ],
            ]
        );

        $this->add_control(
            'regions',
            [
                'label' => __('Bookmaker Regions', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['uk', 'eu'],
                'options' => [
                    'uk' => __('UK Bookmakers', 'textdomain'),
                    'eu' => __('European Bookmakers', 'textdomain'),
                    'us' => __('US Bookmakers', 'textdomain'),
                ],
            ]
        );

        $this->add_control(
            'markets',
            [
                'label' => __('Betting Markets', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['h2h'],
                'options' => [
                    'h2h' => __('Head to Head (1X2)', 'textdomain'),
                    'spreads' => __('Point Spreads', 'textdomain'),
                    'totals' => __('Over/Under', 'textdomain'),
                ],
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => __('Number of Matches', 'textdomain'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 10,
            ]
        );

        $this->end_controls_section();

        // Style Section
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

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Prepare shortcode attributes
        $sport = $settings['sport'];
        $regions = is_array($settings['regions']) ? implode(',', $settings['regions']) : 'uk,eu';
        $markets = is_array($settings['markets']) ? implode(',', $settings['markets']) : 'h2h';
        $limit = $settings['limit'];
        $dark_mode = $settings['dark_mode'] === 'yes' ? 'dark-mode' : '';
        
        echo '<div class="sports-odds-container ' . esc_attr($dark_mode) . '">';
        echo do_shortcode('[sports_odds sport="' . esc_attr($sport) . '" regions="' . esc_attr($regions) . '" markets="' . esc_attr($markets) . '" limit="' . esc_attr($limit) . '"]');
        echo '</div>';
    }
}

// Register the widget
function register_sports_odds_widget() {
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Sports_Odds_Elementor_Widget());
}
add_action('elementor/widgets/widgets_registered', 'register_sports_odds_widget'); 