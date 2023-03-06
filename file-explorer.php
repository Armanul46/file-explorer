<?php
/*
Plugin Name: My File Explorer
Plugin URI: http://example.com/my-file-explorer
Description: A custom file explorer plugin for WordPress.
Version: 1.0
Author: Your Name
Author URI: http://example.com
License: GPL2
*/

// Add a menu item for the plugin
function my_file_explorer_menu() {
    add_menu_page(
        'My File Explorer',
        'File Explorer',
        'manage_options',
        'my-file-explorer',
        'my_file_explorer_page'
    );
}
add_action('admin_menu', 'my_file_explorer_menu');

// Create the file explorer page
function my_file_explorer_page() {

    if( ! empty( $_POST['delete'] ) ) {
        $delete_file = ! empty( $_POST['delete'] ) ? $_POST['delete'] : '';
        
        
    }

    if( ! empty( $_POST['submit'] ) ) {
        $edited_value = ! empty( $_POST['edit_file'] ) ? $_POST['edit_file'] : '';
        if( ! empty( $_GET['file'] ) && is_writable( $_GET['file'] ) ) {
            file_put_contents( $_GET['file'], $edited_value, LOCK_EX );
        }
        
    }
    
    if( ! empty( $_GET['file'] ) && is_readable( $_GET['file'] ) ) {
        $file = file_get_contents( sanitize_text_field( $_GET['file'] ) );
        ?>
        <form action="" method="POST">
            <label for="edit">File Edit</label>
            <div>
                <textarea name="edit_file" cols="100" rows="100"><?php echo ! empty( $file ) ? $file : ''; ?></textarea>
            </div>
            <input type="submit" name="submit">
        </form>
        <?php
        return;

    }
    $directory = ABSPATH;
    echo "<h1>File Explorer</h1><table><tr><th>Name</th><th>Delete</th></tr>";


    if( ! empty( $_GET['path'] ) ) {
        $directory =  $_GET['path'];
        $files = scandir( $_GET['path'] );
        
    }
    $files = scandir( $directory );
   
   foreach ($files as $file) {

        if ( $file == '.' || $file == '..' || $file == '.tmb' ) {
            continue;
        }
        echo "<tr>";
        if( empty( $_GET['path'] ) ) {
            if( is_dir( $directory . '/' . $file ) ) {
                echo '<td><a href="' . add_query_arg( 'path', $directory . '/' . $file ) . '">'. esc_html( $file ) .'</a></td>';
                echo '<td><a href="' . add_query_arg( 'delete', $directory . '/' . $file ) . '">delete</a></td>';
            }
        } else {
            if( is_dir( $directory . '/' . $file ) ) {
                echo '<td><a href="' . add_query_arg( 'path', $directory . '/' . $file ) . '">'. esc_html( $file ).'</a></td>';
                echo '<td><a href="' . add_query_arg( 'delete', $directory . '/' . $file ) . '">delete</a></td>';
            } else {
                echo '<td><a href="' . add_query_arg( 'file', $directory . '/' . $file ) . '">'. esc_html( $file ) .'</a></td>';
                echo '<td><a href="' . add_query_arg( 'delete', $directory . '/' . $file ) . '">delete</a></td>';
            }
        }
        
        
     
       
   }

   echo "</tr></table>";

    
}