<?php

namespace App\Components\Forms;


class Form
{

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function input(string $type, string $required = "", string $name, ?string $nameClass = "", string $label, $autocomplete): string
    {
        return <<<HTML
        <div class="form-group">
            <label for="field{$name}">{$label}</label>
            <input type="{$type}" id="{$name}" name="{$name}" class="form-control {$nameClass}" value="" $required  autocomplete="{$autocomplete}">
        </div>
HTML;
    }

    public function select(string $required = "", string $name, string $label, array $options = null)
    {

        $optionsHTML = [];
        $value = $this->getValue($name);
        foreach ($options as $k => $v) {
            $selected = $k == $value ? " selected" : "";
            $optionsHTML[] = "<option value=\"$k\"  $selected >$v</option>";
        }
        $optionsHTML = implode('', $optionsHTML);
        return <<<HTML
         <div class="form-group">
           <label for="field{$name}">{$label}</label>
            <select id="{$name}" name="{$name}" class="form-control" $required>
            {$optionsHTML}
            </select>
        </div>
HTML;
    }


    public function getValue(string $name)
    {
        if (is_array($this->data)) {
            return $this->data[$name] ?? null;
        }
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $value = $this->data->$method();
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }
}
