<?php
/*
    Plugin Name: Calibre
    Plugin URI: https://github.com/chenxiaoquan233/Calibre-Wordpress
    Description: Wordpress plugin for calibre web managemant
    Version: 1.0
    Author: XQChen
    Author URI: https://xqchen.site
*/

require_once(__DIR__.'/CalibreDB.php');

function calibre_setting_menu() {
    add_menu_page( 
        'Calibre',
        'Calibre',
        'manage_options',
        'Calibre',
        'calibre_books_page',
        'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAPCAYAAADtc08vAAAACXBIWXMAAA7EAAAOxAGVKw4bAAACSklEQVQokXWSvU9TYRTGf+977+2X7aWkAUHEqthAIA4mQqJ/gYsbMcbRmBhHPyY3Byejg64mjCYGEwcNDi4sDA4YTVDAtIkgKpRbSj9ub4He43BvWxPxbCc5z+88ec5Rl4d5LgobAMF5s84twpo+ln2kUNPJdOTVzJfVexxS5pbHFSAV6Cleu3v/tQINcHXs53h2UGdN0xibubR6mB6zL46kM7b4AhWnAoo5lFIAm630+ohZVwIjh6oJN41OZDl+agClIRGLk7ZTpBIJmr5VAFBwUgT1X0C9WkO19hAfXK9BuVKl6ro4NT8fzsU+z90YaovuvJBhENUBaCXE4+Y/9N8VqwAiAB+a1x/ffFpayE29340YsnZ/lrMdQO60TTwaiJRSxGMxAMqesQuUACKqcTHdb18ob3q2+C18YaoD+Lq0wdavEgAiQqbHxjB02KsCQMb47mjDINnbQ8UpgWKyC1h2KBbdbjBag4SONHmAPr1SEpDMUIrtHzWUL10Ho7kUg0cjHYDreViW2XZUAEiZpYSC9exEAjMCKDVx+6UEySWiPr50r2Qnk5Qr1XYoeQTwGRFhPnf+zInwTaxI6+CcBvhWcNl2vA6gWq8jQfj4fuAAJZmEsbMSioMyzEkNMNinGeiVbgZKYVlW2LTybSvD5qLz95m1YlIXG+iF5RbvFltsN4NP3Ns/wDINolGL2YWZDaC53zyQ/p23Owh+GyAwZfbHcRHiwRI8t9nYa3gNM2j1/oOH+Gvj88vVcvMIvtRyz558BAyBJYRPfwA16ucqMNguuwAAAABJRU5ErkJggg==',
        100
    );

    add_submenu_page(
        'Calibre',
        'config',
        'config',
        'manage_options',
        'config',
        'calibre_config_page'
    );
}
add_action( 'admin_menu', 'calibre_setting_menu' );
  
function calibre_books_page() {
    ?>
    <div class="wrap">
        <h1>Calibre Books Management</h1>
        <?php
            $database_path = get_option('calibre_database_path');
            if(check_database($database_path, FALSE)) {
                ?>
                <h2><strong>
                    Book list
                </strong></h2>
                <div class = "wrap">
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>has_cover</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $book_list = CalibreDB::get_book_list(1, 5);
                        foreach($book_list as $book) {
                            ?>
                            <tr>
                                <td><?php echo $book->get_title(); ?></td>
                                <td><?php echo $book->get_author(); ?></td>
                                <td><?php echo $book->get_has_cover(); ?></td>
                            </tr>
                            <?php
                        } 
                        ?>         
                        </tbody>
                    </table>
                </div>
                <?php
            } else {
                ?>
                <p><strong>
                    Please set database path first!
                </strong></p>
                <?php
            }
    ?>
    </div>
    <?php
}

function calibre_config_page() {
    ?>
    <div class="wrap">
        <?php
            if(isset($_POST['database-path']) && check_admin_referer('calibre_config') && check_database($_POST['database-path'], TRUE)) {
                update_option('calibre_database_path', $_POST['database-path']);
                $database_path = $_POST['database-path'];
            } else {
                $database_path = get_option('calibre_database_path');
            }
        ?>
        <h1>Calibre Config</h1>
        <form method="POST" action="">
            <table class="form-table">
                <tr valign="top">
                    <th><label for="calibre-database-path">Calibre Database Dir</label></th>
                    <td><input id="calibre-database-path" name="database-path" value="<?php echo $database_path;?>" /></td>
                </tr>
                <tr valign="top">
                    <td>
                        <input type="submit" name="save" value="save" class="button-primary" />
                        <input type="submit" name="cancel" value="cancel" class="button-secondary" />
                    </td>
                </tr>
            </table>
            <?php
                wp_nonce_field('calibre_config');
            ?>
        </form>
    </div>
    <?php
}

function check_database($database_dir, $echo_message) {
    if(empty($database_dir)) {
        if($echo_message){
            hint_message("error", "Please input the directory path of metadata.db!");
        }
        return FALSE;
    }
    $database = $database_dir.'/metadata.db';
    if(file_exists($database)) {
        $db_perm = fileperms($database);
        if(($db_perm & (1 << 8)) != 0 && ($db_perm & (1 << 7)) != 0) {
            if($echo_message) {
                hint_message("update", "Update Success!");
            }
            return TRUE;
        } else {
            if($echo_message) {
                hint_message("error", "Please check read and write permission of metadata.db!");
            }
        }
    } else {
        if($echo_message) {
            hint_message("error", "metadata.db not found, or check the read permission of the directory.");
        }
    }
    return FALSE;
}

function hint_message($type, $message) {
    ?>
    <div id="message" class="<?php echo $type; ?>">
        <p><strong>
            <?php echo $message; ?>
        </strong></p>
    </div>
    <?php
}