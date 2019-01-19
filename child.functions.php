function houzez_send_emails( $user_email, $subject, $message ){
        $headers = 'From: youremail@gmail.com' . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $enable_html_emails = houzez_option('enable_html_emails');
        $enable_email_header = houzez_option('enable_email_header');
        $enable_email_footer = houzez_option('enable_email_footer');
        if( $enable_html_emails != 0 ) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        }
        $enable_html_emails = houzez_option('enable_html_emails');
        $email_head_logo = houzez_option('email_head_logo', false, 'url');
        $email_head_bg_color = houzez_option('email_head_bg_color');
        $email_foot_bg_color = houzez_option('email_foot_bg_color');
        $email_footer_content = houzez_option('email_footer_content');
        $social_1_icon = houzez_option('social_1_icon', false, 'url');
        $social_1_link = houzez_option('social_1_link');
        $social_2_icon = houzez_option('social_2_icon', false, 'url');
        $social_2_link = houzez_option('social_2_link');
        $social_3_icon = houzez_option('social_3_icon', false, 'url');
        $social_3_link = houzez_option('social_3_link');
        $social_4_icon = houzez_option('social_4_icon', false, 'url');
        $social_4_link = houzez_option('social_4_link');
        $socials = '';
        if( !empty($social_1_icon) || !empty($social_2_icon) || !empty($social_3_icon) || !empty($social_4_icon) ) {
            $socials = '<div style="font-size: 0; text-align: center; padding-top: 20px;">';
            $socials .= '<p style="margin:0;margin-bottom: 10px; text-align: center; font-size: 14px; color:#777777;">'.esc_html__('Follow us on', 'houzez').'</p>';
            if( !empty($social_1_icon) ) {
                $socials .= '<a href="'.esc_url($social_1_link).'" style="margin-right: 5px"><img src="'.esc_url($social_1_icon).'" width="" height="" alt=""> </a>';
            }
            if( !empty($social_2_icon) ) {
                $socials .= '<a href="'.esc_url($social_2_link).'" style="margin-right: 5px"><img src="'.esc_url($social_2_icon).'" width="" height="" alt=""> </a>';
            }
            if( !empty($social_3_icon) ) {
                $socials .= '<a href="'.esc_url($social_3_link).'" style="margin-right: 5px"><img src="'.esc_url($social_3_icon).'" width="" height="" alt=""> </a>';
            }
            if( !empty($social_4_icon) ) {
                $socials .= '<a href="'.esc_url($social_4_link).'" style="margin-right: 5px"><img src="'.esc_url($social_4_icon).'" width="" height="" alt=""> </a>';
            }
            $socials .= '</div>';
        }
        if( $enable_email_header != 0 ) {
            $email_content = '<div style="text-align: center; background-color: ' . esc_attr($email_head_bg_color) . '; padding: 16px 0;">
                            <img src="' . esc_url($email_head_logo) . '" alt="logo">
                        </div>';
        }
        $email_content .= '<div style="background-color: #F6F6F6; padding: 30px;">
                            <div style="margin: 0 auto; width: 620px; background-color: #fff;border:1px solid #eee; padding:30px;">
                                <div style="font-family:\'Helvetica Neue\',\'Helvetica\',Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;display:block;max-width:600px;margin:0 auto;padding:0">
                                '.$message.'
                                </div>
                            </div>
                        </div>';
        if( $enable_email_footer != 0 ) {
            $email_content .= '<div style="padding-top: 30px; text-align:center; padding-bottom: 30px; font-family:\'Helvetica Neue\',\'Helvetica\',Helvetica,Arial,sans-serif;">
                            <div style="width: 640px; background-color: ' . $email_foot_bg_color . '; margin: 0 auto;">
                                ' . $email_footer_content . '
                            </div>
                            ' . $socials . '
                        </div>';
        }
        if( $enable_html_emails != 0 ) {
            $email_messages = $email_content;
        } else {
            $email_messages = $message;
        }
        @wp_mail(
            $user_email,
            $subject,
            $email_messages,
            $headers
        );
    };
