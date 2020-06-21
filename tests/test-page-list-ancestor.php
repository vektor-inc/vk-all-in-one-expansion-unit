<?php
/**
 * Class PageListAncestorTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * PageListAncestor test case.
 */

class PageListAncestorTest extends WP_UnitTestCase {

    public function test_display_shortcode() {
        $id = wp_insert_post([
            'post_title' => 'assert post',
            'post_content' => 'test string',
            'post_type' => 'page'
        ]);

        global $post;
        $post = get_post($id);
        global $wp_query;
        $wp_query->is_page = true;

        // case1
        update_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true );

        $this->assertEquals(
            vkExUnit_pageList_ancestor_contentHook('content'),
            "content\n[pageList_ancestor]"
        );

        // case2
        $wp_query->is_page = false;
        $this->assertEquals(
            vkExUnit_pageList_ancestor_contentHook('content'),
            "content"
        );

        // case3
        $wp_query->is_page = true;
        update_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', false );

        $this->assertEquals(
            vkExUnit_pageList_ancestor_contentHook('content'),
            "content"
        );
    }

    public function test_shortcode() {
        $parent = wp_insert_post([
            'post_title' => 'parent post',
            'post_content' => 'parent string',
            'post_type' => 'page',
            'post_status' => 'publish'
        ]);
        wp_insert_post([
            'post_title' => 'children post',
            'post_parent' => $parent,
            'post_type' => 'page',
            'post_status' => 'publish'
        ]);
        $id = wp_insert_post([
            'post_title' => 'assert post',
            'post_content' => 'test string',
            'post_parent' => $parent,
            'post_type' => 'page',
            'post_status' => 'publish'
        ]);

        global $post;
        $post = get_post($id);

        global $is_pagewidget;
        global $widget_pageid;
        global $wp_query;

        $widget_pageid = $id;

        // case1
        $is_pagewidget = false;
        $wp_query->is_page = true;
        update_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', false );

        $this->assertEquals(
            vkExUnit_pageList_ancestor_shortcode('', false),
            ''
        );

        // case2
        $is_pagewidget = false;
        $wp_query->is_page = false;
        update_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true );

        $this->assertEquals(
            vkExUnit_pageList_ancestor_shortcode('', false),
            ''
        );

        // case3
        $is_pagewidget = false;
        $wp_query->is_page = true;
        update_post_meta( $post->ID, 'vkExUnit_pageList_ancestor', true );

        $this->assertTrue(
            vkExUnit_pageList_ancestor_shortcode('', false) != ''
        );
    }
}
