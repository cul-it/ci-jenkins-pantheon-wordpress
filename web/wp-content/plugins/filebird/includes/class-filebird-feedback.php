<?php

class FileBird_Feedback
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_filebird_feedback'));
    }

    public function enqueue_filebird_feedback()
    {
        if (!in_array(get_current_screen()->id, ['plugins', 'plugins-network'], true)) {
            return;
        }

        add_action('admin_footer', array($this, 'form_feedback'));
        wp_enqueue_script('njt-filebird-feedback', NJT_FILEBIRD_PLUGIN_URL . '/admin/js/filebird-feedback.js', array('jquery'), '1.0', true);
    }

    public function form_feedback()
    {
        $reasons = [
            'no_needed' => [
                'content' => __('I do not need this plugin anymore.', NJT_FILEBIRD_TEXT_DOMAIN),
            ],
            'found_better_plugin' => [
                'content' => __('I have another plugin that do the job better', NJT_FILEBIRD_TEXT_DOMAIN),
                'placeholder' => __('Please tell us which one', NJT_FILEBIRD_TEXT_DOMAIN),
            ],
            'not_know_using' => [
                'content' => __('I don\'t know how to use it', NJT_FILEBIRD_TEXT_DOMAIN),
                'document' => __('Please read FileBird documentation <a target="_blank" href="https://ninjateam.gitbook.io/filebird/">here</a> or <a target="_blank" href="https://ninjateam.org/support/">chat with us</a> if you need help', NJT_FILEBIRD_TEXT_DOMAIN)
            ],
            'temporary_deactivation' => [
                'content' => __('This is temporary. I\'ll use it again soon.', NJT_FILEBIRD_TEXT_DOMAIN),
            ],
            'filebird_pro' => [
                'content' => __('I have purchased FileBird Pro', NJT_FILEBIRD_TEXT_DOMAIN),
            ],
            'not_working' => [
              'content' => __('It\'s not working on my website', NJT_FILEBIRD_TEXT_DOMAIN),
              'support' => __('Need help? We are ready to answer your questions. <a target="_blank" href="https://ninjateam.org/support/">Contact Support</a>', NJT_FILEBIRD_TEXT_DOMAIN),
            ],
            'other' => [
                'content' => __('Other', NJT_FILEBIRD_TEXT_DOMAIN),
                'placeholder' => __('Please share a reason...', NJT_FILEBIRD_TEXT_DOMAIN),
            ],
        ];
        ?>
        <div id="filebird-feedback-window" style="display:none;">
        <div id="filebird-feedback-content">
        <h2>Quick Feedback - FileBird</h2>
        <p class="filebird-feedback-title">Sadly that you are going to deactivate FileBird, can you please tell us why:</p>
        <?php if(NJT_FB_V == '11') unset($reasons['filebird_pro']); ?>
        <?php foreach ($reasons as $key => $arrValue): ?>
            <p class="filebird-feedback-item">
                <input
                    type="radio" id="<?php echo esc_attr($key) ?>"
                    name="reasons"
                    value="<?php echo esc_attr($arrValue['content']) ?>"
                >
                <label for="<?php echo esc_attr($key) ?>"><?php echo esc_html($arrValue['content']); ?></label>
            </p>
            <?php if ($key == 'not_working'): ?>
                <p id="feedback-notwork" class="hidden"><?php echo ($reasons['not_working']['support']) ?></p>
            <?php endif;?>
            <?php if ($key == 'not_know_using'): ?>
                <p id="feedback-document" class="hidden"><?php echo ($reasons['not_know_using']['document']) ?></p>
            <?php endif;?>
            <?php if ($key == 'found_better_plugin'): ?>
                <textarea id="feedback-suggest-plugin" rows="1" placeholder="<?php echo esc_attr($reasons['found_better_plugin']['placeholder']) ?>" class="hidden"></textarea>
            <?php endif;?>
        <?php endforeach;?>
            <textarea id="feedback-description" rows="3" placeholder="<?php echo esc_attr($reasons['other']['placeholder']) ?>" class="hidden"></textarea>
            <div class="filebird-feedback-action">
                <button class="button button-primary" id="feedback-submit"><?php echo __('Submit and Deactivate', NJT_FILEBIRD_TEXT_DOMAIN) ?></button>
                <button id="feedback-skip"><?php _e('Skip and Deactivate', NJT_FILEBIRD_TEXT_DOMAIN)?></button>
            </div>
          </div>
        </div>
    <?php
}
}
?>