import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
import FastModeApp from './app.jsx'
export default class ServiceProvider extends Extension {
    register() {
        RegisterRoute({
            path: '/fastmode',
            component: FastModeApp
        })
    }
    boot() {
        console.log('FastMode loadded');
    }
}