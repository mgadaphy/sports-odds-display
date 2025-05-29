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
                'default' => ['soccer_epl', 'soccer_uefa_champs_league', 'soccer_usa_mls'], // Updated default leagues
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

        // Custom League Titles Section
        $this->start_controls_section(
            'custom_league_titles_section',
            [
                'label' => __('Custom League Titles', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Add a text control for each league to allow custom titles
        $this->add_control(
            'custom_title_soccer_epl',
            [
                'label' => __('English Premier League Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('English Premier League', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_spain_la_liga',
            [
                'label' => __('Spanish La Liga Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Spanish La Liga', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_germany_bundesliga',
            [
                'label' => __('German Bundesliga Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('German Bundesliga', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_italy_serie_a',
            [
                'label' => __('Italian Serie A Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Italian Serie A', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_france_ligue_one',
            [
                'label' => __('French Ligue 1 Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('French Ligue 1', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_usa_mls',
            [
                'label' => __('Major League Soccer (MLS) Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Major League Soccer (MLS)', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_uefa_champs_league',
            [
                'label' => __('UEFA Champions League Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('UEFA Champions League', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_uefa_europa_league',
            [
                'label' => __('UEFA Europa League Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('UEFA Europa League', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_africa_cup_of_nations',
            [
                'label' => __('Africa Cup of Nations Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Africa Cup of Nations', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_soccer_cameroon_league',
            [
                'label' => __('Cameroon Elite One Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Cameroon Elite One', 'textdomain'),
            ]
        );
        // American Sports
         $this->add_control(
            'custom_title_americanfootball_nfl',
            [
                'label' => __('NFL Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('NFL', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_basketball_nba',
            [
                'label' => __('NBA Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('NBA', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_baseball_mlb',
            [
                'label' => __('MLB Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('MLB', 'textdomain'),
            ]
        );
        // Other Sports
         $this->add_control(
            'custom_title_tennis_atp_us_open',
            [
                'label' => __('ATP US Open Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('ATP US Open', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_tennis_atp_wimbledon',
            [
                'label' => __('ATP Wimbledon Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('ATP Wimbledon', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_tennis_atp_french_open',
            [
                'label' => __('ATP French Open Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('ATP French Open', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_tennis_atp_aus_open_singles',
            [
                'label' => __('ATP Australian Open Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('ATP Australian Open', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_tennis_wta_us_open',
            [
                'label' => __('WTA US Open Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('WTA US Open', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_tennis_wta_wimbledon',
            [
                'label' => __('WTA Wimbledon Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('WTA Wimbledon', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_tennis_wta_french_open',
            [
                'label' => __('WTA French Open Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('WTA French Open', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_tennis_wta_aus_open_singles',
            [
                'label' => __('WTA Australian Open Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('WTA Australian Open', 'textdomain'),
            ]
        );
         $this->add_control(
            'custom_title_golf_pga_championship_winner',
            [
                'label' => __('PGA Championship Winner Title', 'textdomain'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('PGA Championship Winner', 'textdomain'),
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

        // Filtering Section
        $this->start_controls_section(
            'filtering_section',
            [
                'label' => __('Filtering', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_date_filter',
            [
                'label' => __('Enable Date Filtering', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'textdomain'),
                'label_off' => __('No', 'textdomain'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'filter_date',
            [
                'label' => __('Select Date', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DATE_TIME,
                'picker_options' => [
                    'enableTime' => false,
                ],
                'condition' => [
                    'enable_date_filter' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Container
        $this->start_controls_section(
            'hot_games_style_container_section',
            [
                'label' => __('Container', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hot_games_container_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sports-hot-games-container' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'hot_games_container_border',
                'selector' => '{{WRAPPER}} .sports-hot-games-container',
            ]
        );

        $this->add_control(
            'hot_games_container_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sports-hot-games-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hot_games_container_box_shadow',
                'selector' => '{{WRAPPER}} .sports-hot-games-container',
            ]
        );

        $this->add_responsive_control(
            'hot_games_container_padding',
            [
                'label' => __('Padding', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sports-hot-games-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Header
        $this->start_controls_section(
            'hot_games_style_header_section',
            [
                'label' => __('Header', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hot_games_header_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-games-header' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'hot_games_header_title_color',
            [
                'label' => __('Title Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-games-header h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_header_title_typography',
                'label' => __('Title Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-games-header h3',
            ]
        );

        $this->end_controls_section();

        // Style Section - Sport Section
        $this->start_controls_section(
            'hot_games_style_sport_section',
            [
                'label' => __('Sport Section', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hot_games_sport_title_color',
            [
                'label' => __('Sport Title Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-games-sport-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_sport_title_typography',
                'label' => __('Sport Title Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-games-sport-title',
            ]
        );

        // Add alignment control for sport title
        $this->add_responsive_control(
            'hot_games_sport_title_align',
            [
                'label' => __('Alignment', 'textdomain'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'textdomain'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'textdomain'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'textdomain'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .hot-games-sport-title' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // Add margin control for sport section
        $this->add_responsive_control(
            'hot_games_sport_section_margin',
            [
                'label' => __('Section Margin', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-games-sport-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Add padding control for sport section
        $this->add_responsive_control(
            'hot_games_sport_section_padding',
            [
                'label' => __('Section Padding', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-games-sport-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Match Cards
        $this->start_controls_section(
            'hot_games_style_cards_section',
            [
                'label' => __('Match Cards', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hot_games_card_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'hot_games_card_border',
                'selector' => '{{WRAPPER}} .hot-game-match',
            ]
        );

        $this->add_control(
            'hot_games_card_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'hot_games_card_box_shadow',
                'selector' => '{{WRAPPER}} .hot-game-match',
            ]
        );

        $this->add_responsive_control(
            'hot_games_card_padding',
            [
                'label' => __('Padding', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Add margin control for match cards
        $this->add_responsive_control(
            'hot_games_card_margin',
            [
                'label' => __('Margin', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Teams
        $this->start_controls_section(
            'hot_games_style_teams_section',
            [
                'label' => __('Teams', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hot_games_team_text_color',
            [
                'label' => __('Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .home-team, {{WRAPPER}} .hot-game-match .away-team' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_team_typography',
                'label' => __('Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-game-match .home-team, {{WRAPPER}} .hot-game-match .away-team',
            ]
        );

        // Add alignment control for teams block
        $this->add_responsive_control(
            'hot_games_teams_align',
            [
                'label' => __('Alignment', 'textdomain'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'textdomain'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'textdomain'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'textdomain'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .teams' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hot_games_vs_text_color',
            [
                'label' => __('VS Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .vs' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_vs_typography',
                'label' => __('VS Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-game-match .vs',
            ]
        );

        $this->add_control(
            'hot_games_match_time_color',
            [
                'label' => __('Match Time Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .match-time' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_match_time_typography',
                'label' => __('Match Time Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-game-match .match-time',
            ]
        );

        // Add padding and margin for match time
        $this->add_responsive_control(
            'hot_games_match_time_padding',
            [
                'label' => __('Match Time Padding', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .match-time' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

         $this->add_responsive_control(
            'hot_games_match_time_margin',
            [
                'label' => __('Match Time Margin', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .match-time' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Add background color, border, and border radius for match time
        $this->add_control(
            'hot_games_match_time_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .match-time' => 'background-color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'hot_games_match_time_border',
                'selector' => '{{WRAPPER}} .hot-game-match .match-time',
            ]
        );

        $this->add_control(
            'hot_games_match_time_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .match-time' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Add alignment control for match time
        $this->add_responsive_control(
            'hot_games_match_time_align',
            [
                'label' => __('Alignment', 'textdomain'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'textdomain'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'textdomain'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'textdomain'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .match-time' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Odds
        $this->start_controls_section(
            'hot_games_style_odds_section',
            [
                'label' => __('Odds', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Add controls for bookmaker name styling
        $this->add_control(
            'hot_games_bookmaker_name_color',
            [
                'label' => __('Bookmaker Name Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .bookmaker-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_bookmaker_name_typography',
                'label' => __('Bookmaker Name Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-game-match .bookmaker-name',
            ]
        );

        $this->add_control(
            'hot_games_odd_item_background_color',
            [
                'label' => __('Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .odd-item' => 'background-color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'hot_games_odd_item_hover_background_color',
            [
                'label' => __('Hover Background Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .odd-item:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'hot_games_odd_outcome_color',
            [
                'label' => __('Outcome Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .outcome-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'hot_games_odd_value_color',
            [
                'label' => __('Odds Value Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .odd-value' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_odd_outcome_typography',
                'label' => __('Outcome Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-game-match .outcome-name',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_odd_value_typography',
                'label' => __('Odds Value Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .hot-game-match .odd-value',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'hot_games_odd_item_border',
                'selector' => '{{WRAPPER}} .hot-game-match .odd-item',
            ]
        );

        $this->add_control(
            'hot_games_odd_item_border_radius',
            [
                'label' => __('Border Radius', 'textdomain'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .hot-game-match .odd-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - See More Link
        $this->start_controls_section(
            'hot_games_style_see_more_section',
            [
                'label' => __('See More Link', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hot_games_see_more_color',
            [
                'label' => __('Text Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .see-more-link a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hot_games_see_more_typography',
                'label' => __('Typography', 'textdomain'),
                'selector' => '{{WRAPPER}} .see-more-link a',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'hot_games_see_more_text_shadow',
                'selector' => '{{WRAPPER}} .see-more-link a',
            ]
        );

        $this->add_control(
            'hot_games_see_more_hover_color',
            [
                'label' => __('Hover Color', 'textdomain'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .see-more-link a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Dark Mode
        $this->start_controls_section(
            'hot_games_style_dark_mode_section',
            [
                'label' => __('Dark Mode', 'textdomain'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hot_games_dark_mode',
            [
                'label' => __('Enable Dark Mode', 'textdomain'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'textdomain'),
                'label_off' => __('No', 'textdomain'),
                'return_value' => 'yes',
                'default' => 'no',
                'selectors' => [
                    '{{WRAPPER}} .sports-hot-games-container' => '{{value}}',
                ],
                'selectors_dictionary' => [
                    'yes' => 'filter: invert(1) hue-rotate(180deg);', // Example dark mode effect, adjust as needed
                    'no' => 'filter: none;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $leagues = is_array($settings['leagues']) ? $settings['leagues'] : array();
        $limit = intval($settings['limit']);
        $bookmakers = isset($settings['bookmakers']) && is_array($settings['bookmakers']) ? implode(',', $settings['bookmakers']) : '';
        $see_more_url = $settings['see_more_link']['url'] ?? '';
        $dark_mode_class = isset($settings['dark_mode']) && $settings['dark_mode'] === 'yes' ? 'dark-mode' : '';
        $refresh_interval = intval($settings['auto_refresh_interval'] ?? 5);

        // Default regions and markets for hot games
        $regions = 'uk,eu';
        $markets = 'h2h';

        $hot_games_data = fetch_odds_for_leagues($leagues, $regions, $markets);

        // Handle fetching errors
        if (isset($hot_games_data['error'])) {
            echo '<div class="odds-error">' . esc_html($hot_games_data['error']) . '</div>';
            return;
        }

        // Handle warnings if any
        $warnings = isset($hot_games_data['warnings']) ? $hot_games_data['warnings'] : array();
        $data = isset($hot_games_data['data']) ? $hot_games_data['data'] : $hot_games_data;

        // Apply limit and bookmaker filtering
        if (!empty($data)) {
            // Filter by selected bookmakers
            $allowed_bookmakers = is_array($settings['bookmakers']) ? $settings['bookmakers'] : array();
            $filter_bookmakers = !empty($allowed_bookmakers);

            if ($filter_bookmakers) {
                $filtered_data = [];
                foreach ($data as $match) {
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
                $data = $filtered_data;
            }

            // Apply the limit
            $data = array_slice($data, 0, $limit);
        }

        ?>
        <div class="sports-hot-games-container sports-odds-container <?php echo esc_attr($dark_mode_class); ?>"
            data-leagues="<?php echo esc_attr(implode(',', $leagues)); ?>"
            data-bookmakers="<?php echo esc_attr($bookmakers); ?>"
            data-limit="<?php echo esc_attr($limit); ?>"
            data-refresh-interval="<?php echo esc_attr($refresh_interval); ?>"
            data-enable-date-filter="<?php echo esc_attr($settings['enable_date_filter'] ?? 'no'); ?>"
            data-filter-date="<?php echo esc_attr($settings['filter_date'] ?? ''); ?>">
            
            <div class="hot-games-header odds-header">
                <h3><?php echo __('Upcoming Hot Games', 'textdomain'); ?></h3>
            </div>

            <?php if (!empty($warnings)): ?>
                <div class="odds-warning">
                    <?php foreach ($warnings as $warning): ?>
                        <p><?php echo esc_html($warning); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($settings['enable_date_filter']) && $settings['enable_date_filter'] === 'yes'): ?>
                <div class="hot-games-date-filter">
                    <label for="hot-games-filter-date-<?php echo $this->get_id(); ?>"><?php echo __('Filter by Date:', 'textdomain'); ?></label>
                    <input type="date" id="hot-games-filter-date-<?php echo $this->get_id(); ?>" class="hot-games-filter-date-input" value="<?php echo esc_attr($settings['filter_date'] ?? ''); ?>">
                </div>
            <?php endif; ?>

            <div class="hot-games-list odds-matches">
                <?php
                $enhanced_settings = get_option('enhanced_odds_settings', array(
                    'locale' => 'en',
                    'timezone' => 'Africa/Douala',
                    'currency' => 'XAF'
                ));

                if (!empty($data)) {
                    // Group matches by sport
                    $matches_by_sport = [];
                    foreach ($data as $match) {
                        $sport_key = $match['sport'] ?? 'unknown_sport';
                        if (!isset($matches_by_sport[$sport_key])) {
                            $matches_by_sport[$sport_key] = [];
                        }
                        $matches_by_sport[$sport_key][] = $match;
                    }

                    // Get the full list of league options to display human-readable names
                    $league_names = [
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
                        'americanfootball_nfl' => __('NFL', 'textdomain'),
                        'basketball_nba' => __('NBA', 'textdomain'),
                        'baseball_mlb' => __('MLB', 'textdomain'),
                        'tennis_atp_us_open' => __('ATP US Open', 'textdomain'),
                        'tennis_atp_wimbledon' => __('ATP Wimbledon', 'textdomain'),
                        'tennis_atp_french_open' => __('ATP French Open', 'textdomain'),
                        'tennis_atp_aus_open_singles' => __('ATP Australian Open', 'textdomain'),
                        'tennis_wta_us_open' => __('WTA US Open', 'textdomain'),
                        'tennis_wta_wimbledon' => __('WTA Wimbledon', 'textdomain'),
                        'tennis_wta_french_open' => __('WTA French Open', 'textdomain'),
                        'tennis_wta_aus_open_singles' => __('WTA Australian Open', 'textdomain'),
                        'golf_pga_championship_winner' => __('PGA Championship Winner', 'textdomain'),
                    ];

                    foreach ($matches_by_sport as $sport_key => $matches) {
                        // Find the human-readable name for the sport key, default to cleaned key if not found
                        $sport_name = $league_names[$sport_key] ?? ucfirst(str_replace(['_', 'soccer', 'americanfootball', 'basketball', 'baseball', 'tennis', 'golf'], [' ', '', '', '', '', '', ''], $sport_key));

                        // Check for custom title
                        $custom_title_setting_key = 'custom_title_' . $sport_key;
                        if (!empty($settings[$custom_title_setting_key])) {
                            $sport_name = $settings[$custom_title_setting_key];
                        }

                        ?>
                        <div class="hot-games-sport-section">
                            <h4 class="hot-games-sport-title"><?php echo esc_html($sport_name); ?></h4>
                            <?php
                            foreach ($matches as $match) {
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
                            ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="odds-no-data">' . __('No hot games available at the moment.', 'textdomain') . '</div>';
                }
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