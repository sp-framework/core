<?php

namespace Applications\Ecom\Admin\Packages\AdminLTETags\Tags\Buttons;

use Applications\Ecom\Admin\Packages\AdminLTETags\AdminLTETags;

class ButtonGroup
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $adminLTETags;

    protected $content;

    protected $params;

    protected $buttonParams = [];

    public function __construct($view, $tag, $links, $escaper, $params, $buttonParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->adminLTETags = new AdminLTETags();

        $this->params = $params;

        $this->buttonParams = $buttonParams;
    }

    public function getContent()
    {
        return $this->content;
    }

// {# Radio Button Group #}
// {% elseif buttonType == 'button-radio-group' %}
//     {% if buttonLabel %}
//         <label style="display:block;">{{buttonLabel|upper}}</label>
//     {% endif %}
//     {% if buttons %}
//         {% if buttonBlock == true %}
//             {% set hasButtonBlock = 'btn-block' %}
//         {% endif %}
//         {% if buttonPosition %}
//             {% set hasButtonPosition = 'float-' ~ buttonPosition %}
//         {% else %}
//             {% set hasButtonPosition = '' %}
//         {% endif %}
//         <div class="btn-group btn-group-toggle btn-group-{{buttonGroupSize|default('sm')}} {{hasButtonBlock}} {{hasButtonPosition}}" data-toggle="buttons">
//         {% for buttonKey, button in buttons %}
//             {% if not button.title == false %}
//                 {% if button.title %}
//                     {% set hasButtonTitle = button.title %}
//                 {% else %}
//                     {% set hasButtonTitle = ' missing_button_title' %}
//                 {% endif %}
//             {% elseif button.title == false %}
//                     {% set hasButtonTitle = '' %}
//             {% endif %}
//             {% if button.size %}
//                 {% set hasButtonSize = 'btn-' ~ button.size %}
//             {% else %}
//                 {% set hasButtonSize = 'btn-sm' %}
//             {% endif %}
//             {% if button.flat == true %}
//                 {% set hasButtonFlat = 'btn-flat' %}
//             {% endif %}
//             {% if button.type %}
//                 {% set hasButtonType = 'btn-' ~ button.type %}
//             {% else %}
//                 {% set hasButtonType = 'btn-primary' %}
//             {% endif %}
//             {% if button.style == 'outline' %}
//                 {% set hasButtonType = 'btn-outline-' ~ button.type %}
//             {% elseif button.style == 'gradient' %}
//                 {% set hasButtonType = 'bg-gradient-' ~ button.type %}
//             {% endif %}
//             {% if button.icon %}
//                 {% if button.title %}
//                     {% if button.iconPosition == 'after' %}
//                         {% set hasButtonIcon = '<i class="fas fa-fw fa-' ~ button.icon ~ ' ml-1"></i>' %}
//                     {% else %}
//                         {% set hasButtonIcon = '<i class="fas fa-fw fa-' ~ button.icon ~ ' mr-1"></i>' %}
//                     {% endif %}
//                 {% else %}
//                     {% set hasButtonIcon = '<i class="fas fa-fw fa-' ~ button.icon ~ '"></i>' %}
//                 {% endif %}
//             {% else %}
//                 {% set hasButtonIcon = '' %}
//             {% endif %}
//             {% if fieldRadioButtonGroupButtonChecked %}
//                 {% if fieldRadioButtonGroupButtonChecked == button.dataValue %}
//                     {% set hasButtonChecked = 'checked' %}
//                     {% set hasButtonCheckedClasses = 'active focus' %}
//                     {% set hasButtonCheckedBgClass = 'bg-' ~ button.type %}
//                 {% else %}
//                     {% set hasButtonChecked = '' %}
//                     {% set hasButtonCheckedClasses = '' %}
//                     {% set hasButtonCheckedBgClass = '' %}
//                 {% endif %}
//             {% else %}
//                 {% if button.checked %}
//                     {% set hasButtonChecked = 'checked' %}
//                     {% set hasButtonCheckedClasses = 'active focus' %}
//                 {% else %}
//                     {% set hasButtonChecked = '' %}
//                     {% set hasButtonCheckedClasses = '' %}
//                 {% endif %}
//             {% endif %}
//             {% if button.hidden == true %}
//                 {% set hasButtonHidden = 'hidden' %}
//             {% else %}
//                 {% set hasButtonHidden = '' %}
//             {% endif %}
//             {% if button.disabled == true %}
//                 {% set hasButtonDisabled = 'disabled' %}
//                 {% set hasButtonCursor = 'style=cursor:default;' %}
//             {% else %}
//                 {% set hasButtonDisabled = '' %}
//                 {% set hasButtonCursor = 'style=cursor:pointer;' %}
//             {% endif %}
//             {% if componentId and sectionId %}
//                 {% set hasButtonId = componentId ~ '-' ~ sectionId ~ '-' ~ buttonKey %}
//             {% else %}
//                 {% set hasButtonId = buttonKey %}
//             {% endif %}
//             <label class="btn {{hasButtonSize}} {{hasButtonFlat}} {{hasButtonType}} {{hasButtonCheckedClasses}} {{hasButtonDisabled}} {{button.buttonAdditionalClass}} {{hasButtonCheckedBgClass}}" {{hasButtonCursor}} {{hasButtonHidden}}>
//                 <input type="radio" name="options" id="{{hasButtonId}}" autocomplete="off" data-value="{{button.dataValue}}" {{hasButtonChecked}}>
//                 {% if button.iconPosition %}
//                     {% if button.iconPosition == 'after' %}
//                         {{buttonIdMissing|upper}}{{hasButtonTitle|upper}} {{hasButtonIcon|raw}}
//                     {% else %}
//                         {{hasButtonIcon|raw}} {{buttonIdMissing|upper}}{{hasButtonTitle|upper}}
//                     {% endif %}
//                 {% else %}
//                     {{hasButtonIcon|raw}} {{buttonIdMissing|upper}}{{hasButtonTitle|upper}}
//                 {% endif %}
//             </label>
//         {% endfor %}
//         </div>
//     {% else %}
//         <span class="text-uppercase text-danger">{{('TEMPLATE ERROR: buttons (ARRAY) MISSING')}}</span>
//     {% endif %}
// {# button-group #}
}