<?php 
namespace Core\Permission\Infrastructure\Helpers;

use App\Exceptions\BadException;

class SupportUINav {
    public array $data = [];
    public function setData(array $data) : SupportUINav {
        $this->data = $data;
        return $this;
    }
    public static function build(array $navs, array $roles) {
        $builds = [];
        $labels = [];
        $i = 0;
        foreach($navs as $key => $value ) {
            if(in_array($value['label'],$labels)) {
                continue;
            }
            $labels[$i] = $value['label'];
            $builds[$i] = $value;
            $builds[$i]['children'] = [];
            foreach($value['children'] as $k => $v ) {
                if(in_array($v['ability'],$roles)) {
                    $builds[$i]['children'][] = $v;
                }
            }
            $i++;
        }
        return $builds;
    }
    public function addItem(string $groupLabel,array $data) : SupportUINav {
        $index = array_search($groupLabel, array_column($this->data, 'label'));
        if($index === false) {
            throw new BadException(__("Group NAV Invalid"));
        }
        $this->data[$index]['children'] = [
            ...$this->data[$index]['children'],
            [
                    'to'      => $data['to'] ?? null,
                    'icon'    => $data['icon'] ?? 'bi bi-gear',
                    'label'   => $data['label'] ?? 'My menu',
                    'ability' => $data['ability'] ?? null,
                ]
        ];
        return $this;
    }
    public function compile() : array {
        return $this->data;
    }
}