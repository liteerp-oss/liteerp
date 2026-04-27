export default class PermissionNode {
    constructor(){
        this.node = '';
    }
    fromNode(name) {
        this.node = name;
        return this;
    }
    getPermission(name){
        return `erp.${this.node}.${this.node}-${name}`;
    }
}