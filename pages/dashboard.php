<div class="wrap">
    <h1 class="wp-heading-inline">Promotator</h1>
    <div id="promotator-composer" class="promotator-composer postbox">
        <div class="row">
            <h2 class="hndle ui-sortable-handle"><span>Pick Receiving Role</span></h2>
            <select id="receiving-role" class="wide-fat">
                <option value=" ">Choose role</option>
                <?php
                global $wp_roles;
                $roles_ = $wp_roles->roles;

                foreach ( $roles_ as $role_key => $role_ ) {
                    ?>

                    <option value="<?php echo $role_key; ?>"><?php echo $role_[ "name" ]; ?></option>

                    <?php
                }
                ?>
            </select>
        </div>
        <div class="row">
            <h2 class="hndle ui-sortable-handle"><span>Pick Email Template</span></h2>
            <select id="email-template" class="wide-fat">
                <option value=" ">Choose template</option>
                <?php
                $handler = glob( plugin_dir_path( __DIR__ ) . "mailings/*.html" );
                foreach ( $handler as $file ) {
                    $file_name = end( explode( "/", $file ) );
                    ?>

                    <option value="<?php echo $file_name; ?>"><?php echo explode( ".html", $file_name )[0]; ?></option>

                    <?php
                }
                ?>
            </select>
        </div>
        <div class="row">
            <h2 class="hndle ui-sortable-handle"><span>Pick Posts</span></h2>
            <div id="posts-selector">
                <?php
                $args = array(
                    "posts_per_page" => -1,
                    "post_type" => "post",
                    "posts_status" => "publish",
                    "orderby" => "ID",
                    "order" => "DESC"
                );
                $posts_ = get_posts( $args );

                foreach ( $posts_ as $post_ ) {
                    $post_featured_image = get_the_post_thumbnail_url( $post_->ID, "full" );
                    ?>

                    <div id="post-<?php echo $post_->ID; ?>" class="post-container">
                        <div class="featured-image" style="background-image: url(<?php echo $post_featured_image; ?>);"></div>
                        <div class="content-container">
                            <h1 class="post-title"><?php echo $post_->post_title; ?></h1>
                        </div>
                    </div>

                    <?php
                }
                ?>
            </div>
        </div>
        <div class="row">
            <h2 class="hndle ui-sortable-handle"><span>Pick the Subject</span></h2>
            <input type="text" id="subject" class="wide-fat" placeholder="Choose your subject">
        </div>
        <div class="row">
            <button id="send-button" class="button button-primary button-large">Send the mailing</button>
        </div>
    </div>
</div>
