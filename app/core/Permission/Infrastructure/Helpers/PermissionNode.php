<?php 
namespace Core\Permission\Infrastructure\Helpers;

use App\Exceptions\BadException;
use Illuminate\Support\Str;

class PermissionNode {
    public array $build = [];
    private ?string $name = null;
    private ?string $node = null;
    private array $permissions = [];
    public function setNode(string $node) : PermissionNode {
        $this->node = $node;
        return $this;
    }
    public function setGroup(string $name) : PermissionNode {
        $this->name = $name;
        return $this;
    }
    public function setPermission(string $name) : PermissionNode {
        $this->checkValid();
        $this->permissions[count($this->permissions)] = "erp.$this->node.$this->node-$name";
        return $this;
    }
    public function getPermission(string $name) : string {
        
        return "erp.$this->node.$this->node-$name";
    }
    public function compile() : array {
        $this->checkValid();
        if(count($this->permissions) == false ) {
            throw new BadException(__("You have not add permissions"));
        }
        if(!$this->name ) {
            throw new BadException(__("You have not add label for permission"));
        }
        $this->build[$this->name] = [
            ...$this->permissions
        ];
        return $this->build;
    }
    public function compileListItem() : array {
        $this->checkValid();
        if(count($this->permissions) == false ) {
            throw new BadException(__("You have not add permissions"));
        }
        if(!$this->name ) {
            throw new BadException(__("You have not add label for permission"));
        }
        return $this->permissions;
    }

    private function checkValid() : BadException | bool {
        if(!$this->node) {
            throw new BadException(__("You have not node for custom permission"));
        }
        return true;
    }
}