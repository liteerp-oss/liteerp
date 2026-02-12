import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
import HrmApp from './app.jsx'
export default class ServiceProvider extends Extension {
    register() {
        RegisterRoute({
            path: '/fastmode',
            component: HrmApp
        })
    }
    boot() {
        console.log('HRM loadded');
    }
}