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

    public function test_block() {
        $first = wp_insert_post(
            array(
                'post_title'   => 'First Page',
                'post_content' => 'First Content',
                'post_type'    => 'page',
                'post_status'  => 'publish',
            )
        );

        $second = wp_insert_post(
            array(
                'post_title'   => 'Second Page',
                'post_content' => 'Second Content',
                'post_parent'  =>  $first,
                'post_type'    => 'page',
                'post_status'  => 'publish',
            )
        );

        $third = wp_insert_post(
            array(
                'post_title'   => 'Third Page',
                'post_content' => 'Third Content',
                'post_parent'  =>  $second,
                'post_type'    => 'page',
                'post_status'  => 'publish',
            )
        );

        $test_array = array(
            'no-class' => array( 
                'attr'    => array(),
                'correct' => '<section class="veu_pageList_ancestor veu_card veu_childPageIndex_block"><div class="veu_card_inner"><h3 class="pageList_ancestor_title veu_card_title"><a href="http://localhost:8889/?page_id=38">First Page</a></h3><ul class="pageList"><li class="page_item page-item-39 page_item_has_children current_page_ancestor current_page_parent"><a href="http://localhost:8889/?page_id=39">Second Page</a><ul class="children"><li class="page_item page-item-40 current_page_item"><a href="http://localhost:8889/?page_id=40">Third Page</a></li></ul></li></ul></div></section>'
            ),
            'empty-class' => array( 
                'attr'    => array(
                    'className' => '',
                ),
                'correct' => '<section class="veu_pageList_ancestor veu_card veu_childPageIndex_block "><div class="veu_card_inner"><h3 class="pageList_ancestor_title veu_card_title"><a href="http://localhost:8889/?page_id=38">First Page</a></h3><ul class="pageList"><li class="page_item page-item-39 page_item_has_children current_page_ancestor current_page_parent"><a href="http://localhost:8889/?page_id=39">Second Page</a><ul class="children"><li class="page_item page-item-40 current_page_item"><a href="http://localhost:8889/?page_id=40">Third Page</a></li></ul></li></ul></div></section>'
            ),
            'class = "aaa"' => array( 
                'attr'    => array(
                    'className' => 'aaa',
                ),
                'correct' => '<section class="veu_pageList_ancestor veu_card veu_childPageIndex_block aaa"><div class="veu_card_inner"><h3 class="pageList_ancestor_title veu_card_title"><a href="http://localhost:8889/?page_id=38">First Page</a></h3><ul class="pageList"><li class="page_item page-item-39 page_item_has_children current_page_ancestor current_page_parent"><a href="http://localhost:8889/?page_id=39">Second Page</a><ul class="children"><li class="page_item page-item-40 current_page_item"><a href="http://localhost:8889/?page_id=40">Third Page</a></li></ul></li></ul></div></section>'
            ),
            'class = "" onmouseover="alert(/XSS/)" style="background:red;""' => array( 
                'attr'    => array(
                    'className' => '" onmouseover="alert(/XSS/)" style="background:red;"',
                ),
                'correct' => '<section class="veu_pageList_ancestor veu_card veu_childPageIndex_block &quot; onmouseover=&quot;alert(/XSS/)&quot; style=&quot;background:red;&quot;"><div class="veu_card_inner"><h3 class="pageList_ancestor_title veu_card_title"><a href="http://localhost:8889/?page_id=38">First Page</a></h3><ul class="pageList"><li class="page_item page-item-39 page_item_has_children current_page_ancestor current_page_parent"><a href="http://localhost:8889/?page_id=39">Second Page</a><ul class="children"><li class="page_item page-item-40 current_page_item"><a href="http://localhost:8889/?page_id=40">Third Page</a></li></ul></li></ul></div></section>'
            ),
            'class = "" onmouseover="alert(/XSS/)" style="background:red;" ' => array( 
                'attr'    => array(
                    'className' => '" onmouseover="alert(/XSS/)" style="background:red;" ',
                ),
                'correct' => '<section class="veu_pageList_ancestor veu_card veu_childPageIndex_block &quot; onmouseover=&quot;alert(/XSS/)&quot; style=&quot;background:red;&quot; "><div class="veu_card_inner"><h3 class="pageList_ancestor_title veu_card_title"><a href="http://localhost:8889/?page_id=38">First Page</a></h3><ul class="pageList"><li class="page_item page-item-39 page_item_has_children current_page_ancestor current_page_parent"><a href="http://localhost:8889/?page_id=39">Second Page</a><ul class="children"><li class="page_item page-item-40 current_page_item"><a href="http://localhost:8889/?page_id=40">Third Page</a></li></ul></li></ul></div></section>'
            ),
        );

        $this->go_to( get_permalink( $third ) );

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'Page List Ancestor Block' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

        foreach ( $test_array as $title => $test ) {
            $return  = str_replace( "'", '"' , str_replace( array( "\r\n", "\r", "\n", "\t" ), "", veu_pageListAncestor_block_callback( $test['attr'] ) ) );
            $correct = $test['correct'];
            print '[' . $title . ']' . PHP_EOL;
            print 'return------------------------------------' . PHP_EOL;
			print $return . PHP_EOL;
			print 'correct------------------------------------' . PHP_EOL;
			print $correct . PHP_EOL;
            $this->assertEquals( $correct, $return );
        }

       
    }
}
