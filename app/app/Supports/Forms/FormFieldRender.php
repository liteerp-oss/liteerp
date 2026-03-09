<?php 
namespace App\Supports\Forms;
final class FormFieldRender {
    function __construct(
    private string $type = FormFieldType::TEXT,
    private string $value = '',
    private string $key = '',
    private ?array $options = [],
    private string $label = '',
    private string $placeHolder = '',
    private bool $required = false
    ) {

    }
    public function toArray(){
        $attributes = [
            'type' => $this->type,
            'value' => $this->value,
            'key' => $this->key,
            'label' => $this->label,
            'placeHolder' => $this->placeHolder,
            'required' => $this->required
        ];
        if($this->type === FormFieldType::SELECT) {
            $attributes = [
                ...$attributes,
                'options' => $this->options,
            ];
        }
        return $attributes;
    }
}
