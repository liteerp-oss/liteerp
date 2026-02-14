import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
import InventoryTrackingApp from './app.jsx'
export default class ServiceProvider extends Extension {
    register() {
        RegisterRoute({
            path: '/inventorytracking',
            component: InventoryTrackingApp
        })
    }
    boot() {
        //console.log('InventoryTracking loadded');
    }
}