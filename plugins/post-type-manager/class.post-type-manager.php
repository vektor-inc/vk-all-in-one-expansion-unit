<?php 

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

if ( ! class_exists( 'Vk_post_type_manager' ) ) {

    class Vk_post_type_manager {

		/*-------------------------------------------*/
		/*	カスタム投稿タイプ制御用投稿タイプを追加
		/*-------------------------------------------*/
		function add_post_type_post_type_manage() {
			global $vk_post_type_manager_textdomain;
			register_post_type( 'post_type_manage', // カスタム投稿タイプのスラッグ
				array(
					'labels' => array(
						'name' => __( 'Custom Post Type Setting', $vk_post_type_manager_textdomain ),
						'singular_name' => __( 'Custom Post Type Setting', $vk_post_type_manager_textdomain ),
					),
					'public' => false,
			        'show_ui' => true,
			        'show_in_menu' => true,
					'menu_position' => 100,
			        'capability_type' => array('post_type_manage','post_type_manages'),
			        'map_meta_cap' => true,
					'has_archive' => false,
					'menu_icon' => 'dashicons-admin-generic',
					'supports' => array('title')
				)
			);
		}

        /*-------------------------------------------*/
        /*  post_type_manage の編集権限を追加
        /*-------------------------------------------*/
        function add_cap_post_type_manage() {
        	$role = get_role('administrator');
			$post_type_name = 'post_type_manage';
			$role->add_cap( 'add_'.$post_type_name );
			$role->add_cap( 'add_'.$post_type_name.'s' );
			$role->add_cap( 'edit_'.$post_type_name );
			$role->add_cap( 'edit_'.$post_type_name.'s' );
			$role->add_cap( 'edit_published_'.$post_type_name.'s' );
			$role->add_cap( 'edit_others_'.$post_type_name.'s' );
			$role->add_cap( 'delete_'.$post_type_name );
			$role->add_cap( 'delete_'.$post_type_name.'s' );
			$role->add_cap( 'delete_private_'.$post_type_name.'s' );
			$role->add_cap( 'delete_others_'.$post_type_name.'s' );
			$role->add_cap( 'delete_published_'.$post_type_name.'s' );
			$role->add_cap( 'publish_'.$post_type_name.'s' );
			$role->add_cap( 'publish_others_'.$post_type_name.'s' );
        }

        /*-------------------------------------------*/
        /*  meta box を作成
        /*-------------------------------------------*/
        // add meta_box
        function add_meta_box() {
        	global $vk_post_type_manager_textdomain;
            add_meta_box( 'meta_box_post_type_manage', __('Custom Post Type Setting',$vk_post_type_manager_textdomain), array( $this, 'add_meta_box_action' ), 'post_type_manage','normal','high' );
        }
        function add_meta_box_action(){
        	global $vk_post_type_manager_textdomain;
            global $post;

		    //CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__post_type_manager」として設定）
		    wp_nonce_field( wp_create_nonce(__FILE__), 'noncename__post_type_manager' ); 

		    // Post Type ID
            echo '<h4>'.__('Post Type ID(Required)', $vk_post_type_manager_textdomain).'</h4>';
            echo '<p>'.__( '20 characters or less in alphanumeric',$vk_post_type_manager_textdomain).'</p>';
            echo '<input class="form-control" type="text" id="veu_post_type_id" name="veu_post_type_id" value="'.esc_attr($post->veu_post_type_id).'" size="30">';
            echo '<hr>';

            $post_type_items_array = array( 
            	'title'     => __( 'title', $vk_post_type_manager_textdomain ),
				'editor'    => __( 'editor', $vk_post_type_manager_textdomain ),
				'author'    => __( 'author', $vk_post_type_manager_textdomain ),
				'thumbnail' => __( 'thumbnail', $vk_post_type_manager_textdomain ),
				'excerpt'   => __( 'excerpt', $vk_post_type_manager_textdomain ),
				'comments'  => __( 'comments', $vk_post_type_manager_textdomain ),
				'revisions' => __( 'revisions', $vk_post_type_manager_textdomain ),
				);

            // Post Type ID
		    echo '<h4>'.__('Supports(Required)', $vk_post_type_manager_textdomain ).'</h4>';
		    $post_type_items_value = get_post_meta( $post->ID,'veu_post_type_items',true );
		    echo '<ul>';
		    foreach ($post_type_items_array as $key => $label) {
		    	$checked = ( isset( $post_type_items_value[$key] ) && $post_type_items_value[$key] ) ? ' checked':'';
			    echo '<li><label>'.'<input type="checkbox" id="veu_post_type_items['.$key.']" name="veu_post_type_items['.$key.']" value="true"'.$checked.'> '.$label.'</label></li>';
		    }
		    echo '</ul>';

		    echo '<hr>';

		    // Menu position
		   	echo '<h4>'.__('Menu position(optional)', $vk_post_type_manager_textdomain ).'</h4>';
		   	echo '<p>'.__('Please enter a number.', $vk_post_type_manager_textdomain ).'</p>';
            echo '<input class="form-control" type="text" id="veu_menu_position" name="veu_menu_position" value="'.esc_attr($post->veu_menu_position).'" size="30">';

            echo '<hr>';

			// Custom taxonomies
			echo '<h4>'.__('Custom taxonomies(optional)', $vk_post_type_manager_textdomain).'</h4>';
			$taxonomies = array( 'taxonomy_id', 'taxonomy_lavel');
			echo '<table>';
			echo '<tr>';
			echo '<th></th>';
			echo '<th>'.__('Custon taxonomy name(slug)', $vk_post_type_manager_textdomain ).'</th>';
			echo '<th>'.__('Custon taxonomy label', $vk_post_type_manager_textdomain ).'</th>';
			echo '</tr>';
			// foreach ($variable as $key => $value) {
			// 	# code...
			// }
			$taxonomy = get_post_meta( $post->ID, 'veu_taxonomy', true );
			for ($i=1; $i <= 3; $i++) { 
				echo '<tr>';
				$slug = ( isset( $taxonomy[$i]['slug'] ) ) ? $taxonomy[$i]['slug'] : '';
				$label = ( isset( $taxonomy[$i]['label'] ) ) ? $taxonomy[$i]['label'] : '';
				echo '<td>'.$i.'</td>';
				echo '<td><input type="text" id="veu_taxonomy['.$i.'][slug]" name="veu_taxonomy['.$i.'][slug]" value="'.esc_attr($slug).'" size="30"></td>';
				echo '<td><input type="text" id="veu_taxonomy['.$i.'][label]" name="veu_taxonomy['.$i.'][label]" value="'.esc_attr($label).'" size="30"></td>';
				
				echo '</tr>';
			}
			echo '</table>';


			echo '<hr>';

			$taxonomy = array( 
				array ( 'category' => 'カテゴリー' ),
			 );
            
        }

		/*-------------------------------------------*/
		/*  入力された値の保存
		/*-------------------------------------------*/

		function save_cf_value($post_id){
		    global $post;

		    //設定したnonce を取得（CSRF対策）
		    $noncename__post_type_manager = isset($_POST['noncename__post_type_manager']) ? $_POST['noncename__post_type_manager'] : null;

		    //nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
		    if(!wp_verify_nonce($noncename__post_type_manager, wp_create_nonce(__FILE__))) {  
		        return $post_id;
		    }

		    //自動保存ルーチンかどうかチェック。そうだった場合は何もしない（記事の自動保存処理として呼び出された場合の対策）
		    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
		    
		    $fields = array( 'veu_post_type_id','veu_post_type_items','veu_menu_position','veu_taxonomy' );

		    foreach ($fields as $key => $field) {
			    $field_value = ( isset( $_POST[$field] ) ) ? $_POST[$field] : '';
			    // データが空だったら入れる
			    if( get_post_meta($post_id, $field ) == ""){
			        add_post_meta($post_id, $field , $field_value, true);
			    // 今入ってる値と違ってたらアップデートする
			    } elseif( $field_value != get_post_meta( $post_id, $field , true)){
			        update_post_meta($post_id, $field , $field_value);
			    // 入力がなかったら消す
			    } elseif( $field_value == "" ){
			        delete_post_meta($post_id, $field , get_post_meta( $post_id, $field , true ));
			    }
		    }

		}

		function add_post_notice(){
			global $pagenow;
			global $vk_post_type_manager_textdomain;
			if ( $pagenow == 'post.php' && get_post_type() == 'post_type_manage' && $_GET['action'] == 'edit' ) {
				$html  = '<div class="notice-warning notice is-dismissible">';
				$link = admin_url().'options-permalink.php';
				// $html .= '<p>'.sprintf( __('設定を更新したら<a href="%s">パーマリンク設定</a>を保存してください。', $vk_post_type_manager_textdomain ),$link ).'</p>';
				$html .= '<p>'.sprintf( __('Please save a <a href="%s">permanent link configuration</a> After updating the setting.', $vk_post_type_manager_textdomain ),$link ).'</p>';	
				$html .= '  <button type="button" class="notice-dismiss">';
				$html .= '    <span class="screen-reader-text">この通知を非表示にする</span>';
				$html .= '  </button>';
				$html .= '</div>';

				echo $html;
			}	
		}


		/*-------------------------------------------*/
		/*	作成したカスタム投稿タイプを追加
		/*-------------------------------------------*/

		function add_post_type() {
			$args = array(
				'posts_per_page'   => -1,
				'post_type'        => 'post_type_manage',
				'post_status'      => 'publish',
				'order'            => 'ASC',
				'orderby'          => 'menu_order',
				'suppress_filters' => true 
			);
		    $custom_post_types = get_posts($args);
		    if ( $custom_post_types ) {
				foreach ($custom_post_types as $key => $post) {

					/*  投稿タイプ追加
					/*-------------------------------------------*/
					$labels = array(
						'name'               => esc_html($post->post_title),
						'singular_name'      => esc_html($post->post_title),
						'menu_name'          => esc_html($post->post_title),
					);

					$post_type_items = get_post_meta( $post->ID, 'veu_post_type_items', true );
					if ( !$post_type_items ) $post_type_items = array('title');
					foreach ($post_type_items as $key => $value) {
						$supports[] = $key;
					}
					// print '<pre style="text-align:left">';print_r($post_type_items);print '</pre>';
					$menu_position = intval( mb_convert_kana ( get_post_meta( $post->ID, 'veu_menu_position', true ), 'n' ) );
					if ( !$menu_position ) $menu_position = 5;
					$args = array(
						'labels'             => $labels,
						'public'             => true,
						'has_archive'        => true,
						'menu_position'      => $menu_position,
						'supports'           => $supports
					);

					$post_type_id = esc_html( get_post_meta( $post->ID, 'veu_post_type_id', true ) );
					if ( $post_type_id ) {
						register_post_type( $post_type_id, $args );

						/*	カスタム分類を追加
						/*-------------------------------------------*/
						$veu_taxonomies = get_post_meta( $post->ID, 'veu_taxonomy', true );

						foreach ($veu_taxonomies as $key => $taxonomy) {
							// print '<pre style="text-align:left">';print_r($taxonomy);print '</pre>';
							if ( $taxonomy['slug'] && $taxonomy['label']){
								register_taxonomy(
										$taxonomy['slug'], 
										$post_type_id,
										array(
											'hierarchical' => true,
											'update_count_callback' => '_update_post_term_count',
											'label' => $taxonomy['label'],
											'singular_label' => $taxonomy['label'],
											'public' => true,
											'show_ui' => true,
										)
									);		
							}

						}

					} // if ( $post_type_id ) {



				} // foreach ($custom_post_types as $key => $post) {

		    } // if ( $custom_post_types ) {

		}

        /*-------------------------------------------*/
        /*  実行
        /*-------------------------------------------*/
        public function __construct(){
            add_action( 'init', array( $this, 'add_post_type_post_type_manage' ) );
            add_action( 'admin_init', array( $this, 'add_cap_post_type_manage' ) );
            add_action( 'save_post', array( $this, 'save_cf_value') );
            add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
            add_action( 'init', array( $this, 'add_post_type' ) ,0);
            // add_action( 'save_post', array( $this, 'add_post_notice' ) );
            add_action( 'admin_notices', array( $this, 'add_post_notice' ) );
        }

    } // class Vk_post_type_manager

    $Vk_post_type_manager = new Vk_post_type_manager();
    
}
    