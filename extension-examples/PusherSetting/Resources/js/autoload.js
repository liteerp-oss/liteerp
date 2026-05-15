import App from './app.jsx'
import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
export default class ServiceProvider extends Extension {
    register() {
        RegisterRoute({
            path: '/pusher-setting',
            component: App
        })
    }
    boot() { }
}
