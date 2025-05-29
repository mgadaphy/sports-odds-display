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
                'options' => [
                    // Soccer Leagues
                    'soccer_epl' => __('English Premier League', 'textdomain'),
                    'soccer_spain_la_liga' => __('Spanish La Liga', 'textdomain'),
                    'soccer_germany_bundesliga' => __('German Bundesliga', 'textdomain'),
                    'soccer_italy_serie_a' => __('Italian Serie A', 'textdomain'),
                    'soccer_france_ligue_one' => __('French Ligue 1', 'textdomain'),
                    'soccer_usa_mls' => __('Major League Soccer (MLS)', 'textdomain'),
                    'soccer_uefa_champs_league' => __('UEFA Champions League', 'textdomain'),
                    'soccer_uefa_europa_league' => __('UEFA Europa League', 'textdomain'),
                    'soccer_africa_cup_of_nations' => __('Africa Cup of Nations', 'textdomain'),
                    'soccer_cameroon_league' => __('Cameroon Elite One (Likely Unsupported)', 'textdomain'),
                    
                    // American Sports
                    'americanfootball_nfl' => __('NFL', 'textdomain'),
                    'basketball_nba' => __('NBA', 'textdomain'),
                    'baseball_mlb' => __('MLB', 'textdomain'),
                    
                    // Other Sports
                    'tennis_atp_us_open' => __('ATP US Open', 'textdomain'),
                    'tennis_atp_wimbledon' => __('ATP Wimbledon', 'textdomain'),
                    'tennis_atp_french_open' => __('ATP French Open', 'textdomain'),
                    'tennis_atp_aus_open_singles' => __('ATP Australian Open', 'textdomain'),
                    'tennis_wta_us_open' => __('WTA US Open', 'textdomain'),
                    'tennis_wta_wimbledon' => __('WTA Wimbledon', 'textdomain'),
                    'tennis_wta_french_open' => __('WTA French Open', 'textdomain'),
                    'tennis_wta_aus_open_singles' => __('WTA Australian Open', 'textdomain'),
                    'golf_pga_championship_winner' => __('PGA Championship Winner', 'textdomain'), // Note: Likely for winner odds, not individual matches
                ],
                'default' => 'soccer_epl',
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
                    'DraftKings' => __('DraftKings', 'textdomain'),
                    'FanDuel' => __('FanDuel', 'textdomain'),
                    'BetMGM' => __('BetMGM', 'textdomain'),
                    'Caesars' => __('Caesars', 'textdomain'),
                    'PointsBet' => __('PointsBet', 'textdomain'),
                    // Add more bookmakers as needed
                ],
                'default' => ['1xBet'], // Default to 1xBet
                'description' => __('Select the bookmakers whose odds you want to display.', 'textdomain'),
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

        // Style Section - Container
        $this->start_controls_section(
            'style_container_section',
            [
                'label' => __('Container', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'container_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sports-odds-container' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .sports-odds-container',
            ]
        );

        $this->add_control(
            'container_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sports-odds-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .sports-odds-container',
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sports-odds-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Header
        $this->start_controls_section(
            'style_header_section',
            [
                'label' => __('Header', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'header_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .odds-header' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'header_title_color',
            [
                'label' => __('Title Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .odds-header h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'header_title_typography',
                'label' => __('Title Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .odds-header h3',
            ]
        );

        $this->end_controls_section();

        // Style Section - Match Tabs
        $this->start_controls_section(
            'style_tabs_section',
            [
                'label' => __('Match Tabs', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'tab_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .match-tab' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'tab_active_background_color',
            [
                'label' => __('Active Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .match-tab.active' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'tab_text_color',
            [
                'label' => __('Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .match-tab' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'tab_active_text_color',
            [
                'label' => __('Active Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .match-tab.active' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tab_typography',
                'label' => __('Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .match-tab',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'tab_border',
                'selector' => '{{WRAPPER}} .match-tab',
            ]
        );

        $this->add_control(
            'tab_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .match-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Match Cards
        $this->start_controls_section(
            'style_cards_section',
            [
                'label' => __('Match Cards', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .match-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .match-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .match-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .match-card',
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Padding', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .match-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Teams
        $this->start_controls_section(
            'style_teams_section',
            [
                'label' => __('Teams', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'team_text_color',
            [
                'label' => __('Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .home-team, {{WRAPPER}} .away-team' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'team_typography',
                'label' => __('Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .home-team, {{WRAPPER}} .away-team',
            ]
        );

        $this->add_control(
            'vs_text_color',
            [
                'label' => __('VS Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .vs' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'vs_typography',
                'label' => __('VS Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .vs',
            ]
        );

        $this->add_control(
            'match_time_color',
            [
                'label' => __('Match Time Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .match-time' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'match_time_typography',
                'label' => __('Match Time Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .match-time',
            ]
        );

        $this->end_controls_section();

        // Style Section - Odds
        $this->start_controls_section(
            'style_odds_section',
            [
                'label' => __('Odds', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'odd_item_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .odd-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'odd_item_hover_background_color',
            [
                'label' => __('Hover Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .odd-item:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'odd_outcome_color',
            [
                'label' => __('Outcome Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .outcome-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'odd_value_color',
            [
                'label' => __('Odds Value Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .odd-value' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'odd_outcome_typography',
                'label' => __('Outcome Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .outcome-name',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'odd_value_typography',
                'label' => __('Odds Value Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .odd-value',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'odd_item_border',
                'selector' => '{{WRAPPER}} .odd-item',
            ]
        );

        $this->add_control(
            'odd_item_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .odd-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Footer
        $this->start_controls_section(
            'style_footer_section',
            [
                'label' => __('Footer', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'footer_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .odds-footer' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'footer_text_color',
            [
                'label' => __('Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .odds-footer p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'footer_typography',
                'label' => __('Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .odds-footer p',
            ]
        );

        $this->add_control(
            'branding_text_color',
            [
                'label' => __('Branding Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .scorido-branding' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'branding_typography',
                'label' => __('Branding Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .scorido-branding',
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
        $dark_mode_class = isset($settings['dark_mode']) && $settings['dark_mode'] === 'yes' ? 'dark-mode' : '';
        $bookmakers = is_array($settings['bookmakers']) ? implode(',', $settings['bookmakers']) : '';
        
        echo '<div class="sports-odds-container ' . esc_attr($dark_mode_class) . '">';
        echo do_shortcode('[sports_odds sport="' . esc_attr($sport) . '" regions="' . esc_attr($regions) . '" markets="' . esc_attr($markets) . '" limit="' . esc_attr($limit) . '" bookmakers="' . esc_attr($bookmakers) . '"]');
        echo '</div>';
    }
}

// Register the widget
function register_sports_odds_widget() {
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Sports_Odds_Elementor_Widget());
}
add_action('elementor/widgets/widgets_registered', 'register_sports_odds_widget'); 