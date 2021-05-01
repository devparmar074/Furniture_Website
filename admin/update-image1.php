= $submenu[ 'yith_plugin_panel' ];

                usort( $sorted_plugins, function ( $a, $b ) {
                    return strcmp( current( $a ), current( $b ) );
                } );

                $submenu[ 'yith_plugin_panel' ] = $sorted_plugins;
            }
        }

        /**
         * add menu class in YITH Plugins menu
         *
         * @since    3.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public static function add_menu_class_in_yith_plugin( $menu ) {
            global $submenu;

            if ( !empty( $submenu[ 'yith_plugin_panel' ] ) ) {
                $item_count = count( $submenu[ 'yith_plugin_panel' ] );
                $columns    = absint( $item_count / 20 ) + 1;
                $columns    = max( 1, min( $columns, 3 ) );
                $columns    = apply_filters( 'yith_plugin_fw_yith_plugins_menu_columns', $columns, $item_count );

                if ( $columns > 1 ) {
                    $class = "yith-plugin-fw-menu-$columns-columns";
                    foreach ( $menu as $order => $top ) {
                        if ( 'yith_plugin_panel' === $top[ 2 ] ) {
                            $c                   = $menu[ $order ][ 4 ];
                            $menu[ $order ][ 4 ] = add_cssclass( $class, $c );
                            break;
                        }
                    }
                }
            }

            return $menu;
        }

        /**
         * Check if inside the admin tab there's the premium tab to
         * check if the plugin is a free or not
         *
         * @author Emanuela Castorina
         */
        function is_free() {
            return ( !empty( $this->settings[ 'admin-tabs' ] ) && isset( $this->settings[ 'admin-tabs' ][ 'premium' ] ) );
        }

        /**
         * Add plugin banner
         */
        public function add_plugin_banner( $page ) {

            if ( $page != $this->settings[ 'page' ] || !isset( $this->settings[ 'class' ] ) ) {
                return;
            }

            if ( $this->is_free() && isset( $this->settings[ 'plugin_slug' ] ) ):
                $rate_link = apply_filters( 'yith_plugin_fw_rate_url', 'https://wordpress.org/support/plugin/' . $this->settings[ 'plugin_slug' ] . '/reviews/?rate=5#new-post' );
                ?>
                <h1 class="notice-container"></h1>
                <div class="yith-plugin-fw-banner">
                    <h1><?php echo esc_html( $this->settings[ 'page_title' ] ) ?></h1>
                </div>
                <div class="yith-plugin-fw-rate">
	                <?php printf('<strong>%s</strong> %s <a href="%s" target="_blank"><u>%s</u> <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a>  %s',
                    __('We need your support','yith-plugin-fw'),
                     __('to keep updating and improving the plugin. Please,','yith-plugin-fw'),
                     $rate_link,
                     __('help us by leaving a five-star rating','yith-plugin-fw' ),
                     __(':) Thanks!','yith-plugin-fw' ) )?>
                    </div>
            <?php else: ?>
                <h1 class="notice-container"></h1>
                <div class="yith-plugin-fw-banner">
                    <h1><?php echo esc_html( $this->settings[ 'page_title' ] ) ?></h1>
                </div>
            <?php endif ?>
            <?php
        }

        /**
         * Add additional element after print the field.
         *
         * @since  3.2
         * @author Emanuela Castorina
         */
        public function add_yith_ui( $field ) {
            global $pagenow;

            $screen = function_exists('get_current_screen') ? get_current_screen() : false;

            if ( empty( $this->settings[ 'class' ] ) || !isset( $field[ 'type' ] ) ) {
                return;
            }
            if ( 'admin.php' === $pagenow && $screen && strpos( $screen->id, $this->settings[ 'page' ] ) !== false ) {
                switch ( $field[ 'type' ] ) {
                    case 'datepicker':
                        echo '<span class="yith-icon icon-calendar"></span>';
                        break;
                    default:
                        break;
                }
            }
        }


        public function get_post_type_tabs( $post_type ) {
            $tabs = array();

            foreach ( $this->get_tabs_hierarchy() as $key => $info ) {
                if ( isset( $info[ 'type' ], $info[ 'post_type' ] ) && 'post_type' === $info[ 'type' ] && $post_type === $info[ 'post_type' ] ) {
                    if ( !empty( $info[ 'parent' ] ) ) {
                        $tab