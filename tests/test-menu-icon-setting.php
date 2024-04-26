<?php
/**
 * Class MenuIconTest
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/**
 * Menu Icon Test
 */
class MenuIconTest extends WP_UnitTestCase {

    private function get_menu_icon_html() {
        $icon = get_option('veu_menu_icon', 'dashicons-admin-post'); // ここで使用するオプション名を適切なものに変更してください
        return '<span class="dashicons ' . esc_attr($icon) . '"></span>';
    }

    public function testMenuIconOutput() {
        update_option('veu_menu_icon', 'dashicons-admin-generic');
        $output = $this->get_menu_icon_html();
        $this->assertStringContainsString('dashicons-admin-generic', $output, 'The output should contain the correct dashicon class.');
    }

    protected function tearDown(): void {
        delete_option('veu_menu_icon');
        parent::tearDown();
    }
}
