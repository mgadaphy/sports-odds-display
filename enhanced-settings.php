<?php
/**
 * Enhanced Settings Handler for Sports Odds Display
 */

class Enhanced_Odds_Settings {
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_settings_page'));
    }
    
    public function register_settings() {
        register_setting('enhanced_odds_settings', 'enhanced_odds_settings');
        
        add_settings_section(
            'enhanced_odds_section',
            __('Enhanced Display Settings', 'textdomain'),
            null,
            'enhanced_odds_settings'
        );
        
        // Add settings fields
        $this->add_settings_fields();
    }
    
    private function add_settings_fields() {
        $fields = array(
            'primary_color' => array(
                'label' => __('Primary Color', 'textdomain'),
                'type' => 'color',
                'default' => '#007bff'
            ),
            'secondary_color' => array(
                'label' => __('Secondary Color', 'textdomain'),
                'type' => 'color',
                'default' => '#6c757d'
            ),
            'success_color' => array(
                'label' => __('Success Color', 'textdomain'),
                'type' => 'color',
                'default' => '#28a745'
            ),
            'border_radius' => array(
                'label' => __('Border Radius', 'textdomain'),
                'type' => 'text',
                'default' => '12px'
            ),
            'currency' => array(
                'label' => __('Currency', 'textdomain'),
                'type' => 'select',
                'options' => array(
                    'XAF' => 'Central African CFA Franc (XAF)',
                    'USD' => 'US Dollar (USD)',
                    'EUR' => 'Euro (EUR)',
                    'GBP' => 'British Pound (GBP)'
                ),
                'default' => 'XAF'
            ),
            'locale' => array(
                'label' => __('Language', 'textdomain'),
                'type' => 'select',
                'options' => array(
                    'en' => 'English',
                    'fr' => 'French'
                ),
                'default' => 'en'
            ),
            'timezone' => array(
                'label' => __('Timezone', 'textdomain'),
                'type' => 'select',
                'options' => array(
                    'Africa/Douala' => 'Cameroon Time (WAT)',
                    'UTC' => 'UTC',
                    'Europe/London' => 'London Time (GMT/BST)'
                ),
                'default' => 'Africa/Douala'
            ),
            'show_live_indicator' => array(
                'label' => __('Show Live Indicator', 'textdomain'),
                'type' => 'checkbox',
                'default' => true
            )
        );
        
        foreach ($fields as $field_id => $field) {
            add_settings_field(
                $field_id,
                $field['label'],
                array($this, 'render_field'),
                'enhanced_odds_settings',
                'enhanced_odds_section',
                array(
                    'field_id' => $field_id,
                    'field_type' => $field['type'],
                    'options' => isset($field['options']) ? $field['options'] : null,
                    'default' => $field['default']
                )
            );
        }
    }
    
    public function render_field($args) {
        $options = get_option('enhanced_odds_settings');
        $value = isset($options[$args['field_id']]) ? $options[$args['field_id']] : $args['default'];
        
        switch ($args['field_type']) {
            case 'color':
                echo '<input type="color" name="enhanced_odds_settings[' . $args['field_id'] . ']" value="' . esc_attr($value) . '">';
                break;
                
            case 'select':
                echo '<select name="enhanced_odds_settings[' . $args['field_id'] . ']">';
                foreach ($args['options'] as $key => $label) {
                    echo '<option value="' . esc_attr($key) . '" ' . selected($value, $key, false) . '>' . esc_html($label) . '</option>';
                }
                echo '</select>';
                break;
                
            case 'checkbox':
                echo '<input type="checkbox" name="enhanced_odds_settings[' . $args['field_id'] . ']" value="1" ' . checked($value, true, false) . '>';
                break;
                
            default:
                echo '<input type="text" name="enhanced_odds_settings[' . $args['field_id'] . ']" value="' . esc_attr($value) . '">';
        }
    }
    
    public function add_settings_page() {
        add_submenu_page(
            'sports_odds',
            __('Enhanced Display Settings', 'textdomain'),
            __('Enhanced Display', 'textdomain'),
            'manage_options',
            'enhanced_odds_settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
            <form action="options.php" method="post">
                <?php
                settings_fields('enhanced_odds_settings');
                do_settings_sections('enhanced_odds_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the settings
Enhanced_Odds_Settings::get_instance(); 