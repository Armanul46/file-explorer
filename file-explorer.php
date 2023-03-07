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

    if( ! empty( $_GET['delete'] ) ) {

        $delete = $_GET['delete'];
        if( is_file( $delete ) ) {
            unlink( $delete );
            echo "deleted successfully";
        } else {
            delete_dir( $delete );
            echo "deleted successfully";
        }
        
        
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
    $settings_page_link = admin_url( 'admin.php?page=my-file-explorer' );
    
    
    // add file / folder 
    if ( ! empty( $_POST['submit'] ) && ! empty( $_FILES['file_upload'] ) ) {
        $file_upload = $_FILES['file_upload'];
        foreach( $file_upload['name'] as $key => $value ) {
            $target_dir = $directory;
            $target_file = $target_dir . '/' . basename( $value );
            
            if( move_uploaded_file( $file_upload['tmp_name'][$key], $target_file ) ) {
                echo "File uploaded successfully.";
            } else {
                echo "Sorry, file not uploaded";
            }
        }
    }
   ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file_upload[]" id="file_upload">
        <input type="submit" value="Upload File" name="submit">
    </form>
   <?php
   foreach ($files as $file) {

        if ( $file == '.' || $file == '..' || $file == '.tmb' ) {
            continue;
        }
        echo "<tr>";
        if( empty( $_GET['path'] ) ) {
            if( is_dir( $directory . '/' . $file ) ) {
                echo '<td><a href="' . add_query_arg( 'path', $directory . '/' . $file, $settings_page_link ) . '">'. esc_html( $file ) .'</a></td>';
            }
        } else {
            if( is_dir( $directory . '/' . $file ) ) {
                echo '<td><a href="' . add_query_arg( 'path', $directory . '/' . $file, $settings_page_link ) . '">'. esc_html( $file ).'</a></td>';
            } else {
                echo '<td><a href="' . add_query_arg( 'file', $directory . '/' . $file, $settings_page_link ) . '">'. esc_html( $file ) .'</a></td>';
            }
            echo '<td><a href="' . add_query_arg( 'delete', $directory . '/' . $file, $settings_page_link ) . '">delete</a></td>';
        }
        
        
     
       
   }

   echo "</tr></table>";

    
}

function delete_dir( $path ) {
    $files_path = scandir( $path );

    if( count( $files_path ) > 2 ) {
        foreach( $files_path as $file ) {
            if( '.' != $file && '..' != $file ) {
                $file_path = $path . DIRECTORY_SEPARATOR . $file;
                
                if( is_dir( $file_path ) ) {
                    delete_dir( $file_path );
                }else{
                    unlink( $file_path );
                }
            }
        }
    }
   rmdir( $path );
}