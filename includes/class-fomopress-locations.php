<?php

class FomoPress_Locations {

    public static function locations( $type = 'global' ) {
        $locations = array(
			'is_front_page'  => __( 'Front page', 'fomopress' ),
			'is_home'        => __( 'Blog page', 'fomopress' ),
			'is_singular'    => __( 'All posts, pages and custom post types', 'fomopress' ),
			'is_single'      => __( 'All posts', 'fomopress' ),
			'is_page'        => __( 'All pages', 'fomopress' ),
			'is_attachment'  => __( 'All attachments', 'fomopress' ),
			'is_search'      => __( 'Search results', 'fomopress' ),
			'is_404'         => __( '404 error page', 'fomopress' ),
			'is_archive'     => __( 'All archives', 'fomopress' ),
			'is_category'    => __( 'All category archives', 'fomopress' ),
			'is_tag'         => __( 'All tag archives', 'fomopress' ),
		);

        if ( 'global' == $type ) {
            return $locations;
        }

        $locations = array();
        $post_types = FomoPress_Helper::post_types();
        $taxonomies = FomoPress_Helper::taxonomies();

        if ( ! empty( $post_types ) ) {

            unset( $post_types['post'] );
            unset( $post_types['page'] );

            foreach ( $post_types as $slug => $type ) {

                $locations[ 'is_singular-' . $slug ] = sprintf( __('All %s posts', 'fomopress'), '<strong>' . $type->label . '</strong>' );

                if ( $type->has_archive ) {
                    $locations[ 'is_archive-' . $slug ] = sprintf( __('All %s archives', 'fomopress'), '<strong>' . $type->label . '</strong>' );
                }
            }

            foreach ( $taxonomies as $slug => $tax ) {
                $locations[ 'is_tax-' . $slug ] = sprintf( __('All %s taxonomy archives', 'fomopress'), '<strong>' . $tax->label . '</strong>' );
            }
        }

        return $locations;
    }

    public static function check_location( $locations = array() ) {
        if ( empty( $locations ) ) {
            return true;
        }

        $status = array(
			'is_front_page'  => is_front_page(),
			'is_home'        => is_home(),
			'is_singular'    => is_singular(),
			'is_single'      => is_singular( 'post' ),
			'is_page'        => ( is_page() && ! is_front_page() ),
			'is_attachment'  => is_attachment(),
			'is_search'      => is_search(),
			'is_404'         => is_404(),
			'is_archive'     => is_archive(),
			'is_category'    => is_category(),
			'is_tag'         => is_tag(),
		);

        $post_types = FomoPress_Helper::post_types();
        $taxonomies = FomoPress_Helper::taxonomies();

        if ( ! empty( $post_types ) ) {

            unset( $post_types['post'] );
            unset( $post_types['page'] );

            foreach ( $post_types as $slug => $type ) {

                $status[ 'is_singular-' . $slug ] = is_singular( $slug );

                if ( $type->has_archive ) {
                    $locations[ 'is_archive-' . $slug ] = is_post_type_archive( $slug );
                }
            }

            foreach ( $taxonomies as $slug => $tax ) {
                $locations[ 'is_tax-' . $slug ] = is_tax( $slug );
            }
        }

        $status_flag = false;

        foreach ( $locations as $location ) {
            if ( ! isset( $status[$location] ) || ! $status[$location] ) {
                continue;
            }
            else {
                $status_flag = true;
            }
        }

        return $status_flag;
    }
}