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

    private $postTypeManager;

    protected function setUp(): void {
        parent::setUp();
        require_once VEU_DIRECTORY_PATH . '/inc/post-type-manager/package/class.post-type-manager.php';
        $this->postTypeManager = new VK_Post_Type_Manager();
        // Mock any necessary WordPress functions here
    }

    public function testMenuIconOutput() {
        // Assuming there's a method to get the menu icon form HTML
        $output = $this->postTypeManager->get_menu_icon_html();
        $this->assertStringContainsString('dashicons-admin-post', $output, 'The output should contain a default dashicon option.');
    }

    protected function tearDown(): void {
        parent::tearDown();
        // Clean up here
    }
}
