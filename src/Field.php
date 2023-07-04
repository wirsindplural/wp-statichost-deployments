<?php

namespace Wirsindplural\statichostDeployments;

class Field
{
    /**
     * Render an input[type=text] field
     *
     * @param array $args
     * @return void
     */
    public static function text($args = [])
    {
        ?><div>
            <input type="text" class="regular-text" name="<?= esc_attr($args['name']); ?>" value="<?= $args['value'] ?>">
            <?= !empty($args['description']) ? "<p class=\"description\">{$args['description']}</p>" : ''; ?>
        </div><?php
    }
}
