<?php
/**
 * Enhanced Odds Elementor Widget
 * Specifically designed for Cameroon betting sites
 */

class Enhanced_Odds_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'enhanced_odds_widget';
    }

    public function get_title() {
        return __('Enhanced Sports Odds', 'textdomain');
    }

    public function get_icon() {
        return 'eicon-sports';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Odds Settings', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'api_sport',
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
                    'americanfootball_nfl' => __('NFL', 'textdomain'),
                    'basketball_nba' => __('NBA', 'textdomain'),
                ],
            ]
        );

        $this->add_control(
            'api_regions',
            [
                'label' => __('Bookmaker Regions', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['uk', 'eu'],
                'options' => [
                    'uk' => __('UK Bookmakers', 'textdomain'),
                    'eu' => __('European Bookmakers', 'textdomain'),
                    'us' => __('US Bookmakers', 'textdomain'),
                    'au' => __('Australian Bookmakers', 'textdomain'),
                ],
            ]
        );

        $this->add_control(
            'api_markets',
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
            'match_limit',
            [
                'label' => __('Number of Matches', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
            ]
        );

        $this->add_control(
            'show_live_indicator',
            [
                'label' => __('Show Live Indicator', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'textdomain'),
                'label_off' => __('Hide', 'textdomain'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'auto_refresh',
            [
                'label' => __('Auto Refresh (minutes)', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
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
            'primary_color',
            [
                'label' => __('Primary Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007bff',
            ]
        );

        $this->add_control(
            'secondary_color',
            [
                'label' => __('Secondary Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#6c757d',
            ]
        );

        $this->add_control(
            'success_color',
            [
                'label' => __('Success Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#28a745',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => __('Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .sports-odds-container',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Card Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
            ]
        );

        $this->end_controls_section();

        // Currency Section
        $this->start_controls_section(
            'currency_section',
            [
                'label' => __('Currency & Localization', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'display_currency',
            [
                'label' => __('Display Currency', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'XAF',
                'options' => [
                    'XAF' => __('Central African CFA Franc (XAF)', 'textdomain'),
                    'USD' => __('US Dollar (USD)', 'textdomain'),
                    'EUR' => __('Euro (EUR)', 'textdomain'),
                    'GBP' => __('British Pound (GBP)', 'textdomain'),
                ],
            ]
        );

        $this->add_control(
            'language_locale',
            [
                'label' => __('Language', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'en',
                'options' => [
                    'en' => __('English', 'textdomain'),
                    'fr' => __('French', 'textdomain'),
                ],
            ]
        );

        $this->add_control(
            'timezone',
            [
                'label' => __('Timezone', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'Africa/Douala',
                'options' => [
                    'Africa/Douala' => __('Cameroon Time (WAT)', 'textdomain'),
                    'UTC' => __('UTC', 'textdomain'),
                    'Europe/London' => __('London Time (GMT/BST)', 'textdomain'),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Prepare shortcode attributes
        $sport = $settings['api_sport'];
        $regions = is_array($settings['api_regions']) ? implode(',', $settings['api_regions']) : 'uk,eu';
        $markets = is_array($settings['api_markets']) ? implode(',', $settings['api_markets']) : 'h2h';
        $limit = $settings['match_limit']['size'];
        $currency = $settings['display_currency'];
        $locale = $settings['language_locale'];
        $timezone = $settings['timezone'];
        
        // Generate custom CSS
        $primary_color = $settings['primary_color'];
        $secondary_color = $settings['secondary_color'];
        $success_color = $settings['success_color'];
        $border_radius = $settings['card_border_radius']['size'] . 'px';
        
        ?>
        <style>
        .elementor-widget-enhanced_odds_widget .match-tab.active {
            background: <?php echo $primary_color; ?> !important;
            border-color: <?php echo $primary_color; ?> !important;
        }
        .elementor-widget-enhanced_odds_widget .match-card {
            border-radius: <?php echo $border_radius; ?> !important;
        }
        .elementor-widget-enhanced_odds_widget .odd-item:hover {
            border-color: <?php echo $primary_color; ?> !important;
        }
        .elementor-widget-enhanced_odds_widget .live-indicator {
            background: <?php echo $success_color; ?> !important;
        }
        </style>
        
        <div class="enhanced-odds-widget" 
             data-sport="<?php echo esc_attr($sport); ?>"
             data-regions="<?php echo esc_attr($regions); ?>"
             data-markets="<?php echo esc_attr($markets); ?>"
             data-currency="<?php echo esc_attr($currency); ?>"
             data-locale="<?php echo esc_attr($locale); ?>"
             data-timezone="<?php echo esc_attr($timezone); ?>"
             data-refresh="<?php echo esc_attr($settings['auto_refresh']['size']); ?>">
            
            <?php 
            echo do_shortcode('[sports_odds sport="' . $sport . '" regions="' . $regions . '" markets="' . $markets . '" limit="' . $limit . '"]');
            ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Custom refresh interval for this widget
            const refreshMinutes = <?php echo $settings['auto_refresh']['size']; ?>;
            const widget = $('.enhanced-odds-widget');
            
            if (refreshMinutes > 0) {
                setInterval(function() {
                    // Trigger refresh for this specific widget
                    refreshWidgetOdds(widget);
                }, refreshMinutes * 60 * 1000);
            }
            
            function refreshWidgetOdds(widget) {
                const sport = widget.data('sport');
                const regions = widget.data('regions');
                const markets = widget.data('markets');
                
                // Add your refresh logic here
                console.log('Refreshing odds for widget:', sport, regions, markets);
            }
        });
        </script>
        <?php
    }
}

// Register the widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Enhanced_Odds_Elementor_Widget());